<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Http\Requests\StoreUsuarioRequest;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json(['usuarios' => $usuarios]);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json(['usuario' => $usuario]);
    }

    public function create(StoreUsuarioRequest $request)
    {
        $usuario = Usuario::create($request->validated());
        return response()->json(['usuario' => $usuario], 201);
    }

    public function update(StoreUsuarioRequest $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $usuario->update($request->validated());
        return response()->json(['usuario' => $usuario]);
    }

    public function delete($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $usuario->delete();
        
        return response()->json(['message' => 'Usuário excluído com sucesso']);
    }
}
