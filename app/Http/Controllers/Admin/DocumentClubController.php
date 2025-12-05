<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\Media;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
use App\Models\DocumentUpdateLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentClubController extends Controller
{
    // Hiển thị danh sách tài liệu phân loại theo MIME type
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $selectedType = $request->input('type', 'all');

        $documentsQuery = Document::with('club', 'uploader')->latest();

        if ($search !== '') {
            $documentsQuery->where(function ($query) use ($search) {
                $query->where('file_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('uploader', function ($uploaderQuery) use ($search) {
                        $uploaderQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($selectedType) && $selectedType !== 'all') {
            $documentsQuery->where('file_type', $selectedType);
        }

        $documents = $documentsQuery->get();

        // Nhóm theo CLB trước, sau đó nhóm theo tag
        $documentsByClub = $documents->groupBy(function ($doc) {
            return $doc->club->name ?? 'Không CLB';
        })->map(function ($clubDocs) {
            $tagGroups = collect();

            foreach ($clubDocs as $doc) {
                $tags = array_filter(array_map('trim', explode(',', $doc->tags ?? '')));
                if (empty($tags)) {
                    $tagGroups->push(['tag' => 'Không có tag', 'doc' => $doc]);
                } else {
                    foreach ($tags as $tag) {
                        $tagGroups->push(['tag' => $tag, 'doc' => $doc]);
                    }
                }
            }

            // Nhóm theo tag
            return $tagGroups->groupBy('tag')->map(function ($group) {
                return $group->pluck('doc');
            });
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

        return view('admin.documentclub.index', [
            'documentsByClub' => $documentsByClub,
            'search' => $search,
            'selectedType' => $selectedType,
            'typeOptions' => $typeOptions,
        ]);
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
        // Ghi log toàn bộ request
        Log::info('DocumentClubStore Request:', $request->all());

        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,svg,mp3,wav,mp4,sql',
            'clb_id' => 'required|exists:clubs,id',
            'access_level' => 'required|array',
            'access_level.*' => 'in:public,guest,member,communication,event_manager,secretary,treasurer,deputy_manager,club_manager,admin',
            'tags' => 'nullable|string'
        ]);

        $file = $request->file('file');

        // Kiểm tra file có hợp lệ
        if (!$file->isValid()) {
            Log::error('Uploaded file is not valid', ['error' => $file->getError()]);
            return back()->withErrors(['file' => 'File tải lên không hợp lệ.']);
        }

        $mime = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

        // Phân loại thư mục theo MIME
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
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'sql',
            ]) => 'documents',
            default => 'others',
        };

        // Log thông tin trước khi lưu file
        Log::info('Saving file', [
            'original_name' => $originalName,
            'file_name' => $fileName,
            'mime' => $mime,
            'folder' => $folder,
        ]);

        // Lưu file vào storage
        try {
            $filePath = $file->storeAs($folder, $fileName, 'public');
        } catch (\Exception $e) {
            Log::error('Failed to store file', ['exception' => $e->getMessage()]);
            return back()->withErrors(['file' => 'Không lưu được file.']);
        }

        // Log access_level
        Log::info('Access level selected', ['access_level' => $request->access_level]);

        // Lưu vào DB
        try {
            $document = Document::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $extension,
                'clb_id' => $request->clb_id,
                'uploaded_by' => Auth::id(),
                'access_level' => $request->access_level, // Laravel sẽ tự cast sang JSON
                'tags' => $request->tags,
                'status' => 'approved'
            ]);
            Log::info('Document created successfully', ['id' => $document->id]);
        } catch (\Exception $e) {
            Log::error('Failed to save document to DB', ['exception' => $e->getMessage()]);
            return back()->withErrors(['db' => 'Không lưu được dữ liệu vào cơ sở dữ liệu.']);
        }

        return redirect()->route('admin.documentclub.index')->with('success', 'Tài liệu đã được tải lên thành công 🎉');
    }



    // Form chỉnh sửa tài liệu
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,svg,mp3,wav,mp4',
            'clb_id' => 'required|exists:clubs,id',
            'access_level' => 'required|array',
            'access_level.*' => 'in:public,guest,member,communication,event_manager,secretary,treasurer,deputy_manager,club_manager,admin',
            'tags' => 'nullable|string',
        ]);

        $data = $request->only([
            'title',
            'description',
            'clb_id',
            'tags',
        ]);

        // Lưu access_level dưới dạng JSON
        $data['access_level'] = json_encode($request->access_level);

        // Nếu có file mới
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;

            $folder = match (true) {
                Str::startsWith($file->getMimeType(), 'image/') || Str::contains($file->getMimeType(), 'svg') => 'images',
                Str::startsWith($file->getMimeType(), 'audio/') => 'audio',
                Str::startsWith($file->getMimeType(), 'video/') => 'videos',
                Str::contains($file->getMimeType(), [
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

            // Xóa file cũ nếu có
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $data['file_name'] = $fileName;
            $data['file_path'] = $filePath;
            $data['file_type'] = $extension;
        }

        // Cập nhật document
        $document->update($data);

        // ✅ Lưu log thay đổi
        DocumentUpdateLog::create([
            'document_id' => $document->id,
            'changed_by' => auth()->id(),
            'changes' => [
                'title' => $request->title,
                'description' => $request->description,
                'clb_id' => $request->clb_id,
                'tags' => $request->tags,
                'access_level' => $request->access_level,
                'file' => $request->hasFile('file') ? 'updated' : 'unchanged',
            ],
        ]);

        return redirect()
            ->route('admin.documentclub.index')
            ->with('success', 'Tài liệu đã được cập nhật thành công ✅ và đã lưu log.');
    }



    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $clubs = Club::all();

        return view('admin.documentclub.edit', compact('document', 'clubs'));
    }
    // Xóa tài liệu
    public function destroy(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $reason = $request->input('reason', 'Vi phạm nội quy');

        // ✅ Xóa mềm media liên quan
        Media::where('related_type', 'document')
            ->where('related_id', $document->id)
            ->delete(); // chỉ gán deleted_at

        // ✅ Không xóa file vật lý — giữ nguyên để khôi phục
        // Nếu muốn xóa file thì thêm đoạn Storage::delete()

        // ✅ Xóa mềm tài liệu
        $document->delete();

        // ✅ Lưu log xóa
        DocumentUpdateLog::create([
            'document_id' => $document->id,
            'changed_by' => auth()->id(),
            'changes' => [
                'deleted' => [null, 'deleted'],
                'delete_reason' => $reason,
            ],
        ]);

        // ✅ Gửi thông báo cho chủ nhiệm CLB hoặc người upload
        if ($document->uploader) {
            $batchId = 'document_deleted_' . $document->id . '_' . Str::random(6);

            dispatch(new SendNotificationJob(
                userId: $document->uploader->id,
                title: 'Tài liệu bị xóa',
                content: "Tài liệu \"{$document->title}\" đã bị xóa. Lý do: {$reason}",
                sendVia: 'both',
                batchId: $batchId,
                force: false
            ));
        }

        return redirect()->route('admin.documentclub.index')
            ->with('success', 'Đã xóa tài liệu, lưu log và gửi thông báo cho chủ nhiệm.');
    }

    public function trash(Request $request)
    {
        $query = Document::onlyTrashed()->with(['club', 'uploader']);

        // ✅ Lọc theo từ khóa (tiêu đề hoặc mô tả)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // ✅ Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('clb_id', $request->club_id);
        }

        // ✅ Lọc theo người tải lên
        if ($request->filled('uploader')) {
            $query->whereHas('uploader', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->uploader}%");
            });
        }

        // ✅ Lọc theo tag
        if ($request->filled('tag')) {
            $query->where('tags', 'like', "%{$request->tag}%");
        }

        // Phân trang + giữ tham số lọc
        $trashedDocuments = $query->orderBy('deleted_at', 'desc')->paginate(10);
        $trashedDocuments->appends($request->query());

        // Nạp danh sách CLB để hiển thị dropdown lọc
        $clubs = Club::orderBy('name')->get();

        return view('admin.trash.documentclub.index', compact('trashedDocuments', 'clubs'));
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
    public function showTrash($id)
    {
        $document = Document::onlyTrashed()
            ->with(['club', 'uploader'])
            ->findOrFail($id);

        return view('admin.trash.documentclub.show', compact('document'));
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

        // Chuyển thành mảng để front-end dễ render
        $results = $documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'file_name' => $doc->file_name,
                'file_type' => $doc->file_type,
                'club_name' => $doc->club->name ?? 'Không CLB',
                'uploader_name' => $doc->uploader->name ?? '-',
                'status' => $doc->status,
                'is_visible' => $doc->is_visible,
                'tags' => $doc->tags ?: 'Không có tag',
            ];
        });

        return response()->json($results);
    }
    public function approve(Document $document)
    {
        if ($document->status !== 'pending') {
            return back()->with('error', 'Tài liệu không thể duyệt.');
        }

        $document->status = 'approved';
        $document->approved_by = auth()->id();
        $document->approved_at = now();
        $document->save();

        // Gửi thông báo In-App
        $batchId = 'document_approval_' . $document->id . '_' . time();
        $title = 'Tài liệu đã được duyệt';
        $content = "Tài liệu '{$document->title}' của bạn đã được duyệt.";
        dispatch(new SendNotificationJob($document->uploaded_by, $title, $content, 'database', $batchId));

        return back()->with('success', 'Duyệt tài liệu thành công.');
    }

    public function reject(Request $request, Document $document)
    {
        if ($document->status !== 'pending') {
            return back()->with('error', 'Tài liệu không thể từ chối.');
        }

        $reason = $request->input('reason', 'Không có lý do');
        $document->status = 'rejected';
        $document->rejected_reason = $reason;
        $document->approved_by = auth()->id();
        $document->approved_at = now();
        $document->save();

        // Gửi thông báo In-App
        $batchId = 'document_rejection_' . $document->id . '_' . time();
        $title = 'Tài liệu bị từ chối';
        $content = "Tài liệu '{$document->title}' của bạn bị từ chối. Lý do: {$reason}";
        dispatch(new SendNotificationJob($document->uploaded_by, $title, $content, 'database', $batchId));

        return back()->with('success', 'Từ chối tài liệu thành công.');
    }




}
