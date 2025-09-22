@extends('admins.layouts.master')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Analysis Dashboard</h2>

    <!-- Row 1: Upload & Connection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Upload & Kết nối Model</div>
                <div class="card-body">
                    <form id="model-form">
                        <div class="form-group">
                            <label for="model_path">Đường dẫn mô hình (.sdb)</label>
                            <input type="text" id="model_path" class="form-control" placeholder="C:\sap2000\model.sdb">
                        </div>
                        <button type="button" class="btn btn-primary" id="btn-connect">
                            Kết nối & Chạy toàn bộ
                        </button>
                    </form>
                    <div class="mt-3" id="connect-status">
                        @if($models->first()?->connected)
                            <span class="badge badge-success">✅ Connected</span>
                        @else
                            <span class="badge badge-secondary">⚪ Not Connected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Config & Selection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Cấu hình kết quả</div>
                <div class="card-body">
                    <form id="analysis-config">
                        <div class="form-group">
                            <label>Load Cases</label>
                            <select id="load_case" class="form-control">
                                @foreach($models->first()?->loadCases ?? [] as $case)
                                    <option value="{{ $case }}">{{ $case }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-success mt-2" id="btn-run-case">Apply Load Case</button>
                        </div>
                        <div class="form-group mt-3">
                            <label>Load Combinations</label>
                            <select id="load_combination" class="form-control">
                                @foreach($models->first()?->loadCombinations ?? [] as $combo)
                                    <option value="{{ $combo }}">{{ $combo }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary mt-2" id="btn-run-combo">Apply Load Combination</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Selection</div>
                <div class="card-body">
                    <button type="button" class="btn btn-info mb-2" id="btn-refresh-selection">Refresh Selection</button>
                    <ul class="list-group" id="selection-list">
                        <li class="list-group-item text-muted">Chưa chọn đối tượng nào</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Results -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">Kết quả phân tích</div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="resultTabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#forces">Frame Forces</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#displ">Joint Displacements</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reac">Reactions</a></li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="forces">
                            <table class="table table-bordered table-sm">
                                <thead><tr>
                                    <th>Frame</th><th>LoadCase</th><th>P</th><th>V2</th><th>V3</th><th>T</th><th>M2</th><th>M3</th>
                                </tr></thead>
                                <tbody id="forces-body">
                                    <tr><td colspan="8" class="text-center">Chưa có dữ liệu</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="displ">
                            <table class="table table-bordered table-sm">
                                <thead><tr>
                                    <th>Joint</th><th>LoadCase</th><th>UX</th><th>UY</th><th>UZ</th><th>RX</th><th>RY</th><th>RZ</th>
                                </tr></thead>
                                <tbody id="displ-body">
                                    <tr><td colspan="8" class="text-center">Chưa có dữ liệu</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="reac">
                            <table class="table table-bordered table-sm">
                                <thead><tr>
                                    <th>Joint</th><th>LoadCase</th><th>FX</th><th>FY</th><th>FZ</th><th>MX</th><th>MY</th><th>MZ</th>
                                </tr></thead>
                                <tbody id="reac-body">
                                    <tr><td colspan="8" class="text-center">Chưa có dữ liệu</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary mt-3">Export CSV</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Visualization & Log -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Visualization</div>
                <div class="card-body"><p>[Placeholder for charts/plots]</p></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Analysis Log</div>
                <div class="card-body" id="analysis-log" style="max-height:300px;overflow-y:auto;font-family:monospace;">
                    <p>[System ready]</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function appendLog(msg) {
    const log = document.getElementById('analysis-log');
    const p = document.createElement('p');
    p.textContent = "[" + new Date().toLocaleTimeString() + "] " + msg;
    log.appendChild(p);
    log.scrollTop = log.scrollHeight;
}

// --- Kết nối & phân tích toàn bộ ---
document.getElementById('btn-connect').addEventListener('click', function() {
    const modelPath = document.getElementById('model_path').value;
    appendLog("Connecting to SAP2000 & running full analysis...");
    fetch('/api/analysis/connect', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({model_path: modelPath})
    })
    .then(res => res.json())
    .then(data => {
        console.log("Connect response:", data);
        if (data.status === 'success') {
            document.getElementById('connect-status').innerHTML =
                '<span class="badge badge-success">✅ Connected & Analyzed</span>';
            appendLog("Connected to model: " + (data.model || ''));

            // Render LoadCases
            const loadCaseSelect = document.getElementById('load_case');
            loadCaseSelect.innerHTML = "";
            if (Array.isArray(data.load_cases)) {
                data.load_cases.forEach(c => loadCaseSelect.innerHTML += `<option value="${c}">${c}</option>`);
            }

            // Render LoadCombinations
            const comboSelect = document.getElementById('load_combination');
            comboSelect.innerHTML = "";
            if (Array.isArray(data.load_combinations)) {
                data.load_combinations.forEach(c => comboSelect.innerHTML += `<option value="${c}">${c}</option>`);
            }

            appendLog("Loaded " + (data.load_cases?.length || 0) + " load cases, "
                + (data.load_combinations?.length || 0) + " combinations.");
        } else {
            document.getElementById('connect-status').innerHTML =
                '<span class="badge badge-danger">❌ Not Connected</span>';
            appendLog("Connection failed: " + (data.message || 'Unknown error'));
        }
    })
    .catch(err => {
        document.getElementById('connect-status').innerHTML =
            '<span class="badge badge-danger">❌ Not Connected</span>';
        appendLog("Connection error: " + err);
    });
});

// --- Render kết quả ---
function renderResults(data) {
    // Frames
    const forcesBody = document.getElementById('forces-body');
    forcesBody.innerHTML = (data.frames?.length > 0) ? '' : '<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>';
    data.frames?.forEach(frame => {
        (frame.forces || []).forEach(f => {
            forcesBody.innerHTML += `
                <tr>
                    <td>${frame.name}</td>
                    <td>${f.LoadCase || ''}</td>
                    <td>${f.P ?? 0}</td>
                    <td>${f.V2 ?? 0}</td>
                    <td>${f.V3 ?? 0}</td>
                    <td>${f.T ?? 0}</td>
                    <td>${f.M2 ?? 0}</td>
                    <td>${f.M3 ?? 0}</td>
                </tr>`;
        });
    });

    // Joints
    const displBody = document.getElementById('displ-body');
    displBody.innerHTML = (data.joints?.length > 0) ? '' : '<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>';
    data.joints?.forEach(joint => {
        (joint.displacements || []).forEach(d => {
            displBody.innerHTML += `
                <tr>
                    <td>${joint.name}</td>
                    <td>${d.LoadCase || ''}</td>
                    <td>${d.UX ?? 0}</td>
                    <td>${d.UY ?? 0}</td>
                    <td>${d.UZ ?? 0}</td>
                    <td>${d.RX ?? 0}</td>
                    <td>${d.RY ?? 0}</td>
                    <td>${d.RZ ?? 0}</td>
                </tr>`;
        });
    });

    // Reactions
    const reacBody = document.getElementById('reac-body');
    reacBody.innerHTML = (data.reactions?.length > 0) ? '' : '<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>';
    data.reactions?.forEach(joint => {
        (joint.reactions || []).forEach(r => {
            reacBody.innerHTML += `
                <tr>
                    <td>${joint.name}</td>
                    <td>${r.LoadCase || ''}</td>
                    <td>${r.FX ?? 0}</td>
                    <td>${r.FY ?? 0}</td>
                    <td>${r.FZ ?? 0}</td>
                    <td>${r.MX ?? 0}</td>
                    <td>${r.MY ?? 0}</td>
                    <td>${r.MZ ?? 0}</td>
                </tr>`;
        });
    });
}

// --- Apply Load Case ---
document.getElementById('btn-run-case').addEventListener('click', function() {
    const loadCase = document.getElementById('load_case').value;
    appendLog("Fetching results for Load Case: " + loadCase);

    fetch(`/api/analysis/results?loadcase=${encodeURIComponent(loadCase)}&run=1`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                appendLog("Results loaded for Load Case: " + loadCase);
                renderResults(data.results);
            } else {
                appendLog("Error fetching Load Case: " + (data.message || 'Unknown error'));
            }
        })
        .catch(err => appendLog("Load Case fetch error: " + err));
});

