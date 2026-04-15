@extends('layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Licenses</h1>

    @if ($clientActive)
        <div class="mb-4 bg-white border rounded px-4 py-3 text-sm">
            <div class="flex items-center gap-3 mb-1">
                <span class="font-semibold">Client status:</span>
                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Active</span>
            </div>
            <div class="text-gray-600 text-xs">
                <span class="font-semibold">URL:</span> <span class="font-mono">{{ $redirectUrl }}</span>
            </div>
        </div>
    @else
        <div class="mb-4 bg-red-50 border border-red-200 rounded px-4 py-3 text-sm">
            <div class="flex items-center gap-3 mb-1">
                <span class="font-semibold text-red-800">Client status:</span>
                <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">Failed</span>
                <a href="" class="text-blue-600 underline">Retry</a>
            </div>
            <div class="text-red-700 text-xs">
                <div><span class="font-semibold">URL:</span> <span class="font-mono">{{ $redirectUrl ?: '(not set)' }}</span></div>
                <div><span class="font-semibold">Error:</span> {{ $clientError }}</div>
            </div>
        </div>
    @endif

    @if ($licenseKey)
        <div class="mb-4 flex items-center gap-3 bg-blue-50 border border-blue-200 rounded px-4 py-2 text-sm">
            <span>Filtered by license key <span class="font-mono">{{ $licenseKey }}</span></span>
            <a href="{{ route('dashboard') }}" class="text-blue-600 underline">Clear filter</a>
        </div>
    @endif

    @if ($licenses->isEmpty())
        <p class="text-gray-500">No licenses yet. <a href="{{ route('buy') }}" class="text-blue-600 underline">Buy one</a>.</p>
    @else
        <table class="w-full bg-white shadow rounded overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Buyer</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">License Key</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tier</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Seats</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($licenses as $license)
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold">{{ $license->buyer_name }}</td>
                        <td class="px-4 py-3 text-sm font-mono">{{ Str::limit($license->license_key, 12) }}</td>
                        <td class="px-4 py-3 text-sm">Tier {{ $license->tier }}</td>
                        <td class="px-4 py-3 text-sm">{{ $license->seats }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',
                                'bg-yellow-100 text-yellow-800' => $license->status === 'inactive',
                                'bg-green-100 text-green-800' => $license->status === 'active',
                                'bg-red-100 text-red-800' => $license->status === 'deactivated',
                            ])>{{ ucfirst($license->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $license->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-sm space-x-2">
                            @if ($license->status === 'inactive')
                                <form action="{{ route('licenses.activate', $license) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">Activate</button>
                                </form>
                            @endif

                            @if ($license->status === 'active')
                                <a href="{{ route('oauth.authorize', ['license_id' => $license->id]) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 inline-block">Start OAuth</a>

                                <form action="{{ route('licenses.change-tier', $license) }}" method="POST" class="inline">
                                    @csrf
                                    <select name="tier" class="border border-gray-300 rounded text-xs px-1 py-1">
                                        @foreach (range(1, 5) as $t)
                                            @if ($t !== $license->tier)
                                                <option value="{{ $t }}">Tier {{ $t }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-orange-500 text-white px-3 py-1 rounded text-xs hover:bg-orange-600">Change Tier</button>
                                </form>

                                <form action="{{ route('licenses.deactivate', $license) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Deactivate</button>
                                </form>
                            @endif

                            @if ($license->status === 'deactivated')
                                <form action="{{ route('licenses.activate', $license) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">Reactivate</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
