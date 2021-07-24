<?php

namespace App\Http\Controllers;

use App\Models\BookStoreUsers;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
// use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $req = FacadesValidator::make($request->all(), [
            'fullName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:_book_store_users_',
            'mobile' => 'required|digits:10'
        ]);

        $req2 = FacadesValidator::make($request->all(), [
            'password' => 'required|min:3',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = new BookStoreUsers();
        $user->fullName = $request->input('fullName');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->mobile = $request->input('mobile');
        $email = $request->get('email');

        $userEmail = BookStoreUsers::where('email', $email)->first();
        if ($userEmail) {
            return response()->json(['status' => 409, 'message' => "This email already exists...."]);
        }

        if ($req->fails()) {
            return response()->json(['status' => 403, 'message' => "Please enter the valid details"]);
        }

        if ($req2->fails()) {
            return response()->json(['status' => 403, 'message' => "Password doesn't match"]);
        }
        $user->save();
        return response()->json(['status' => 201, 'message' => 'User succesfully registered!']);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|
            min:5',
        ]);

        $email = $request->get('email');
        $user = BookStoreUsers::where('email', $email)->first();

        if (!$user) {
            return response()->json(['status' => 400, 'message' => "Invalid credentials! email doesn't exists"]);
        }
        if (!$token = JWTAuth::fromUser($user)) {

            return response()->json(['status' => 401, 'message' => 'Unauthenticated']);
        }

        return $this->generateToken($token);
    }

    /**
     * generates token 
     * @creates a streamed response
     */
    protected function generateToken($token)
    {
        return response()->json([
            'status' => 200,
            'message' => 'succesfully logged in',
            'token' => $token
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $user = BookStoreUsers::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => 401, 'message' => "we can't find a user with that email address."]);
        }
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => JWTAuth::fromUser($user)
            ]
        );
        if ($user && $passwordReset) {
            $user->notify(new ResetPasswordNotification($passwordReset->token));
        }
        return response()->json(['status' => 200, 'message' => 'we have emailed your password reset link to respective mail']);
    }
}
