<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateShortenerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow owner to update
        return $this->user()?->id === $this->shortener?->UserID;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
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
            'description.max' => __('shortener.description_max'),
            'publish_date.date_format' => __('shortener.publish_format'),
            'expiration_date.date_format' => __('shortener.expiration_format'),
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (
                    $validator->errors()->hasAny(['publish_date', 'expiration_date'])
                ) {
                    return;
                }

                $shortener = $this->route('shortener');
                $publishDate = $this->has('publish_date')
                    ? $this->input('publish_date')
                    : $shortener?->PublishDate?->format('Y-m-d H:i');
                $expirationDate = $this->has('expiration_date')
                    ? $this->input('expiration_date')
                    : $shortener?->ExpirationDate?->format('Y-m-d H:i:s');

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
