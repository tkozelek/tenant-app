<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        $bundles = Bundle::query()
            ->where('is_active', true)
            ->with(['tenant', 'items.variant.activePriceHistory'])
            ->latest()
            ->paginate(12);

        return view('bundles.index', compact('bundles'));
    }
}
