<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Http\Requests\StoreLivroRequest;

class LivroController extends Controller
{
    public function index()
    {
        $livros = Livro::all();
        return response()->json(['livros' => $livros]);
    }

    public function show($id)
    {
        $livro = Livro::find($id);

        if (!$livro) {
            return response()->json(['message' => 'Livro não encontrado.'], 404);
        }

        return response()->json(['livro' => $livro]);
    }

    public function create(StoreLivroRequest $request)
    {
        $livro = Livro::create($request->validated());
        return response()->json(['livro' => $livro], 201);
    }

    public function update(StoreLivroRequest $request, $id)
    {
        $livro = Livro::find($id);

        if (!$livro) {
            return response()->json(['message' => 'Livro não encontrado.'], 404);
        }

        $livro->update($request->validated());

        return response()->json(['livro' => $livro]);
    }

    public function delete($id)
    {
        $livro = Livro::find($id);

        if (!$livro) {
            return response()->json(['message' => 'Livro não encontrado.'], 404);
        }

        $livro->delete();

        return response()->json(['message' => 'Livro excluído com sucesso.']);
    }
}