<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectParameter;
use App\Models\PreliminarySuggestion;
use App\Models\Section;

class PreliminaryController extends Controller
{
    /**
     * Display a listing of the preliminary parameters and suggestions.
     */
    public function index(Project $project)
    {
        // Lấy parameters và suggestions cho project cụ thể
        $params = $project->parameters;
        $suggestions = $project->preliminarySuggestions()->with('section')->orderBy('similarity_score', 'desc')->get();

        return view('admins.preliminary.index', compact('project', 'params', 'suggestions'));
    }

    /**
     * Display the preliminary creation form.
     */
    public function create(Project $project)
    {
        $params = $project->parameters;
        return view('admins.preliminary.create', compact('project', 'params'));
    }

    /**
     * Store preliminary parameters and generate suggestions.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'dead_load_roof' => 'required|numeric|min:0',
            'live_load_roof' => 'required|numeric|min:0',
            'eave_height' => 'required|numeric|min:0',
            'total_spans' => 'required|integer|min:1',
            'max_span' => 'required|numeric|min:0',
            'has_crane' => 'boolean',
            'crane_details' => 'nullable|array',
            'crane_details.crane_weight' => 'nullable|numeric|min:0',
            'crane_details.hoist_weight' => 'nullable|numeric|min:0',
            'crane_details.mode' => 'nullable|string|max:50',
            'crane_details.count' => 'nullable|integer|min:1',
            'extra_params' => 'nullable|array',
        ]);

        // Lưu hoặc cập nhật parameters
        $params = $project->parameters()->updateOrCreate(
            ['project_id' => $project->id],
            $validated
        );

        // Chạy logic tìm tương đồng và gợi ý
        $suggestions = $this->findSimilarProjects($params);

        // Xóa suggestions cũ và lưu mới
        $project->preliminarySuggestions()->delete();
        $suggestionsData = [];
        foreach ($suggestions as $suggestion) {
            $sug = PreliminarySuggestion::create([
                'project_id' => $project->id,
                'suggested_section_id' => $suggestion['section_id'],
                'similarity_score' => $suggestion['score'],
                'meta' => $suggestion['meta'] ?? null,
            ]);
            $suggestionsData[] = $sug;
        }

        // Update project meta
        $project->update([
            'preliminary_status' => 'suggested',
            'preliminary_meta' => $suggestionsData,
        ]);

        return redirect()->route('admins.preliminary.show', $project)->with('success', 'Preliminary suggestions generated!');
    }

    /**
     * Display the preliminary suggestions.
     */
    public function show(Project $project, Request $request)
    {
        $query = $project->preliminarySuggestions()->with('section');

        // Lọc nhanh dựa trên request
        if ($request->has('min_score')) {
            $query->where('similarity_score', '>=', (float) $request->min_score);
        }
        if ($request->has('section_type')) {
            $query->whereHas('section', function ($q) use ($request) {
                $q->where('type', $request->section_type);
            });
        }

        $suggestions = $query->orderBy('similarity_score', 'desc')->get();
        $params = $project->parameters;

        return view('admins.preliminary.show', compact('project', 'suggestions', 'params'));
    }

    /**
     * Show the form for editing the preliminary parameters.
     */
    public function edit(Project $project)
    {
        $params = $project->parameters;
        return view('admins.preliminary.edit', compact('project', 'params'));
    }

