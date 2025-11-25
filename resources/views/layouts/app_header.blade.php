@vite(['resources/sass/header.scss'])

<header class="main-header">
  <div class="row d-flex justify-content-between">
    <div style="height: 60px !important;" class="col-4 logo d-flex align-items-center">
      <div class="row" style="-webkit-transform: skew(-20deg) !important;">
        <div style="margin-left: 30px" class="col-auto p-2">
          <a href="" style="color: #0e2238">            
            <i class="fa-solid fa-folder-open" style="margin-right: 1rem"></i>
            <span class="logo-text">VisionDoc</span>
          </a>
        </div>
      </div>
    </div>
    <div class="col-auto d-flex align-items-center">
      <div class="action d-flex align-items-center gap-3">
        {{-- <div class="bnt-notify" onclick="notifyToggle();">
          <i class="fa-solid fa-bell"></i>
        </div> --}}
        <div class="d-flex align-items-center gap-3 user-menu" style="cursor: pointer" onclick="menuToggle();">
          <i class="fa-solid fa-user"></i>
          <span>{{ Auth::user()?->name }}</span>
          <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="menu">
          <ul class="m-0">
            <li>
              <a href="#" class="d-flex align-items-center gap-3"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i>{{ __('Logout') }}
              </a>
            </li>
          </ul>
        </div>
        <div class="notify">
          <ul class="m-0 p-0">
            <li>
              <a href="#" class="notify-card">
                <strong>Pontos vencendo</strong> <br>
                <p>Pontos de Emilson estão para vencer</p>
              </a>
            </li>

            <li>
              <a href="#" class="notify-card">
                <strong>Pontos vencendo</strong> <br>
                <p>Pontos de Emilson estão para vencer</p>
              </a>
            </li>

            <li>
              <a href="#" class="notify-card">
                <strong>Pontos vencendo</strong> <br>
                <p>Pontos de Emilson estão para vencer</p>
              </a>
            </li>

            <li>
              <a href="#" class="notify-card">
                <strong>Pontos vencendo</strong> <br>
                <p>Pontos de Emilson estão para vencer</p>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
  </form>
</header>

<script>
  function menuToggle() {
    const toggleMenu = document.querySelector(".menu");
    toggleMenu.classList.toggle("active");
  }
</script>

<script>
  function notifyToggle() {
    const toggleMenu = document.querySelector(".notify");
    toggleMenu.classList.toggle("active-notify");
  }
</script>
