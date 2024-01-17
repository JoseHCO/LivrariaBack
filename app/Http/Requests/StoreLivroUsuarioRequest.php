<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLivroUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void 
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', $this->cpf)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $livroId = $this->route('livroId');
        $usuarioId = $this->route('usuarioId');

        return [
            'dt_aluguel_ini' => [
                'required',
                'date',
                'after_or_equal:now',
                Rule::unique('livro_usuario')->where(function ($query) {
                    return $query->where('livro_id', $this->livro_id)
                                 ->where('dt_aluguel_ini', '<=', $this->dt_aluguel_ini)
                                 ->where('dt_aluguel_fim', '>=', $this->dt_aluguel_ini);
                }),
            ],
            'dt_aluguel_fim' => 'required|date|after:dt_aluguel_ini',
        ];
    }

    /**
     * Format date to 'Y-m-d' for database comparison.
     *
     * @param string $date
     * @return string
     */
    private function formatDate(string $date): string
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }
}