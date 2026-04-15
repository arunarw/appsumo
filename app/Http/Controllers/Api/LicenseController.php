<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function show(Request $request, string $licenseKey): JsonResponse
    {
        $apiKey = $request->header('X-AppSumo-Licensing-Key');

        if (! $apiKey || ! hash_equals((string) config('appsumo.api_key'), $apiKey)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $license = License::where('license_key', $licenseKey)->first();

        if (! $license) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $dashboardUrl = route('dashboard', ['license_key' => $license->license_key]);

        return response()->json([
            'license_key' => $license->license_key,
            'license_redemption_url' => $dashboardUrl,
            'license_change_plan_url' => $dashboardUrl,
            'status' => $license->status,
            'tier' => $license->tier,
            'created_at' => $license->created_at?->toIso8601ZuluString(),
            'updated_at' => $license->updated_at?->toIso8601ZuluString(),
        ]);
    }
}
