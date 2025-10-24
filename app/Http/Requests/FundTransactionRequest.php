<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FundTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'club_id' => 'required|exists:clubs,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'category' => 'nullable|string|max:255',
            'status' => 'sometimes|in:pending,approved,rejected',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'club_id.required' => 'Vui lòng chọn câu lạc bộ.',
            'club_id.exists' => 'Câu lạc bộ không tồn tại.',
            'type.required' => 'Vui lòng chọn loại giao dịch.',
            'type.in' => 'Loại giao dịch không hợp lệ.',
            'amount.required' => 'Vui lòng nhập số tiền.',
            'amount.numeric' => 'Số tiền phải là số.',
            'amount.min' => 'Số tiền phải lớn hơn 0.',
            'description.required' => 'Vui lòng nhập mô tả giao dịch.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'category.max' => 'Danh mục không được vượt quá 255 ký tự.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
