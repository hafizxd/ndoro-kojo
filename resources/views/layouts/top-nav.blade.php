<nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarTop">
    <div class="navbar-logo">

        <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
        <a class="navbar-brand me-sm-3 me-1" href="{{ route('dashboard') }}">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center"><img src="{{ asset('assets/img/ndorokojo_logo.png') }}" alt="ndorokojo" width="27" />
                    <p class="logo-text d-none d-sm-block ms-2">ndorokojo</p>
                </div>
            </div>
        </a>
    </div>
    <div class="navbar-collapse navbar-top-collapse order-lg-0 justify-content-center collapse order-1" id="navbarTopCollapse">
        <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link lh-1 @if (Request::routeIs('dashboard')) bg-primary-100 text-primary @endif" href="{{ route('dashboard') }}"><span class="uil fs-0 uil-chart-pie me-2"></span>Dashboard</a>
            </li>

            {{-- Ternak --}}
            <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1 @if (Request::routeIs('livestock.*')) bg-primary-100 text-primary @endif" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 uil-cube me-2"></span>Ternak</a>
                <ul class="dropdown-menu navbar-dropdown-caret">
                    <li><a class="dropdown-item @if (Request::routeIs('livestock.report')) bg-primary-100 text-primary @endif" href="{{ route('livestock.report') }}">
                            <div class="dropdown-item-wrapper"><span class="uil uil-money-bill me-2"></span>Report
                            </div>
                        </a>
                    </li>
                    <li><a class="dropdown-item @if (Request::routeIs('livestock.index')) bg-primary-100 text-primary @endif" href="{{ route('livestock.index') }}">
                            <div class="dropdown-item-wrapper"><span class="uil uil-newspaper me-2"></span>All Ternak
                            </div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- User --}}
            @auth('web')
                @if (auth('web')->user()->role == \App\Constants\UserRole::SUPERADMIN)
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1 @if (Request::routeIs('farmer.*') || Request::routeIs('operator.*')) bg-primary-100 text-primary @endif" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 uil-users-alt me-2"></span>Manajemen User</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li><a class="dropdown-item @if (Request::routeIs('farmer.index')) bg-primary-100 text-primary @endif" href="{{ route('farmer.index') }}">
                                    <div class="dropdown-item-wrapper"><span class="uil uil-users me-2"></span>Peternak</div>
                                </a>
                            </li>
                            <li><a class="dropdown-item @if (Request::routeIs('operator.index')) bg-primary-100 text-primary @endif" href="{{ route('operator.index') }}">
                                    <div class="dropdown-item-wrapper"><span class="uil uil-users me-2"></span>Operator</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (auth('web')->user()->role != \App\Constants\UserRole::OPERATOR)
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1 @if (Request::routeIs('slider.*')) bg-primary-100 text-primary @endif" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 uil-postcard me-2"></span>Sliders</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li><a class="dropdown-item @if (Request::routeIs('slider.category.index')) bg-primary-100 text-primary @endif" href="{{ route('slider.category.index') }}">
                                    <div class="dropdown-item-wrapper"><span class="uil uil-postcard me-2"></span>Manajemen Slider
                                    </div>
                                </a>
                            </li>

                            @php
                                $categories = \App\Models\ArticleCategory::all();
                            @endphp
                            @foreach ($categories as $value)
                                <li><a class="dropdown-item @if (Request::is('sliders/' . $value->slug)) bg-primary-100 text-primary @endif" href="{{ route('slider.index', $value->slug) }}">
                                        <div class="dropdown-item-wrapper"><span class="uil uil-postcard me-2"></span>{{ $value->title }}
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endauth
        </ul>
    </div>
    <ul class="navbar-nav navbar-nav-icons flex-row">
        <li class="nav-item">
            <div class="theme-control-toggle fa-icon-wait px-2">
                <input class="form-check-input theme-control-toggle-input ms-0" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                <label class="theme-control-toggle-label theme-control-toggle-light mb-0" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label>
                <label class="theme-control-toggle-label theme-control-toggle-dark mb-0" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label>
            </div>
        </li>
        @auth('web')
            <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-l">
                        <img class="rounded-circle" src="{{ asset('assets/img/team/avatar.webp') }}" alt="" />

                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret dropdown-profile border-300 border py-0 shadow" aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="card-body p-0">
                            <div class="pb-3 pt-4 text-center">
                                <div class="avatar avatar-xl">
                                    <img class="rounded-circle" src="{{ asset('assets/img/team/avatar.webp') }}" alt="" />

                                </div>
                                <h6 class="mt-2 text-black">{{ auth()->guard('web')->user()->name }}</h6>
                            </div>
                        </div>
                        <div>
                            {{-- <ul class="nav d-flex flex-column mb-2 pb-1">
              <li class="nav-item"><a class="nav-link px-3" href="#!"> <span class="me-2 text-900" data-feather="user"></span><span>Profile</span></a></li>
            </ul> --}}
                            <div class="px-3 pb-3"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="#!" onclick="document.querySelector('#logoutForm').submit()"> <span class="me-2" data-feather="log-out"> </span>Sign out</a></div>
                            <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ route('login') }}" type="button" class="btn btn-primary">Masuk</a>
            </li>
        @endauth
    </ul>
</nav>
