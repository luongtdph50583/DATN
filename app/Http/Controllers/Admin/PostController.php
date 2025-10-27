<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->back()->with('success', 'Đã xóa bài viết.');
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
        ]);

        // ✅ Nếu có ảnh đại diện mới → xóa ảnh cũ và lưu ảnh mới
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu có
            if ($post->thumbnail && file_exists(storage_path('app/public/' . $post->thumbnail))) {
                unlink(storage_path('app/public/' . $post->thumbnail));
            }

            // Lưu ảnh mới
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        $post->fill($validated);
        $post->save();

        // ✅ Gán lại các media có related_id = 0 cho bài viết hiện tại
        Media::where('related_type', 'post')
            ->where('related_id', 0)
            ->update(['related_id' => $post->id]);

        $currentFiles = [];

        // ✅ Phân tích nội dung HTML
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($validated['content'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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

            $relativePath = Str::after($url, '/storage/');
            $fullPath = storage_path('app/public/' . $relativePath);
            if (!file_exists($fullPath))
                continue;

            $mime = mime_content_type($fullPath);
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            // ✅ Xác định thư mục theo MIME/extension
            $folder = match (true) {
                Str::startsWith($mime, 'image/') => 'images',
                Str::startsWith($mime, 'video/')
                || $mime === 'application/octet-stream'
                || in_array($extension, ['mp4', 'mov', 'avi', 'mkv']) => 'video',
                Str::startsWith($mime, 'audio/') => 'audio',
                Str::startsWith($mime, 'application/pdf')
                || Str::startsWith($mime, 'application/msword')
                || Str::startsWith($mime, 'application/vnd') => 'documents',
                default => 'other',
            };

            $fileName = basename($fullPath);

            // ✅ Nếu file nằm trong uploads/posts thì chuyển sang thư mục đúng
            if (Str::startsWith($relativePath, 'uploads/posts/')) {
                $newRelativePath = $folder . '/' . $fileName;
                $newFullPath = storage_path('app/public/' . $newRelativePath);

                if (!file_exists(dirname($newFullPath))) {
                    mkdir(dirname($newFullPath), 0755, true);
                }

                rename($fullPath, $newFullPath);

                // 🔥 Cập nhật lại file_path trong DB nếu có bản ghi cũ
                Media::where('file_path', $relativePath)
                    ->where('related_type', 'post')
                    ->update(['file_path' => $newRelativePath]);

                $relativePath = $newRelativePath;
            }

            $currentFiles[] = $relativePath;

            // ✅ Cập nhật hoặc tạo mới Media
            Media::withTrashed()->updateOrCreate(
                [
                    'file_path' => $relativePath,
                    'related_id' => $post->id,
                    'related_type' => 'post',
                ],
                [
                    'file_name' => $fileName,
                    'file_type' => $mime,
                    'uploaded_by' => Auth::id(),
                    'deleted_at' => null, // 👈 khôi phục nếu từng bị xóa tạm
                ]
            );
        }

        // ✅ Xóa tạm Media không còn trong content
        $oldMedia = Media::where('related_type', 'post')
            ->where('related_id', $post->id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($oldMedia as $media) {
            if (!in_array($media->file_path, $currentFiles)) {
                $media->delete(); // 👈 xóa mềm
            }
        }

        return redirect()->route('admin.posts.show', $post->id)
            ->with('success', 'Đã cập nhật bài viết thành công!');
    }








    public function uploadFile(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['success' => false, 'message' => 'Không có file nào được gửi.']);
            }

            $file = $request->file('file');
            $path = $file->store('uploads/posts', 'public');
            $fileType = $file->getMimeType();

            $media = Media::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $fileType,
                'related_id' => $request->input('related_id', 0),
                'related_type' => $request->input('related_type', 'post'), // ✅ giữ nguyên chữ “post”
                'uploaded_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path),
                'name' => $file->getClientOriginalName(),
                'type' => $fileType,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
