<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;
use App\Notifications\CustomNotification;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index()
    {
        $posts = Post::with(['club', 'user'])->latest()->get();
        return view('admin.posts.index', compact('posts'));
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
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        Media::onlyTrashed()
            ->where('related_type', 'post')
            ->where('related_id', $post->id)
            ->restore();

        return redirect()->route('admin.posts.trash')
            ->with('success', 'Bài viết và media đã được khôi phục.');
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
    public function trash()
    {
           $posts = Post::onlyTrashed()
            ->with(['user', 'club']) // nếu bạn cần hiển thị người đăng và CLB
            ->latest('deleted_at')
            ->get();

        return view('admin.posts.trash', compact('posts'));
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
        // Nếu bạn muốn ẩn thumbnail, có thể gán cờ hoặc xử lý ở view

        // ✅ Xóa mềm bài viết
        $post->delete();

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
            ->with('success', 'Đã xóa bài viết và gửi thông báo cho tác giả.');
    }


    public function show($id)
    {
        $post = Post::with(['club', 'user', 'media'])->findOrFail($id);
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Cập nhật bài viết
     */
    // Helper detect mime type chính xác


    // Hàm detectMimeType
    private function detectMimeType($fullPath)
    {
        $mime = mime_content_type($fullPath);
        if ($mime === false || $mime === 'application/octet-stream') {
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            return match ($ext) {
                'mp4' => 'video/mp4',
                'mov' => 'video/quicktime',
                'avi' => 'video/x-msvideo',
                'mkv' => 'video/x-matroska',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                default => 'application/octet-stream',
            };
        }
        return $mime;
    }
    public function uploadFile(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['success' => false, 'message' => 'Không có file nào được gửi.']);
            }

            $file = $request->file('file');
            $fileType = $file->getMimeType();
            $originalName = $file->getClientOriginalName();
            $folder = storage_path('app/public/uploads/posts');

            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Nếu file trùng tên thì thêm số đếm
            $i = 1;
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();
            $finalName = $originalName;
            while (file_exists($folder . '/' . $finalName)) {
                $finalName = $baseName . "($i)." . $ext;
                $i++;
            }

            // Di chuyển file giữ nguyên tên
            $file->move($folder, $finalName);
            $path = 'uploads/posts/' . $finalName;

            $media = Media::create([
                'file_name' => $finalName,
                'file_path' => $path,
                'file_type' => $fileType,
                'related_id' => $request->input('related_id', 0),
                'related_type' => $request->input('related_type', 'post'),
                'uploaded_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path),
                'name' => $finalName,
                'type' => $fileType,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

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
        'files.*' => 'file',
    ]);

    // ✅ Xử lý thumbnail
    if ($request->hasFile('thumbnail')) {
        $file = $request->file('thumbnail');
        $folder = storage_path('app/public/uploads/thumbnails');

        if (!file_exists($folder)) mkdir($folder, 0755, true);

        $fileName = $file->getClientOriginalName();
        $i = 1;
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $finalName = $fileName;
        while (file_exists($folder . '/' . $finalName)) {
            $finalName = $baseName . "($i)." . $ext;
            $i++;
        }

        $file->move($folder, $finalName);
        $post->thumbnail = 'uploads/thumbnails/' . $finalName;
    }

    // ✅ Cập nhật thông tin bài viết
    $post->title = $validated['title'];
    $post->content = $validated['content'];
    $post->type = $validated['type'];
    $post->status = $validated['status'];
    $post->visibility = $validated['visibility'];
    $post->club_id = $validated['club_id'];
    $post->is_visible = $validated['is_visible'] ?? true;
    $post->is_featured = $validated['is_featured'] ?? false;

    // ✅ Gán thông tin duyệt nếu status là approved
    if ($validated['status'] === 'approved') {
        $post->approved_by = auth()->id();
        $post->approved_at = now();
        $post->published_at = now();
    }

    $post->save();

    // ✅ Upload file mới từ editor
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $fileType = $file->getMimeType();
            $originalName = $file->getClientOriginalName();
            $tempFolder = storage_path('app/public/uploads/posts');

            if (!file_exists($tempFolder)) mkdir($tempFolder, 0755, true);

            $i = 1;
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();
            $finalName = $originalName;
            while (file_exists($tempFolder . '/' . $finalName)) {
                $finalName = $baseName . "($i)." . $ext;
                $i++;
            }

            $file->move($tempFolder, $finalName);
        }
    }

    // ✅ Lấy danh sách file đang dùng trong editor
    $usedFiles = [];
    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($post->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $tags = array_merge(
        iterator_to_array($dom->getElementsByTagName('img')),
        iterator_to_array($dom->getElementsByTagName('a'))
    );

    foreach ($tags as $tag) {
        if (!$tag instanceof \DOMElement) continue;
        $attr = $tag->tagName === 'img' ? 'src' : 'href';
        $url = $tag->getAttribute($attr);
        if (!Str::contains($url, '/storage/')) continue;
        $usedFiles[] = basename(Str::after($url, '/storage/'));
    }

    // ✅ Soft delete Media không còn dùng
    $allMedia = Media::withTrashed()
        ->where('related_type', 'post')
        ->where('related_id', $post->id)
        ->get()
        ->keyBy('file_name');

    foreach ($allMedia as $fileName => $media) {
        if (!in_array($fileName, $usedFiles)) {
            $media->delete();
        } else {
            if ($media->trashed()) $media->restore();
        }
    }

    // ✅ Di chuyển file từ uploads/posts sang thư mục đúng
    $tempFiles = glob(storage_path('app/public/uploads/posts/*'));

    foreach ($tempFiles as $fullPath) {
        if (!file_exists($fullPath)) continue;

        $fileName = basename($fullPath);
        $mime = mime_content_type($fullPath);
        $lowerFileName = Str::lower($fileName);

        $folderType = match (true) {
            Str::startsWith($mime, 'image/') => 'images',
            Str::startsWith($mime, 'video/') => 'video',
            Str::startsWith($mime, 'audio/') => 'audio',
            Str::startsWith($mime, 'application/pdf')
            || Str::startsWith($mime, 'application/msword')
            || Str::startsWith($mime, 'application/vnd')
            || Str::endsWith($lowerFileName, '.txt')
            || Str::endsWith($lowerFileName, '.csv')
            || Str::endsWith($lowerFileName, '.xlsx') => 'documents',
            default => 'other',
        };

        $newRelativePath = $folderType . '/' . $fileName;
        $newFullPath = storage_path('app/public/' . $newRelativePath);

        if (!file_exists(dirname($newFullPath))) mkdir(dirname($newFullPath), 0755, true);
        if (file_exists($newFullPath)) unlink($newFullPath);

        rename($fullPath, $newFullPath);

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

    // ✅ Xóa media mồ côi
    Media::where('related_type', 'post')
        ->where('related_id', 0)
        ->get()
        ->each(function ($media) {
            $fullPath = storage_path('app/public/' . $media->file_path);
            if (file_exists($fullPath)) unlink($fullPath);
            $media->forceDelete();
        });

    return redirect()->route('admin.posts.show', $post->id)
        ->with('success', 'Đã cập nhật bài viết thành công!');
}


    public function create()
    {
         $clubs = Club::where('status', 'active')->get(); // nếu cần chọn CLB
        return view('admin.posts.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:post,notice,document',
            'visibility' => 'required|in:internal,public',
            'club_id' => 'required|exists:clubs,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $post = new Post();
        $post->fill($validated);
        $post->user_id = Auth::id();

        // ✅ Mặc định duyệt bài viết
        $post->status = 'approved';
        $post->approved_by = Auth::id();
        $post->approved_at = now();
        $post->published_at = now();

        // ✅ Gán mặc định nếu không có trong form
        $post->is_visible = $validated['is_visible'] ?? true;
        $post->is_featured = $validated['is_featured'] ?? false;

        // ✅ Ảnh đại diện
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = $file->store('thumbnails', 'public');
            $post->thumbnail = $path;
        }

        $post->save();

        $currentFiles = [];

        // ✅ Đọc HTML và lấy tất cả tag chứa file
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($validated['content'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $tags = array_merge(
            iterator_to_array($dom->getElementsByTagName('img')),
            iterator_to_array($dom->getElementsByTagName('a')),
            iterator_to_array($dom->getElementsByTagName('source'))
        );

        foreach ($tags as $tag) {
            if (!$tag instanceof \DOMElement)
                continue;

            $attr = $tag->hasAttribute('src') ? 'src' : ($tag->hasAttribute('href') ? 'href' : null);
            if (!$attr)
                continue;

            $url = $tag->getAttribute($attr);
            if (!Str::contains($url, '/storage/'))
                continue;

            $relativePath = Str::after($url, '/storage/');
            $fullPath = storage_path('app/public/' . $relativePath);

            if (!file_exists($fullPath))
                continue;

            $mime = mime_content_type($fullPath);
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            $folder = match (true) {
                Str::startsWith($mime, 'image/') => 'images',
                Str::startsWith($mime, 'video/')
                || $mime === 'application/octet-stream'
                || in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']) => 'videos',
                Str::startsWith($mime, 'audio/') => 'audios',
                Str::startsWith($mime, 'application/pdf')
                || Str::startsWith($mime, 'application/msword')
                || Str::startsWith($mime, 'application/vnd') => 'documents',
                default => 'others',
            };

            $fileName = basename($fullPath);
            $newRelativePath = $folder . '/' . $fileName;
            $newFullPath = storage_path('app/public/' . $newRelativePath);

            if ($relativePath !== $newRelativePath) {
                if (!file_exists(dirname($newFullPath))) {
                    mkdir(dirname($newFullPath), 0755, true);
                }
                rename($fullPath, $newFullPath);
                $relativePath = $newRelativePath;
            }

            $currentFiles[] = $relativePath;

            Media::updateOrCreate(
                [
                    'file_path' => $relativePath,
                    'related_type' => $post->getMorphClass(),
                ],
                [
                    'file_name' => $fileName,
                    'file_type' => $mime,
                    'uploaded_by' => Auth::id(),
                    'related_id' => $post->id,
                ]
            );
        }

        // ✅ Gán lại media “mồ côi”
        Media::where('uploaded_by', Auth::id())
            ->where('related_id', 0)
            ->where('related_type', $post->getMorphClass())
            ->update(['related_id' => $post->id]);

        // ✅ Xóa file không còn trong nội dung
        $oldMedia = Media::where('uploaded_by', Auth::id())
            ->where('related_type', $post->getMorphClass())
            ->where('related_id', $post->id)
            ->get();

        foreach ($oldMedia as $media) {
            if (!in_array($media->file_path, $currentFiles)) {
                $media->forceDelete();
                @unlink(storage_path('app/public/' . $media->file_path));
            }
        }

        return redirect()->route('admin.posts.show', $post->id)
            ->with('success', 'Đã thêm bài viết thành công!');
    }
    /**
     * Upload ảnh từ Quill Editor
     */
    public function uploadImage(Request $request)
    {
        $file = $request->file('image') ?? $request->file('upload');

        if (!$file) {
            return response()->json(['error' => 'Không có ảnh'], 400);
        }

        $path = $file->store('images', 'public');
        $url = asset('storage/' . $path);

        return response()->json(['url' => $url]);
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
