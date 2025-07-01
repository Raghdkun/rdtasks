<!DOCTYPE html>
<html>
<head>
    @php
        $settings = getAllSettings();
        $loggedInUserId = getLoggedInUserId();  
    @endphp
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title') | {{ html_entity_decode($settings['app_name']) }}</title>
    <link rel="shortcut icon" href="{{ asset($settings['favicon']) }}" type="image/x-icon" sizes="16x16">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timedropper/1.0/timedropper.css">
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
            @include('layouts.header')
            @include('profile.edit_profile')
            @include('profile.change_password')
            @include('profile.notification_modal')
        </nav>
        <div class="main-sidebar main-sidebar-postion">
            @include('layouts.sidebar')
        </div>
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
            @include('time_tracker.index')
            @include('time_tracker.task_modal')
            @include('time_tracker.adjust_time_entry')
        </div>
        <footer class="main-footer">
            @include('layouts.footer')
        </footer>
    </div>
</div>

</body>
<!-- Add this before closing body tag -->
<!-- Task Rating Popup Modal -->
<div class="modal fade" id="taskRatingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Ratings</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="taskRatingContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <div id="ratingActions">
                    <!-- Action buttons loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTaskRatingPopup(taskId) {
    $('#taskRatingModal').modal('show');
    $('#taskRatingContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#ratingActions').html('');
    
    $.get(`/task-ratings/${taskId}/popup`)
        .done(function(data) {
            $('#taskRatingContent').html(data.content);
            $('#ratingActions').html(data.actions);
        })
        .fail(function() {
            $('#taskRatingContent').html('<div class="alert alert-danger">Error loading rating details</div>');
        });
}
</script>
<script src="{{ asset('assets/js/push.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('assets/js/handlebars.js') }}"></script>
<script src="{{ asset('assets/js/jsrender.js') }}"></script>
<script src="{{ asset('messages.js') }}"></script>

<!-- datedropper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/timedropper/1.0/timedropper.min.js"></script>
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
    let loggedInUserId = "{{ $loggedInUserId }}";
    let myTasksUrl = "{{url('my-tasks')}}";
    let closeWatchImg = "{{asset('assets/img/close.png')}}";
    let stopWatchImg = "{{asset('assets/img/stopwatch.png')}}";
    let timer = "{{asset('assets/img/timer2.gif')}}";
    let baseUrl = "{{url('/')}}/";
    let deleteMessage = "{{ __('messages.common.are_you_sure_delete') }}";
    let yesMessages = "{{ __('messages.common.yes') }}";
    let noMessages = "{{ __('messages.common.no') }}";
    let deleteHeading = "{{ __('messages.common.delete') }}";
    let deleteConfirm = "{{ __('messages.common.delete_confirm') }}";
    let toTypeDelete = "{{ __('messages.common.to_delete_this') }}";
    let deleteWord = "{{ __('messages.common.delete') }}";
    let url = '{{ parse_url(url()->current(),PHP_URL_PATH) }}';
    let loggedInUserName = "{{ Auth::check() ? Auth::user()->name : '' }}";
    let notificationImg = "{{ asset('assets/img/favicon.png') }}";
    let notificationUrl = '{{url('task-details')}}';
    let viewText = '{{__('messages.view')}}';
    let editText = '{{__('messages.common.edit')}}';
    let deleteText = '{{__('messages.common.delete')}}';
    let searchText = '{{__('messages.common.search')}}';
    let showingText = '{{__('messages.common.showing')}}';
    let toText = '{{__('messages.invoice.to')}}';
    let ofText = '{{__('messages.common.of')}}';
    let entriesText = '{{__('messages.common.entries')}}';
</script>
<script src="{{ mix('assets/js/time_tracker/time_tracker.js') }}"></script>
@yield('scripts')
<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
<script src="{{ mix('assets/js/profile/profile.js') }}"></script>
@stack('scripts')
<script>
    $(document).ready(function () {
        $('.alert').delay(4000).slideUp(300);
    });
    var loginUrl = '{{ route('login') }}';
    // Loading button plugin (removed from BS4)
    (function ($) {
        $.fn.button = function (action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false);
            }
        };
    }(jQuery));

</script>
</html>
