@extends('admins.layouts.master')

@section('content')
<div class="container">
    <h2>Debug SAP2000 API</h2>

    <form id="debug-form">
        <div class="form-group">
            <label>Model Path</label>
            <input type="text" class="form-control" id="model_path"
                   placeholder="C:\sap2000\model.sdb"
                   value="C:\Users\ducvu\Desktop\api-test\api-test.sdb">
        </div>

        <div class="form-group">
            <label>Load Case</label>
            <input type="text" class="form-control" id="load_case"
                   placeholder="D" value="D">
        </div>

        <button type="submit" class="btn btn-primary mt-2" id="runBtn">Run Debug</button>
    </form>

    <h4 class="mt-4">Result:</h4>
    <div id="spinner" class="mt-2" style="display:none;">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Running...
    </div>
    <pre id="debug-result" class="mt-3 bg-light p-3" style="white-space: pre-wrap;"></pre>
</div>

<script>
(function(){
    const form = document.getElementById('debug-form');
    const runBtn = document.getElementById('runBtn');
    const spinner = document.getElementById('spinner');
    const resultEl = document.getElementById('debug-result');

    form.addEventListener('submit', function(e){
        e.preventDefault(); // 🔑 Chặn reload trang

        const modelPath = document.getElementById('model_path').value;
        const loadCase = document.getElementById('load_case').value;

        // Reset UI
        resultEl.innerText = '';
        spinner.style.display = 'inline-block';
        runBtn.disabled = true;

        fetch('/api/debug/analysis', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ model_path: modelPath, load_case: loadCase })
        })
        .then(res => res.json())
        .then(data => {
            spinner.style.display = 'none';
            runBtn.disabled = false;
            resultEl.innerText = JSON.stringify(data, null, 2);
        })
        .catch(err => {
            spinner.style.display = 'none';
            runBtn.disabled = false;
            resultEl.innerText = JSON.stringify({error: err.message}, null, 2);
        });
    });
})();
</script>
@endsection