<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderDetail(Order $order){
        return $order;
    }

    public function orderRequirements(Request $request, Order $order){
        dd(json_encode( $request->all()));
    }
}
