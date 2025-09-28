<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg main-nav">
    <a class="navbar-brand" href="{{ route('analysis.index') }}">API</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="{{ route('load_combinations.index') }}" class="nav-link">Load Combinations</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarProjects" role="button" data-toggle="dropdown">
                    Projects
                </a>
                <div class="dropdown-menu">
                    @foreach($projects as $p)
                        <a class="dropdown-item" href="{{ route('preliminary.index', $p->id) }}">
                            {{ $p->name }}
                        </a>
                    @endforeach
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarSites" role="button" data-toggle="dropdown">
                    Site DB
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('parameters.index') }}">Site Parameters</a>
                    <a class="dropdown-item" href="{{ route('wind.index') }}">Wind Parameters</a>
                    <a class="dropdown-item" href="{{ route('seismic.index') }}">Seismic Parameters</a>
                </div>
            </li>

            <li class="nav-item">
                <a href="{{ route('fire_resistance.index') }}" class="nav-link">Fire Resistance</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarSites" role="button" data-toggle="dropdown">
                    Libraries
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('sections.index') }}">Steel Sections</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarSites" role="button" data-toggle="dropdown">
                    Wind Load
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('windload.tcvn27372023') }}">TCVN 2737-2023</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarConnections" role="button" data-toggle="dropdown">
                    Connections
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('connections.index') }}">Danh sách liên kết</a>
                    <a class="dropdown-item" href="{{ route('connections.create', ['type' => 'moment', 'standard' => 'aisc360-10']) }}">
                        Tạo Moment (AISC 360-10)
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>