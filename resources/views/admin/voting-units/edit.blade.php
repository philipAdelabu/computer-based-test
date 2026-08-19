@extends('layouts.app')

@section('title', 'Edit Voting Unit')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Voting Unit</h2>

            <form action="{{ route('admin.voting-units.update', $votingUnit) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="local_government_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Local Government *
                    </label>
                    <select id="local_government_id" 
                            name="local_government_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">Select Local Government</option>
                        @foreach($localGovernments as $lg)
                            <option value="{{ $lg->id }}" {{ $votingUnit->ward->local_government_id == $lg->id ? 'selected' : '' }}>
                                {{ $lg->code }} - {{ $lg->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('local_government_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Ward *
                    </label>
                    <select id="ward_id" 
                            name="ward_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">Select Ward</option>
                        @foreach($votingUnit->ward->localGovernment->wards as $ward)
                            <option value="{{ $ward->id }}" {{ $votingUnit->ward_id == $ward->id ? 'selected' : '' }}>
                                {{ $ward->code }} - {{ $ward->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('ward_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Code (2 digits) *
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code', $votingUnit->code) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., 01"
                           maxlength="2"
                           pattern="[0-9]{2}"
                           required>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Name *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $votingUnit->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter voting unit name"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        Update Voting Unit
                    </button>
                    <a href="{{ route('admin.voting-units.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg text-center transition duration-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lgSelect = document.getElementById('local_government_id');
    const wardSelect = document.getElementById('ward_id');

    // Store current ward id
    const currentWardId = wardSelect.value;

    lgSelect.addEventListener('change', function() {
        const lgId = this.value;
        wardSelect.innerHTML = '<option value="">Loading...</option>';
        wardSelect.disabled = true;

        if (lgId) {
            fetch(`/admin/wards/${lgId}`)
                .then(response => response.json())
                .then(data => {
                    wardSelect.innerHTML = '<option value="">Select Ward</option>';
                    data.forEach(ward => {
                        const selected = ward.id == currentWardId ? 'selected' : '';
                        wardSelect.innerHTML += `<option value="${ward.id}" ${selected}>${ward.code} - ${ward.name}</option>`;
                    });
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
});
</script>
@endsection