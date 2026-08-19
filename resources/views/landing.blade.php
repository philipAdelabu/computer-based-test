@extends('layouts.app')

@section('title', 'Voting System')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to Voting System</h1>
            <p class="text-xl text-gray-600">Accord vs APC</p>
            @auth
                <p class="text-sm text-gray-500 mt-2">Logged in as: {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</p>
            @endauth
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-2xl mx-auto">
            @auth
                @if(auth()->user()->isOfficer() || auth()->user()->isAdmin())
                    <a href="{{ route('vote.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-8 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105 shadow-lg">
                        <div class="text-3xl mb-2">📝</div>
                        <div class="text-xl">Cast Vote</div>
                        <div class="text-sm mt-2 opacity-75">Submit new voting results</div>
                    </a>
                @endif

                <a href="{{ route('dashboard') }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-8 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-3xl mb-2">📊</div>
                    <div class="text-xl">Dashboard</div>
                    <div class="text-sm mt-2 opacity-75">View voting statistics</div>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-8 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-3xl mb-2">🔐</div>
                    <div class="text-xl">Login</div>
                    <div class="text-sm mt-2 opacity-75">Sign in to your account</div>
                </a>

                <a href="{{ route('register') }}" 
                   class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-8 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-3xl mb-2">📝</div>
                    <div class="text-xl">Register</div>
                    <div class="text-sm mt-2 opacity-75">Create a new account</div>
                </a>
            @endauth
        </div>

        @auth
            <div class="mt-8 text-center">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        Logout
                    </button>
                </form>
            </div>
        @endauth
    </div>
</div>
@endsection