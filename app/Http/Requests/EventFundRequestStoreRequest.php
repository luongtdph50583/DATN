
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventFundRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cho phép tất cả admin tạo yêu cầu
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'amount_requested' => 'required|numeric|min:1000',
            'source_id' => 'required|exists:fund_sources,id',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'Vui lòng chọn sự kiện.',
            'event_id.exists' => 'Sự kiện không hợp lệ.',
            'amount_requested.required' => 'Vui lòng nhập số tiền yêu cầu.',
            'amount_requested.numeric' => 'Số tiền phải là số.',
            'amount_requested.min' => 'Số tiền phải lớn hơn 1.000 VNĐ.',
            'source_id.required' => 'Vui lòng chọn nguồn quỹ.',
            'source_id.exists' => 'Nguồn quỹ không hợp lệ.',
        ];
    }
}
