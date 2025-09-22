<?php

namespace App\Http\Controllers;

use App\Models\SiteParameter;
use Illuminate\Http\Request;

class SiteParameterController extends Controller
{
    public function index()
    {
        $sites = SiteParameter::all();
        return view('admins.sites.parameters.index', compact('sites'));
    }

    public function create()
    {
        return view('admins.sites.parameters.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'country' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'elevation' => 'nullable|numeric',
            'terrain_category' => 'nullable|string',
            'exposure_category' => 'nullable|string',
            'topography_factor' => 'nullable|numeric',
            'importance_category' => 'nullable|string',
        ]);
        SiteParameter::create($data);
        return redirect()->route('parameters.index')->with('success','Site created');
    }

    public function show(SiteParameter $parameter)
    {
        return view('admins.sites.parameters.show', compact('parameter'));
    }

    public function edit(SiteParameter $parameter)
    {
        return view('admins.sites.parameters.edit', compact('parameter'));
    }

    public function update(Request $request, SiteParameter $parameter)
    {
        $data = $request->all();
        $parameter->update($data);
        return redirect()->route('parameters.index')->with('success','Site updated');
    }

    public function destroy(SiteParameter $parameter)
    {
        $parameter->delete();
        return redirect()->route('parameters.index')->with('success','Site deleted');
    }
}

