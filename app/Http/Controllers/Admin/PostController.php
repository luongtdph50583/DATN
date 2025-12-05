<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Post;
use App\Models\Media;
use App\Models\ClubMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PostUpdateLog;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomNotification;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index(Request $request)
    {
        $query = Post::with(['club', 'user']);

        // ✅ Lọc theo từ khóa (tiêu đề hoặc người đăng)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // ✅ Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ✅ Lọc theo hiển thị
        if ($request->filled('is_visible')) {
            $query->where('is_visible', $request->is_visible);
        }

        // ✅ Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Phân trang + giữ tham số lọc
        $posts = $query->latest()->paginate(15);
        $posts->appends($request->query());

        // Nạp danh sách CLB để hiển thị dropdown lọc
        $clubs = Club::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'clubs'));
    }


    /**
     * Ẩn/Hiện bài viết
     */
    public function toggle($id)
    {
        $post = Post::findOrFail($id);
        $post->status = $post->status === 'visible' ? 'hidden' : 'visible';
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Đã cập nhật trạng thái bài viết.');
    }
    public function restore($id)
    {
        $post = Post::onlyTrashed()->with('club', 'user')->findOrFail($id);
        $post->restore();

        Media::onlyTrashed()
            ->where('related_type', 'post')
            ->where('related_id', $post->id)
            ->restore();

        // ✅ Gửi thông báo cho chủ nhiệm CLB
        if ($post->club) {
            // giả sử bạn có quan hệ club->manager hoặc club->owner
            $clubManager = $post->club->manager ?? null;

            if ($clubManager) {
                $batchId = 'post_restored_' . $post->id . '_' . Str::random(6);

                dispatch(new SendNotificationJob(
                    userId: $clubManager->id,
                    title: 'Bài viết đã được khôi phục',
                    content: "Bài viết \"{$post->title}\" trong CLB \"{$post->club->name}\" đã được khôi phục.",
                    sendVia: 'both',
                    batchId: $batchId,
                    force: false
                ));
            }
        }

        return redirect()->route('admin.posts.trash')
            ->with('success', 'Bài viết và media đã được khôi phục, đã gửi thông báo cho chủ nhiệm CLB.');
    }


    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        // ✅ Xóa file media vật lý và bản ghi
        $mediaList = Media::withTrashed()
            ->where('related_type', 'post')
            ->where('related_id', $post->id)
            ->get();

        foreach ($mediaList as $media) {
            $filePath = storage_path('app/public/' . $media->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $media->forceDelete();
        }

        // ✅ Xóa ảnh đại diện nếu có
        if ($post->thumbnail && file_exists(storage_path('app/public/' . $post->thumbnail))) {
            unlink(storage_path('app/public/' . $post->thumbnail));
        }

        // ✅ Xóa vĩnh viễn bài viết
        $post->forceDelete();

        return redirect()->route('admin.posts.trash')
            ->with('success', 'Bài viết đã bị xóa vĩnh viễn.');
    }
    public function trash(Request $request)
    {
        $query = Post::onlyTrashed()->with(['user', 'club']);

        // ✅ Lọc theo từ khóa (tiêu đề)
        if ($request->filled('keyword')) {
            $query->where('title', 'like', "%{$request->keyword}%");
        }

        // ✅ Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // ✅ Lọc theo người đăng
        if ($request->filled('author')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->author}%");
            });
        }

        // Phân trang + giữ tham số lọc
        $posts = $query->orderBy('deleted_at', 'desc')->paginate(10);
        $posts->appends($request->query());

        // Nạp danh sách CLB để hiển thị dropdown lọc
        $clubs = Club::orderBy('name')->get();

        return view('admin.posts.trash', compact('posts', 'clubs'));
    }



    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $reason = $request->input('reason', 'Vi phạm nội quy');

        // ✅ Xóa mềm media liên quan
        Media::where('related_type', 'post')
            ->where('related_id', $post->id)
            ->delete(); // chỉ gán deleted_at

        // ✅ Không xóa file thumbnail — giữ nguyên để khôi phục

        // ✅ Xóa mềm bài viết
        $post->delete();

        // ✅ Lưu log xóa
        $log = PostUpdateLog::create([
            'post_id' => $post->id,
            'changed_by' => auth()->id(),
            'changes' => [
                'deleted' => [null, 'deleted'],
                'delete_reason' => $reason,
            ],
        ]);

        // ✅ Gửi thông báo cho người đăng bài
        if ($post->user) {
            $batchId = 'post_deleted_' . $post->id . '_' . Str::random(6);

            dispatch(new SendNotificationJob(
                userId: $post->user->id,
                title: 'Bài viết bị xóa',
                content: "Bài viết \"{$post->title}\" đã bị xóa. Lý do: {$reason}",
                sendVia: 'both',
                batchId: $batchId,
                force: false
            ));
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Đã xóa bài viết, lưu log và gửi thông báo cho tác giả.');
    }



    public function show($id)
    {
        $post = Post::with(['club', 'user', 'media'])->findOrFail($id);
        return view('admin.posts.show', compact('post'));
    }



    /**
 * Upload file từ CKEditor (AJAX endpoint)
 *
 * Chức năng:
 * - Xử lý upload từ CKEditor (drag & drop, paste image, upload button)
 * - Check trùng file hash → tránh duplicate
 * - Lưu tạm vào uploads/posts (temp folder)
 * - Tạo Media record với related_id = 0 (orphan)
 * - Return URL để CKEditor insert vào content
 *

 * Flow:
 * 1. User upload file trong CKEditor
 * 2. Check file hash → nếu trùng → return file cũ
 * 3. File mới → lưu vào uploads/posts (temp)
 * 4. Media record tạo với related_id = 0
 * 5. Khi save post → update related_id
 * 6. Khi save post → move file vào folder đúng
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function uploadFile(Request $request)
{
    try {
        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Không có file nào được gửi.'
            ]);
        }

        $file = $request->file('file');
        $fileType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();

        // ✅ CHECK DUPLICATE FILE
        $fileHash = md5_file($file->getRealPath());

        $existingMedia = Media::where('uploaded_by', auth()->id())
            ->where('file_type', $fileType)
            ->get()
            ->first(function ($media) use ($fileHash) {
                $existingPath = storage_path('app/public/' . $media->file_path);
                if (file_exists($existingPath)) {
                    return md5_file($existingPath) === $fileHash;
                }
                return false;
            });

        if ($existingMedia) {
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $existingMedia->file_path),
                'name' => $existingMedia->file_name,
                'type' => $existingMedia->file_type,
                'message' => 'File đã tồn tại'
            ]);
        }

        // ✅ UPLOAD FILE MỚI - Lưu vào temp folder
        $timestamp = time();
        $sanitizedName = $this->sanitizeFileName($originalName);
        $finalName = $timestamp . '_' . $sanitizedName;

        // Lưu vào uploads/posts (temp location)
        $folder = 'uploads/posts';
        $path = $file->storeAs($folder, $finalName, 'public');

        // ✅ TẠO MEDIA RECORD ORPHAN
        // Dùng Post::class thay vì 'post' để khớp với store()
        $media = Media::create([
            'file_name' => $finalName,
            'file_path' => $path,
            'file_type' => $fileType,
            'related_id' => 0, // Orphan
            'related_type' => Post::class, // ← FIX: dùng Post::class thay vì 'post'
            'uploaded_by' => auth()->id(),
        ]);

        // Return URL để insert vào CKEditor
        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
            'name' => $finalName,
            'type' => $fileType,
        ]);

    } catch (\Exception $e) {
        Log::error('File upload failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Lỗi upload: ' . $e->getMessage()
        ]);
    }
}
/**

 * Flow xử lý CHUẨN:
 * 1. Validate input
 * 2. Xử lý thumbnail (xóa cũ nếu có)
 * 3. Cập nhật Post data
 * 4. Parse content HTML → lấy danh sách files đang dùng
 * 5. Soft delete Media không còn trong content
 * 6. Di chuyển files từ uploads/posts → folders đúng (images/videos/...)
 * 7. Tạo/Update Media records
 * 8. Gán orphan media vào post
 * 9. Cleanup orphan media
 *
 * @param Request $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:post,notice,document',
            'status' => 'required|in:pending,approved,rejected',
            'visibility' => 'required|in:internal,public',
            'club_id' => 'required|exists:clubs,id',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {

            // ================================
            // ✔ BƯỚC 1: XỬ LÝ THUMBNAIL
            // ================================
            if ($request->hasFile('thumbnail')) {

                if ($post->thumbnail && Storage::disk('public')->exists($post->thumbnail)) {
                    Storage::disk('public')->delete($post->thumbnail);
                }

                $file = $request->file('thumbnail');
                $timestamp = time();
                $sanitizedName = $this->sanitizeFileName($file->getClientOriginalName());
                $fileName = $timestamp . '_' . $sanitizedName;

                $path = $file->storeAs('uploads/thumbnails', $fileName, 'public');
                $post->thumbnail = $path;

                // Lưu thay đổi thumbnail vào validated để log
                $validated['thumbnail'] = $path;
            }

            // ================================
            // ✔ BƯỚC 2: LƯU ORIGINAL ĐỂ LOG
            // ================================
            $original = $post->getOriginal();

            // ================================
            // ✔ BƯỚC 3: CẬP NHẬT POST
            // ================================
            $post->title = $validated['title'];
            $post->content = $validated['content'];
            $post->type = $validated['type'];
            $post->status = $validated['status'];
            $post->visibility = $validated['visibility'];
            $post->club_id = $validated['club_id'];
            $post->is_visible = $validated['is_visible'] ?? true;
            $post->is_featured = $validated['is_featured'] ?? false;

            if ($validated['status'] === 'approved' && $post->status !== 'approved') {
                $post->approved_by = auth()->id();
                $post->approved_at = now();
                $post->published_at = $post->published_at ?? now();
            }

            $post->save();

            // ================================
            // 🔥 BƯỚC 4: LOG THAY ĐỔI
            // ================================
            $changes = [];

            foreach ($validated as $field => $newValue) {
                if (!array_key_exists($field, $original))
                    continue;
                $oldValue = $original[$field];

                if ($oldValue != $newValue) {
                    $changes[$field] = [
                        $oldValue,
                        $newValue
                    ];
                }
            }

            // Lưu log nếu có thay đổi
            if (!empty($changes)) {
                PostUpdateLog::create([
                    'post_id' => $post->id,
                    'changed_by' => auth()->id(),
                    'changes' => $changes,
                ]);
            }

            // ================================
            // ✔ BƯỚC 5: MEDIA HANDLING
            // ================================
            $usedFiles = $this->extractMediaFromContent($post->content);

            $allMedia = Media::withTrashed()
                ->where('related_type', 'post')
                ->where('related_id', $post->id)
                ->get();

            foreach ($allMedia as $media) {
                if (!in_array($media->file_name, $usedFiles)) {
                    if (!$media->trashed()) {
                        $media->delete();
                    }
                } else {
                    if ($media->trashed()) {
                        $media->restore();
                    }
                }
            }

            $tempFiles = Storage::disk('public')->files('uploads/posts');

            foreach ($tempFiles as $relativePath) {
                $fullPath = storage_path('app/public/' . $relativePath);

                if (!file_exists($fullPath))
                    continue;

                $fileName = basename($fullPath);

                if (!in_array($fileName, $usedFiles))
                    continue;

                $mime = mime_content_type($fullPath);
                $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

                $folderType = match (true) {
                    Str::startsWith($mime, 'image/') => 'images',
                    Str::startsWith($mime, 'video/') ||
                    $mime === 'application/octet-stream' ||
                    in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']) => 'videos',
                    Str::startsWith($mime, 'audio/') => 'audios',
                    Str::startsWith($mime, 'application/pdf') ||
                    Str::startsWith($mime, 'application/msword') ||
                    Str::startsWith($mime, 'application/vnd') ||
                    in_array($extension, ['txt', 'csv', 'xlsx', 'doc', 'docx']) => 'documents',
                    default => 'others',
                };

                $newRelativePath = $folderType . '/' . $fileName;
                $newFullPath = storage_path('app/public/' . $newRelativePath);

                if (!file_exists(dirname($newFullPath))) {
                    mkdir(dirname($newFullPath), 0755, true);
                }

                if (file_exists($newFullPath) && $fullPath !== $newFullPath) {
                    unlink($newFullPath);
                }

                if ($fullPath !== $newFullPath) {
                    rename($fullPath, $newFullPath);
                }

                Media::updateOrCreate(
                    [
                        'file_name' => $fileName,
                        'related_type' => 'post',
                        'related_id' => $post->id,
                    ],
                    [
                        'file_path' => $newRelativePath,
                        'file_type' => $mime,
                        'uploaded_by' => auth()->id(),
                        'deleted_at' => null
                    ]
                );
            }

            Media::where('related_type', 'post')
                ->where('related_id', 0)
                ->where('uploaded_by', auth()->id())
                ->whereIn('file_name', $usedFiles)
                ->update(['related_id' => $post->id]);

            $orphanMedia = Media::where('related_type', 'post')
                ->where('related_id', 0)
                ->where('uploaded_by', auth()->id())
                ->get();

            foreach ($orphanMedia as $media) {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->forceDelete();
            }

            DB::commit();

            return redirect()
                ->route('admin.posts.show', $post->id)
                ->with('success', 'Đã cập nhật bài viết thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Post update failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    /**
     * Extract tất cả media filenames từ HTML content
     *
     * Xử lý nhiều trường hợp CKEditor insert media:
     * - <img src="...">
     * - <a href="...">Download link</a>
     * - <video><source src="..."></video> ← Video thường dùng source
     * - <audio><source src="..."></audio>
     * - <video src="...">
     * - <oembed url="..."> (nếu dùng)
     *
     * @param string $content
     * @return array - Array of filenames
     */
