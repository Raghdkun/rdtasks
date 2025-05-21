<html>
<head>
    <title>{{ __('messages.projects') }}</title>
    <link rel="shortcut icon" href="{{ asset(getSettingValue('favicon')) }}" type="image/x-icon" sizes="16x16">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 4.1.1 -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-toggle.min.css') }}">
    <link href="{{mix('assets/style/css/style.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/components.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/style/css/login-page.css') }}">
</head>
<body class="bg-image">
<div class="container mt-5">
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.projects') }}</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $inStyle = 'style';
                                    $style = 'border-top: 3px solid';
                                    $bgColor = 'background-color';
                                @endphp
                                @forelse($projects as $project)
                                    <div class="col-12 col-md-6 col-lg-6 col-xl-4 extra-large">
                                        <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded removeMarginX" {{$inStyle}}="{{$style}} {{ $project->color }}">
                                        <div class="col-md-12">
                                            <div class="progress progress-bar-mini height-25 mt-2 project-progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                     role="progressbar" aria-valuenow="" aria-valuemin="0"
                                                     aria-valuemax="100" {{$inStyle}}="
                                                width:{{$project->projectProgress()}}% ; {{$bgColor}}
                                                : {{ $project->color }}">
                                            </div>
                                            <p class="project-progress-width-text {{ ($project->projectProgress() > 55) ? 'text-white' : 'text-dark' }}">{{number_format($project->projectProgress(),2)}}%</p>
                                        </div>
                                    </div>
                                    <div class="card-header d-flex justify-content-between align-items-center pt-0 pr-3 pb-3 pl-3">
                                        <div class="d-flex">
                                            (<small class="{{ $loop->odd ? 'text-primary' : 'text-dark'}}">{{ $project->prefix }}</small>)-
                                            <a href="#"><h4
                                                        class="{{ $loop->odd ? 'text-primary' : 'text-dark'}} card-report-name">{{ $project->name }}</h4>
                                            </a>
                                        </div>
                                        <a class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                                                                                    class="notification-toggle action-dropdown d-none mr-1"><i
                                                        class="fas fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-list-content dropdown-list-icons">
                                                    <a href="#" class="dropdown-item dropdown-item-desc edit-btn"
                                                       data-id="{{ $project->id }}"><i
                                                                class="fas fa-edit mr-2 card-edit-icon"></i> {{ __('messages.common.edit') }}
                                                    </a>
                                                    <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                                       data-id="{{ $project->id }}"><i
                                                                class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="card-body pt-0 pl-3">
                                        <div>
                                            <span class="badge {{\App\Models\Project::STATUS_BADGE[$project->status]}} text-uppercase projectStatus">{{ \App\Models\Project::STATUS[$project->status] }}</span>
                                            <span class="float-right projectStatistics">
                                                    <b>{{ count($project->tasks->where('status', '=', \App\Models\Task::$status['STATUS_ACTIVE']))}} </b> <span>{{ __('messages.task.open_task') }} | </span>
                    </span>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        @empty
                            <div class="mt-0 mb-5 col-12 d-flex justify-content-center shadow mb-5 rounded">
                                <div class="p-2">
                                        <p class="text-dark">{{ __('messages.project.no_project_found') }}</p>
                                </div>
                            </div>
                        @endforelse

                    </div>

                </div>
            </div>
        </div>


</section>
</div>
</body>
</html>
