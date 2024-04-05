<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\UsuarioCreateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function create(UsuarioCreateRequest $request)
    {
        try {
            $query = User::create([
                'name' => $request->get('nome'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'erro' => false,
                'mensagem' => 'Usuário criado com sucesso.',
                'usuario' => $query
            ], 201);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', '=', $request->get('email'))->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Usuário autenticado com sucesso.',
                    'user' => $user,
                    'token' => $user->createToken($user->email)->plainTextToken
                ], 200);
            } else {
                return response()->json([ 
                    'erro' => true,
                    'mensagem' => 'Senha inválida',
                ], 401);
            }
        } else {
            return response()->json([
                'erro' => true,
                'mensagem' => 'Credenciais inválidas',
            ], 401);
        }
    }
}
