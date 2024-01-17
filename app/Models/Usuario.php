<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    const CREATED_AT = 'dt_inclusao';
    const UPDATED_AT = 'dt_alteracao';

    protected $fillable = [
        'nome',
        'cpf',
    ];

    public function livros()
    {
        return $this->belongsToMany(Livro::class)
            ->withPivot(['dt_aluguel_ini', 'dt_aluguel_fim'])
            ->withTimestamps();
    }

    public static function criarUsuario($dados)
    {
        return self::create($dados);
    }

    public static function obterUsuarios()
    {
        return self::all();
    }

    public static function obterUsuarioId($usuarioId)
    {
        return self::find($usuarioId);
    }

    public static function atualizarUsuario($usuarioId, $dados)
    {
        $usuario = self::find($usuarioId);
        if ($usuario) {
            $usuario->update($dados);
            return $usuario;
        }
        return null;
    }

    public static function excluirUsuario($usuarioId)
    {
        $usuario = self::find($usuarioId);
        if ($usuario) {
            $usuario->delete();
            return true;
        }
        return false;
    }
}