<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <img class="navbar-brand-full app-header-logo" src="{{asset(getLogoUrl())}}" width="65"
             alt="Infyom Logo">
        <p class="mb-1"> <a href="{{ route('dashboard') }}">{{ getAppName() }}</a></p>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/') }}" class="small-sidebar-text">
            <img class="navbar-brand-full" src="{{ asset(getLogoUrl()) }}" width="50px" alt="{{ getAppName() }}"/>
        </a>
    </div>
    <ul class="sidebar-menu">
        <li class="side-menus {{ Request::is('client/dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                <span>{{ __('messages.dashboard') }}</span></a></li>
    </ul>
    <ul class="sidebar-menu">
        @include('client_panel.layouts.menu')
    </ul>
</aside>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
