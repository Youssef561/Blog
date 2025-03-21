<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {

        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);


//        $credentials = $request->only('email', 'password');

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        if (! $token = Auth()->attempt($validatedData->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //rateLimiter('login_attempts')->hit();

        return $this->respondWithToken($token);
    }



    public function register(Request $request): \Illuminate\Http\JsonResponse
    {

//        $validatedData = Validator::make($request->all(), [
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
//            'password' => 'required|string|min:8',
//        ]);

//        if ($validatedData->fails()) {
//            return response()->json($validatedData->errors(), 400);
//        }

        //$data = $validatedData->validated();


        try {

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);


            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // $token = Auth::attempt(['email' => $user->email, 'password' => $request->password]);         // we use this line to make the user login directly after register


            return response()->json([
                'message' => 'User registered successfully.',
                'user' => $user,
                // 'token' => $token,                                                   // this also needed for direct login after register
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                'message' => 'Validation failed',
                'error' => $e->errors(),
            ], 422);
        }

    }



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
