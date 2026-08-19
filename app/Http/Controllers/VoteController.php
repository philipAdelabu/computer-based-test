<?php

namespace App\Http\Controllers;

use App\Models\LocalGovernment;
use App\Models\Vote;
use App\Models\VotingUnit;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function create()
    {
        $localGovernments = LocalGovernment::with('wards.votingUnits')->get();
        return view('votes.create', compact('localGovernments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'voting_unit_id' => 'required|exists:voting_units,id',
            'party' => 'required|in:Accord,APC',
            'score' => 'required|integer|min:0'
        ]);

        // Check if user already voted for this unit
        $existingVote = Vote::where('voting_unit_id', $request->voting_unit_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already recorded a vote for this unit. Please contact admin to update.');
        }

        Vote::create([
            'voting_unit_id' => $request->voting_unit_id,
            'user_id' => Auth::id(),
            'party' => $request->party,
            'score' => $request->score
        ]);

        return redirect()->route('dashboard')->with('success', 'Vote recorded successfully!');
    }

    public function getWards($lgId)
    {
        try {
            $wards = Ward::where('local_government_id', $lgId)
                ->orderBy('code')
                ->get(['id', 'code', 'name']);
            
            return response()->json($wards);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch wards'], 500);
        }
    }

    public function getVotingUnits($wardId)
    {
        try {
            $units = VotingUnit::where('ward_id', $wardId)
                ->orderBy('code')
                ->get(['id', 'code', 'name', 'full_code']);
            
            return response()->json($units);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch voting units'], 500);
        }
    }
}