<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TrashController extends Controller
{
    // 🗑️ Danh sách media đã xóa (thùng rác)
    public function index()
    {
        // Sử dụng query builder để gọi scopeTrashed
        $mediaList = Media::onlyTrashed()->get();

        return view('admin.trash.media.index', compact('mediaList'));
    }

    // 🔄 Khôi phục media từ thùng rác
    public function restore($id)
    {
        $media = Media::onlyTrashed()->findOrFail($id);

        $media->restore();

        return redirect()->route('admin.trash.media.index')
            ->with('success', 'Khôi phục media thành công!');
    }

    // ❌ Xóa vĩnh viễn media
    public function forceDelete($id)
    {
        $media = Media::onlyTrashed()->findOrFail($id);

        // Xóa file vật lý nếu tồn tại
        $filePath = storage_path('app/public/' . $media->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $media->forceDelete();

        return redirect()->route('admin.trash.media.index')
            ->with('success', 'Xóa media vĩnh viễn thành công!');
    }
}
