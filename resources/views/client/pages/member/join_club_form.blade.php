@extends('client.layouts.app')

@section('title', 'Đăng ký tham gia CLB: ' . $club->name)

@section('content')
<div class="container py-4">
    <h3>Đăng ký tham gia CLB: {{ $club->name }}</h3>
    <p>{{ $club->description }}</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('clubs.join.submit', $club->id) }}" method="POST">
        @csrf

        @foreach($questions as $question)
            <div class="mb-3">
                <label class="form-label fw-bold">
                    {{ $question->question }}
                    @if($question->is_required) <span class="text-danger">*</span> @endif
                </label>
                @if($question->description)
                    <p class="small text-muted">{{ $question->description }}</p>
                @endif

                @php
                    $fieldName = "questions[{$question->id}]";
                    $oldValue = old("questions.{$question->id}");
                    // Nếu options là JSON string mới decode, nếu là mảng thì giữ nguyên
                    $options = is_string($question->options) ? json_decode($question->options, true) : ($question->options ?? []);
                @endphp

                @switch($question->type)
                    @case('text')
                    @case('email')
                    @case('number')
                    @case('phone')
                    @case('date')
                        <input type="{{ $question->type }}" 
                               name="{{ $fieldName }}" 
                               class="form-control @error('questions.'.$question->id) is-invalid @enderror"
                               value="{{ old("questions.{$question->id}", '') }}">
                        @error('questions.'.$question->id)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        @break

                    @case('textarea')
                        <textarea name="{{ $fieldName }}" 
                                  class="form-control @error('questions.'.$question->id) is-invalid @enderror"
                                  rows="3">{{ old("questions.{$question->id}", '') }}</textarea>
                        @error('questions.'.$question->id)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        @break

                    @case('select')
                        <select name="{{ $fieldName }}" 
                                class="form-select @error('questions.'.$question->id) is-invalid @enderror">
                            <option value="">-- Chọn --</option>
                            @foreach($options as $option)
                                <option value="{{ $option }}" {{ $oldValue == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        @error('questions.'.$question->id)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        @break

                    @case('radio')
                        @foreach($options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       name="{{ $fieldName }}" 
                                       value="{{ $option }}" 
                                       {{ $oldValue == $option ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $option }}</label>
                            </div>
                        @endforeach
                        @error('questions.'.$question->id)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        @break

                    @case('checkbox')
                        @php
                            $oldValueArray = is_array($oldValue) ? $oldValue : [];
                        @endphp
                        @foreach($options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="{{ $fieldName }}[]" 
                                       value="{{ $option }}"
                                       {{ in_array($option, $oldValueArray) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $option }}</label>
                            </div>
                        @endforeach
                        @error('questions.'.$question->id)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        @break
                @endswitch
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
</div>
@endsection
