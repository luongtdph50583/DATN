{{-- Partial: form thêm/sửa club --}}
@php
    $club = $club ?? null;
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Tên CLB</label>
    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $club->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Mô tả</label>
    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $club->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="field" class="form-label">Lĩnh vực</label>
    <input id="field" name="field" type="text" class="form-control" value="{{ old('field', $club->field ?? '') }}">
</div>

<div class="mb-3">
    <label for="logo" class="form-label">Logo (file)</label>
    <input id="logo" name="logo" type="file" class="form-control">
    @if(!empty($club->logo))
        <div class="mt-2">
            <img src="{{ Storage::url($club->logo) }}" alt="logo" width="80">
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="status" class="form-label">Trạng thái</label>
    <select id="status" name="status" class="form-select">
        <option value="active" {{ old('status', $club->status ?? '') === 'active' ? 'selected' : '' }}>Hoạt động</option>
        <option value="pending" {{ old('status', $club->status ?? '') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
        <option value="inactive" {{ old('status', $club->status ?? '') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
    </select>
</div>
