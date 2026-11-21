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
            'message' => 'Validation failed',
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
            'url.required' => 'Eine URL ist erforderlich.',
            'url.url' => 'Bitte geben Sie eine gültige URL ein (http, https, ftp, ftps).',
            'url.max' => 'Die URL darf max. '.config('shortener.url.max_length', 1024).' Zeichen lang sein.',
            'hash.max' => 'Der Hash darf max. '.config('shortener.hash.max_length', 16).' Zeichen lang sein.',
            'hash.regex' => 'Der Hash darf nur Buchstaben, Zahlen, Bindestrich und Unterstrich enthalten.',
            'hash.unique' => 'Dieser Hash ist bereits vergeben.',
            'description.max' => 'Die Beschreibung darf max. '.config('shortener.description.max_length', 1024).' Zeichen lang sein.',
            'publish_date.date_format' => 'Das Publish-Datum muss das Format YYYY-MM-DD HH:MM haben.',
            'expiration_date.date_format' => 'Das Verfallsdatum muss das Format YYYY-MM-DD HH:MM:SS haben.',
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
                        'Das Verfallsdatum darf nicht vor dem Publish-Datum liegen.',
                    );
                }
            },
        ];
    }
}
