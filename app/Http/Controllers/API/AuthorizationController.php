<?php

namespace App\Http\Controllers\API;

use App\DataTransferObjects\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthorizationController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(protected UserRepository $userRepository)
    {
        //
    }

    public function register(RegisterRequest $request) : JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $user = $this->userRepository->create(
                new UserData(
                    name: $validated['name'],
                    email: $validated['email'],
                    password: $validated['password'],
                )
            );
            $user->assignRole(config('permission.user_roles.member'));
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->respondWithSuccess([
                'token' => $token,
            ]);
        });
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $user = $this->userRepository->getUserByEmail($validated['email']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->respondUnAuthenticated(__("The provided credentials are incorrect."));
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->respondWithSuccess(['message' => 'Logged out successfully']);
    }
}
