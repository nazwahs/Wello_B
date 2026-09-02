<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => "required|string|min:3|max:255",
                "email" => "required|email|string|min:3|max:255|unique:user",
                "password" => "required|string|min:3|max:255|confirmed",
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => "validasi gagal",
                    "errors" => $validator->errors()
                ], 422);
            }
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                "success" => true,
                "message" => "Register Berhasil",
                "data" => $user,
                "access_token" => $token,
            ], 201);
        } catch (Throwable $e) {
            return response()->json(["success" => false, "message" => "terjadi kesalahan sistem", "errors" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
