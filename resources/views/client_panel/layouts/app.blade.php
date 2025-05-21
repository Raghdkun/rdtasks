<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title') | {{ html_entity_decode(getAppName()) }}</title>
    <link rel="shortcut icon" href="{{ asset(getSettingValue('favicon')) }}" type="image/x-icon" sizes="16x16">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 4.1.1 -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Ionicons -->
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/sweetalert.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-toggle.min.css') }}">
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.css') }}">
    <link href="//fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" type="text/css"/>

@yield('page_css')
<!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/components.css')}}">
    <link href="{{ mix('assets/style/css/infy-loader.css') }}" rel="stylesheet" type="text/css"/>
    @yield('page_css')

    <link href="{{mix('assets/style/css/style.css')}}" rel="stylesheet" type="text/css"/>

    @yield('css')
</head>
<body>

<div id="app">
    <div class="infy-loader" id="overlay-screen-lock">
        @include('loader')
    </div>
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <nav class="navbar navbar-expand-lg main-navbar">
            @include('client_panel.layouts.header')
            @include('client_panel.profile.edit_profile')
            @include('client_panel.profile.change_password')
        </nav>
        <div class="main-sidebar main-sidebar-postion">
            @include('client_panel.layouts.sidebar')
        </div>
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
        <footer class="main-footer">
            @include('client_panel.layouts.footer')
        </footer>
    </div>
</div>

</body>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('assets/js/handlebars.js') }}"></script>
<script src="{{ asset('assets/js/jsrender.js') }}"></script>
<script src="{{ asset('messages.js') }}"></script>
<!-- Template JS File -->
<script src="{{ asset('web/js/stisla.js') }}"></script>
<script src="{{ asset('web/js/scripts.js') }}"></script>
@routes
@yield('page_js')
<script>
    let languageName = '{{ Auth::user()->language }}';
    Lang.setLocale(languageName)
</script>
<script src="{{mix('assets/js/custom.js')}}" type="text/javascript"></script>
<script>
    let deleteMessage = "{{ __('messages.common.are_you_sure_delete') }}"
    let yesMessages = "{{ __('messages.common.yes') }}"
    let noMessages = "{{ __('messages.common.no') }}"
    let deleteHeading = "{{ __('messages.common.delete') }}"
    let deleteConfirm = "{{ __('messages.common.delete_confirm') }}"
    let toTypeDelete = "{{ __('messages.common.to_delete_this') }}"
    let deleteWord = "{{ __('messages.common.delete') }}"
    let loggedInUserId = "{{ getLoggedInUserId() }}"
    let stopWatchImg = "{{asset('assets/img/stopwatch.png')}}"
    let searchText = '{{__('messages.common.search')}}'
</script>
@yield('scripts')
<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
<script src="{{ mix('assets/js/clients/profile/profile.js') }}"></script>
<script src="{{ mix('assets/js/time_tracker/time_tracker.js') }}"></script>
<script>
    // Loading button plugin (removed from BS4)
    (function ($) {
        $.fn.button = function (action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true)
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false)
            }
        }
    }(jQuery));

</script>
</html>
