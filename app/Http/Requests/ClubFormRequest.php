<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ClubFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'field' => 'required|string|max:100',
            'status' => 'required|in:active,pending,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên CLB là bắt buộc.',
            'name.max' => 'Tên CLB không được vượt quá 255 ký tự.',
            'field.required' => 'Lĩnh vực là bắt buộc.',
            'field.max' => 'Lĩnh vực không được vượt quá 100 ký tự.',
            'logo.image' => 'Logo phải là hình ảnh.',
            'logo.mimes' => 'Logo phải có định dạng jpg, png hoặc jpeg.',
            'logo.max' => 'Logo không được vượt quá 2MB.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là "Hoạt động", "Chờ duyệt", hoặc "Không hoạt động".',
        ];
    }
}