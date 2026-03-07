<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row ">
    <div class=" navbar-brand-wrapper d-flex align-items-center justify-content-center p-2">
        <a class="navbar-brand brand-logo  d-flex flex-row align-items-center" style="font-size: 1.2rem"
            href={{ route('admin.dashboard') }}>
            <img src="{{ asset('assets/images/logo-mi.png') }}" alt="logo" />
            <span class="text-success ml-2 font-weight-bold brand-logo">MI Miftahul Ulum</span>
        </a>

    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">

            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="https://i.pinimg.com/736x/8b/47/03/8b4703c39d86010958b30f777bd7259d.jpg" alt="profile" />
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item" href={{ route('logout') }}>
                            <i class="ti-power-off text-primary"></i>
                            Logout
                        </button>

                    </form>
                </div>
            </li>

        </ul>

    </div>
</nav>
