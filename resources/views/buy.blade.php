@extends('layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Buy a License</h1>

    <div class="mb-6">
        <label for="buyer_name" class="block text-sm font-semibold text-gray-700 mb-1">Your Name</label>
        <input type="text" id="buyer_name" class="border border-gray-300 rounded px-3 py-2 w-full max-w-xs" placeholder="e.g. Aruna" value="{{ old('buyer_name') }}" required>
        @error('buyer_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach ($tiers as $number => $tier)
            <div class="bg-white shadow rounded p-6 text-center">
                <h2 class="text-lg font-bold mb-2">{{ $tier['name'] }}</h2>
                <p class="text-3xl font-bold text-blue-600 mb-2">{{ $tier['seats'] }}</p>
                <p class="text-gray-500 text-sm mb-1">seats</p>
                <p class="text-sm font-semibold mb-4">
                    <span @class([
                        'px-2 py-1 rounded text-xs',
                        'bg-blue-100 text-blue-800' => $tier['plan'] === 'Starter',
                        'bg-purple-100 text-purple-800' => $tier['plan'] === 'Essential',
                    ])>{{ $tier['plan'] }}</span>
                </p>
                <form action="{{ route('buy.store') }}" method="POST" onsubmit="this.querySelector('[name=buyer_name]').value = document.getElementById('buyer_name').value">
                    @csrf
                    <input type="hidden" name="buyer_name" value="">
                    <input type="hidden" name="tier" value="{{ $number }}">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">
                        Buy
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endsection
