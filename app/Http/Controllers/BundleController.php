<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        return view('bundles.index');
    }
}
