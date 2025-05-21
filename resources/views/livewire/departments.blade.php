<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        @if($totalDepartments != 0)
            <div class="pr-0 pl-2 pt-2 pb-2 mb-3">
                <input wire:model.debounce.100ms="search" type="search" class="form-control"
                       placeholder="{{ __('messages.common.search') }}"
                       id="search">
            </div>
        @endif
    </div>
    <div class="col-md-12">
        <div wire:loading id="live-wire-screen-lock">
            <div class="live-wire-infy-loader">
                @include('loader')
            </div>
        </div>
    </div>
    @php
        $inStyle = 'style';
        $style = 'border-top: 3px solid';
    @endphp
    @forelse($departments as $department)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded removeMarginX hover-card"
            {{$inStyle}}="{{$style}} {{ $department->color }}">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
                <h4 class="{{ $loop->odd ? 'text-primary' : 'text-dark'}}"><a href="#" class="show-btn"
                                                                              data-id="{{ $department->id }}">{{ html_entity_decode($department->name) }}</a>
                </h4>
                <a class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                                                            class="notification-toggle mr-1 action-dropdown d-none"><i
                                class="fas fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-list-content dropdown-list-icons">
                            <a href="#" class="dropdown-item dropdown-item-desc edit-btn"
                                   data-id="{{ $department->id }}"><i
                                            class="fas fa-edit mr-2 card-edit-icon"></i> {{ __('messages.common.edit') }}
                                </a>
                                <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                   data-id="{{ $department->id }}"><i
                                            class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
            <div class="p-2">
                @if(empty($search))
                    <p class="text-dark">{{ __('messages.department.no_department_available') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.department.no_department_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

<div class="mt-0 mb-5 col-12">
    <div class="row paginatorRow">
        <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
            @if($totalDepartments != 0)
                <span class="d-inline-flex">
                    {{ __('messages.common.showing') }} 
                    <span class="font-weight-bold ml-1 mr-1">{{ $departments->firstItem() }}</span> - 
                    <span class="font-weight-bold ml-1 mr-1">{{ $departments->lastItem() }}</span> {{ __('messages.common.of') }} 
                    <span class="font-weight-bold ml-1">{{ $departments->total() }}</span>
                </span>
            @endif
        </div>
        <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
            {{ $departments->links() }}
        </div>
    </div>
</div>
</div>
