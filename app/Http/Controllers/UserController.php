<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Notifications\ResetPasswordNotification;
use CreateBooksTable;
use CreateUsersTable;
use Exception;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTAuth as JWTAuthJWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $req = FacadesValidator::make($request->all(), [
            'fullName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:user',
            'password' => 'required|min:3',
            'mobile' => 'required|digits:10'
        ]);

        $user = new User();
        $user->fullName = $request->input('fullName');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->mobile = $request->input('mobile');
        $email = $request->get('email');

        $userEmail = User::where('email', $email)->first();
        if ($userEmail) {
            return response()->json(['status' => 409, 'message' => "This email already exists...."]);
        }

        if ($req->fails()) {
            return response()->json(['status' => 403, 'message' => "Please enter the valid details"]);
        }

        $user->save();
        return response()->json(['status' => 201, 'message' => 'User succesfully registered!']);
    }

    public function login(Request $request)
    {
        $req = FacadesValidator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|
            min:5',
            ]
        );

        $email = $request->get('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['status' => 400, 'message' => "Invalid credentials! email doesn't exists"]);
        }
        if (!$token = JWTAuth::attempt($req->validated())) {
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
            'status' => 201,
            'message' => 'succesfully logged in',
            'token' => $token
        ]);
    }

    public function signout()
    {
        try {
            auth()->logout();
        } catch (Exception $e) {

            return response()->json(['status' => 201, 'message' => 'Token is invalid'], 201);
        }

        return response()->json(['status' => 200, 'message' => 'User logged out'], 200);
    }

    /**
     * refresh the token
     * @param refresh() refresh an instance on the given target and method
     */
    public function refresh()
    {
        return $this->generateToken(auth()->refresh());
    }

    /**
     * User function gets the user from the database
     */
    public function user()
    {
        return response()->json(auth()->user());
    }


    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
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
        return response()->json(['status' => 201, 'message' => 'we have emailed your password reset link to respective mail']);
    }

    public function resetPassword(Request $request)
    {
        $validate = FacadesValidator::make($request->all(), [
            'new_password' => 'min:6|required|',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 201, 'message' => "Password doesn't match"]);
        }
        $passwordReset = PasswordReset::where([
            ['token', $request->token]
        ])->first();

        if (!$passwordReset) {
            return response()->json(['status' => 401, 'message' => 'This token is invalid']);
        }
        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json(['status' => 201, 'message' => "we can't find the user with that e-mail address"], 201);
        } else {
            $user->password = bcrypt($request->new_password);
            $user->save();
            $passwordReset->delete();
            return response()->json(['status' => 201, 'message' => 'Password reset successfull!']);
        }
    }
}
