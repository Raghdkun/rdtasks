<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        <div class="pr-0 pl-2 pt-2 pb-2">
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
    @forelse($roles as $role)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded removeMarginX hover-card">
                <div class="card-header d-flex justify-content-between align-items-center p-3 role-cards">
                    <a href="{{ route('roles.show',$role->id) }}"><h4
                                class="{{ $loop->odd ? 'text-primary' : 'text-dark'}} ml-2">{{ html_entity_decode($role->name) }}</h4></a>
                    <a class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                                                                class="notification-toggle action-dropdown d-none mr-1"><i
                                    class="fas fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-list-content dropdown-list-icons">
                                <a href="{{ route('roles.edit', ['role' => $role->id]) }}"
                                   class="dropdown-item dropdown-item-desc edit-btn"
                                   data-id="{{ $role->id }}"><i
                                            class="fas fa-edit mr-2 card-edit-icon"></i> {{ __('messages.common.edit') }}
                                </a>
                                <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                   data-id="{{ $role->id }}"><i
                                            class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center p-2 mt-3">
                    <div>
                        @if(!$role->permissions->where('name','=','role_client')->count())
                        <span class="total-permission-count"><big
                                    class="font-weight-bold">{{ $role->permissions->where('name','=','role_client')->count() ? 0 : $role->permissions_count }} </big>{{ __('messages.role.permissions') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
            <div class="p-2">
                @if(empty($search))
                    <p class="text-dark">{{ __('messages.role.no_role_available') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.role.no_role_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                <span class="d-inline-flex">
                    {{ __('messages.common.showing') }}
                    <span class="font-weight-bold ml-1 mr-1">{{ $roles->firstItem() }}</span> -
                    <span class="font-weight-bold ml-1 mr-1">{{ $roles->lastItem() }}</span> {{ __('messages.common.of') }}
                    <span class="font-weight-bold ml-1">{{ $roles->total() }}</span>
                </span>
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
