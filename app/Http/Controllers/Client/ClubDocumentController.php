<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use App\Models\User;
use App\Models\Member;
use App\Models\Document;
use App\Models\ClubMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DocumentUpdateLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClubDocumentController extends Controller
{
    /**
     * Danh sách tài liệu của CLB
     */
    public function index(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id); // ✅ đổi từ $clubs thành $club

        $member = Member::where('user_id', auth()->id())->first();

        if (!$member) {
            $userRole = null;
        } else {
            $clubMember = ClubMember::where('club_id', $club_id)
                ->where('member_id', $member->id)
                ->first();

            $userRole = $clubMember->role ?? null;
        }

        $documents = Document::where('clb_id', $club_id)
            ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.pages.documents.index', compact(
            'club',      // ✅ truyền đúng tên biến
            'documents',
            'userRole'
        ));
    }

    public function filter(Request $request)
    {
        $query = DocumentUpdateLog::with(['document.club', 'changedBy']);

        // lọc theo keyword
        if ($request->filled('keyword')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->keyword}%");
            });
        }

        // lọc theo club_id
        if ($request->filled('club_id')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->where('clb_id', $request->club_id);
            });
        }

        // lọc theo role
        if ($request->filled('role')) {
            $query->whereHas('changedBy', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $logs = $query->latest()->paginate(20);
        $logs->appends($request->query());

        $logRoles = []; // xử lý role như bạn đã làm
        $clubs = Club::orderBy('name')->get();

        return view('admin.document_update_logs.index', compact('logs', 'logRoles', 'clubs'));
    }


    /**
     * Form tạo tài liệu mới
     */
    public function create($club_id)
    {
        $club = Club::findOrFail($club_id);

        // Kiểm tra quyền quản lý CLB
        $this->authorizeClubManager($club);

        return view('client.pages.documents.create', compact('club'));

    }

    /**
     * Lưu tài liệu mới
     */
    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);

        // Kiểm tra quyền
        $this->authorizeClubManager($club);

        Log::info('DocumentClubStore Request:', $request->all());

        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,ppt,pptx,txt,jpeg,png,jpg,svg,mp3,wav,mp4|max:10240',
            'access_level' => 'required|array',
            'access_level.*' => 'in:public,member,communication,event_manager,secretary,treasurer,deputy_manager,club_manager',
            'tags' => 'nullable|string'
        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            Log::error('Uploaded file is not valid', ['error' => $file->getError()]);
            return back()->withErrors(['file' => 'File tải lên không hợp lệ.']);
        }

        $mime = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

        // Xác định folder theo mime
        $folder = match (true) {
            Str::startsWith($mime, 'image/') || Str::contains($mime, 'svg') => 'club_documents/images',
            Str::startsWith($mime, 'audio/') => 'club_documents/audio',
            Str::startsWith($mime, 'video/') => 'club_documents/videos',
            Str::contains($mime, ['pdf', 'msword', 'spreadsheet', 'officedocument']) => 'club_documents/documents',
            default => 'club_documents/others',
        };

        Log::info('Saving file', [
            'original_name' => $originalName,
            'file_name' => $fileName,
            'mime' => $mime,
            'folder' => $folder,
        ]);

        try {
            $filePath = $file->storeAs($folder, $fileName, 'public');
        } catch (\Exception $e) {
            Log::error('Failed to store file', ['exception' => $e->getMessage()]);
            return back()->withErrors(['file' => 'Không lưu được file.']);
        }

        // Lấy role của user trong CLB này
        $member = Member::where('user_id', Auth::id())->first();
        $clubMember = ClubMember::where('club_id', $club_id)
            ->where('member_id', $member->id)
            ->first();
        $userRole = $clubMember->role ?? null;

        // Chủ nhiệm CLB → tự động approved
        if (in_array($userRole, ['club_manager', 'deputy_manager'])) {
            $status = 'approved';
        } else {
            $status = 'pending';
        }

        try {
            $document = Document::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_name' => $originalName,
                'file_path' => $filePath,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
                'clb_id' => $club_id,
                'uploaded_by' => Auth::id(),
                'access_level' => $request->access_level,
                'tags' => $request->tags,
                'status' => $status
            ]);

            Log::info('Document created successfully', ['id' => $document->id]);

        } catch (\Exception $e) {
            Log::error('Failed to save document to DB', ['exception' => $e->getMessage()]);
            return back()->withErrors(['db' => 'Không lưu được dữ liệu vào cơ sở dữ liệu.']);
        }

        if ($status === 'pending') {
            return redirect()->route('club_manager.club.documents.index', $club_id)
                ->with('success', 'Tài liệu đã được gửi để duyệt. Vui lòng chờ admin xét duyệt.');
        }

        return redirect()->route('club_manager.club.documents.index', $club_id)
            ->with('success', 'Tài liệu đã được tải lên thành công! 🎉');
    }

    /**
     * Xem chi tiết tài liệu
     */
    public function view($club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $document = Document::where('clb_id', $club_id)
            ->where('id', $id)
            ->firstOrFail();

        // Kiểm tra quyền xem
        $this->checkDocumentAccess($document);

        return view('client.pages.documents.view', compact('club', 'document'));
    }

    /**
     * Form chỉnh sửa tài liệu
     */
    public function edit($club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $document = Document::where('clb_id', $club_id)
            ->where('id', $id)
            ->firstOrFail();

        // Kiểm tra quyền
        $this->authorizeClubManager($club);

        // Không cần json_decode nữa vì đã cast sang array
        return view('client.pages.documents.edit', compact('club', 'document'));
    }

    /**
     * Cập nhật tài liệu
     */
    public function update(Request $request, $club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $document = Document::where('clb_id', $club_id)
            ->where('id', $id)
            ->firstOrFail();

        // Kiểm tra quyền
        $this->authorizeClubManager($club);

        // Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,ppt,pptx,txt,jpeg,png,jpg,svg,mp3,wav,mp4|max:10240',
            'access_level' => 'required|array',
            'access_level.*' => 'in:public,member,communication,event_manager,secretary,treasurer,deputy_manager,club_manager',
            'tags' => 'nullable|string'
        ]);

        // Lấy dữ liệu cũ trước khi update
        $oldData = $document->getOriginal();

        // Nếu có file mới
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if (!$file->isValid()) {
                return back()->withErrors(['file' => 'File tải lên không hợp lệ.']);
            }

            // Xóa file cũ
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $mime = $file->getMimeType();
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

            $folder = match (true) {
                Str::startsWith($mime, 'image/') => 'club_documents/images',
                Str::startsWith($mime, 'audio/') => 'club_documents/audio',
                Str::startsWith($mime, 'video/') => 'club_documents/videos',
                Str::contains($mime, ['pdf', 'msword', 'spreadsheet', 'officedocument']) => 'club_documents/documents',
                default => 'club_documents/others',
            };

            $filePath = $file->storeAs($folder, $fileName, 'public');

            $document->file_name = $originalName;
            $document->file_path = $filePath;
            $document->file_type = $extension;
            $document->file_size = $file->getSize();
        }

        // Cập nhật thông tin mới
        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'access_level' => $request->access_level,
            'tags' => $request->tags,
        ]);

        // ✅ Xác định các trường thay đổi
        $changes = [];

        if (($oldData['title'] ?? null) !== $document->title) {
            $changes['title'] = [
                'old' => $oldData['title'] ?? null,
                'new' => $document->title,
            ];
        }

        if (($oldData['description'] ?? null) !== $document->description) {
            $changes['description'] = [
                'old' => $oldData['description'] ?? null,
                'new' => $document->description,
            ];
        }

        if (($oldData['access_level'] ?? null) != $document->access_level) {
            $changes['access_level'] = [
                'old' => $oldData['access_level'] ?? null,
                'new' => $document->access_level,
            ];
        }

        if (($oldData['tags'] ?? null) !== $document->tags) {
            $changes['tags'] = [
                'old' => $oldData['tags'] ?? null,
                'new' => $document->tags,
            ];
        }

        if (($oldData['file_path'] ?? null) !== $document->file_path) {
            $changes['file'] = [
                'old' => $oldData['file_path'] ?? null,
                'new' => $document->file_path,
            ];
        }

        // ✅ Chỉ tạo log nếu có thay đổi
        if (!empty($changes)) {
            $log = DocumentUpdateLog::create([
                'document_id' => $document->id,
                'changed_by' => auth()->id(),
                'changes' => $changes,
            ]);

            // ✅ Gửi thông báo cho admin
            $admins = User::where('role', 'admin')->get();
            $batchId = 'document_updated_' . $document->id . '_' . Str::random(6);

            $title = "Tài liệu đã được cập nhật trong CLB {$club->name}";
            $htmlMessage =
                "<p>Tài liệu <strong>{$document->title}</strong> của CLB <strong>{$club->name}</strong> đã được cập nhật.</p>" .
                "<p>Người cập nhật: <strong>" . auth()->user()->name . "</strong></p>";

            $textMessage = "Tài liệu '{$document->title}' của CLB {$club->name} đã được cập nhật.";

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\ClientNotification(
                    title: $title,
                    contentHtml: $htmlMessage,
                    contentText: $textMessage,
                    batchId: $batchId,
                    actionType: 'document_update',
                    relatedId: $log->id,
                    relatedModel: 'DocumentUpdateLog'
                ));
            }
        }

        return redirect()
            ->route('club_manager.club.documents.index', $club_id)
            ->with('success', 'Cập nhật tài liệu thành công và đã gửi thông báo cho admin!');
    }



    /**
     * Xóa tài liệu
     */
    public function destroy(Request $request, $club_id, $id)
    {
        $club = Club::findOrFail($club_id);
        $document = Document::where('clb_id', $club_id)
            ->where('id', $id)
            ->firstOrFail();

        // Kiểm tra quyền
        $this->authorizeClubManager($club);

        // Lý do xóa (truyền từ form hidden input hoặc prompt)
        $reason = $request->input('delete_reason', 'Không có lý do');

        // Xóa file vật lý
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Soft delete record
        $document->delete();

        // ✅ Lưu log
        $log = DocumentUpdateLog::create([
            'document_id' => $document->id,
            'changed_by' => auth()->id(),
            'changes' => [
                'deleted' => true,
                'delete_reason' => $reason,
            ],
        ]);

        // ✅ Gửi thông báo cho admin
        $admins = User::where('role', 'admin')->get();
        $batchId = 'document_deleted_' . $document->id . '_' . Str::random(6);

        $title = "Tài liệu đã bị xóa trong CLB {$club->name}";
        $htmlMessage =
            "<p>Tài liệu <strong>{$document->title}</strong> của CLB <strong>{$club->name}</strong> đã bị xóa.</p>" .
            "<p>Lý do: <em>{$reason}</em></p>" .
            "<p>Người xóa: <strong>" . auth()->user()->name . "</strong></p>";

        $textMessage = "Tài liệu '{$document->title}' của CLB {$club->name} đã bị xóa. Lý do: {$reason}.";

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\ClientNotification (
                title: $title,
                contentHtml: $htmlMessage,
                contentText: $textMessage,
                batchId: $batchId,
                actionType: 'document_delete',
                relatedId: $log->id,
                relatedModel: 'DocumentUpdateLog'
            ));
        }

        return redirect()
            ->route('club_manager.club.documents.index', $club_id)
            ->with('success', 'Đã xóa tài liệu .');
    }


    /**
     * Kiểm tra quyền truy cập tài liệu
     */
    private function checkDocumentAccess(Document $document)
    {
        $accessLevels = $document->access_level; // luôn là array

        // 1. Public => ai cũng xem được
        if (in_array('public', $accessLevels)) {
            return true;
        }

        // 2. Phải đăng nhập
        if (!Auth::check()) {
            abort(403, 'Bạn cần đăng nhập để xem tài liệu này.');
        }

        // 3. Lấy Member của user
        $member = Member::where('user_id', Auth::id())->first();
        if (!$member) {
            abort(403, 'Bạn không phải thành viên CLB.');
        }

        // 4. Lấy vai trò trong CLB
        $clubMember = ClubMember::where('club_id', $document->clb_id)
            ->where('member_id', $member->id)
            ->first();

        $userRole = $clubMember->role ?? null;

        if (!$userRole) {
            abort(403, 'Bạn không có quyền xem tài liệu này.');
        }

        // 5. Kiểm tra quyền truy cập
        if (in_array($userRole, $accessLevels) || in_array('member', $accessLevels)) {
            return true;
        }

        abort(403, 'Bạn không có quyền xem tài liệu này.');
    }


    /**
     * Kiểm tra quyền quản lý CLB
     */
    private function authorizeClubManager($club)
    {
        $member = Member::where('user_id', Auth::id())->first();

        if (!$member) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }

        $clubMember = ClubMember::where('club_id', $club->id)
            ->where('member_id', $member->id)
            ->first();

        $userRole = $clubMember->role ?? null;

        // Chỉ cho phép ban quản lý
        if (!in_array($userRole, ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication'])) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}