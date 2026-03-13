<?php

namespace App\Http\Requests\Poke;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchPokemonRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome do Pokémon deve ser uma string.',
            'name.max' => 'O nome do Pokémon não pode exceder 255 caracteres.',
            'page.integer' => 'A página deve ser um número inteiro.',
            'page.min' => 'A página deve ser pelo menos 1.',
        ];
    }
}
