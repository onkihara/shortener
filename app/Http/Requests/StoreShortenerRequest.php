<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreShortenerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow both authenticated and unauthenticated users
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, response()->json([
            'success' => false,
            'message' => __('shortener.validation_failed'),
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'url:http,https,ftp,ftps',
                'max:'.config('shortener.url.max_length', 1024),
            ],
            'hash' => [
                'nullable',
                'max:'.config('shortener.hash.max_length', 16),
                'regex:/^'.config('shortener.hash.allowed').'+$/',
                'unique:shortcuts,Hash',
            ],
            'description' => [
                'nullable',
                'string',
                'max:'.config('shortener.description.max_length', 1024),
            ],
            'publish_date' => [
                'nullable',
                'date_format:Y-m-d H:i',
            ],
            'expiration_date' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'url.required' => __('shortener.url_required'),
            'url.url' => __('shortener.url_invalid'),
            'url.max' => __('shortener.url_max'),
            'hash.max' => __('shortener.hash_max'),
            'hash.regex' => __('shortener.hash_invalid'),
            'hash.unique' => __('shortener.hash_taken'),
            'description.max' => __('shortener.description_max'),
            'publish_date.date_format' => __('shortener.publish_format'),
            'expiration_date.date_format' => __('shortener.expiration_format'),
        ];
    }

    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator): void {
                if ($validator->errors()->hasAny(['publish_date', 'expiration_date'])) {
                    return;
                }

                $publishDate = $this->input('publish_date');
                $expirationDate = $this->input('expiration_date');

                if (! $publishDate || ! $expirationDate) {
                    return;
                }

                $publish = Carbon::createFromFormat('Y-m-d H:i', $publishDate);
                $expiration = Carbon::createFromFormat('Y-m-d H:i:s', $expirationDate);

                if ($expiration->lessThan($publish)) {
                    $validator->errors()->add(
                        'expiration_date',
                        __('shortener.date_validation'),
                    );
                }
            },
        ];
    }
}
