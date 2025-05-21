<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        @if($totalStatus != 0)
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
    @forelse($status as $ob)
        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded hover-card">
                <div class="card-body d-flex justify-content-between align-items-center p-3 ">
                    <div class="w-75 pb-1">
                        <h6 class="mb-0 {{ $loop->odd ? 'text-primary' : 'text-dark'}}">{{ html_entity_decode($ob->name) }}</h6>
                    </div>
                   
                        <a class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                                                                    class="notification-toggle action-dropdown d-none mr-1"><i
                                        class="fas fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-list-content dropdown-list-icons">
                                    <a href="#" class="dropdown-item dropdown-item-desc edit-btn"
                                       data-id="{{ $ob->id }}"><i
                                                class="fas fa-edit mr-2 card-edit-icon"></i> {{ __('messages.common.edit') }}
                                    </a>
                                    @if($ob->status!=0 && $ob->status!=1)
                                    <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                       data-id="{{ $ob->id }}"><i
                                                class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                    </a>
                                    @endif  
                                </div>
                            </div>
                        </a>
                   
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
            <div class="p-2">
                @if(empty($search))
                    <p class="text-dark">{{ __('messages.status.no_status_available') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.status.no_status_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                @if($totalStatus != 0)
                    <span class="d-inline-flex">
                    {{ __('messages.common.showing') }} 
                    <span class="font-weight-bold ml-1 mr-1">{{ $status->firstItem() }}</span> - 
                    <span class="font-weight-bold ml-1 mr-1">{{ $status->lastItem() }}</span> {{ __('messages.common.of') }} 
                    <span class="font-weight-bold ml-1">{{ $status->total() }}</span>
                </span>
                @endif
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                {{ $status->links() }}
            </div>
        </div>
    </div>
</div>
