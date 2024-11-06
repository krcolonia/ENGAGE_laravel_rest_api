<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'fullname' => 'required|string|max:255',
			'email' => ['required', 'email', Rule::unique('users', 'email')],
			'password' => 'required|string|min:8',
		]);

		if ($validator->fails()) {
			return response()->json([
				'message' => 'All fields are mandatory',
				'error' => $validator->errors(),
			], 422);
		}

		$user = User::create([
			'fullname' => $request->fullname,
			'email' => $request->email,
			'password' => Hash::make($request->password),
		]);

		$token = $user->createToken('auth_token')->plainTextToken;

		return response()->json([
			'data' => $user,
			'access_token' => $token,
			'token_type' => 'Bearer',
		], 200);
	}

	public function login(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => ['required', 'email'],
			'password' => 'required|string|min:8',
		]);

		if ($validator->fails()) {
			return response()->json([
				'message' => 'All fields are mandatory',
				'error' => $validator->errors(),
			], 422);
		}

		$credentials = $request->only('email', 'password');

		if(!Auth::attempt($credentials)) {
			return response()->json([
				'message' => 'User not found',
			], 401);
		}

		$user = User::where('email', $request->email)->firstOrFail();
		$token = $user->createToken('auth_token')->plainTextToken;

		return response()->json([
			'message' => 'Login Success',
			'access_token' => $token,
			'token_type' => 'Bearer',
		], 200);
	}

	public function logout() 
	{
		Auth::user()->tokens()->delete();

		return response()->json([
			'message' => 'Logout successful',
		], 200);
	}
}
