@extends('admin.layouts.blank')

@section('title', 'Duyệt đơn CLB')

@section('card-body')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Duyệt yêu cầu CLB</h5>

            <!-- Hiển thị PDF đơn xin tham gia CLB người dùng đã ký -->
            @if($request->membership_file)
                <div class="mb-3">
                    <iframe id="pdfViewer" src="{{ asset('storage/' . $request->membership_file) }}" width="100%"
                        height="600px"></iframe>
                </div>
            @else
                <div class="alert alert-warning">Người dùng chưa tải lên đơn xin tham gia CLB.</div>
            @endif

            <!-- Canvas ký admin -->
            <label class="form-label">Ký tên (Admin)</label>
            <canvas id="adminSignaturePad" style="border:1px solid #ccc; width:100%; height:150px;"></canvas>
            <input type="hidden" name="admin_signature" id="adminSignatureInput">
            <button type="button" id="clearSignature" class="btn btn-sm btn-warning mt-2">Xóa chữ ký</button>

            <!-- Form duyệt/từ chối -->
            <form id="handleForm" method="POST" action="{{ route('admin.club_requests.handle', $request->id) }}"
                class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ghi chú (nếu có)</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note', $request->note) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="status" value="approved" class="btn btn-success">Duyệt</button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger">Từ chối</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOM loaded - chuẩn bị ký admin');

                const canvas = document.getElementById('adminSignaturePad');
                const signatureInput = document.getElementById('adminSignatureInput');
                const clearBtn = document.getElementById('clearSignature');
                const form = document.getElementById('handleForm');

                if (!canvas || !signatureInput || !form) {
                    console.error('Canvas, input hoặc form không tồn tại!');
                    return;
                }

                const signaturePad = new SignaturePad(canvas);
                console.log('SignaturePad đã khởi tạo');

                // Xóa chữ ký
                clearBtn.addEventListener('click', function () {
                    signaturePad.clear();
                    console.log('Canvas đã xóa chữ ký');
                });

                // Khi submit form, lưu chữ ký Base64 vào input
                form.addEventListener('submit', function (e) {
                    if (signaturePad.isEmpty()) {
                        e.preventDefault();
                        alert('Vui lòng ký tên trước khi duyệt hoặc từ chối!');
                        console.log('Chưa có chữ ký, ngăn submit');
                    } else {
                        signatureInput.value = signaturePad.toDataURL();
                        console.log('Chữ ký admin đã lưu vào input:', signatureInput.value);
                    }
                });
            });
        </script>
    @endpush
@endsection