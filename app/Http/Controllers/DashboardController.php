<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $licenseKey = $request->query('license_key');

        $licenses = License::query()
            ->when($licenseKey, fn ($q) => $q->where('license_key', $licenseKey))
            ->latest()
            ->get();

        return view('dashboard', compact('licenses', 'licenseKey'));
    }
}
