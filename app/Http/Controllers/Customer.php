<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Orders;
use App\Models\User;
use App\Notifications\orderSuccessfullyPlacedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Customer extends Controller
{
    public function customerRegister(Request $request)
    {
        $customer = new Customers();
        $customer->name = $request->input('name');
        $customer->phoneNumber = $request->input('phoneNumber');
        $customer->pincode = $request->input('pincode');
        $customer->locality = $request->input('locality');
        $customer->city = $request->input('city');
        $customer->address = $request->input('address');
        $customer->landmark = $request->input('landmark');
        $customer->type = $request->input('type');
        $customer->user_id = auth()->id();
        $customer->save();
        return response()->json(['status' => 201, 'message' => 'customer registered successfully']);
    }

    public function orderPlacedSuccessfull(Request $request)
    {
        $user = new Customers();
        $user->user_id = auth()->id();
        $user_id = Customers::where('user_id', $user->user_id)->value('user_id');

        $user_email = User::where('id', $user_id)->value('email');

        $order = User::where('email', $user_email)->first();
        $ord = Orders::create(
            [
                'orderNumber' => $order->orderNumber = Str::random(12),
                'customer_id' => $order->id,
                'order_date' => $order->order_date = Carbon::now(),
            ]
        );
        if ($order && $ord) {
            $order->notify(new orderSuccessfullyPlacedNotification($ord->orderNumber));
        }
        return response()->json(['message' => 'order created successfully']);
    }
}
