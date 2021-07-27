<?php

namespace App\Http\Controllers;

use App\Models\Books;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function addBook(Request $request)
    {
        $book = new Books();
        $user = new User();

        $user->id = auth()->id();

        $userInDB = User::where('id', $user->id)->value('id');

        $book->name = $request->input('name');
        $book->author = $request->input('author');
        $book->user_id = $userInDB;
        $book->price = $request->input('price');
        $book->quantity = $request->input('quantity');
        $book->description = $request->input('description');
        $book->file = $request->input('file');

        $book->save();
        return response()->json(['status' => 201, 'message' => "Successfully added"]);
    }

    public function displayBooks()
    {
        try {
            $user = new Books();
            $user->user_id = auth()->id();
            return DB::table('books')->where('user_id', $user->user_id)->get();
        } catch (Exception $e) {
            return response()->json(['status' => 401, 'message' => 'invalid token']);
        }
    }

    public function addToCart(Request $request)
    {
        $book = new Books();
        $book->user_id = auth()->id();
        $book->id = $request->input('id');

        $userInDB = User::where('id', $book->user_id)->value('id');

        if ($userInDB == auth()->id()) {
            $book = Books::where('id', $book->id)->update(array('value' => '1',));
            return response()->json(['status' => 201, 'message' => 'Book added to cart successfully']);
        }
    }

    public function displayBooksInCart()
    {
        $user = new User();
        $user->user_id = auth()->id();

        $userInDB = User::where('id', $user->user_id)->value('id');

        if ($user->user_id == $userInDB) {
            return Books::select('id', 'name', 'author', 'file', 'price', 'value')
                ->where([
                    ['user_id', '=', $user->user_id],
                    ['value', '=', '1']
                ])->get();
        }
        return response()->json(['status' => 403, 'message' => 'Invalid token']);
    }

    public function removeFromCart(Request $request)
    {
        $book = new Books();
        $book->user_id = auth()->id();
        $book->id = $request->input('id');

        $userInDB = User::where('id', $book->user_id)->value('id');

        if ($userInDB == auth()->id()) {
            $book = Books::where('id', $book->id)->update(array('value' => '0',));
            return response()->json(['status' => 201, 'message' => 'removed from cart']);
        }
    }
}
