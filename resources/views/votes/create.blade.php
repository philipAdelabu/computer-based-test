@extends('layouts.app')

@section('title', 'Cast Vote')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Cast Your Vote</h2>
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('vote.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="local_government" class="block text-sm font-medium text-gray-700 mb-2">
                        Local Government *
                    </label>
                    <select id="local_government" 
                            name="local_government_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">Select Local Government</option>
                        @foreach($localGovernments as $lg)
                            <option value="{{ $lg->id }}">{{ $lg->code }} - {{ $lg->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="ward" class="block text-sm font-medium text-gray-700 mb-2">
                        Ward *
                    </label>
                    <select id="ward" 
                            name="ward_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required disabled>
                        <option value="">Select Local Government first</option>
                    </select>
                </div>

                <div>
                    <label for="voting_unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Voting Unit *
                    </label>
                    <select id="voting_unit" 
                            name="voting_unit_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required disabled>
                        <option value="">Select Ward first</option>
                    </select>
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
                        <option value="Accord">Accord</option>
                        <option value="APC">APC</option>
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
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>


   <script>
document.addEventListener('DOMContentLoaded', function() {
    const lgSelect = document.getElementById('local_government');
    const wardSelect = document.getElementById('ward');
    const unitSelect = document.getElementById('voting_unit');

    const baseUrl = "{{ url('/') }}"; // Get the base URL of the application

    lgSelect.addEventListener('change', function() {
        const lgId = this.value;
        wardSelect.innerHTML = '<option value="">Loading...</option>';
        wardSelect.disabled = true;
        unitSelect.innerHTML = '<option value="">Select Ward first</option>';
        unitSelect.disabled = true;

        if (lgId) {
            // Use the API route with the full URL
            const url = `${baseUrl}/wards/${lgId}`;
            console.log('Fetching wards from:', url); // For debugging
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    wardSelect.innerHTML = '<option value="">Select Ward</option>';
                    if (data.length === 0) {
                        wardSelect.innerHTML = '<option value="">No wards found</option>';
                    } else {
                        data.forEach(ward => {
                            wardSelect.innerHTML += `<option value="${ward.id}">${ward.code} - ${ward.name}</option>`;
                        });
                    }
                    wardSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching wards:', error);
                    wardSelect.innerHTML = '<option value="">Error loading wards</option>';
                    wardSelect.disabled = false;
                });
        } else {
            wardSelect.innerHTML = '<option value="">Select Local Government first</option>';
        }
    });

    wardSelect.addEventListener('change', function() {
        const wardId = this.value;
        unitSelect.innerHTML = '<option value="">Loading...</option>';
        unitSelect.disabled = true;

        if (wardId) {
            const url = `${baseUrl}/voting-units/${wardId}`;
            console.log('Fetching voting units from:', url); // For debugging
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    unitSelect.innerHTML = '<option value="">Select Voting Unit</option>';
                    if (data.length === 0) {
                        unitSelect.innerHTML = '<option value="">No voting units found</option>';
                    } else {
                        data.forEach(unit => {
                            unitSelect.innerHTML += `<option value="${unit.id}">${unit.full_code} - ${unit.name}</option>`;
                        });
                    }
                    unitSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching voting units:', error);
                    unitSelect.innerHTML = '<option value="">Error loading voting units</option>';
                    unitSelect.disabled = false;
                });
        } else {
            unitSelect.innerHTML = '<option value="">Select Ward first</option>';
        }
    });
});
</script>

@endsection