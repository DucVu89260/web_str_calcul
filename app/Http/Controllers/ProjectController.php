<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('admins.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admins.projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Project::create($data);
        return redirect()->route('projects.index')->with('success', 'Tạo Project thành công');
    }

    public function show(Project $project)
    {
        return view('admins.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admins.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $project->update($data);
        return redirect()->route('projects.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Xoá thành công');
    }
}
