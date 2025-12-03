@extends('client.layouts.app')
@section('title', 'Form tuyển thành viên - ' . $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="mb-0">Form tuyển thành viên - {{ $club->name }}</h2>
            <div class="btn-group">
                <a href="{{ route('club_manager.recruit_forms.list', ['club_id' => $club->id]) }}"
                    class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i> Quản lý forms
                </a>
                {{-- <a href="{{ route('club_manager.recruit.index', ['club_id' => $club->id]) }}"
                    class="btn btn-outline-secondary">
                    <i class="fas fa-users me-1"></i> Danh sách yêu cầu
                </a> --}}
                <a href="{{ route('club_manager.recruit_forms.list', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Chọn form --}}
        @if(isset($allForms) && $allForms->count() > 0)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chọn form để quản lý:</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select" id="formSelector" data-select2="true"
                                onchange="window.location.href='{{ route('club_manager.recruit_form.create', ['club_id' => $club->id]) }}/' + this.value">
                                @foreach($allForms as $f)
                                    <option value="{{ $f->id }}" {{ isset($form) && $form->id == $f->id ? 'selected' : '' }}>
                                        {{ $f->name }} @if($f->is_default) (Mặc định) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(isset($form))
                        <div class="mt-3">
                            <p class="mb-1"><strong>Form hiện tại:</strong> {{ $form->name }}</p>
                            @if($form->description)
                                <p class="mb-0 text-muted small">{{ $form->description }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Các câu hỏi hiện tại</h5>
                @if(isset($questions))
                    <span class="text-muted small">Hiển thị {{ $questions->count() }} câu hỏi.</span>
                @endif
            </div>
            <div class="card-body">
                @if(!isset($questions) || $questions->isEmpty())
                    <p class="text-muted mb-0">Chưa có câu hỏi nào trong form tuyển thành viên.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Câu hỏi</th>
                                    <th>Loại</th>
                                    <th>Bắt buộc</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $index => $question)
                                    @php
                                        $questionPayload = [
                                            'question' => $question->question,
                                            'description' => $question->description,
                                            'type' => $questionTypes[$question->type] ?? $question->type,
                                            'is_required' => $question->is_required,
                                            'options' => $question->options ?? [],
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $question->question }}</strong>
                                            @if($question->description)
                                                <p class="mb-0 text-muted small">{{ $question->description }}</p>
                                            @endif
                                        </td>
                                        <td>{{ $questionTypes[$question->type] ?? $question->type }}</td>
                                        <td>
                                            @if($question->is_required)
                                                <span class="badge bg-success">Có</span>
                                            @else
                                                <span class="badge bg-secondary">Không</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($question->is_active)
                                                <span class="badge bg-primary">Đang hiển thị</span>
                                            @else
                                                <span class="badge bg-dark">Đã ẩn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2 js-question-detail"
                                                data-question='@json($questionPayload)' data-bs-toggle="modal"
                                                data-bs-target="#questionDetailModal">
                                                Xem chi tiết
                                            </button>
                                            <form
                                                action="{{ route('club_manager.recruit_form.toggle', ['club_id' => $club->id, 'question' => $question->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $question->is_active ? 'btn-outline-dark' : 'btn-outline-success' }}">
                                                    {{ $question->is_active ? 'Ẩn câu hỏi' : 'Hiển thị' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thêm câu hỏi mới</h5>
            </div>
            <div class="card-body">
                <form id="recruitQuestionForm"
                    action="{{ route('club_manager.recruit_form.store', ['club_id' => $club->id]) }}" method="POST">
                    @csrf
                    @if(isset($form))
                        <input type="hidden" name="form_id" value="{{ $form->id }}">
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control" value="{{ old('question') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="2" class="form-control"
                            placeholder="Mô tả chi tiết cho câu hỏi (nếu có)">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required data-select2="true"
                                data-placeholder="Chọn loại câu hỏi">
                                @foreach($questionTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="order" class="form-control" min="1" value="{{ old('order') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="1" id="is_required"
                                    name="is_required" {{ old('is_required') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">
                                    Bắt buộc trả lời
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Lựa chọn (mỗi dòng một lựa chọn)</label>
                        <div id="optionBuilderWrapper" class="d-none border rounded p-3">
                            <div id="optionList" class="d-flex flex-column gap-2"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="addOptionRow">
                                <i class="fa-solid fa-plus me-1"></i>Thêm lựa chọn
                            </button>
                        </div>
                        <textarea name="options" id="optionsTextarea" class="d-none">{{ old('options') }}</textarea>
                        <small class="text-muted d-block mt-2">
                            Tính năng này chỉ áp dụng cho câu hỏi dạng lựa chọn (select/checkbox).
                        </small>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Thêm câu hỏi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<div class="modal fade" id="questionDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionDetailTitle">Chi tiết câu hỏi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="questionDetailBody">
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.querySelector('select[name="type"]');
            const builderWrapper = document.getElementById('optionBuilderWrapper');
            const optionList = document.getElementById('optionList');
            const addOptionBtn = document.getElementById('addOptionRow');
            const textarea = document.getElementById('optionsTextarea');
            const form = document.getElementById('recruitQuestionForm');

            if (!typeSelect || !builderWrapper) {
                return;
            }

            const isMultiChoice = value => ['select', 'checkbox'].includes(value);

            const toggleBuilder = () => {
                if (isMultiChoice(typeSelect.value)) {
                    builderWrapper.classList.remove('d-none');
                    if (!optionList.childElementCount) {
                        addOptionRow();
                    }
                } else {
                    builderWrapper.classList.add('d-none');
                }
            };

            const addOptionRow = (value = '') => {
                const wrapper = document.createElement('div');
                wrapper.className = 'd-flex gap-2 align-items-center option-row';
                wrapper.innerHTML = `
                                <input type="text" class="form-control" placeholder="Nội dung lựa chọn" value="${value}">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-option">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            `;
                optionList.appendChild(wrapper);
            };

            const syncTextarea = () => {
                if (!isMultiChoice(typeSelect.value)) {
                    textarea.value = '';
                    return;
                }
                const values = Array.from(optionList.querySelectorAll('input'))
                    .map(input => input.value.trim())
                    .filter(Boolean);
                textarea.value = values.join('\n');
            };

            typeSelect.addEventListener('change', () => {
                toggleBuilder();
                syncTextarea();
            });

            addOptionBtn?.addEventListener('click', () => addOptionRow());

            optionList?.addEventListener('click', function (event) {
                if (event.target.closest('.remove-option')) {
                    const row = event.target.closest('.option-row');
                    row?.remove();
                    syncTextarea();
                }
            });

            form?.addEventListener('submit', syncTextarea);

            if (textarea.value) {
                textarea.value.split(/\r?\n/).forEach(value => value && addOptionRow(value));
            }

            toggleBuilder();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const titleEl = document.getElementById('questionDetailTitle');
            const bodyEl = document.getElementById('questionDetailBody');

            document.querySelectorAll('.js-question-detail').forEach(button => {
                button.addEventListener('click', function () {
                    try {
                        const data = JSON.parse(this.dataset.question);
                        titleEl.textContent = data.question || 'Chi tiết câu hỏi';
                        let html = `<p><strong>Loại:</strong> ${data.type || '—'}</p>`;
                        html += `<p><strong>Bắt buộc:</strong> ${data.is_required ? 'Có' : 'Không'}</p>`;
                        if (data.description) {
                            html += `<p><strong>Mô tả:</strong> ${data.description}</p>`;
                        }
                        if (Array.isArray(data.options) && data.options.length) {
                            html += '<p><strong>Lựa chọn:</strong></p><ul class="mb-0">';
                            data.options.forEach(opt => html += `<li>${opt}</li>`);
                            html += '</ul>';
                        } else {
                            html += '<p><strong>Lựa chọn:</strong> —</p>';
                        }
                        bodyEl.innerHTML = html;
                    } catch (error) {
                        console.error(error);
                        bodyEl.innerHTML = '<p class="text-danger">Không thể hiển thị chi tiết.</p>';
                    }
                });
            });
        });
    </script>
@endpush