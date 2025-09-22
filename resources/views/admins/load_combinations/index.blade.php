@extends('admins.layouts.master')
@section('content')
<div class="container mt-4">
   <h2 class="mb-4 text-white p-3 rounded" style="background-color:#003366;">
      Generate Load Combinations
   </h2>

   @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
   @endif
   @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
   @endif

   <form action="{{ route('load_combinations.generate') }}" method="POST" class="p-3 mb-4 rounded shadow-sm" style="background:#f8f9fa;">
      @csrf
      <div class="form-row">
         <div class="form-group col-md-6">
            <label for="dl">Dead Load:</label>
            <input type="text" class="form-control @error('dl') is-invalid @enderror" name="dl" id="dl" value="{{ old('dl', $dl ?? 'DL') }}" placeholder="e.g., DL or DL1">
            @error('dl')
               <div class="invalid-feedback">{{ $message }}</div>
            @enderror
         </div>
         <div class="form-group col-md-6">
            <label for="ll">Live Load:</label>
            <input type="text" class="form-control @error('ll') is-invalid @enderror" name="ll" id="ll" value="{{ old('ll', $ll ?? 'LL') }}" placeholder="e.g., LL or LL1">
            @error('ll')
               <div class="invalid-feedback">{{ $message }}</div>
            @enderror
         </div>
      </div>
      <div class="form-group">
         <label for="winds">Winds (comma separated):</label>
         <input type="text" class="form-control @error('winds') is-invalid @enderror" name="winds" id="winds" value="{{ old('winds', isset($winds) ? implode(', ', $winds) : 'WX1+, WX1-') }}" placeholder="e.g., WX1+, WX1-, WY1+">
         @error('winds')
            <div class="invalid-feedback">{{ $message }}</div>
         @enderror
      </div>
      <div class="form-group">
         <label for="cranes">Cranes (comma separated):</label>
         <input type="text" class="form-control @error('cranes') is-invalid @enderror" name="cranes" id="cranes" value="{{ old('cranes', isset($cranes) ? implode(', ', $cranes) : 'C1, C2') }}" placeholder="e.g., C1, C2">
         @error('cranes')
            <div class="invalid-feedback">{{ $message }}</div>
         @enderror
      </div>
      <button type="submit" class="btn text-white px-4" style="background-color:#003366;">Generate</button>
   </form>

   @if (empty($combinations))
      <div class="alert alert-info">No load combinations generated. Enter data and click Generate.</div>
   @else
      <div class="card shadow-sm">
         <div class="card-header text-white" style="background-color:#003366;">
            Generated Combinations
         </div>
         <div class="card-body p-0">
            <table class="table table-striped mb-0">
               <thead class="thead-dark">
                  <tr>
                     <th style="width:60px;">No</th>
                     <th>Combination</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($combinations as $i => $combo)
                     <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $combo }}</td>
                     </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
      <div class="mt-3">
         <a href="{{ route('load_combinations.export') }}" class="btn text-white mr-2" style="background-color:#006699;">Export CSV</a>
         <form action="{{ route('load_combinations.push') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn text-white" style="background-color:#cc6600;">Push to SAP2000</button>
         </form>
      </div>
   @endif
</div>
@endsection