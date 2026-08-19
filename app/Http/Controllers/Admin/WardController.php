<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocalGovernment;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WardController extends Controller
{
    /**
     * Display a listing of wards.
     */
    public function index()
    {
        $wards = Ward::with('localGovernment')
            ->orderBy('local_government_id')
            ->orderBy('code')
            ->get();
        return view('admin.wards.index', compact('wards'));
    }

    /**
     * Show the form for creating a new ward.
     */
    public function create()
    {
        $localGovernments = LocalGovernment::orderBy('code')->get();
        return view('admin.wards.create', compact('localGovernments'));
    }

    /**
     * Store a newly created ward in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local_government_id' => 'required|exists:local_governments,id',
            'code' => 'required|string|size:2|regex:/^[0-9]{2}$/',
            'name' => 'required|string|max:255',
        ]);

        // Check if ward code already exists for this local government
        $exists = Ward::where('local_government_id', $request->local_government_id)
            ->where('code', $request->code)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['code' => 'This ward code already exists for the selected local government.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Ward::create($request->all());

        return redirect()->route('admin.wards.index')
            ->with('success', 'Ward created successfully!');
    }

    /**
     * Show the form for editing the specified ward.
     */
    public function edit(Ward $ward)
    {
        $localGovernments = LocalGovernment::orderBy('code')->get();
        return view('admin.wards.edit', compact('ward', 'localGovernments'));
    }

    /**
     * Update the specified ward in storage.
     */
    public function update(Request $request, Ward $ward)
    {
        $validator = Validator::make($request->all(), [
            'local_government_id' => 'required|exists:local_governments,id',
            'code' => 'required|string|size:2|regex:/^[0-9]{2}$/',
            'name' => 'required|string|max:255',
        ]);

        // Check if ward code already exists for this local government (excluding current)
        $exists = Ward::where('local_government_id', $request->local_government_id)
            ->where('code', $request->code)
            ->where('id', '!=', $ward->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['code' => 'This ward code already exists for the selected local government.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ward->update($request->all());

        return redirect()->route('admin.wards.index')
            ->with('success', 'Ward updated successfully!');
    }

    /**
     * Remove the specified ward from storage.
     */
    public function destroy(Ward $ward)
    {
        // Check if it has related records
        if ($ward->votingUnits()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete Ward because it has associated voting units.');
        }

        $ward->delete();

        return redirect()->route('admin.wards.index')
            ->with('success', 'Ward deleted successfully!');
    }

    /**
     * Get wards by local government (AJAX).
     */
     public function getByLocalGovernment($lgId)
    {
        try {
            $wards = Ward::where('local_government_id', $lgId)
                ->orderBy('code')
                ->get(['id', 'code', 'name']);
            
            return response()->json($wards);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch wards: ' . $e->getMessage()], 500);
        }
    }
}