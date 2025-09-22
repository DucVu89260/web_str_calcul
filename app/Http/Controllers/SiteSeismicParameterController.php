<?php

namespace App\Http\Controllers;

use App\Models\SiteSeismicParameter;
use App\Models\SiteParameter;
use Illuminate\Http\Request;

class SiteSeismicParameterController extends Controller
{
    public function index()
    {
        $seismics = SiteSeismicParameter::with('site')->get();
        return view('admins.sites.seismic.index', compact('seismics'));
    }

    public function create()
    {
        $sites = SiteParameter::all();
        return view('admins.sites.seismic.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'site_parameter_id' => 'required|exists:site_parameters,id',
            'standard_code' => 'required|string',
            'agR' => 'nullable|numeric',
            'site_class' => 'nullable|string',
            'importance_factor' => 'nullable|numeric',
            'soil_factor' => 'nullable|numeric',
            'Ss' => 'nullable|numeric',
            'S1' => 'nullable|numeric',
            'sd_short' => 'nullable|numeric',
            'sd_1' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        SiteSeismicParameter::create($data);
        return redirect()->route('seismic.index')->with('success', 'Seismic parameter created.');
    }

    public function show(SiteSeismicParameter $seismic)
    {
        return view('admins.sites.seismic.show', compact('seismic'));
    }

    public function edit(SiteSeismicParameter $seismic)
    {
        $sites = SiteParameter::all();
        return view('admins.sites.seismic.edit', compact('seismic', 'sites'));
    }

    public function update(Request $request, SiteSeismicParameter $seismic)
    {
        $seismic->update($request->all());
        return redirect()->route('seismic.index')->with('success', 'Seismic parameter updated.');
    }

    public function destroy(SiteSeismicParameter $seismic)
    {
        $seismic->delete();
        return redirect()->route('seismic.index')->with('success', 'Seismic parameter deleted.');
    }
}