@extends('client.layouts.app')
@section('title', 'Đăng ký tham gia CLB - ' . $club->name)

@section('content')
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Đăng ký tham gia CLB: {{ $club->name }}
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($form->description)
                        <div class="alert alert-info">
                            <strong>Mô tả form:</strong> {{ $form->description }}
                        </div>
                    @endif

                    <form action="{{ route('club.member.join.submit', ['club_id' => $club->id]) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Lý do tham gia CLB</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Nhập lý do bạn muốn tham gia CLB này..."></textarea>
                        </div>

                        <hr>

                        <h5 class="mb-3">Câu hỏi tuyển thành viên</h5>

                        @foreach($questions as $index => $question)
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    {{ $index + 1 }}. {{ $question->question }}
                                    @if($question->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                
                                @if($question->description)
                                    <p class="text-muted small mb-2">{{ $question->description }}</p>
                                @endif

                                @switch($question->type)
                                    @case('short_text')
                                    @case('text')
                                        <input type="text" 
                                               name="answers[{{ $question->id }}]" 
                                               class="form-control"
                                               value="{{ old("answers.{$question->id}") }}"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('long_text')
                                    @case('textarea')
                                        <textarea name="answers[{{ $question->id }}]" 
                                                  class="form-control" 
                                                  rows="4"
                                                  {{ $question->is_required ? 'required' : '' }}>{{ old("answers.{$question->id}") }}</textarea>
                                        @break

                                    @case('number')
                                        <input type="number" 
                                               name="answers[{{ $question->id }}]" 
                                               class="form-control"
                                               value="{{ old("answers.{$question->id}") }}"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('email')
                                        <input type="email" 
                                               name="answers[{{ $question->id }}]" 
                                               class="form-control"
                                               value="{{ old("answers.{$question->id}") }}"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('phone')
                                        <input type="tel" 
                                               name="answers[{{ $question->id }}]" 
                                               class="form-control"
                                               value="{{ old("answers.{$question->id}") }}"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('date')
                                        <input type="date" 
                                               name="answers[{{ $question->id }}]" 
                                               class="form-control"
                                               value="{{ old("answers.{$question->id}") }}"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('select')
                                        <select name="answers[{{ $question->id }}]" 
                                                class="form-select" 
                                                data-select2="true"
                                                {{ $question->is_required ? 'required' : '' }}>
                                            <option value="">-- Chọn --</option>
                                            @foreach($question->options ?? [] as $option)
                                                <option value="{{ $option }}" {{ old("answers.{$question->id}") === $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('radio')
                                        <div>
                                            @foreach($question->options ?? [] as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="radio" 
                                                           name="answers[{{ $question->id }}]" 
                                                           id="q{{ $question->id }}_opt{{ $loop->index }}"
                                                           value="{{ $option }}"
                                                           {{ old("answers.{$question->id}") === $option ? 'checked' : '' }}
                                                           {{ $question->is_required ? 'required' : '' }}>
                                                    <label class="form-check-label" for="q{{ $question->id }}_opt{{ $loop->index }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('checkbox')
                                        <div>
                                            @foreach($question->options ?? [] as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="answers[{{ $question->id }}][]" 
                                                           id="q{{ $question->id }}_opt{{ $loop->index }}"
                                                           value="{{ $option }}"
                                                           {{ in_array($option, old("answers.{$question->id}", [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="q{{ $question->id }}_opt{{ $loop->index }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break
                                @endswitch
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('club.member.view', ['club_id' => $club->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Gửi yêu cầu tham gia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


