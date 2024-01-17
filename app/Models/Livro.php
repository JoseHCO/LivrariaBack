<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livro extends Model
{
    use HasFactory;

    protected $table = 'livros';
    const CREATED_AT = 'dt_inclusao';
    const UPDATED_AT = 'dt_alteracao';

    protected $fillable = [
        'nome_livro',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class)
            ->withPivot(['dt_aluguel_ini', 'dt_aluguel_fim'])
            ->withTimestamps();
    }

    public static function criarLivro($dados)
    {
        return self::create($dados);
    }

    public static function obterLivros()
    {
        return self::all();
    }

    public static function obterLivroId($livroId)
    {
        return self::find($livroId);
    }

    public static function atualizarLivro($livroId, $dados)
    {
        $livro = self::find($livroId);
        if ($livro) {
            $livro->update($dados);
            return $livro;
        }
        return null;
    }

    public static function excluirLivro($livroId)
    {
        $livro = self::find($livroId);
        if ($livro) {
            $livro->delete();
            return true;
        }
        return false;
    }
}