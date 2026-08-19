<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocalGovernment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocalGovernmentController extends Controller
{
    /**
     * Display a listing of local governments.
     */
    public function index()
    {
        $localGovernments = LocalGovernment::withCount('wards')->orderBy('code')->get();
        return view('admin.local-governments.index', compact('localGovernments'));
    }

    /**
     * Show the form for creating a new local government.
     */
    public function create()
    {
        return view('admin.local-governments.create');
    }

    /**
     * Store a newly created local government in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:2|unique:local_governments,code|regex:/^[0-9]{2}$/',
            'name' => 'required|string|max:255|unique:local_governments,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        LocalGovernment::create($request->all());

        return redirect()->route('admin.local-governments.index')
            ->with('success', 'Local Government created successfully!');
    }

    /**
     * Show the form for editing the specified local government.
     */
    public function edit(LocalGovernment $localGovernment)
    {
        return view('admin.local-governments.edit', compact('localGovernment'));
    }

    /**
     * Update the specified local government in storage.
     */
    public function update(Request $request, LocalGovernment $localGovernment)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:2|regex:/^[0-9]{2}$/|unique:local_governments,code,' . $localGovernment->id,
            'name' => 'required|string|max:255|unique:local_governments,name,' . $localGovernment->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $localGovernment->update($request->all());

        return redirect()->route('admin.local-governments.index')
            ->with('success', 'Local Government updated successfully!');
    }

    /**
     * Remove the specified local government from storage.
     */
    public function destroy(LocalGovernment $localGovernment)
    {
        // Check if it has related records
        if ($localGovernment->wards()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete Local Government because it has associated wards.');
        }

        $localGovernment->delete();

        return redirect()->route('admin.local-governments.index')
            ->with('success', 'Local Government deleted successfully!');
    }
}