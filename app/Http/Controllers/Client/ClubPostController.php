<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ClubPostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết của CLB
     */
    public function index($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);
        
        $posts = Post::where('club_id', $club_id)
            ->with(['user', 'media'])
            ->latest()
            ->paginate(15);

        return view('client.pages.post.index', compact('club', 'posts'));
    }

    /**
     * Hiển thị form tạo bài viết
     */
    public function create($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        return view('client.pages.post.new_post', compact('club'));
    }

    /**
     * Lưu bài viết mới
     */
    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:post,notice,document',
            'visibility' => 'required|in:internal,public',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $post = new Post();
        $post->fill($validated);
        $post->club_id = $club_id;
        $post->user_id = Auth::id();
        $post->status = 'pending'; // Chờ duyệt
        $post->is_visible = $validated['is_visible'] ?? true;
        $post->is_featured = $validated['is_featured'] ?? false;

        // Ảnh đại diện
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = $file->store('thumbnails', 'public');
            $post->thumbnail = $path;
        }

        $post->save();

        // Xử lý media từ editor
        $this->processMediaFromContent($post, $validated['content']);

        return redirect()->route('club_manager.posts.index', ['club_id' => $club_id])
            ->with('success', 'Đã thêm bài viết thành công! Bài viết đang chờ duyệt.');
    }

    /**
     * Hiển thị chi tiết bài viết
     */
    public function show($club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $post = Post::where('club_id', $club_id)
            ->with(['user', 'media', 'club'])
            ->findOrFail($id);

        return view('client.pages.post.new-detail', compact('club', 'post'));
    }

    /**
     * Hiển thị form sửa bài viết
     */
    public function edit($club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $post = Post::where('club_id', $club_id)->findOrFail($id);

        return view('client.pages.post.new_post', compact('club', 'post'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, $club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $post = Post::where('club_id', $club_id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:post,notice,document',
            'visibility' => 'required|in:internal,public',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->type = $validated['type'];
        $post->visibility = $validated['visibility'];
        $post->is_visible = $validated['is_visible'] ?? true;
        $post->is_featured = $validated['is_featured'] ?? false;

        // Nếu đã được duyệt, khi sửa sẽ về pending
        if ($post->status === 'approved') {
            $post->status = 'pending';
            $post->approved_by = null;
            $post->approved_at = null;
        }

        // Xử lý thumbnail
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

        $post->save();

        // Xử lý media từ editor
        $this->processMediaFromContent($post, $validated['content']);

        return redirect()->route('club_manager.posts.show', ['club_id' => $club_id, 'post' => $post->id])
            ->with('success', 'Đã cập nhật bài viết thành công!');
    }

    /**
     * Xóa bài viết
     */
    public function destroy($club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $post = Post::where('club_id', $club_id)->findOrFail($id);
        $post->delete();

        return redirect()->route('club_manager.posts.index', ['club_id' => $club_id])
            ->with('success', 'Đã xóa bài viết thành công.');
    }

    /**
     * Xử lý media từ nội dung HTML
     */
    private function processMediaFromContent($post, $content)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $tags = array_merge(
            iterator_to_array($dom->getElementsByTagName('img')),
            iterator_to_array($dom->getElementsByTagName('a')),
            iterator_to_array($dom->getElementsByTagName('source'))
        );

        foreach ($tags as $tag) {
            if (!$tag instanceof \DOMElement) continue;

            $attr = $tag->hasAttribute('src') ? 'src' : ($tag->hasAttribute('href') ? 'href' : null);
            if (!$attr) continue;

            $url = $tag->getAttribute($attr);
            if (!Str::contains($url, '/storage/')) continue;

            $relativePath = Str::after($url, '/storage/');
            $fullPath = storage_path('app/public/' . $relativePath);

            if (!file_exists($fullPath)) continue;

            $mime = mime_content_type($fullPath);
            $fileName = basename($fullPath);

            Media::updateOrCreate(
                [
                    'file_path' => $relativePath,
                    'related_type' => 'post',
                ],
                [
                    'file_name' => $fileName,
                    'file_type' => $mime,
                    'uploaded_by' => Auth::id(),
                    'related_id' => $post->id,
                ]
            );
        }

        // Gán lại media "mồ côi"
        Media::where('uploaded_by', Auth::id())
            ->where('related_id', 0)
            ->where('related_type', 'post')
            ->update(['related_id' => $post->id]);
    }

    /**
     * Kiểm tra quyền quản lý CLB
     */
    private function authorizeClubManager($club)
    {
        $user = Auth::user();
        $managedClubs = $user->getManagedClubs();

        if (!$managedClubs->contains('id', $club->id)) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}

