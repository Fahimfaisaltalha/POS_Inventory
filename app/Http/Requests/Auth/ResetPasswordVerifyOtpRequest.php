<?php

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Rules\Auth\ResetPasswordOtpVerifyRule;

class ResetPasswordVerifyOtpRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'otp'=>[
                'required',
                'digits:6',
                new ResetPasswordOtpVerifyRule($this->email)
            ]
        ];
    }
        protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->all(),
            'data' => null
        ], 200));
    }
}
