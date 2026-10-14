<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GradingScaleController extends Controller
{
    public function index()
    {
        $scales = GradingScale::withCount('items')
                             ->orderBy('is_default', 'desc')
                             ->orderBy('name')
                             ->get();
        
        return view('admin.grading.index', compact('scales'));
    }

    public function create()
    {
        return view('admin.grading.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.grade' => 'required|string|max:10',
            'items.*.min_score' => 'required|numeric|min:0|max:100',
            'items.*.max_score' => 'required|numeric|min:0|max:100|gte:items.*.min_score',
            'items.*.remark' => 'nullable|string|max:100',
            'items.*.color' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // If this is the default, unset other defaults
            if (!empty($validated['is_default'])) {
                GradingScale::where('is_default', true)->update(['is_default' => false]);
            }

            $scale = GradingScale::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default' => $validated['is_default'] ?? false,
                'is_active' => true,
            ]);

            foreach ($validated['items'] as $index => $item) {
                $scale->items()->create([
                    'grade' => $item['grade'],
                    'min_score' => $item['min_score'],
                    'max_score' => $item['max_score'],
                    'remark' => $item['remark'] ?? null,
                    'color' => $item['color'] ?? 'secondary',
                    'order' => $index,
                ]);
            }

            Cache::forget('default_grading_scale');
            DB::commit();

            return redirect()->route('admin.grading.index')
                ->with('success', 'Grading scale created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create grading scale: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function edit($id)
    {
        $scale = GradingScale::with('items')->findOrFail($id);
        return view('admin.grading.edit', compact('scale'));
    }

    public function update(Request $request, $id)
    {
        $scale = GradingScale::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.grade' => 'required|string|max:10',
            'items.*.min_score' => 'required|numeric|min:0|max:100',
            'items.*.max_score' => 'required|numeric|min:0|max:100',
            'items.*.remark' => 'nullable|string|max:100',
            'items.*.color' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            if (!empty($validated['is_default'])) {
                GradingScale::where('id', '!=', $id)
                            ->where('is_default', true)
                            ->update(['is_default' => false]);
            }

            $scale->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default' => $validated['is_default'] ?? false,
            ]);

            // Remove existing items and re-create
            $scale->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                $scale->items()->create([
                    'grade' => $item['grade'],
                    'min_score' => $item['min_score'],
                    'max_score' => $item['max_score'],
                    'remark' => $item['remark'] ?? null,
                    'color' => $item['color'] ?? 'secondary',
                    'order' => $index,
                ]);
            }

            Cache::forget('default_grading_scale');
            DB::commit();

            return redirect()->route('admin.grading.index')
                ->with('success', 'Grading scale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update grading scale: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function delete($id)
    {
        $scale = GradingScale::findOrFail($id);
        
        if ($scale->is_default) {
            return back()->with('error', 'Cannot delete the default grading scale. Set another scale as default first.');
        }
        
        $scale->items()->delete();
        $scale->delete();
        
        Cache::forget('default_grading_scale');
        
        return redirect()->route('admin.grading.index')
            ->with('success', 'Grading scale deleted successfully.');
    }

    public function setDefault($id)
    {
        DB::beginTransaction();
        try {
            GradingScale::where('is_default', true)->update(['is_default' => false]);
            
            $scale = GradingScale::findOrFail($id);
            $scale->update(['is_default' => true]);
            
            Cache::forget('default_grading_scale');
            DB::commit();
            
            return redirect()->route('admin.grading.index')
                ->with('success', "{$scale->name} is now the default grading scale.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to set default scale: ' . $e->getMessage());
        }
    }

    public function preview(Request $request)
    {
        $score = $request->get('score', 75);
        $scale = GradingScale::getDefault();
        
        if (!$scale) {
            return response()->json(['error' => 'No grading scale configured'], 404);
        }
        
        $item = $scale->getGradeForScore($score);
        
        return response()->json([
            'score' => $score,
            'grade' => $item ? $item->grade : 'N/A',
            'remark' => $item ? $item->remark : 'N/A',
            'color' => $item ? $item->color : 'secondary',
        ]);
    }
}
