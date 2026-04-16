<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $licenseKey = $request->query('license_key');

        $licenses = License::query()
            ->with('replacement')
            ->when($licenseKey, fn ($q) => $q->where('license_key', $licenseKey))
            ->latest()
            ->get();

        $clientActive = false;
        $clientError = null;
        $redirectUrl = config('appsumo.oauth_redirect_url');

        if (! $redirectUrl) {
            $clientError = 'CLIENT_OAUTH_REDIRECT_URL is not configured.';
        } else {
            try {
                $status = Http::timeout(3)->get($redirectUrl)->status();
                $clientActive = $status === 200;
                if (! $clientActive) {
                    $clientError = "Client responded with HTTP {$status}.";
                }
            } catch (ConnectionException $e) {
                $clientError = 'Could not reach client: '.$e->getMessage();
            }
        }

        return view('dashboard', compact('licenses', 'licenseKey', 'clientActive', 'clientError', 'redirectUrl'));
    }
}
