<?php

use App\Http\Controllers\Api\LicenseController as ApiLicenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/buy', [LicenseController::class, 'buy'])->name('buy');
Route::post('/buy', [LicenseController::class, 'store'])->name('buy.store');
Route::post('/licenses/{license}/activate', [LicenseController::class, 'activate'])->name('licenses.activate');
Route::post('/licenses/{license}/deactivate', [LicenseController::class, 'deactivate'])->name('licenses.deactivate');
Route::post('/licenses/{license}/change-tier', [LicenseController::class, 'changeTier'])->name('licenses.change-tier');

Route::get('/oauth/authorize', [OAuthController::class, 'authorize'])->name('oauth.authorize');
Route::post('/openid/token/', [OAuthController::class, 'token'])->name('oauth.token');
Route::get('/openid/license_key/', [OAuthController::class, 'licenseKey'])->name('oauth.license-key');

Route::get('/v2/licenses/{licenseKey}', [ApiLicenseController::class, 'show'])->name('api.licenses.show');
