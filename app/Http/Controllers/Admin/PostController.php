<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index()
    {

        // Lấy danh sách bài viết kèm theo thông tin CLB và người đăng
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

    // Chuyển hướng về trang danh sách bài viết
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
    public function show($id)
{
    

    $post = Post::with(['club', 'user', 'media'])->findOrFail($id);

    return view('admin.posts.show', compact('post'));
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
