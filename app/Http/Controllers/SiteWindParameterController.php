<?php

namespace App\Http\Controllers;

use App\Models\SiteWindParameter;
use App\Models\SiteParameter;
use Illuminate\Http\Request;

class SiteWindParameterController extends Controller
{   
    public function index(Request $request)
    {
        $search = $request->input('search');

        $winds = SiteWindParameter::with('site')
            ->when($search, function ($query, $search) {
                $query->whereHas('site', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->join('site_parameters', 'site_wind_parameters.site_parameter_id', '=', 'site_parameters.id')
            ->orderBy('site_parameters.name', 'asc')
            ->select('site_wind_parameters.*')
            ->paginate(5);

        return view('admins.sites.wind.index', compact('winds', 'search'));
    }

    public function create()
    {
        $sites = SiteParameter::orderBy('name', 'asc')->get();
        return view('admins.sites.wind.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'site_parameter_id' => 'required|exists:site_parameters,id',
            'standard_code' => 'required|string',
            'basic_wind_speed' => 'nullable|numeric',
            'pressure_reference' => 'nullable|numeric',
            'map_region' => 'nullable|string',
            'terrain_factors' => 'nullable|json',
            'gust_effect_factor' => 'nullable|numeric',
            'directionality_factor' => 'nullable|numeric',
            'conversion_to_other' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);
        SiteWindParameter::create($data);
        return redirect()->route('wind.index')->with('success', 'Wind parameter created.');
    }

    public function show(SiteWindParameter $wind)
    {
        return view('admins.sites.wind.show', compact('wind'));
    }

    public function edit(SiteWindParameter $wind)
    {
        $sites = SiteParameter::all();
        return view('admins.sites.wind.edit', compact('wind', 'sites'));
    }

    public function update(Request $request, SiteWindParameter $wind)
    {
        $wind->update($request->all());
        return redirect()->route('wind.index')->with('success', 'Wind parameter updated.');
    }

    public function destroy(SiteWindParameter $wind)
    {
        $wind->delete();
        return redirect()->route('wind.index')->with('success', 'Wind parameter deleted.');
    }
}