<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreLivroUsuarioRequest;
use App\Models\Livro;
use App\Models\Usuario;
use Illuminate\Http\Request;

class LivroUsuarioController extends Controller
{
    public function index()
    {
        $livrosUsuarios = DB::table('livro_usuario')->get();

        return response()->json($livrosUsuarios);
    }

    public function show($livroId, $usuarioId)
    {
        $livroUsuario = DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$livroUsuario) {
            return response()->json(['message' => 'Relação livro-usuario não encontrada.'], 404);
        }

        return response()->json($livroUsuario);
    }

    public function alugarLivro(StoreLivroUsuarioRequest $request, $livroId, $usuarioId)
    {
        ['livro' => $livro, 'usuario' => $usuario] = $this->getLivroUsuario($livroId, $usuarioId);

        if ($usuario->livros()->where('livro_id', $livroId)->exists()) {
            return response()->json(['message' => 'Usuário já possui este livro.'], 422);
        }

        $request->validate($request->rules());

        $livroDisponivel = $this->verificarDisponibilidadeLivro($livroId, $request->dt_aluguel_ini, $request->dt_aluguel_fim);

        if (!$livroDisponivel) {
            return response()->json(['message' => 'Livro não disponível nesta data e hora.'], 422);
        }

        DB::beginTransaction();

        try {
            $livroUsuario = DB::table('livro_usuario')
                ->insertGetId([
                    'livro_id' => $livroId,
                    'usuario_id' => $usuarioId,
                    'dt_aluguel_ini' => $request->dt_aluguel_ini,
                    'dt_aluguel_fim' => $request->dt_aluguel_fim,
                    'dt_inclusao' => now(),
                    'dt_alteracao' => now(),
                ]);

            DB::commit();

            return response()->json(['message' => 'Livro alugado com sucesso.', 'livroUsuario' => $livroUsuario]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Erro ao alugar o livro.'], 500);
        }
    }

    public function devolverLivro(StoreLivroUsuarioRequest $request, $livroId, $usuarioId)
    {
        $livroUsuario = DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$livroUsuario) {
            return response()->json(['message' => 'Usuário não possui este livro.'], 404);
        }

        DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->delete();

        return response()->json(['message' => 'Livro devolvido com sucesso.']);
    }

    public function update(StoreLivroUsuarioRequest $request, $livroId, $usuarioId)
    {
        $livroUsuario = DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$livroUsuario) {
            return response()->json(['message' => 'Usuário não possui este livro.'], 404);
        }

        DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->update([
                'dt_aluguel_ini' => $request->dt_aluguel_ini,
                'dt_aluguel_fim' => $request->dt_aluguel_fim,
                'dt_alteracao' => now(),
            ]);

        return response()->json(['message' => 'Informações do livro atualizadas com sucesso.']);
    }

    public function delete($livroId, $usuarioId)
    {
        $livroUsuario = DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$livroUsuario) {
            return response()->json(['message' => 'Usuário não possui este livro.'], 404);
        }

        DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where('usuario_id', $usuarioId)
            ->delete();

        return response()->json(['message' => 'Registro removido com sucesso.']);
    }

    public function exportarLivrosUsuarios()
    {
        $livrosUsuarios = DB::table('livro_usuario')->get();

        $exportedData = '';

        foreach ($livrosUsuarios as $livroUsuario) {
            $exportedData .= "Livro ID: {$livroUsuario->livro_id}\n";
            $exportedData .= "Usuário ID: {$livroUsuario->usuario_id}\n";
            $exportedData .= "Data de Aluguel: {$livroUsuario->dt_aluguel_ini}\n";
            $exportedData .= "Data de Devolução: {$livroUsuario->dt_aluguel_fim}\n";
            $exportedData .= "Data de Inclusão: {$livroUsuario->dt_inclusao}\n";
            $exportedData .= "Data de Alteração: {$livroUsuario->dt_alteracao}\n\n";
        }

        $filePath = storage_path('exported_data.txt');
        \Illuminate\Support\Facades\File::put($filePath, $exportedData);

        return response()->json(['message' => 'Dados exportados com sucesso.', 'file_path' => $filePath]);
    }

    protected function getLivroUsuario($livroId, $usuarioId)
    {
        $livro = Livro::findOrFail($livroId);
        $usuario = Usuario::findOrFail($usuarioId);

        if (!$livro || !$usuario) {
            return response()->json(['message' => 'Livro ou usuário não encontrado.'], 404);
        }

        return compact('livro', 'usuario');
    }

    protected function verificarDisponibilidadeLivro($livroId, $dtAluguelIni, $dtAluguelFim)
    {
        $livroDisponivel = DB::table('livro_usuario')
            ->where('livro_id', $livroId)
            ->where(function ($query) use ($dtAluguelIni, $dtAluguelFim) {
                $query->where(function ($q) use ($dtAluguelIni, $dtAluguelFim) {
                    $q->where('dt_aluguel_ini', '>=', $dtAluguelIni)
                        ->where('dt_aluguel_ini', '<=', $dtAluguelFim);
                })
                ->orWhere(function ($q) use ($dtAluguelIni, $dtAluguelFim) {
                    $q->where('dt_aluguel_fim', '>=', $dtAluguelIni)
                        ->where('dt_aluguel_fim', '<=', $dtAluguelFim);
                })
                ->orWhere(function ($q) use ($dtAluguelIni, $dtAluguelFim) {
                    $q->where('dt_aluguel_ini', '<', $dtAluguelIni)
                        ->where('dt_aluguel_fim', '>', $dtAluguelFim);
                });
            })
            ->doesntExist();

        return $livroDisponivel;
    }
}