@extends('layouts.app')

@section('title', 'Cast Vote')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Cast Your Vote</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('vote.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="voting_unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Voting Unit *
                    </label>
                    <input type="text" 
                           id="voting_unit" 
                           name="voting_unit" 
                           value="{{ old('voting_unit') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter voting unit name"
                           required>
                    @error('voting_unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="party" class="block text-sm font-medium text-gray-700 mb-2">
                        Party *
                    </label>
                    <select id="party" 
                            name="party" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">Select a party</option>
                        <option value="Accord" {{ old('party') == 'Accord' ? 'selected' : '' }}>Accord</option>
                        <option value="APC" {{ old('party') == 'APC' ? 'selected' : '' }}>APC</option>
                    </select>
                    @error('party')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="score" class="block text-sm font-medium text-gray-700 mb-2">
                        Voting Score *
                    </label>
                    <input type="number" 
                           id="score" 
                           name="score" 
                           value="{{ old('score') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter voting score"
                           min="0"
                           required>
                    @error('score')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                        Submit Vote
                    </button>
                    <a href="{{ route('landing') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection