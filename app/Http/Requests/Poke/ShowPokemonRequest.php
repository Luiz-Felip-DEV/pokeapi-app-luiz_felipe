<?php

namespace App\Http\Requests\Poke;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ShowPokemonRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'source.string' => 'A fonte deve ser uma string.',
        ];
    }
}
