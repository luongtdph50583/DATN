<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Media;
use App\Models\User;

class DocumentPostController extends Controller
{
    /**
     * Kiểm tra quyền admin.
     * Nếu chưa đăng nhập hoặc không phải admin thì chặn truy cập.
     */
    protected function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }
        return null;
    }

    /**
     * Hiển thị trang quản lý tài liệu.
     */
    public function index()
    {
        // Kiểm tra quyền admin
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        // Lấy danh sách các định dạng file (unique)
        $fileTypes = Media::select('file_type')->distinct()->pluck('file_type');

        // Lấy toàn bộ tài liệu có thông tin uploader
        $media = Media::with('uploader')->latest()->get();

        return view('admin.documents.index', compact('media', 'fileTypes'));
    }

    /**
     * API lọc tài liệu (AJAX)
     */
    public function filter(Request $request)
    {
        // Kiểm tra quyền admin
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        // Tạo query, có quan hệ uploader
        $query = Media::with('uploader');

        /**
         * Lọc theo định dạng MIME (ví dụ: image/jpeg, application/pdf, ...)
         * Dùng LIKE để tránh thiếu các định dạng tương tự.
         */
        if ($request->filled('file_type') && $request->file_type !== 'all') {
            $query->where('file_type', 'like', '%' . $request->file_type . '%');
        }

        /**
         * Lọc theo từ khóa nhập vào (keyword)
         * Tìm trong: tên file OR tên người upload
         */
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('file_name', 'like', "%{$keyword}%")
                  ->orWhereHas('uploader', function ($sub) use ($keyword) {
                      $sub->where('name', 'like', "%{$keyword}%");
                  });
            });
        }
                // SELECT media.*
                // FROM media
                // WHERE (
                //     media.file_name LIKE '%keyword%'
                //     OR EXISTS (
                //         SELECT 1
                //         FROM users
                //         WHERE users.id = media.uploaded_by
                //         AND users.name LIKE '%keyword%'
                //     )
                // );



        // Lấy kết quả mới nhất
        $media = $query->latest()->get();

        // Trả về JSON để AJAX cập nhật bảng dữ liệu
        return response()->json(['data' => $media]);
    }


}
