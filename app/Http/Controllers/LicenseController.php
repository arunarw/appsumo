<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Services\WebhookSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    public function __construct(private WebhookSender $webhookSender) {}

    public function buy()
    {
        $tiers = config('appsumo.tiers');

        return view('buy', compact('tiers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'buyer_name' => 'required|string|max:255',
            'tier' => 'required|integer|between:1,5',
        ]);

        $tier = (int) $request->input('tier');
        $tierConfig = config("appsumo.tiers.{$tier}");

        $license = License::create([
            'buyer_name' => $request->input('buyer_name'),
            'license_key' => (string) Str::uuid(),
            'tier' => $tier,
            'seats' => $tierConfig['seats'],
            'status' => 'inactive',
        ]);

        $this->webhookSender->send($license, 'purchase');

        return redirect()->route('dashboard')->with('success', "Purchased {$tierConfig['name']} license.");
    }

    public function activate(License $license): RedirectResponse
    {
        abort_if($license->replaced_by, 403, 'Cannot reactivate a license that was replaced by a tier change.');

        $license->update(['status' => 'active']);

        $this->webhookSender->send($license, 'activate');

        return redirect()->route('dashboard')->with('success', 'License activated. Click "Start OAuth" to link account.');
    }

    public function deactivate(License $license): RedirectResponse
    {
        $license->update(['status' => 'deactivated']);

        $this->webhookSender->send($license, 'deactivate');

        return redirect()->route('dashboard')->with('success', 'License deactivated.');
    }

    public function changeTier(Request $request, License $license): RedirectResponse
    {
        $request->validate([
            'tier' => 'required|integer|between:1,5',
        ]);

        $newTier = (int) $request->input('tier');
        $tierConfig = config("appsumo.tiers.{$newTier}");
        $event = $newTier > $license->tier ? 'upgrade' : 'downgrade';

        $newLicense = License::create([
            'buyer_name' => $license->buyer_name,
            'license_key' => (string) Str::uuid(),
            'tier' => $newTier,
            'seats' => $tierConfig['seats'],
            'status' => 'inactive',
        ]);

        if ($this->webhookSender->send($newLicense, $event, $license)) {
            $newLicense->update(['status' => 'active']);
        }

        $license->update(['status' => 'deactivated', 'replaced_by' => $newLicense->id]);
        $this->webhookSender->send($license, 'deactivate');

        return redirect()->route('dashboard')->with('success', ucfirst($event) . " from Tier {$license->tier} to Tier {$newTier}.");
    }
}
