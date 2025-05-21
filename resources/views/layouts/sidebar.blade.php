<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <img class="navbar-brand-full app-header-logo" src="{{asset(getLogoUrl())}}" width="65"
             alt="Infyom Logo">
        <p class="mb-1"><a href="{{ route('home') }}">{{ html_entity_decode($settings['app_name']) }}</a></p>
        <div class="input-group">
            <input type="text" class="form-control searchTerm ml-3" id="searchText"
                   placeholder="{{ __('messages.common.search').' '.__('messages.common.menu') }}"
                   autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text menu-search-icon mr-3">
                    <i class="fas fa-search search-sign"></i>
                    <i class="fas fa-times close-sign"></i>
                </div>
            </div>
        </div>
        <div class="no-results mt-3"><p>{{ __('messages.common.no_matching_records_found') }}</p></div>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/') }}" class="small-sidebar-text">
            <img class="navbar-brand-full" src="{{ asset(getLogoUrl()) }}" width="50px" alt="{{ $settings['app_name'] }}"/>
        </a>
    </div>
    <ul class="sidebar-menu mt-3">
        <li class="side-menus {{ Request::is('dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                <span>{{ __('messages.dashboard') }}</span></a></li>
    </ul>
    <ul class="sidebar-menu">
        @include('layouts.menu')
        <li class="side-menus {{ Request::is('task-reports*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('task.reports') }}">
                <i class="fas fa-file-export" aria-hidden="true"></i>
                <span>Custom Task Reports</span>
            </a>
        </li>
    </ul>
</aside>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ mix('assets/js/sidebar_menu_search/sidebar_menu_search.js') }}"></script>
