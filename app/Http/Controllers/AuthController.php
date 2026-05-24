<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario con nombre, email y contraseña.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Autentica al usuario por email o nombre + contraseña y devuelve un token.
     *
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        $identifier = $request->input('identifier');
        $password   = $request->input('password');

        $user = User::where('email', $identifier)
            ->orWhere('name', $identifier)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $tokenResult = $user->createToken('personal-access-token');

        return response()->json([
            'message'      => 'Sesión iniciada correctamente.',
            'token_type'   => 'Bearer',
            'access_token' => $tokenResult->accessToken,
            'expires_in'   => $tokenResult->expiresIn,
        ]);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Cierra la sesión del usuario eliminando todos sus tokens.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente. Todos los tokens han sido eliminados.',
        ]);
    }
}
