<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ValidacaoDataAluguel implements Rule
{
    protected $livroId;
    protected $dtAluguelIni;

    /**
     * Create a new rule instance.
     *
     * @param int $livroId
     * @param string $dtAluguelIni
     */
    public function __construct($livroId, $dtAluguelIni)
    {
        $this->livroId = $livroId;
        $this->dtAluguelIni = $dtAluguelIni;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $dtAluguelIni = $this->dtAluguelIni;
        $livroId = $this->livroId;

        $result = DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('dt_aluguel_ini', '<=', $dtAluguelIni)
            ->where('dt_aluguel_fim', '>=', $dtAluguelIni)
            ->exists();

        return !$result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O livro já está alocado na data e hora especificadas.';
    }
}