protected function extractMediaFromContent($content)
{
    $usedFiles = [];

    if (empty($content)) {
        return $usedFiles;
    }

    // ============================================
    // METHOD 1: Parse bằng DOMDocument
    // ============================================
    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);

    // Convert UTF-8
    $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
    $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    // Lấy tất cả tags có thể chứa media
    $tags = array_merge(
        iterator_to_array($dom->getElementsByTagName('img')),
        iterator_to_array($dom->getElementsByTagName('a')),
        iterator_to_array($dom->getElementsByTagName('video')),
        iterator_to_array($dom->getElementsByTagName('audio')),
        iterator_to_array($dom->getElementsByTagName('source'))
    );

    foreach ($tags as $tag) {
        if (!$tag instanceof \DOMElement) continue;

        // Check cả src và href
        $url = $tag->getAttribute('src') ?: $tag->getAttribute('href');

        if (empty($url)) continue;

        // Chỉ lấy local storage URLs
        if (!Str::contains($url, '/storage/')) continue;

        // Extract filename: /storage/videos/video.mp4 → video.mp4
        $usedFiles[] = basename($url);
    }

    // ============================================
    // METHOD 2: Regex Fallback (bắt cases DOMDocument miss)
    // ============================================
    // Regex tìm tất cả URLs chứa /storage/
    // Bắt: src="..." hoặc href="..."
    preg_match_all('/(?:src|href)=["\']([^"\']*\/storage\/[^"\']*)["\']/', $content, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $url) {
            $usedFiles[] = basename($url);
        }
    }

    // ============================================
    // METHOD 3: Debug - Log content nếu cần
    // ============================================
    // Uncomment để debug:
    // Log::info('Extracted files from content:', [
    //     'used_files' => $usedFiles,
    //     'content_preview' => Str::limit($content, 500)
    // ]);

    return array_unique($usedFiles);
}

