<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        @if($totalClients != 0)
            <div class="pr-0 pl-2 pt-2 pb-2">
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
    @forelse($clients as $client)
        <div class="col-12 col-md-6 col-lg-4 col-xl-4 extra-large">
            <div class="livewire-card card author-box shadow mb-5 rounded client-card-view hover-card"
            {{$inStyle}}="{{$style}} {{ $client->department ? $client->department->color : '#6777ef' }}">
            <div class="card-header client-card d-flex align-items-center user-card-index d-sm-flex-wrap-0">
                <div class="author-box-left pl-0 mb-auto uAvatarCon">
                    <img alt="image" width="50" src="{{ $client->avatar }}"
                         class="rounded-circle user-avatar-image uAvatar">
                </div>
                <div class="ml-2 w-100 mb-auto">
                    <div class="justify-content-between d-flex">
                        <div class="user-card-name pb-1">
                            <h4><a href="#" class="show-btn" data-id="{{ $client->id }}">{{ html_entity_decode($client->name) }}</a>
                            </h4>
                            </div>
                            <a class="dropdown dropdown-list-toggle">
                                <a href="#" data-toggle="dropdown"
                                   class="notification-toggle action-dropdown d-none position-xs-bottom">
                                    <i class="fas fa-ellipsis-v action-toggle-mr"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-list-content dropdown-list-icons">
                                        <a href="#" class="dropdown-item dropdown-item-desc edit-btn"
                                           data-id="{{ $client->id }}"><i
                                                    class="fas fa-edit mr-2 card-edit-icon"></i> {{ __('messages.common.edit') }}
                                        </a>
                                        <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                           data-id="{{ $client->id }}"><i
                                                    class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                        </a>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if(!empty($client->email))
                            <div class="client-card-department"><a href="mailto:{{$client->email}}"
                                                                   class="text-decoration-none">{{$client->email}}</a>
                            </div>
                        @endif
                        @if(!empty($client->website ))
                            <div class="card-client-website mb-3"><a href="{{ $client->website }}" target="-_blank"
                                                                     class="text-decoration-none"> {{ $client->website }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
            <div class="p-2">
                @if($search == null || empty($search))
                    <p class="text-dark">{{ __('messages.client.no_client_available') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.client.no_client_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

<div class="mt-0 mb-5 col-12">
    <div class="row paginatorRow">
        <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
            @if($totalClients != 0)
                <span class="d-inline-flex">
                                        {{ __('messages.common.showing') }} 
 
                    <span class="font-weight-bold ml-1 mr-1">{{ $clients->firstItem() }}</span> - 
                    <span class="font-weight-bold ml-1 mr-1">{{ $clients->lastItem() }}</span> {{ __('messages.common.of') }} 
                    <span class="font-weight-bold ml-1">{{ $clients->total() }}</span>
                </span>
            @endif
        </div>
        <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
            {{ $clients->links() }}
        </div>
    </div>
</div>
</div>
