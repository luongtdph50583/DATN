<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentClubController extends Controller
{
    // Hiển thị danh sách tài liệu phân loại theo MIME type
    public function index()
    {
        $documents = Document::with('club', 'uploader')->latest()->get();

        $tagGroups = collect();

        foreach ($documents as $doc) {
            $tags = array_filter(array_map('trim', explode(',', $doc->tags ?? '')));
            if (empty($tags)) {
                $tagGroups->push(['tag' => 'Không có tag', 'doc' => $doc]);
            } else {
                foreach ($tags as $tag) {
                    $tagGroups->push(['tag' => $tag, 'doc' => $doc]);
                }
            }
        }

        $documentsByTag = $tagGroups->groupBy('tag')->map(function ($group) {
            return $group->pluck('doc');
        });

        return view('admin.documentclub.index', compact('documentsByTag'));
    }



    // Form thêm tài liệu mới
    public function create()
    {
        $clubs = Club::all();
        return view('admin.documentclub.create', compact('clubs'));
    }

    // Lưu tài liệu mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,svg,mp3,wav,mp4',
            'clb_id' => 'required|exists:clubs,id',
            'access_level' => 'required|in:public,member,admin',
            'tags' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;

        // 📁 Phân loại thư mục theo MIME
        $folder = match (true) {
            // Hình ảnh (bao gồm cả SVG và logo)
            Str::startsWith($mime, 'image/') || Str::contains($mime, 'svg') => 'images',

            // Âm thanh
            Str::startsWith($mime, 'audio/') => 'audio',

            // Video
            Str::startsWith($mime, 'video/') => 'videos',

            // Tài liệu văn bản, bao gồm cả Excel
            Str::contains($mime, [
                'pdf',
                'msword',
                'spreadsheet',
                'officedocument',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ]) => 'documents',

            // Mặc định
            default => 'others',
        };


        // 📥 Lưu file vào thư mục tương ứng
        $filePath = $file->storeAs($folder, $fileName, 'public');

        // 📝 Lưu vào DB
        Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $extension,
            'clb_id' => $request->clb_id,
            'uploaded_by' => Auth::id(),
            'access_level' => $request->access_level,
            'tags' => $request->tags
        ]);

        return redirect()->route('admin.documentclub.index')->with('success', 'Tài liệu đã được tải lên thành công 🎉');
    }

    // Form chỉnh sửa tài liệu
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,svg,mp3,wav,mp4',
            'clb_id' => 'required|exists:clubs,id',
            'access_level' => 'required|in:public,member,club_manager,admin',
            'tags' => 'nullable|string'
        ]);

        $data = $request->only([
            'title',
            'description',
            'clb_id',
            'access_level',
            'tags'
        ]);

        // Nếu có file mới
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mime = $file->getMimeType();
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;

            $folder = match (true) {
                Str::startsWith($mime, 'image/') || Str::contains($mime, 'svg') => 'images',
                Str::startsWith($mime, 'audio/') => 'audio',
                Str::startsWith($mime, 'video/') => 'videos',
                Str::contains($mime, [
                    'pdf',
                    'msword',
                    'spreadsheet',
                    'officedocument',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                ]) => 'documents',
                default => 'others',
            };

            $filePath = $file->storeAs($folder, $fileName, 'public');

            // Xóa file cũ nếu cần
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Cập nhật thông tin file mới
            $data['file_name'] = $fileName;
            $data['file_path'] = $filePath;
            $data['file_type'] = $extension;
        }

        $document->update($data);

        return redirect()->route('admin.documentclub.index')->with('success', 'Tài liệu đã được cập nhật thành công ✅');
    }
    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $clubs = Club::all();

        return view('admin.documentclub.edit', compact('document', 'clubs'));
    }
    // Xóa tài liệu
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        // (Tùy chọn) Xóa file vật lý nếu bạn muốn
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Xóa mềm: Laravel sẽ gán deleted_at
        $document->delete();

        return redirect()->route('admin.documentclub.index')->with('success', 'Tài liệu đã được đưa vào thùng rác 🗑️');
    }
public function trash()
{
    $trashedDocuments = Document::onlyTrashed()->with('club', 'uploader')->latest()->get();
    return view('admin.trash.documentclub.index', compact('trashedDocuments'));
}

public function restore($id)
{
    $document = Document::onlyTrashed()->findOrFail($id);
    $document->restore();

    return redirect()->route('admin.documentclub.trash')->with('success', 'Tài liệu đã được khôi phục ✅');
}

public function forceDelete($id)
{
    $document = Document::onlyTrashed()->findOrFail($id);

    // Xóa file vật lý nếu tồn tại
    if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
        Storage::disk('public')->delete($document->file_path);
    }

    $document->forceDelete();

    return redirect()->route('admin.documentclub.trash')->with('success', 'Tài liệu đã bị xóa vĩnh viễn 🗑️');
}

    // Download tài liệu
    public function download(Document $document)
    {
        $path = storage_path('app/public/' . $document->file_path);

        if (file_exists($path)) {
            return response()->download($path, $document->file_name);
        }

        return redirect()->back()->with('error', 'File không tồn tại hoặc đã bị xóa.');
    }

    public function show($id)
    {
        $document = Document::with('club', 'uploader')->findOrFail($id);
        return view('admin.documentclub.show', compact('document'));
    }
    public function search(Request $request)
    {
        $keyword = $request->input('search');
        $type = $request->input('type');

        $query = Document::query()->with('club', 'uploader');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('file_name', 'like', "%{$keyword}%")
                    ->orWhere('title', 'like', "%{$keyword}%")
                    ->orWhereHas('uploader', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($type && $type !== 'all') {
            $query->where('file_type', $type);
        }

        $documents = $query->latest()->get();

        return response()->json($documents);
    }



}
