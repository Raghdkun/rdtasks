<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        @if($totalUsers != 0)
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
    @forelse($users as $user)
        <div class="col-12 col-md-6 col-lg-4 col-xl-4 extra-large">
            <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded user-card-view hover-card">
                <div class="card-header d-flex align-items-center user-card-index d-sm-flex-wrap-0">
                    <div class="author-box-left pl-0 mb-auto">
                            <img alt="image" width="50" src="{{ $user->img_avatar }}"
                                 class="rounded-circle user-avatar-image uAvatar">
                        @if(is_null($user->deleted_at))
                        <div class="mt-2 ml-2 userActiveDeActiveChk">
                            @if($user->id != getLoggedInUserId())
                                <label class="custom-switch pl-0" data-placement="bottom"
                                       title="{{ $user->is_active ? __('messages.user.active') : __('messages.user.deactive') }}">
                                    <input type="checkbox" name="is_active" class="custom-switch-input is-active"
                                           data-id="{{ $user->id }}" value="1"
                                           data-class="is_active" {{ $user->is_active ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            @endif
                        </div>
                            @endif
                    </div>
                    <div class="ml-2 w-100 mb-auto">
                        <div class="justify-content-between d-flex">
                            <div class="user-card-name pb-1">
                                <a @if(is_null($user->deleted_at)) href="{{ route('users.show',$user->id) }}" @endif><h4>{{ html_entity_decode(ucfirst($user->name)) }}</h4></a>
                            </div>
                            <a class="dropdown dropdown-list-toggle">
                                <a href="#" data-toggle="dropdown"
                                   class="notification-toggle action-dropdown d-none position-xs-bottom">
                                    <i class="fas fa-ellipsis-v action-toggle-mr"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if(!is_null($user->deleted_at))
                                        <div class="dropdown-list-content dropdown-list-icons">
                                            <a href="#" class="dropdown-item dropdown-item-desc permanent-delete"
                                            data-id="{{ $user->id }}"><i
                                                class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                            </a>
                                        </div>
                                    @else
                                        <div class="dropdown-list-content dropdown-list-icons">
                                            <a href="#" class="dropdown-item dropdown-item-desc edit-btn"
                                               data-id="{{ $user->id }}"><i
                                                        class="fas fa-edit mr-2 card-edit-icon"></i> {{ __('messages.common.edit') }}
                                            </a>
                                            <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                               data-id="{{ $user->id }}"><i
                                                        class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                            </a>
                                        </div>
                                        @endif
                                </div>
                            </a>
                        </div>
                        @if(!empty($user->role_names))
                            <div class="card-client-website ">
                                {{ html_entity_decode($user->role_names) }}
                            </div>
                        @endif
                        <div class="card-user-email pt-1 mb-3">
                            {{ $user->email }}
                            @if(!empty($user->email_verified_at))
                                <span data-toggle="tooltip" title="{{ __('messages.user.email_is_verified') }}"><i
                                            class="fas fa-check-circle email-verified"></i></span>
                            @else
                                <span data-toggle="tooltip" title="{{ __('messages.user.email_is_not_verified') }}"><i
                                            class="fas fa-times-circle email-not-verified"></i></span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body d-flex align-items-center pt-0 pl-3">
                    <div class="mr-3">
                        <span class="badge badge-primary text-uppercase">{{ $user->projects_count }}</span> {{ __('messages.projects') }}
                    </div>
                    <div>
                        <span class="badge badge-dark text-uppercase">{{ $user->user_active_task_count }}</span> {{ __('messages.user.tasks_active') }}
                    </div>
                    @if(empty($user->email_verified_at) && $user->deleted_at == null)
                    <div class="ml-auto">
                        <button class="btn btn-primary btn-sm p-0 pl-1 pr-1 email-btn" data-id="{{ $user->id }}" data-toggle="tooltip" title="Resend Email Verification"><i class="fas fa-sync font-size-12px"></i></button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
            <div class="p-2">
                @if(empty($search))
                    <p class="text-dark">{{ __('messages.user.no_user_found') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.user.no_user_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                @if($totalUsers != 0)
                    <span class="d-inline-flex">
                    {{ __('messages.common.showing') }}
                    <span class="font-weight-bold ml-1 mr-1">{{ $users->firstItem() }}</span> -
                    <span class="font-weight-bold ml-1 mr-1">{{ $users->lastItem() }}</span> {{ __('messages.common.of') }}
                    <span class="font-weight-bold ml-1">{{ $users->total() }}</span>
                </span>
                @endif
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