/**
 * Sanitize filename
 *
 * @param string $fileName
 * @return string
 */
protected function sanitizeFileName($fileName)
{
    $pathInfo = pathinfo($fileName);
    $name = $pathInfo['filename'];
    $ext = $pathInfo['extension'] ?? '';

    // Remove special characters
    $name = Str::slug($name, '_');
    $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);

    return $name . ($ext ? '.' . $ext : '');
}

/**
 * Lấy danh sách media đính kèm của post (để hiển thị UI)
 * Chỉ lấy media chưa bị soft delete
 *
 * @param int $postId
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getPostMedia($postId)
{
    return Media::where('related_type', 'post')
        ->where('related_id', $postId)
        ->whereNull('deleted_at') // Chỉ lấy media chưa xóa
        ->orderBy('created_at', 'desc')
        ->get();
}
public function create()
{
    // Lấy danh sách clubs đang active
    // Sử dụng trong dropdown select: <select name="club_id">
    $clubs = Club::where('status', 'active')->get();

    // Return view với biến $clubs
    // View có thể truy cập: @foreach($clubs as $club)
    return view('admin.posts.create', compact('clubs'));
}

/**

 * Flow xử lý:
 * 1. Validate dữ liệu đầu vào
 * 2. Tạo Post record với status = approved (auto publish)
 * 3. Xử lý thumbnail upload
 * 4. Parse content HTML → tìm tất cả media files
 * 5. Di chuyển files vào folders đúng (images/videos/documents...)
 * 6. Tạo Media records trong database
 * 7. Gán orphan media (uploaded trước đó) vào post này
 * 8. Cleanup media không còn dùng
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    // ============================================
    // BƯỚC 1: VALIDATE INPUT
    // ============================================
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'type' => 'required|in:post,notice,document',
        'visibility' => 'required|in:internal,public',
        'club_id' => 'required|exists:clubs,id',
        'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Max 2MB
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
    ]);

    // Bắt đầu transaction
    DB::beginTransaction();

    try {
        // ============================================
        // BƯỚC 2: TẠO POST RECORD
        // ============================================
        $post = new Post();

        // Mass assignment các fields từ validated data
        $post->fill($validated);

        // Set user_id = current logged in user
        $post->user_id = Auth::id();

        // ✅ Mặc định auto-approve khi tạo mới
        $post->status = 'approved';
        $post->approved_by = Auth::id();
        $post->approved_at = now();
        $post->published_at = now();

        // ✅ Set default values nếu không có trong form
        $post->is_visible = $validated['is_visible'] ?? true;
        $post->is_featured = $validated['is_featured'] ?? false;

        // ============================================
        // BƯỚC 3: XỬ LÝ THUMBNAIL (Featured Image)
        // ============================================
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');

            // Sanitize filename để tránh lỗi
            $timestamp = time();
            $originalName = $file->getClientOriginalName();
            $sanitizedName = $this->sanitizeFileName($originalName);
            $fileName = $timestamp . '_' . $sanitizedName;

            // Store vào storage/app/public/thumbnails/
            $path = $file->storeAs('thumbnails', $fileName, 'public');
            $post->thumbnail = $path;
        }

        // Lưu post vào database để có ID
        $post->save();

        // ============================================
        // BƯỚC 4: PARSE HTML CONTENT → TÌM MEDIA FILES
        // ============================================
        // Array chứa tất cả file paths đang được dùng trong content
        $currentFiles = [];

        // Parse HTML bằng DOMDocument
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Tắt warning cho HTML không chuẩn

        // Convert UTF-8 để xử lý tiếng Việt đúng
        $content = mb_convert_encoding($validated['content'], 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Lấy tất cả tags có thể chứa media:
        // - <img src="..."> → images
        // - <a href="..."> → download links
        // - <source src="..."> → video/audio sources
        $tags = array_merge(
            iterator_to_array($dom->getElementsByTagName('img')),
            iterator_to_array($dom->getElementsByTagName('a')),
            iterator_to_array($dom->getElementsByTagName('source'))
        );

        // ============================================
        // BƯỚC 5: XỬ LÝ TỪNG MEDIA FILE
        // ============================================
        foreach ($tags as $tag) {
            // Skip nếu không phải DOMElement
            if (!$tag instanceof \DOMElement)
                continue;

            // Xác định attribute chứa URL: src hoặc href
            $attr = $tag->hasAttribute('src') ? 'src' : ($tag->hasAttribute('href') ? 'href' : null);
            if (!$attr)
                continue;

            $url = $tag->getAttribute($attr);

            // Chỉ xử lý local storage URLs, bỏ qua external URLs
            if (!Str::contains($url, '/storage/'))
                continue;

            // Extract relative path: '/storage/images/photo.jpg' → 'images/photo.jpg'
            $relativePath = Str::after($url, '/storage/');
            $fullPath = storage_path('app/public/' . $relativePath);

            // Skip nếu file không tồn tại
            if (!file_exists($fullPath))
                continue;

            // ============================================
            // BƯỚC 6: XÁC ĐỊNH FOLDER ĐÚNG CHO FILE
            // ============================================
            $mime = mime_content_type($fullPath);
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            // Phân loại file theo MIME type + extension
            $folder = match (true) {
                // Images: jpg, png, gif, webp...
                Str::startsWith($mime, 'image/') => 'images',

                // Videos: mp4, mov, avi...
                Str::startsWith($mime, 'video/')
                || $mime === 'application/octet-stream'
                || in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']) => 'videos',

                // Audio: mp3, wav, ogg...
                Str::startsWith($mime, 'audio/') => 'audios',

                // Documents: PDF, Word, Excel...
                Str::startsWith($mime, 'application/pdf')
                || Str::startsWith($mime, 'application/msword')
                || Str::startsWith($mime, 'application/vnd') => 'documents',

                // Default: other files
                default => 'others',
            };

            // ============================================
            // BƯỚC 7: DI CHUYỂN FILE VÀO FOLDER ĐÚNG
            // ============================================
            $fileName = basename($fullPath);
            $newRelativePath = $folder . '/' . $fileName;
            $newFullPath = storage_path('app/public/' . $newRelativePath);

            // Chỉ move nếu file chưa ở đúng folder
            if ($relativePath !== $newRelativePath) {
                // Tạo folder nếu chưa tồn tại
                if (!file_exists(dirname($newFullPath))) {
                    mkdir(dirname($newFullPath), 0755, true);
                }

                // Move file: uploads/posts/photo.jpg → images/photo.jpg
                rename($fullPath, $newFullPath);
                $relativePath = $newRelativePath;
            }

            // Add vào danh sách files đang dùng
            $currentFiles[] = $relativePath;

            // ============================================
            // BƯỚC 8: TẠO MEDIA RECORD TRONG DATABASE
            // ============================================
            Media::updateOrCreate(
                [
                    'file_path' => $relativePath,
                    'related_type' => 'post', // ✅ Dùng 'post'
                ],
                [
                    'file_name' => $fileName,
                    'file_type' => $mime,
                    'uploaded_by' => Auth::id(),
                    'related_id' => $post->id,
                ]
            );
        }

        // ============================================
        // BƯỚC 9: GÁN ORPHAN MEDIA VÀO POST
        // ============================================
        // Orphan media = files được upload trước đó (qua uploadFile)
        // nhưng chưa có related_id (chưa biết thuộc post nào)
        $orphanMedia = Media::where('uploaded_by', Auth::id())
            ->where('related_id', 0)
            ->where('related_type', 'post') // ✅ Dùng 'post'
            ->get();

        foreach ($orphanMedia as $media) {
            $oldPath = storage_path('app/public/' . $media->file_path);

            if (!file_exists($oldPath)) continue;

            // Xác định folder mới dựa trên file type
            $mime = $media->file_type;
            $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));

            $folder = match (true) {
                Str::startsWith($mime, 'image/') => 'images',
                Str::startsWith($mime, 'video/')
                    || in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']) => 'videos',
                Str::startsWith($mime, 'audio/') => 'audios',
                Str::startsWith($mime, 'application/pdf')
                    || Str::startsWith($mime, 'application/msword')
                    || Str::startsWith($mime, 'application/vnd') => 'documents',
                default => 'others',
            };

            // Move file từ uploads/posts → folder đúng
            $newRelativePath = $folder . '/' . $media->file_name;
            $newFullPath = storage_path('app/public/' . $newRelativePath);

            if (!file_exists(dirname($newFullPath))) {
                mkdir(dirname($newFullPath), 0755, true);
            }

            // Di chuyển file
            rename($oldPath, $newFullPath);

            // Update media record
            $media->update([
                'related_id' => $post->id,
                'file_path' => $newRelativePath
            ]);

            // Thêm vào currentFiles để không bị cleanup
            $currentFiles[] = $newRelativePath;
        }

        // ============================================
        // BƯỚC 10: CLEANUP MEDIA KHÔNG CÒN DÙNG
        // ============================================
        // Lấy tất cả media của user này + post này
        $oldMedia = Media::where('uploaded_by', Auth::id())
            ->where('related_type', 'post') // ✅ Dùng 'post'
            ->where('related_id', $post->id)
            ->get();

        // Xóa media không xuất hiện trong $currentFiles
        foreach ($oldMedia as $media) {
            if (!in_array($media->file_path, $currentFiles)) {
                // Force delete: xóa hẳn khỏi database
                $media->forceDelete();

                // Xóa file vật lý (@ để suppress error nếu file không tồn tại)
                @unlink(storage_path('app/public/' . $media->file_path));
            }
        }

        // Commit transaction
        DB::commit();

        return redirect()->route('admin.posts.show', $post->id)
            ->with('success', 'Đã thêm bài viết thành công!');

    } catch (\Exception $e) {
        // Rollback nếu có lỗi
        DB::rollBack();

        Log::error('Post creation failed: ' . $e->getMessage());

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}



    /**
     * Form sửa bài viết
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $clubs = Club::all(); // Lấy danh sách CLB

        return view('admin.posts.edit', compact('post', 'clubs'));
    }

    /**
     * Lọc bài viết theo từ khóa
     */
    public function filter(Request $request)
    {
        $keyword = $request->input('keyword');
        $query = Post::with(['club', 'user'])->latest();

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $posts = $query->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'user_name' => $post->user->name ?? 'Không xác định',
                'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                'status' => $post->status,
                'is_visible' => $post->is_visible,
            ];
        });

        return response()->json(['data' => $posts]);
    }

    public function approve($id)
    {
        $post = Post::findOrFail($id);

        $post->status = 'approved';
        $post->approved_by = auth()->id();
        $post->approved_at = now();
        $post->published_at = now();
        $post->rejection_reason = null;

        $post->save();

        return redirect()->back()->with('success', 'Bài viết đã được duyệt.');
    }

    public function reject(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $post->status = 'rejected';
        $post->approved_by = auth()->id();
        $post->approved_at = now();
        $post->published_at = null;
        $post->rejection_reason = $request->rejection_reason;

        $post->save();

        return redirect()->back()->with('success', 'Bài viết đã bị từ chối.');
    }
    public function showTrash($id)
    {
        $post = Post::withTrashed()->with('club', 'user')->findOrFail($id);

        // tìm log xóa để lấy thông tin người xóa
        $deleteLog = PostUpdateLog::with('updatedBy')
            ->where('post_id', $post->id)
            ->orderByDesc('created_at')
            ->get()
            ->first(function ($log) {
                $changes = $log->changes ?? [];
                return is_array($changes) && array_key_exists('deleted', $changes);
            });

        $deleter = $deleteLog?->updatedBy;
        $deletedRoleDisplay = 'Không rõ';
        $deleteReason = $deleteLog?->changes['delete_reason'] ?? null;

        if ($deleter) {
            if ($deleter->role === 'admin') {
                $deletedRoleDisplay = 'Admin';
            } else {
                $clubId = $post->club_id;
                $clubMember = ClubMember::with('member.user')
                    ->where('club_id', $clubId)
                    ->whereHas('member.user', function ($q) use ($deleter) {
                        $q->where('id', $deleter->id);
                    })
                    ->first();

                $deletedRoleDisplay = $clubMember->role ?? 'Member';
            }
        }

        return view('admin.posts.show_trash', compact('post', 'deleter', 'deletedRoleDisplay', 'deleteReason'));
    }





    /**
     * Kiểm tra quyền admin
     */
    protected function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }
        return null;
    }

}
