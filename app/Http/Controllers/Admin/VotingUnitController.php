<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocalGovernment;
use App\Models\VotingUnit;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VotingUnitController extends Controller
{
    /**
     * Display a listing of voting units.
     */
    public function index()
    {
        $votingUnits = VotingUnit::with(['ward.localGovernment'])
            ->orderBy('ward_id')
            ->orderBy('code')
            ->get();
        return view('admin.voting-units.index', compact('votingUnits'));
    }

    /**
     * Show the form for creating a new voting unit.
     */
    public function create()
    {
        $localGovernments = LocalGovernment::with('wards')->orderBy('code')->get();
        return view('admin.voting-units.create', compact('localGovernments'));
    }

    /**
     * Store a newly created voting unit in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ward_id' => 'required|exists:wards,id',
            'code' => 'required|string|size:2|regex:/^[0-9]{2}$/',
            'name' => 'required|string|max:255',
        ]);

        // Check if unit code already exists for this ward
        $exists = VotingUnit::where('ward_id', $request->ward_id)
            ->where('code', $request->code)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['code' => 'This voting unit code already exists for the selected ward.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate full code
        $ward = Ward::with('localGovernment')->find($request->ward_id);
        $fullCode = VotingUnit::generateFullCode(
            $ward->localGovernment->code,
            $ward->code,
            $request->code
        );

        VotingUnit::create([
            'ward_id' => $request->ward_id,
            'code' => $request->code,
            'name' => $request->name,
            'full_code' => $fullCode,
        ]);

        return redirect()->route('admin.voting-units.index')
            ->with('success', 'Voting Unit created successfully!');
    }

    /**
     * Show the form for editing the specified voting unit.
     */
    public function edit(VotingUnit $votingUnit)
    {
        $localGovernments = LocalGovernment::with('wards')->orderBy('code')->get();
        return view('admin.voting-units.edit', compact('votingUnit', 'localGovernments'));
    }

    /**
     * Update the specified voting unit in storage.
     */
    public function update(Request $request, VotingUnit $votingUnit)
    {
        $validator = Validator::make($request->all(), [
            'ward_id' => 'required|exists:wards,id',
            'code' => 'required|string|size:2|regex:/^[0-9]{2}$/',
            'name' => 'required|string|max:255',
        ]);

        // Check if unit code already exists for this ward (excluding current)
        $exists = VotingUnit::where('ward_id', $request->ward_id)
            ->where('code', $request->code)
            ->where('id', '!=', $votingUnit->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['code' => 'This voting unit code already exists for the selected ward.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate full code
        $ward = Ward::with('localGovernment')->find($request->ward_id);
        $fullCode = VotingUnit::generateFullCode(
            $ward->localGovernment->code,
            $ward->code,
            $request->code
        );

        $votingUnit->update([
            'ward_id' => $request->ward_id,
            'code' => $request->code,
            'name' => $request->name,
            'full_code' => $fullCode,
        ]);

        return redirect()->route('admin.voting-units.index')
            ->with('success', 'Voting Unit updated successfully!');
    }

    /**
     * Remove the specified voting unit from storage.
     */
    public function destroy(VotingUnit $votingUnit)
    {
        // Check if it has related records
        if ($votingUnit->votes()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete Voting Unit because it has associated votes.');
        }

        $votingUnit->delete();

        return redirect()->route('admin.voting-units.index')
            ->with('success', 'Voting Unit deleted successfully!');
    }

    /**
     * Get voting units by ward (AJAX).
     */
    public function getByWard($wardId)
    {
        $units = VotingUnit::where('ward_id', $wardId)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'full_code']);
        return response()->json($units);
    }
}