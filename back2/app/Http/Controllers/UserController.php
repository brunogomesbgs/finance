<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Js;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            if (Auth::user()->isAdmin()) {
                $users = User::all();
            } else {
                $users = Auth::user();
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json('Unable to list Users. Try later!', 400);
        }

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->document = $request['document'];
            $user->is_admin = $request['is_admin'] ?? 0;
            $user->save();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json('Unable to create an User. Try later!', 400);
        }

        return response()->json('User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $result = User::find($id);
        } catch (\Exception $e) {
            Log::error("User not found: ", ['errors' => $e]);
            return response()->json("User not found", 404);
        }

        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            User::find($id)->update($request->all());
        } catch (\Exception $e) {
            Log::error("Unable to update User: ", ['errors' => $e]);
            return response()->json("Unable to update User", 400);
        }

        return response()->json('User updated successfully');
    }

    public function updatePassword(string $id, Request $request): JsonResponse
    {
        try {
            User::find($id)->update([
                'password' => Hash::make($request['password'])
            ]);
        } catch (\Exception $e) {
            Log::error("Unable to change User´s password: ", ['errors' => $e]);
            return response()->json('Unable to change User´s password', 400);
        }

        return response()->json('User password changed successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            User::destroy($id);
        } catch (\Exception $e) {
            Log::error("Unable to delete User: ", ['errors' => $e]);
            return response()->json("Unable to delete User", 400);
        }

        return response()->json("User deleted successfully");
    }

    public function logIn(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'error' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json(['user' => $user, 'token' => $user->createToken($request->email)->plainTextToken]);
    }

    public function createAccount(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'document' => 'required|string|min:11|unique:users|max:14',
        ]);

        try {
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->document = $request['document'];
            $user->is_admin = 0;
            $user->save();
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json('Unable to create an User. Try later!', 400);
        }

        return response()->json('User created successfully!');
    }
}
