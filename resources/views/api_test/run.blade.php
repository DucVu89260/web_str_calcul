@extends('admins.layouts.master')

@section('content')
<div class="container">
    <h2>Test SAP2000 API</h2>
    <form id="api-form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <label>Model Path</label>
            <input type="text" class="form-control" id="model_path" placeholder="C:\sap2000\model.sdb" value="C:\Users\ducvu\Desktop\api-test\api-test.sdb">
        </div>
        <div class="form-group">
            <label>Load Case</label>
            <input type="text" class="form-control" id="load_case" placeholder="D" value="D">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Run Analysis</button>
    </form>

    <div id="api-status" class="mt-3"></div>
    <pre id="api-result" class="mt-3 bg-light p-3" style="white-space: pre-wrap;"></pre>
</div>

<script>
document.getElementById('api-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const modelPath = document.getElementById('model_path').value;
    const loadCase = document.getElementById('load_case').value;

    const statusDiv = document.getElementById('api-status');
    const resultPre = document.getElementById('api-result');
    statusDiv.innerText = 'Looking up model...';
    resultPre.innerText = '';

    try {
        const res1 = await fetch('/api/models/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ model_path: modelPath })
        });
        const data1 = await res1.json();
        if (!res1.ok) {
            throw new Error(data1.error || 'Model search failed');
        }

        statusDiv.innerText = `Found model: ${data1.name} (id ${data1.id}). Starting analysis...`;

        const res2 = await fetch('/api/analysis/run', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ model_path: modelPath, load_case: loadCase })
        });

        const data2 = await res2.json();

        if (!res2.ok) {
            throw new Error(data2.error || JSON.stringify(data2));
        }

        statusDiv.innerText = 'Analysis completed successfully.';
        resultPre.innerText = JSON.stringify(data2, null, 2);
    } catch (err) {
        statusDiv.innerText = 'Error';
        resultPre.innerText = JSON.stringify({ error: err.message }, null, 2);
    }
});
</script>
@endsection
