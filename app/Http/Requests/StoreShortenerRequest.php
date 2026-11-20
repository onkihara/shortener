<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'after_or_equal:now',
            ],
            'expiration_date' => [
                'nullable',
                'date_format:Y-m-d H:i',
                'after:publish_date',
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
            'publish_date.after_or_equal' => 'Das Publish-Datum muss in der Zukunft oder jetzt sein.',
            'expiration_date.date_format' => 'Das Verfallsdatum muss das Format YYYY-MM-DD HH:MM haben.',
            'expiration_date.after' => 'Das Verfallsdatum muss nach dem Publish-Datum liegen.',
        ];
    }
}