    /**
     * Update the preliminary parameters and regenerate suggestions.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'dead_load_roof' => 'required|numeric|min:0',
            'live_load_roof' => 'required|numeric|min:0',
            'eave_height' => 'required|numeric|min:0',
            'total_spans' => 'required|integer|min:1',
            'max_span' => 'required|numeric|min:0',
            'has_crane' => 'boolean',
            'crane_details' => 'nullable|array',
            'crane_details.crane_weight' => 'nullable|numeric|min:0',
            'crane_details.hoist_weight' => 'nullable|numeric|min:0',
            'crane_details.mode' => 'nullable|string|max:50',
            'crane_details.count' => 'nullable|integer|min:1',
            'extra_params' => 'nullable|array',
        ]);

        // Cập nhật parameters
        $params = $project->parameters()->updateOrCreate(
            ['project_id' => $project->id],
            $validated
        );

        // Regenerate suggestions
        $suggestions = $this->findSimilarProjects($params);

        // Xóa suggestions cũ và lưu mới
        $project->preliminarySuggestions()->delete();
        $suggestionsData = [];
        foreach ($suggestions as $suggestion) {
            $sug = PreliminarySuggestion::create([
                'project_id' => $project->id,
                'suggested_section_id' => $suggestion['section_id'],
                'similarity_score' => $suggestion['score'],
                'meta' => $suggestion['meta'] ?? null,
            ]);
            $suggestionsData[] = $sug;
        }

        // Update project meta
        $project->update([
            'preliminary_status' => 'suggested',
            'preliminary_meta' => $suggestionsData,
        ]);

        return redirect()->route('preliminary.show', $project)->with('success', 'Preliminary parameters updated and suggestions regenerated!');
    }

    /**
     * Remove the preliminary parameters and suggestions.
     */
    public function destroy(Project $project)
    {
        $project->parameters()->delete();
        $project->preliminarySuggestions()->delete();
        $project->update([
            'preliminary_status' => 'none',
            'preliminary_meta' => null,
        ]);

        return redirect()->route('preliminary.index', $project)->with('success', 'Preliminary data deleted!');
    }

    /**
     * Find similar projects and suggest sections.
     */
    private function findSimilarProjects(ProjectParameter $newParams)
    {
        $similarParams = ProjectParameter::where('project_id', '!=', $newParams->project_id)
            ->whereBetween('dead_load_roof', [$newParams->dead_load_roof * 0.8, $newParams->dead_load_roof * 1.2])
            ->whereBetween('max_span', [$newParams->max_span * 0.9, $newParams->max_span * 1.1])
            ->whereBetween('eave_height', [$newParams->eave_height * 0.9, $newParams->eave_height * 1.1])
            ->where('total_spans', $newParams->total_spans)
            ->when($newParams->has_crane, function ($query) {
                $query->where('has_crane', true);
            })
            ->limit(10)
            ->get();

        $suggestions = [];
        foreach ($similarParams as $oldParam) {
            $oldProject = $oldParam->project;
            $oldModel = $oldProject->models()->first();
            if ($oldModel) {
                $sections = $oldProject->sections;
                foreach ($sections->take(3) as $section) {
                    $score = $this->calculateSimilarity($newParams, $oldParam);
                    if ($score > 50) {
                        $suggestions[] = [
                            'section_id' => $section->id,
                            'score' => $score,
                            'meta' => [
                                'from_project' => $oldProject->name,
                                'reason' => "Similar loads and spans",
                            ],
                        ];
                    }
                }
            }
        }

        return collect($suggestions)->sortByDesc('score')->take(5)->values()->all();
    }

    /**
     * Calculate similarity score (0-100).
     */
    private function calculateSimilarity(ProjectParameter $new, ProjectParameter $old)
    {
        $score = 0;
        $weights = [
            'dead_load_roof' => 25,
            'live_load_roof' => 20,
            'max_span' => 25,
            'eave_height' => 15,
            'total_spans' => 10,
            'has_crane' => 5,
        ];

        foreach ($weights as $field => $weight) {
            if ($field === 'has_crane') {
                $score += ($new->$field === $old->$field) ? $weight : 0;
            } elseif ($field === 'total_spans') {
                $score += ($new->$field === $old->$field) ? $weight : 0;
            } else {
                $diff = abs($new->$field - $old->$field) / max($new->$field, 1);
                $fieldScore = (1 - $diff) * $weight;
                $score += max(0, $fieldScore);
            }
        }

        return $score;
    }
}