// --- Apply Load Combination ---
document.getElementById('btn-run-combo').addEventListener('click', function() {
    const loadCombo = document.getElementById('load_combination').value;
    appendLog("Fetching results for Load Combination: " + loadCombo);

    fetch(`/api/analysis/results?loadcombination=${encodeURIComponent(loadCombo)}&run=1`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                appendLog("Results loaded for Load Combination: " + loadCombo);
                renderResults(data.results);
            } else {
                appendLog("Error fetching Load Combination: " + (data.message || 'Unknown error'));
            }
        })
        .catch(err => appendLog("Load Combination fetch error: " + err));
});

// --- Refresh Selection ---
document.getElementById('btn-refresh-selection').addEventListener('click', function() {
    appendLog("Refreshing selection...");
    fetch('/api/analysis/selection')
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById('selection-list');
        list.innerHTML = "";
        if (data.status === 'success' && data.selection?.length > 0) {
            data.selection.forEach(item => {
                const li = document.createElement('li');
                li.className = "list-group-item";
                li.textContent = `${item.type}: ${item.name}`;
                list.appendChild(li);
            });
            appendLog("Selection refreshed (" + data.selection.length + " items).");
        } else {
            list.innerHTML = '<li class="list-group-item text-muted">Chưa chọn đối tượng nào</li>';
            appendLog("No selection found.");
        }
    })
    .catch(err => appendLog("Refresh selection error: " + err));
});
</script>
@endpush