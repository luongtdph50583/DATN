<?php

namespace App\Http\Controllers\Admin;
use App\Models\DeletedComment;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{

    public function index()
    {
        $comments = Comment::with('post', 'user')->orderBy('created_at', 'desc')->get();
        return view('admin.comments.index', compact('comments'));
    }

    public function show(Comment $comment)
    {
        return view('admin.comments.show', compact('comment'));
    }

  
public function destroy(Request $request, Comment $comment)
{
    $request->validate([
        'deleted_reason' => 'required|string|max:255',
    ]);

    // Tạo record trong deleted_comments
    DeletedComment::create([
        'comment_id' => $comment->id,
        'deleted_by' => auth()->id(),
        'deleted_reason' => $request->deleted_reason,
    ]);

    // Xóa comment (soft delete)
    $comment->delete();

    return redirect()->back()->with('success', 'Comment đã được xóa thành công!');
}

    public function toggleStatus(Comment $comment)
    {
        // Đảo trạng thái
        $comment->status = $comment->status === 'visible' ? 'hidden' : 'visible';
        $comment->save();

        // Thông báo
        $message = $comment->status === 'visible'
            ? '✅ Bình luận đã được hiển thị lại.'
            : '🙈 Bình luận đã được ẩn.';

        return redirect()->back()->with('success', $message);
    }
}
