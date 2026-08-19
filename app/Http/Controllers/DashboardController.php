<?php

namespace App\Http\Controllers;

use App\Models\LocalGovernment;
use App\Models\Vote;
use App\Models\VotingUnit;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $level = $request->get('level', 'national');
        $id = $request->get('id');
        
        $data = $this->getStatistics($level, $id);
        $localGovernments = LocalGovernment::with(['wards.votingUnits'])->get();
        
        return view('dashboard.index', array_merge($data, compact('localGovernments', 'level', 'id')));
    }

    private function getStatistics($level, $id = null)
    {
        $query = Vote::query();
        $unitId = null;
        $title = "National Overview";
        $breadcrumb = ['National' => '#'];
        
        switch ($level) {
            case 'unit':
                $votingUnit = VotingUnit::with(['ward.localGovernment'])->findOrFail($id);
                $votes = $votingUnit->votes();
                $title = "Voting Unit: {$votingUnit->full_code} - {$votingUnit->name}";
                $breadcrumb = [
                    'National' => route('dashboard'),
                    $votingUnit->ward->localGovernment->name => route('dashboard', ['level' => 'lg', 'id' => $votingUnit->ward->local_government_id]),
                    $votingUnit->ward->name => route('dashboard', ['level' => 'ward', 'id' => $votingUnit->ward_id]),
                    $votingUnit->name => '#'
                ];
                $unitId = $votingUnit->id;
                break;
                
            case 'ward':
                $ward = Ward::with(['localGovernment', 'votingUnits'])->findOrFail($id);
                $votes = $ward->votes();
                $title = "Ward: {$ward->getFullCode()} - {$ward->name}";
                $breadcrumb = [
                    'National' => route('dashboard'),
                    $ward->localGovernment->name => route('dashboard', ['level' => 'lg', 'id' => $ward->local_government_id]),
                    $ward->name => '#'
                ];
                $unitId = $ward->votingUnits->pluck('id')->toArray();
                break;
                
            case 'lg':
                $lg = LocalGovernment::with('wards')->findOrFail($id);
                $votes = $lg->votes();
                $title = "Local Government: {$lg->code} - {$lg->name}";
                $breadcrumb = [
                    'National' => route('dashboard'),
                    $lg->name => '#'
                ];
                $unitId = $lg->votingUnits->pluck('id')->toArray();
                break;
                
            default: // national
                $votes = Vote::query();
                $title = "National Overview";
                $breadcrumb = ['National' => '#'];
                $unitId = null;
                break;
        }

        // Calculate statistics
        $accordVotes = (clone $votes)->where('party', 'Accord')->sum('score');
        $apcVotes = (clone $votes)->where('party', 'APC')->sum('score');
        $totalVotes = $accordVotes + $apcVotes;
        
        $accordPercentage = $totalVotes > 0 ? round(($accordVotes / $totalVotes) * 100, 2) : 0;
        $apcPercentage = $totalVotes > 0 ? round(($apcVotes / $totalVotes) * 100, 2) : 0;
        
        $leadingParty = $accordVotes > $apcVotes ? 'Accord' : ($apcVotes > $accordVotes ? 'APC' : 'Tie');
        
        // Get recent votes
        $recentVotes = Vote::with(['votingUnit.ward.localGovernment', 'user'])
            ->when($unitId, function ($q) use ($unitId) {
                if (is_array($unitId)) {
                    return $q->whereIn('voting_unit_id', $unitId);
                }
                return $q->where('voting_unit_id', $unitId);
            })
            ->latest()
            ->take(20)
            ->get();
            
        // Get unit breakdown
        $unitBreakdown = Vote::select('voting_unit_id', 'party', DB::raw('SUM(score) as total_score'))
            ->when($unitId, function ($q) use ($unitId) {
                if (is_array($unitId)) {
                    return $q->whereIn('voting_unit_id', $unitId);
                }
                return $q->where('voting_unit_id', $unitId);
            })
            ->groupBy('voting_unit_id', 'party')
            ->with('votingUnit.ward.localGovernment')
            ->get()
            ->groupBy('voting_unit_id');

        return compact(
            'accordVotes', 
            'apcVotes', 
            'totalVotes', 
            'accordPercentage', 
            'apcPercentage', 
            'leadingParty',
            'recentVotes',
            'unitBreakdown',
            'title',
            'breadcrumb'
        );
    }
}