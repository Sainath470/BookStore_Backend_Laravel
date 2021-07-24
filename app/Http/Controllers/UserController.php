<?php

namespace App\Http\Controllers;

use App\Models\BookStoreUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

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
}
