<div class="row">
    @if($totalProjects != 0)
        <div class="col-12 d-flex justify-content-end flex-wrap">
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-12 mb-3">
                {{Form::select('drp_status', \App\Models\Project::STATUS, $projectStatus, ['id'=>'projectStatus', 'class'=>'form-control'])  }}
            </div>
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-12 col-12 mb-3 pr-0">
                <input wire:model.debounce.100ms="search" type="search" class="form-control"
                       placeholder="{{ __('messages.common.search') }}"
                       id="search">
            </div>
        </div>
        <div class="col-md-12">
            <div wire:loading id="live-wire-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
    @endif
    @php
        $inStyle = 'style';
        $style = 'border-top: 3px solid';
        $bgColor = 'background-color';
    @endphp
    @forelse($projects as $project)
        <div class="col-12 col-md-6 col-lg-6 col-xl-4 extra-large">
            <div class="livewire-card card shadow mb-5 rounded removeMarginX hover-card">
                <div class="col-md-12">
                    <div class="progress progress-bar-mini height-25 mt-2 project-progress">
                        <div class="progress-bar" role="progressbar"
                             aria-valuenow="" aria-valuemin="0" aria-valuemax="100" {{$inStyle}}="
                    width:{{$project->projectProgress()}}% ; {{$bgColor}}: {{ $project->color }}">
                </div>
                <p class="project-progress-width-text {{ ($project->projectProgress() > 55) ? 'text-white' : 'text-dark' }} {{ ($project->color ==  '#FFFFFF' && $project->projectProgress() > 55) ? 'text-dark' : '' }}">{{number_format($project->projectProgress(),2)}}
                    %</p>
            </div>
        </div>
        <div class="card-header d-flex justify-content-between align-items-center pt-0 pr-3 pb-3 pl-3">
            <div class="d-flex">
                (<small class="{{ $loop->odd ? 'text-primary' : 'text-dark'}}">{{ $project->prefix }}</small>)-
                <a href="{{route('client.projects.show',$project->id)}}"><h4
                            class="{{ $loop->odd ? 'text-primary' : 'text-dark'}} card-report-name">{{ html_entity_decode($project->name) }}</h4>
                </a>
            </div>
        </div>
        <div class="card-body pt-0 pl-3">
            <div>
                <span class="badge {{\App\Models\Project::STATUS_BADGE[$project->status]}} text-uppercase projectStatus">{{ \App\Models\Project::STATUS[$project->status] }}</span>
                <span class="float-right projectStatistics">
                        @if(!empty($project->tasks))
                        <b>{{ $project->tasks_count }} </b>
                        <span>{{__('messages.task.pending')}} {{ __('messages.time_entry.task').'(s)' }}   </span>
                    @endif
                    </span>
            </div>
        </div>
        <div class="card-body d-flex justify-content-between align-items-center pt-0 pl-3 pb-2">
            <div class="d-inline-block">
                @foreach($project->users->where('is_active','=',true) as $counter => $user)
                    @if($counter < 7)
                        <img class="projectUserAvatar p-0"
                             src="{{ $user->img_avatar }}"
                             title="{{ $user->name }}">
                    @elseif($counter == (count($project->users) - 1))
                        <span class="project_remaining_user"><b> + {{ (count($project->users) - 7) }}</b></span>
                    @endif
                @endforeach
            </div>
        </div>
</div>
</div>
@empty
    <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
        <div class="p-2">
            @if(empty($search))
                <p class="text-dark">{{ __('messages.project.no_project_available') }}</p>
            @else
                <p class="text-dark">{{ __('messages.project.no_project_found') }}</p>
            @endif
        </div>
    </div>
@endforelse

<div class="mt-0 mb-5 col-12">
    <div class="row paginatorRow">
        <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
            @if($totalProjects != 0)
                <span class="d-inline-flex">
                    {{ __('messages.common.showing') }} 
                    <span class="font-weight-bold ml-1 mr-1">{{ $projects->firstItem() }}</span> - 
                    <span class="font-weight-bold ml-1 mr-1">{{ $projects->lastItem() }}</span> {{ __('messages.common.of') }} 
                    <span class="font-weight-bold ml-1">{{ $projects->total() }}</span>
                </span>
            @endif
        </div>
        <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
            {{ $projects->links() }}
        </div>
    </div>
</div>
</div>
