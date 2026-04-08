<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\OauthCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    public function authorize(Request $request)
    {
        $request->validate([
            'license_id' => 'required|exists:licenses,id',
        ]);

        $license = License::findOrFail($request->input('license_id'));

        $oauthCode = OauthCode::create([
            'code' => Str::random(40),
            'license_id' => $license->id,
        ]);

        $redirectUrl = config('appsumo.oauth_redirect_url');

        return redirect("{$redirectUrl}?code={$oauthCode->code}");
    }

    public function token(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required',
            'client_secret' => 'required',
            'code' => 'required',
            'grant_type' => 'required|in:authorization_code',
            'redirect_uri' => 'required',
        ]);

        if ($request->input('client_id') !== config('appsumo.client_id')
            || $request->input('client_secret') !== config('appsumo.client_secret')) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        $oauthCode = OauthCode::where('code', $request->input('code'))
            ->where('used', false)
            ->first();

        if (! $oauthCode) {
            return response()->json(['error' => 'invalid_grant'], 400);
        }

        $accessToken = Str::random(64);
        $oauthCode->update(['used' => true, 'access_token' => $accessToken]);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => 3600,
        ]);
    }

    public function licenseKey(Request $request): JsonResponse
    {
        $accessToken = $request->query('access_token');

        if (! $accessToken) {
            return response()->json(['error' => 'missing_token'], 401);
        }

        $oauthCode = OauthCode::where('access_token', $accessToken)->first();

        if (! $oauthCode) {
            return response()->json(['error' => 'invalid_token'], 401);
        }

        $license = $oauthCode->license;

        return response()->json([
            'license_key' => $license->license_key,
            'status' => $license->status,
            'scopes' => ['read_license'],
        ]);
    }
}
