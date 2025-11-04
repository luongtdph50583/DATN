<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Post;
use App\Models\Media;
use App\Models\ClubMember;
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

    /**
     * Xóa bài viết
     */



    public function destroy(Request $request, $id)
    {
        $club = Club::findOrFail($id);
        $reason = $request->input('delete_reason', 'Không có lý do');

        \DB::beginTransaction();

        try {
            // 1️⃣ Lấy manager (Chủ nhiệm) tương tự phần edit/update
            $managerId = $club->manager_id;
            $managerUser = null;

            if ($managerId) {
                $manager = ClubMember::with('member.user')
                    ->where('club_id', $club->id)
                    ->where('member_id', $managerId)
                    ->first();

                if ($manager && $manager->member && $manager->member->user) {
                    $managerUser = $manager->member->user;
                }
            }

            // 2️⃣ Xóa cứng posts liên quan
            $posts = Post::where('club_id', $club->id)->get();
            foreach ($posts as $post) {
                // Xóa media liên quan nếu có
                $mediaList = Media::withTrashed()
                    ->where('related_type', 'post')
                    ->where('related_id', $post->id)
                    ->get();

                foreach ($mediaList as $media) {
                    $filePath = storage_path('app/public/' . $media->file_path);
                    if (file_exists($filePath))
                        unlink($filePath);
                    $media->forceDelete();
                }

                // Xóa ảnh thumbnail
                if ($post->thumbnail && file_exists(storage_path('app/public/' . $post->thumbnail))) {
                    unlink(storage_path('app/public/' . $post->thumbnail));
                }

                $post->forceDelete();
            }

            // 3️⃣ Xóa cứng documents liên quan
            $documents = Document::where('clb_id', $club->id)->get();
            foreach ($documents as $doc) {
                $filePath = storage_path('app/public/' . $doc->file_path);
                if (file_exists($filePath))
                    unlink($filePath);
                $doc->forceDelete();
            }

            // 4️⃣ Hạ role tất cả club_members về 'member'
            ClubMember::where('club_id', $club->id)->update([
                'role' => 'member',
                'appointed_at' => null,
                'updated_at' => now(),
            ]);

            // 5️⃣ Xóa CLB (cứng)
            if ($club->logo && file_exists(storage_path('app/public/' . $club->logo))) {
                unlink(storage_path('app/public/' . $club->logo));
            }

            $clubName = $club->name;
            $club->delete();

            \DB::commit();

            // 6️⃣ Gửi thông báo/email cho manager
            if ($managerUser) {
                $batchId = uniqid();

                SendNotificationJob::dispatch(
                    $managerUser->email,
                    new CustomNotification(
                        "CLB bị xóa",
                        "Câu lạc bộ '{$clubName}' đã bị xóa. Lý do: {$reason}",
                        $batchId
                    )
                );

                // Debug ra log để kiểm tra
                \Log::info("DEBUG: Mail gửi tới manager", [
                    'email' => $managerUser->email,
                    'club' => $clubName,
                    'reason' => $reason,
                    'batchId' => $batchId,
                ]);
            }

            return redirect()->route('admin.clubs.index')
                ->with('success', "Đã xóa CLB '{$clubName}' và gửi thông báo cho Chủ nhiệm.");
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Lỗi khi xóa CLB', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Xảy ra lỗi khi xóa CLB: ' . $e->getMessage()]);
        }
    }



    /**
     * Hiển thị chi tiết bài viết
     */
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
            'status' => 'required|in:visible,hidden',
            'visibility' => 'required|in:internal,public',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'files.*' => 'file',
        ]);

        // =============================
        // 1. Xử lý Thumbnail (chỉ posts.thumbnail)
        // =============================
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $folder = storage_path('app/public/uploads/thumbnails');

            if (!file_exists($folder))
                mkdir($folder, 0755, true);

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

        // =============================
        // 2. Cập nhật thông tin khác của post
        // =============================
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->type = $validated['type'];
        $post->status = $validated['status'];
        $post->visibility = $validated['visibility'];
        $post->save();

        // =============================
        // 3. Upload file mới từ editor (files[])
        // =============================
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileType = $file->getMimeType();
                $originalName = $file->getClientOriginalName();
                $tempFolder = storage_path('app/public/uploads/posts');

                if (!file_exists($tempFolder))
                    mkdir($tempFolder, 0755, true);

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

        // =============================
        // 4. Lấy danh sách file editor đang dùng
        // =============================
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
            if (!$tag instanceof \DOMElement)
                continue;
            $attr = $tag->tagName === 'img' ? 'src' : 'href';
            $url = $tag->getAttribute($attr);
            if (!Str::contains($url, '/storage/'))
                continue;
            $usedFiles[] = basename(Str::after($url, '/storage/'));
        }

        // =============================
        // 5. Soft delete Media không còn trong editor
        // =============================
        $allMedia = Media::withTrashed()
            ->where('related_type', 'post')
            ->where('related_id', $post->id)
            ->get()
            ->keyBy('file_name');

        foreach ($allMedia as $fileName => $media) {
            if (!in_array($fileName, $usedFiles)) {
                $media->delete();
            } else {
                if ($media->trashed())
                    $media->restore();
            }
        }
        // =============================
        // 6. Move tất cả file editor trong uploads/posts -> folder đúng, cập nhật Media
        // Thumbnail KHÔNG có trong uploads/posts, nên sẽ không bị tạo Media
        // =============================
        $tempFiles = glob(storage_path('app/public/uploads/posts/*'));

        foreach ($tempFiles as $fullPath) {
            if (!file_exists($fullPath))
                continue;

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

            if (!file_exists(dirname($newFullPath)))
                mkdir(dirname($newFullPath), 0755, true);
            if (file_exists($newFullPath))
                unlink($newFullPath);

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

        // =============================
        // 7. Cleanup Media có related_id = 0
        // =============================
        Media::where('related_type', 'post')
            ->where('related_id', 0)
            ->get()
            ->each(function ($media) {
                $fullPath = storage_path('app/public/' . $media->file_path);
                if (file_exists($fullPath))
                    unlink($fullPath);
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
            'status' => 'required|in:visible,hidden',
            'visibility' => 'required|in:internal,public',
            'club_id' => 'required|exists:clubs,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $post = new Post();
        $post->fill($validated);
        $post->user_id = Auth::id();

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

        // Lấy <img>, <a> và <source> (trong video/audio)
        $tags = array_merge(
            iterator_to_array($dom->getElementsByTagName('img')),
            iterator_to_array($dom->getElementsByTagName('a')),
            iterator_to_array($dom->getElementsByTagName('source'))
        );

        foreach ($tags as $tag) {
            if (!$tag instanceof \DOMElement)
                continue;

            // ✅ Lấy đường dẫn file
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

            // ✅ Phân loại folder
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

            // ✅ Chuyển file sang đúng thư mục
            if ($relativePath !== $newRelativePath) {
                if (!file_exists(dirname($newFullPath))) {
                    mkdir(dirname($newFullPath), 0755, true);
                }
                rename($fullPath, $newFullPath);
                $relativePath = $newRelativePath;
            }

            $currentFiles[] = $relativePath;

            // ✅ Cập nhật hoặc tạo mới Media
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
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('images', 'public');
            $url = asset('storage/' . $path);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'Không có ảnh'], 400);
    }

    /**
     * Form sửa bài viết
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Lọc bài viết theo từ khóa
     */
    public function filter(Request $request)
    {
        $keyword = $request->input('keyword');
        $query = Post::with(['club', 'user'])->latest();

        if (!empty($keyword)) {
            $query->where('title', 'like', "%{$keyword}%")
                ->orWhereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
        }

        $posts = $query->get();
        return response()->json(['data' => $posts]);
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
