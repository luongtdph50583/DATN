<?php

namespace App\Http\Controllers\Admin;

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

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('admin.comments.index')->with('success', 'Bình luận đã được xóa thành công!');
    }

    public function toggleStatus(Comment $comment)
    {
        $comment->update(['status' => $comment->status === 'visible' ? 'hidden' : 'visible']);
        return redirect()->back()->with('success', 'Trạng thái bình luận đã được cập nhật!');
    }
}