@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Voting Dashboard</h1>
            <div class="flex gap-4">
                <a href="{{ route('vote.form') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    + Cast New Vote
                </a>
                <a href="{{ route('landing') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Home
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
                <div class="text-sm opacity-75">Total Votes</div>
                <div class="text-3xl font-bold">{{ number_format($totalVotes) }}</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
                <div class="text-sm opacity-75">Accord</div>
                <div class="text-3xl font-bold">{{ number_format($accordVotes) }}</div>
                <div class="text-sm">{{ $accordPercentage }}%</div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6">
                <div class="text-sm opacity-75">APC</div>
                <div class="text-3xl font-bold">{{ number_format($apcVotes) }}</div>
                <div class="text-sm">{{ $apcPercentage }}%</div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
                <div class="text-sm opacity-75">Total Units</div>
                <div class="text-3xl font-bold">{{ number_format($totalUnits) }}</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Vote Distribution</h2>
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                            Accord
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-blue-600">
                            {{ $accordPercentage }}%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-6 mb-4 text-xs flex rounded bg-blue-200">
                    <div style="width: {{ $accordPercentage }}%" 
                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500">
                    </div>
                </div>
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">
                            APC
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-red-600">
                            {{ $apcPercentage }}%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-6 mb-4 text-xs flex rounded bg-red-200">
                    <div style="width: {{ $apcPercentage }}%" 
                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500 transition-all duration-500">
                    </div>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Leading Party: <span class="font-bold text-lg 
                    {{ $leadingParty == 'Accord' ? 'text-blue-600' : ($leadingParty == 'APC' ? 'text-red-600' : 'text-gray-600') }}">
                    {{ $leadingParty }}
                </span>
            </div>
        </div>

        <!-- Voting Units Table -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Votes by Voting Unit</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accord</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">APC</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($votesByUnit as $unit => $unitVotes)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                    {{ $unitVotes->where('party', 'Accord')->sum('total_score') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                    {{ $unitVotes->where('party', 'APC')->sum('total_score') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    {{ $unitVotes->sum('total_score') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No votes recorded yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Votes -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Votes</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Party</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentVotes as $vote)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vote->voting_unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $vote->party == 'Accord' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $vote->party }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($vote->score) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vote->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No votes recorded yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection