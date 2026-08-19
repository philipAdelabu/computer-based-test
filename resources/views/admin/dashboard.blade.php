@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
                <div class="text-4xl mb-4">🏛️</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Local Governments</h3>
                <p class="text-gray-600 text-sm mb-4">Manage local government areas</p>
                <a href="{{ route('admin.local-governments.index') }}" 
                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Manage →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
                <div class="text-4xl mb-4">🗺️</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Wards</h3>
                <p class="text-gray-600 text-sm mb-4">Manage wards within LGs</p>
                <a href="{{ route('admin.wards.index') }}" 
                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Manage →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
                <div class="text-4xl mb-4">📮</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Voting Units</h3>
                <p class="text-gray-600 text-sm mb-4">Manage voting units within wards</p>
                <a href="{{ route('admin.voting-units.index') }}" 
                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Manage →
                </a>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('dashboard') }}" 
               class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300 inline-block">
                View Voting Dashboard →
            </a>
        </div>
    </div>
</div>
@endsection