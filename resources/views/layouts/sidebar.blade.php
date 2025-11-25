@vite(['resources/js/sidebar.js'])
@vite(['resources/sass/sidebar.scss'])

@php
  $currentRoute = request()->route()->getName();
  $isSidebarExpanded = false;
  $auth = auth()->user();  
@endphp

<aside id="sidebar">

  <ul class="sidebar-nav toggle-btn">

    
    <li class="sidebar-item">
      <a href="{{route('projects')}}" class="sidebar-link {{ $currentRoute == 'tasks' ? 'active-sidebar-link' : '' }}">
        <i class="fa-solid fa-layer-group"></i>
        <span>Projetos</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a href="{{route('documents', ['project_id' => session('project_id')])}}" class="sidebar-link {{ $currentRoute == 'documents' ? 'active-sidebar-link' : '' }}">
        <i class="fa-solid fa-file-lines"></i>
        <span>Documentos</span>
      </a>
    </li>

    

    @if($auth->read_temporality)
      <li class="sidebar-item">
        <a href="{{route('temporalitys')}}" class="sidebar-link {{ $currentRoute == 'temporalitys' ? 'active-sidebar-link' : '' }}">
          <i class="fa-solid fa-barcode"></i>
          <span>Temporalidades</span>
        </a>
      </li>
    @endif

    @if($auth->read_collection)
      <li class="sidebar-item">
        <a href="{{route('document.collections')}}" class="sidebar-link {{ $currentRoute == 'document.collections' ? 'active-sidebar-link' : '' }}">
          <i class="fa-solid fa-cash-register"></i>
          <span>Acervo e protocolo</span>
        </a>
      </li>
    @endif
    
    @if($auth->read_elimination)
      <li class="sidebar-item">
        <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
          data-bs-target="#elimination" aria-expanded="false" aria-controls="elimination">
          <i class="fa-solid fa-circle-xmark"></i>
          <span>Lista de eliminação</span>
        </a>
        <ul id="elimination" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#elimination">
          <li class="sidebar-item">
            <a href="{{route('elimination_list')}}" class="sidebar-link {{ $currentRoute == 'elimination_list' ? 'active-sidebar-link' : '' }}">Listas</a>
          </li>
          <li class="sidebar-item">
            <a href="{{route('eliminations', ['project_id' => session('project_id')])}}" class="sidebar-link {{ $currentRoute == 'eliminations' ? 'active-sidebar-link' : '' }}">Eliminação</a>
          </li>
        </ul>
      </li>
    @endif

    <li class="sidebar-item">
      <a href="{{route('alerts')}}" class="sidebar-link {{ $currentRoute == 'alerts' ? 'active-sidebar-link' : '' }}">
        <i class="fa-solid fa-bell"></i>
        <span>Alertas</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a href="{{route('reports')}}" class="sidebar-link {{ $currentRoute == 'reports' ? 'active-sidebar-link' : '' }}">
        <i class="fa-solid fa-file-lines"></i>
        <span>Relatórios</span>
      </a>
    </li>


    @if($auth->is_admin)
      <li class="sidebar-item">
        <a href="{{route('users')}}" class="sidebar-link {{ $currentRoute == 'users' ? 'active-sidebar-link' : '' }}">
          <i class="fa-solid fa-users-line"></i>
          <span>Usuários</span>
        </a>
      </li>
    @endif

    @if($auth->is_admin)
      <li class="sidebar-item">
        <a href="{{route('logs')}}" class="sidebar-link {{ $currentRoute == 'logs' ? 'active-sidebar-link' : '' }}">
          <i class="fa-solid fa-clipboard-list"></i>
          <span>Auditoria</span>
        </a>
      </li>
    @endif

      <li class="sidebar-item">
        <a href="" class="sidebar-link {{ $currentRoute == 'helps' ? 'active-sidebar-link' : '' }}">
          <i class="fa-solid fa-circle-question"></i>
          <span>Ajuda</span>
        </a>
      </li>

  </ul>

  <div class="sidebar-footer toggle-btn">
    <a href="#" class="sidebar-link">
      <i id="toggle-icon" class="fa-solid fa-chevron-right"></i>
      <span>Recolher Menu</span>
    </a>
  </div>
  {{--<div class="sidebar-footer">
    <a href="#" class="sidebar-link {{ $currentRoute == 'projects' ? 'active-sidebar-link' : '' }}">
      <i class="lni lni-exit"></i>
      <span>Logout</span>
    </a>
  </div>--}}
</aside>
