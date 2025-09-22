<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\ResultItem;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $results = Result::withCount('items')->latest()->paginate(10);
        return response()->json($results);
    }

    public function show(Result $result)
    {
        return response()->json($result->load('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'analysis_run_id' => 'nullable|integer',
            'summary' => 'nullable|array',
            'items' => 'required|array',
            'items.*.element_ref' => 'required|string',
            'items.*.type' => 'required|string',
            'items.*.values' => 'required|array',
        ]);

        $result = Result::create([
            'analysis_run_id' => $data['analysis_run_id'] ?? null,
            'summary' => $data['summary'] ?? [],
        ]);

        foreach ($data['items'] as $item) {
            $result->items()->create([
                'element_ref' => $item['element_ref'],
                'type' => $item['type'],
                'values' => $item['values'],
                'max_moment' => $item['values']['Mmax'] ?? null,
                'max_shear' => $item['values']['Vmax'] ?? null,
                'status_pass' => $item['values']['status_pass'] ?? null,
            ]);
        }

        return response()->json($result->load('items'), 201);
    }

    public function destroy(Result $result)
    {
        $result->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
