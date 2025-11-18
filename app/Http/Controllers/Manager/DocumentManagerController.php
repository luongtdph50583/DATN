<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentManagerController extends Controller

{
        protected function CheckRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'manager') {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }
        return null;
    }

    // Hiển thị danh sách tài liệu phân loại theo MIME type và tag
    public function index(Request $request)
    {
        $manager = Auth::user();
        $clbId = $manager->club_id;

        $search = trim((string) $request->input('search', ''));
        $selectedType = $request->input('type', 'all');

        $documentsQuery = Document::where('clb_id', $clbId)->latest();

        if ($search !== '') {
            $documentsQuery->where(function ($query) use ($search) {
                $query->where('file_name', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if (!empty($selectedType) && $selectedType !== 'all') {
            $documentsQuery->where('file_type', $selectedType);
        }

        $documents = $documentsQuery->get();

        // Nhóm theo tag
        $documentsByTag = $documents->groupBy(function ($doc) {
            $tags = array_filter(array_map('trim', explode(',', $doc->tags ?? '')));
            return empty($tags) ? ['Không có tag'] : $tags;
        });

        $typeOptions = [
            'all' => 'Tất cả loại',
            'pdf' => 'PDF',
            'doc' => 'Word',
            'xls' => 'Excel',
            'jpg' => 'Hình ảnh',
            'mp3' => 'Âm thanh',
            'mp4' => 'Video',
        ];

        return view('manager.document.index', [
            'documentsByTag' => $documentsByTag,
            'search' => $search,
            'selectedType' => $selectedType,
            'typeOptions' => $typeOptions,
        ]);
    }

    // Form thêm tài liệu mới
    public function create()
    {
        return view('manager.document.create');
    }

    // Lưu tài liệu mới
    public function store(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,svg,mp3,wav,mp4',
            'tags' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;

        $folder = match (true) {
            str_starts_with($file->getMimeType(), 'image/') || str_contains($file->getMimeType(), 'svg') => 'images',
            str_starts_with($file->getMimeType(), 'audio/') => 'audio',
            str_starts_with($file->getMimeType(), 'video/') => 'videos',
            default => 'documents',
        };

        $filePath = $file->storeAs($folder, $fileName, 'public');

        Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $extension,
            'clb_id' => $manager->club_id,
            'uploaded_by' => $manager->id,
            'tags' => $request->tags,
            'status' => 'approved', // Manager tự approve tài liệu CLB
        ]);

        return redirect()->route('manager.document.index')->with('success', 'Tài liệu đã được tải lên thành công 🎉');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $document = Document::where('clb_id', Auth::user()->club_id)->findOrFail($id);
        return view('manager.document.edit', compact('document'));
    }

    // Cập nhật tài liệu
    public function update(Request $request, $id)
    {
        $document = Document::where('clb_id', Auth::user()->club_id)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,svg,mp3,wav,mp4',
            'tags' => 'nullable|string'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags
        ];

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;

            $folder = match (true) {
                str_starts_with($file->getMimeType(), 'image/') || str_contains($file->getMimeType(), 'svg') => 'images',
                str_starts_with($file->getMimeType(), 'audio/') => 'audio',
                str_starts_with($file->getMimeType(), 'video/') => 'videos',
                default => 'documents',
            };

            $filePath = $file->storeAs($folder, $fileName, 'public');

            $data['file_name'] = $fileName;
            $data['file_path'] = $filePath;
            $data['file_type'] = $extension;
        }

        $document->update($data);

        return redirect()->route('manager.document.index')->with('success', 'Tài liệu đã được cập nhật thành công ✅');
    }

    // Xóa mềm
    public function destroy($id)
    {
        $document = Document::where('clb_id', Auth::user()->club_id)->findOrFail($id);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('manager.document.index')->with('success', 'Tài liệu đã được đưa vào thùng rác 🗑️');
    }

    // Thùng rác
    public function trash()
    {
        $manager = Auth::user();
        $trashedDocuments = Document::onlyTrashed()
            ->where('clb_id', $manager->club_id)
            ->latest()
            ->get();

        return view('manager.document.trash', compact('trashedDocuments'));
    }

    // Khôi phục
    public function restore($id)
    {
        $document = Document::onlyTrashed()
            ->where('clb_id', Auth::user()->club_id)
            ->findOrFail($id);

        $document->restore();

        return redirect()->route('manager.document.trash')->with('success', 'Tài liệu đã được khôi phục ✅');
    }

    // Download
    public function download($id)
    {
        $document = Document::where('clb_id', Auth::user()->club_id)->findOrFail($id);
        $path = storage_path('app/public/' . $document->file_path);

        if (file_exists($path)) {
            return response()->download($path, $document->file_name);
        }

        return redirect()->back()->with('error', 'File không tồn tại hoặc đã bị xóa.');
    }

    // Show chi tiết tài liệu
    public function show($id)
    {
        $document = Document::where('clb_id', Auth::user()->club_id)->findOrFail($id);
        return view('manager.document.show', compact('document'));
    }

    // Search Ajax
    public function search(Request $request)
    {
        $manager = Auth::user();
        $keyword = $request->input('search');
        $type = $request->input('type');

        $query = Document::where('clb_id', $manager->club_id);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('file_name', 'like', "%{$keyword}%")
                  ->orWhere('title', 'like', "%{$keyword}%");
            });
        }

        if ($type && $type !== 'all') {
            $query->where('file_type', $type);
        }

        $documents = $query->latest()->get();

        $results = $documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'file_name' => $doc->file_name,
                'file_type' => $doc->file_type,
                'tags' => $doc->tags ?: 'Không có tag',
                'status' => $doc->status,
            ];
        });

        return response()->json($results);
    }
}
