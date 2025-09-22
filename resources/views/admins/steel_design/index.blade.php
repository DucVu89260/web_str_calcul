@extends('admins.layouts.master')

@section('content')
<div class="container">
    <h2>Steel Member Design</h2>

    <div class="row">
        <div class="col-md-4">
            <h5>Sections</h5>
            <ul class="list-group" id="member-list">
                @foreach($members as $member)
                    <li class="list-group-item" data-id="{{ $member->id }}">
                        {{ $member->name }} (Model: {{ $member->model_name }})
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-8">
            <h5>Actions</h5>
            <div class="mb-2">
                <button class="btn btn-primary action-btn" data-action="bolt_check">Bolt Check</button>
                <button class="btn btn-secondary action-btn" data-action="weld_check">Weld Check</button>
                <button class="btn btn-success action-btn" data-action="full_design">Full Design</button>
            </div>

            <div id="spinner" style="display:none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Running...
            </div>

            <h5 class="mt-3">Result:</h5>
            <pre id="design-result" class="bg-light p-3" style="white-space: pre-wrap;"></pre>
        </div>
    </div>
</div>

@section('scripts')
<script>
(function(){
    const memberList = document.getElementById('member-list');
    const actionButtons = document.querySelectorAll('.action-btn');
    const spinner = document.getElementById('spinner');
    const resultEl = document.getElementById('design-result');
    let selectedMemberId = null;

    // Chọn member
    memberList.addEventListener('click', function(e){
        const li = e.target.closest('li');
        if(!li) return;

        memberList.querySelectorAll('li').forEach(i => i.classList.remove('active'));
        li.classList.add('active');
        selectedMemberId = li.dataset.id;
    });

    // Trigger action
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function(){
            if(!selectedMemberId){
                alert('Please select a section first.');
                return;
            }

            const action = btn.dataset.action;

            spinner.style.display = 'inline-block';
            resultEl.innerText = '';

            fetch('{{ route("steel_design.run") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ section_id: selectedMemberId, action })
            })
            .then(res => res.json())
            .then(data => {
                spinner.style.display = 'none';
                resultEl.innerText = JSON.stringify(data, null, 2);
            })
            .catch(err => {
                spinner.style.display = 'none';
                resultEl.innerText = JSON.stringify({error: err.message}, null, 2);
            });
        });
    });
})();
</script>
@endsection
@endsection
