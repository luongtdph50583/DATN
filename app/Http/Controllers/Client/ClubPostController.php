<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\Post;
use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PostUpdateLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClubPostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết của CLB
     */
    public function index($club_id)
    {
        // Lấy thông tin CLB
        $club = Club::findOrFail($club_id);

        // Kiểm tra quyền (chỉ quản lý CLB hoặc admin mới xem trang này)
        $this->authorizeClubManager($club);

        // Lấy danh sách bài viết của CLB - sort theo bài mới nhất
        $posts = Post::where('club_id', $club_id)
            ->with(['user', 'media'])
            ->orderBy('created_at', 'desc')  // 👈 sort theo created_at mới nhất
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

        // Validate dữ liệu
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:post,notice,document',
            'visibility' => 'required|in:internal,public',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // không bắt buộc
            'is_visible' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Tạo đối tượng post mới
        $post = new Post();
        $post->fill($validated);
        $post->club_id = $club_id;
        $post->user_id = Auth::id();
        $post->status = 'pending'; // Chờ duyệt
        $post->is_visible = $validated['is_visible'] ?? true;
        $post->is_featured = $validated['is_featured'] ?? false;

        // Xử lý ảnh đại diện nếu có
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $file = $request->file('thumbnail');
            $path = $file->store('thumbnails', 'public');
            $post->thumbnail = $path;
        }

        // Lưu bài viết
        $post->save();

        // Cập nhật media đã upload trước đó (trong trường hợp upload trước khi tạo post)
        Media::where('uploaded_by', Auth::id())
            ->where('related_id', 0)
            ->where('related_type', 'post')
            ->update(['related_id' => $post->id]);

        // Xử lý media chèn từ nội dung editor
        $this->processMediaFromContent($post, $validated['content']);

        return redirect()
            ->route('club_manager.posts.index', ['club_id' => $club_id])
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

        $post->refresh(); // 🔥 ép reload lại dữ liệu sau khi update

        return view('client.pages.post.edit', compact('club', 'post'));
    }


    /**
     * Cập nhật bài viết
     */

    public function update(Request $request, $club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $post = Post::where('club_id', $club_id)->findOrFail($id);

        // Validate
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:post,notice,document',
            'visibility' => 'required|in:internal,public',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_visible' => 'required|boolean',
            'is_featured' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            // Thumbnail
            if ($request->hasFile('thumbnail')) {
                if ($post->thumbnail && Storage::disk('public')->exists($post->thumbnail)) {
                    Storage::disk('public')->delete($post->thumbnail);
                }

                $file = $request->file('thumbnail');
                $fileName = time() . '_' .
                    Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('uploads/thumbnails', $fileName, 'public');
                $post->thumbnail = $path;
            }

            // Lấy dữ liệu gốc trước khi fill
            $oldData = $post->getOriginal();

            // Fill
            $post->fill($validated);

            // Lấy các field thay đổi (dirty) trước khi save
            $changes = [];
            foreach ($post->getDirty() as $field => $newVal) {
                $oldVal = $oldData[$field] ?? null;
                $changes[$field] = [$oldVal, $newVal];
            }

            // Save
            $post->save();

            // Ghi log thay đổi
            $log = null;
            if (!empty($changes)) {
                $log = PostUpdateLog::create([
                    'post_id' => $post->id,
                    'changes' => $changes, // nhờ casts => array
                    'changed_by' => auth()->id(),
                ]);
            }

            // Notify admins (không rollback nếu lỗi)
            if (!empty($changes)) {
                try {
                    $adminUsers = User::where('role', 'admin')->get();
                    $batchId = Str::uuid()->toString();

                    $typeText = match ($post->type) {
                        'post' => 'bài viết',
                        'notice' => 'thông báo',
                        'document' => 'tài liệu',
                        default => 'nội dung'
                    };

                    $changeList = '';
                    foreach ($changes as $field => $val) {
                        $changeList .= "<li><strong>{$field}</strong>: thay đổi</li>";
                    }

                    $htmlMessage =
                        "<p>{$typeText} <strong>{$post->title}</strong> của CLB <strong>{$club->name}</strong> vừa được cập nhật.</p>" .
                        "<p>Người cập nhật: <strong>" . auth()->user()->name . "</strong></p>" .
                        "<p><strong>Các thay đổi:</strong></p><ul>{$changeList}</ul>";

                    $textMessage =
                        ucfirst($typeText) . " '{$post->title}' của CLB {$club->name} đã được cập nhật.";

                    foreach ($adminUsers as $admin) {
                        $admin->notify(new \App\Notifications\ClientNotification(
                            title: "Cập nhật {$typeText} trong CLB {$club->name}",
                            contentHtml: $htmlMessage,
                            contentText: $textMessage,
                            batchId: $batchId,
                            actionType: 'post_update',
                            relatedId: $log?->id,
                            relatedModel: 'PostUpdateLog'
                        ));
                    }
                } catch (\Exception $notifyErr) {
                    // bỏ qua lỗi notify
                }
            }

            // Media attach
            $usedFiles = $this->extractMediaFromContent($post->content);

            Media::where('related_type', 'post')
                ->where('related_id', 0)
                ->where('uploaded_by', Auth::id())
                ->whereIn('file_name', $usedFiles)
                ->update(['related_id' => $post->id]);

            // Orphan cleanup
            $orphan = Media::where('related_type', 'post')
                ->where('related_id', 0)
                ->where('uploaded_by', Auth::id())
                ->get();

            foreach ($orphan as $media) {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->forceDelete();
            }

            // Remove old media not in content
            foreach ($post->media as $media) {
                if (!in_array($media->file_name, $usedFiles)) {
                    if (Storage::disk('public')->exists($media->file_path)) {
                        Storage::disk('public')->delete($media->file_path);
                    }
                    $media->forceDelete();
                }
            }

            DB::commit();

            return redirect()
                ->route('club_manager.posts.show', ['club_id' => $club_id, 'post' => $post->id])
                ->with('success', 'Cập nhật thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }









    protected function extractMediaFromContent($content)
    {
        $usedFiles = [];

        if (empty($content)) {
            return $usedFiles;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $tags = array_merge(
            iterator_to_array($dom->getElementsByTagName('img')),
            iterator_to_array($dom->getElementsByTagName('a')),
            iterator_to_array($dom->getElementsByTagName('video')),
            iterator_to_array($dom->getElementsByTagName('audio')),
            iterator_to_array($dom->getElementsByTagName('source'))
        );

        foreach ($tags as $tag) {
            if (!$tag instanceof \DOMElement)
                continue;

            $url = $tag->getAttribute('src') ?: $tag->getAttribute('href');
            if (!$url)
                continue;

            if (!Str::contains($url, '/storage/'))
                continue;

            $usedFiles[] = basename($url);
        }

        return array_unique($usedFiles);
    }



    /**
     * Xóa bài viết
     */
    public function destroy(Request $request, $club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $post = Post::where('club_id', $club_id)->findOrFail($id);

        $reason = $request->input('delete_reason');

        // Soft delete
        $post->delete();

        // Lưu log xóa
        $log = PostUpdateLog::create([
            'post_id' => $post->id,
            'changed_by' => auth()->id(),
            'changes' => [
                'deleted' => [null, 'deleted'],
                'delete_reason' => $reason,
            ],
        ]);

        // Notify admins
        $adminUsers = User::where('role', 'admin')->get();
        $batchId = Str::uuid()->toString();

        $title = "Bài viết đã bị xóa trong CLB {$club->name}";
        $htmlMessage =
            "<p>Bài viết <strong>{$post->title}</strong> của CLB <strong>{$club->name}</strong> đã bị xóa.</p>" .
            "<p>Người xóa: <strong>" . auth()->user()->name . "</strong></p>" .
            ($reason ? "<p><strong>Lý do:</strong> {$reason}</p>" : "");

        $textMessage = "Bài viết '{$post->title}' của CLB {$club->name} đã bị xóa.";

        foreach ($adminUsers as $admin) {
            $admin->notify(new \App\Notifications\ClientNotification(
                title: $title,
                contentHtml: $htmlMessage,
                contentText: $textMessage,
                batchId: $batchId,
                actionType: 'post_deleted',
                relatedId: $log->id,              // link tới chi tiết log
                relatedModel: 'PostUpdateLog'
            ));
        }

        return redirect()->route('club_manager.posts.index', ['club_id' => $club_id])
            ->with('success', 'Đã xóa bài viết thành công.');
    }




    /**
     * Xử lý media từ nội dung HTML
     */
    /**
     * Xử lý các media (ảnh, file, video...) xuất hiện trong content CKEditor
     * Gắn các file này vào bài viết hiện tại để quản lý media không bị "mồ côi"(store).
     */
    private function processMediaFromContent($post, $content)
    {
        // Tạo DOMDocument để parse HTML
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // tránh báo lỗi HTML không hợp lệ

        // Load HTML vào DOM (CKEditor thường có HTML rút gọn nên cần options)
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Gom toàn bộ các thẻ chứa link file: img (src), a (href), source (src)
        $tags = array_merge(
            iterator_to_array($dom->getElementsByTagName('img')),
            iterator_to_array($dom->getElementsByTagName('a')),
            iterator_to_array($dom->getElementsByTagName('source'))
        );

        // Duyệt từng tag để lấy file
        foreach ($tags as $tag) {
            if (!$tag instanceof \DOMElement)
                continue;

            // Xác định thuộc tính chứa URL (src hoặc href)
            $attr = $tag->hasAttribute('src')
                ? 'src'
                : ($tag->hasAttribute('href') ? 'href' : null);

            if (!$attr)
                continue;

            // Lấy đường dẫn file
            $url = $tag->getAttribute($attr);

            // Chỉ xử lý file thuộc thư mục public/storage/
            if (!Str::contains($url, '/storage/'))
                continue;

            // Lấy đường dẫn tương đối trong storage: uploads/.../image.png
            $relativePath = Str::after($url, '/storage/');

            // Lấy đường dẫn thật trên server
            $fullPath = storage_path('app/public/' . $relativePath);

            // Nếu file không tồn tại thì bỏ qua
            if (!file_exists($fullPath))
                continue;

            // Lấy MIME type file
            $mime = mime_content_type($fullPath);

            // Lấy tên file
            $fileName = basename($fullPath);

            // Tạo hoặc cập nhật bản ghi media trong database
            Media::updateOrCreate(
                [
                    'file_path' => $relativePath, // unique theo file
                    'related_type' => 'post',
                ],
                [
                    'file_name' => $fileName,
                    'file_type' => $mime,
                    'uploaded_by' => Auth::id(),
                    'related_id' => $post->id, // Gắn vào bài viết
                ]
            );
        }

        // Gán lại các media "mồ côi" (related_id = 0) cho bài viết
        Media::where('uploaded_by', Auth::id())
            ->where('related_id', 0)
            ->where('related_type', 'post')
            ->update(['related_id' => $post->id]);
    }


    /**
     * Upload image cho CKEditor
     */
    public function uploadImage(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $file = $request->file('upload') ?? $request->file('image');

        if (!$file) {
            return response()->json(['error' => ['message' => 'Không có file nào được gửi.']], 400);
        }

        try {
            $folder = storage_path('app/public/uploads/posts/images');
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $originalName = $file->getClientOriginalName();
            $i = 1;
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();
            $finalName = $originalName;
            while (file_exists($folder . '/' . $finalName)) {
                $finalName = $baseName . "($i)." . $ext;
                $i++;
            }

            $file->move($folder, $finalName);
            $path = 'uploads/posts/images/' . $finalName;
            $url = asset('storage/' . $path);

            // Tạo media record
            $postId = $request->input('post_id', 0);
            Media::create([
                'file_name' => $finalName,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'related_id' => $postId,
                'related_type' => 'post',
                'uploaded_by' => Auth::id(),
            ]);

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }

    /**
     * Upload file cho CKEditor
     */
    public function uploadFile(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $file = $request->file('upload') ?? $request->file('file');

        if (!$file) {
            return response()->json(['error' => ['message' => 'Không có file nào được gửi.']], 400);
        }

        try {
            $mimeType = $file->getMimeType();
            $fileType = explode('/', $mimeType)[0];

            $folderMap = [
                'image' => 'images',
                'video' => 'videos',
                'audio' => 'audios',
            ];

            $subfolder = $folderMap[$fileType] ?? 'documents';
            $folder = storage_path("app/public/uploads/posts/{$subfolder}");

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $originalName = $file->getClientOriginalName();
            $i = 1;
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();
            $finalName = $originalName;
            while (file_exists($folder . '/' . $finalName)) {
                $finalName = $baseName . "($i)." . $ext;
                $i++;
            }

            $file->move($folder, $finalName);
            $path = "uploads/posts/{$subfolder}/{$finalName}";
            $url = asset('storage/' . $path);

            // Tạo media record (related_id = 0 nếu chưa có post, sẽ update sau khi save post)
            $postId = $request->input('post_id', 0);
            Media::create([
                'file_name' => $finalName,
                'file_path' => $path,
                'file_type' => $mimeType,
                'related_id' => $postId,
                'related_type' => 'post',
                'uploaded_by' => Auth::id(),
            ]);

            return response()->json([
                'url' => $url,
                'name' => $finalName,
                'type' => $mimeType,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 500);
        }
    }
    // private function createDiff($old, $new)
    // {
    //     $changes = [];

    //     foreach ($new as $key => $value) {
    //         if (!array_key_exists($key, $old))
    //             continue;

    //         if ($old[$key] != $value) {
    //             $changes[$key] = [
    //                 'old' => $old[$key],
    //                 'new' => $value
    //             ];
    //         }
    //     }

    //     return $changes;
    // }


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

