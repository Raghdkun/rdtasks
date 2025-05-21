<form class="form-inline mr-auto">
    <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
</form>
<ul class="navbar-nav navbar-right">
    <li class="dropdown dropdown-list-toggle mt-1"><a href="#" data-toggle="dropdown"
                                                      class="nav-link notification-toggle nav-link-lg"
                                                      title="{{__('messages.notification.notifications')}}">
            <i class="far fa-bell"></i></a>
        <div class="dropdown-menu dropdown-list dropdown-menu-right" id="notification">
            <div class="dropdown-header">
                <div class="row justify-content-between">
                    <div class="px-3">{{__('messages.notification.notifications')}}</div>
                    <div class="px-3" id="allRead">
                        <a href="#" class="text-decoration-none">{{__('messages.notification.mark_all_as_read')}}</a>
                    </div>
                </div>
            </div>
            <div class="dropdown-list-content dropdown-list-icons notification-content">
                <div class="empty-state empty-notification d-none" data-height="300" style="padding: 0px 40px;">
                    <div class="empty-state-icon">
                        <i class="fas fa-question mt-4"></i>
                    </div>
                    <h2>{{__('messages.notification.empty_notifications')}}</h2>
                </div>
            </div>
        </div>
    </li>
    <li class="dropdown language-menu ml-3">
        <a href="#" class="dropdown-toggle text-white text-decoration-none"
           data-toggle="dropdown" role="button" title="{{ __('messages.user.change_language') }}">
            {{ strtoupper(getCurrentLanguageName()) }}&nbsp;
            <span class="caret"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right w-100" role="menu">
            @foreach(getUserLanguages() as $key => $value)
                <span class="language-item"><a href="javascript:void(0)"
                                               class="changeLanguage mb-1 dropdown-item {{getCurrentLanguageName() == $key ? 'active' : ''}}"
                                               data-prefix-value="{{ $key }}">{{ $value }}</a></span>
            @endforeach
        </div>
    </li>
    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="{{ Auth::user()->client->avatar }}"
                 class="rounded-circle mr-1 thumbnail-rounded user-thumbnail">
            <div class="d-sm-none d-lg-inline-block">{{ html_entity_decode(Auth::user()->name) }}</div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a href="#" class="dropdown-item has-icon btn-flat edit-profile" data-id="{{ getLoggedInUserId() }}">
                <i class="far fa-user"></i> {{ __('messages.user.profile') }}
            </a>
            <a href="#" class="dropdown-item has-icon btn-flat changePasswordModal" data-id="{{ getLoggedInUserId() }}">
                <i class="fa fa-key"></i>{{ __('messages.user.change_password') }}
            </a>
            <a href="{{ url('/logout') }}" class="dropdown-item text-danger has-icon"
               onclick="event.preventDefault(); localStorage.clear(); document.getElementById('logout-form').submit();">
                <i class="fa fa-lock"></i>{{ __('messages.user.logout') }}
            </a>
            <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="display-none">
                {{ csrf_field() }}
            </form>
        </div>
    </li>
</ul>
