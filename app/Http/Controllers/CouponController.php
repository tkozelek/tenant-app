<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        return view('coupons.index');
    }
}
