
@can('manage_users')
<li class="side-menus {{ Request::is('users*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('users.index') }}">
        <i class="fas fa-users " aria-hidden="true"></i><span>{{ __('messages.users') }}</span>
    </a>
</li>
@endcan

{{-- Generals Dropdown --}}
<li class="side-menus nav-item dropdown">
    <a href="#" class="nav-link has-dropdown"><i class="fas fa-home"></i>
        <span>Generals</span></a>
    <ul class="dropdown-menu side-menus">
        {{-- Calendar (Public) --}}
        <li class="side-menus {{ Request::is('time-entries-calendar*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('time-entries-calendar') }}">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i><span>{{ __('messages.calendar') }}</span>
            </a>
        </li>
        
        {{-- Reports (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('report*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('reports') }}">
                    <i class="fas fa-file" aria-hidden="true"></i><span>{{ __('messages.reports') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Archived Users (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('archived-users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('archived-users') }}">
                    <i class="fas fa-users-slash" aria-hidden="true"></i><span>{{ __('messages.archived_users') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Sales (Commented for now) --}}
        {{-- @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-wallet"></i>
                    <span>{{__('messages.common.sales')}}</span></a>
                <ul class="dropdown-menu side-menus">
                    <li class="side-menus {{ Request::is('invoice*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('invoices') }}">
                            <i class="fas fa-file-invoice" aria-hidden="true"></i><span>{{ __('messages.invoices') }}</span>
                        </a>
                    </li>
                    <li class="side-menus {{ Request::is('expenses*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('expenses.index') }}">
                            <i class="fas fa-rupee-sign" aria-hidden="true"></i><span>{{__('messages.expenses')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif --}}
        
        {{-- Activity Logs (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('activity-logs*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('activity-logs') }}">
                    <i class="fas fa-clipboard-check" aria-hidden="true"></i><span>{{ __('messages.activity_log.activity_logs') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Events (Public) --}}
        <li class="side-menus {{ Request::is('events*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('events.index') }}">
                <i class="fas fa-calendar-day" aria-hidden="true"></i><span>{{__('messages.events')}}</span>
            </a>
        </li>
    </ul>
</li>

{{-- Platform Dropdown --}}
<li class="side-menus nav-item dropdown">
    <a href="#" class="nav-link has-dropdown"><i class="fas fa-cogs"></i>
        <span>Platform</span></a>
    <ul class="dropdown-menu side-menus">
        {{-- Projects (Public) --}}
        <li class="side-menus {{ Request::is('projects*') || Request::is('user-assign-projects*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('projects.index') }}">
                <i class="fas fa-folder-open" aria-hidden="true"></i><span>{{ __('messages.projects') }}</span>
            </a>
        </li>
        
        {{-- Departments (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('departments*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('departments.index') }}">
                    <i class="fas fa-building"></i><span>{{ __('messages.departments') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Clients (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('clients*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('clients.index') }}">
                    <i class="fas fa-user-tie" aria-hidden="true"></i><span>{{ __('messages.clients') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Users (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-users" aria-hidden="true"></i><span>{{ __('messages.users') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Roles (Admin Only) --}}
        @if(getLoggedInUser()->hasRole('Admin'))
            <li class="side-menus {{ Request::is('roles*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('roles') }}">
                    <i class="fas fa-user" aria-hidden="true"></i><span>{{ __('messages.roles') }}</span>
                </a>
            </li>
        @endif
    </ul>
</li>

{{-- Schedule Dropdown --}}
<li class="side-menus nav-item dropdown">
    <a href="#" class="nav-link has-dropdown"><i class="fas fa-calendar"></i>
        <span>Schedule</span></a>
    <ul class="dropdown-menu side-menus">
        {{-- My Days Off (Public) --}}
        <li class="side-menus {{ Request::is('user-days-off*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user-days-off.index') }}">
                <i class="fas fa-calendar-check" aria-hidden="true"></i><span>My Days Off</span>
            </a>
        </li>
        
        {{-- Team Schedule (Public) --}}
        <li class="side-menus {{ Request::is('days-off-public*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('days-off-public.index') }}">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i><span>Team Schedule</span>
            </a>
        </li>
    </ul>
</li>

{{-- KPI Dropdown --}}
@if(getLoggedInUser()->hasRole('Admin'))
    <li class="side-menus nav-item dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-chart-line"></i>
            <span>KPI</span></a>
        <ul class="dropdown-menu side-menus">
            {{-- Team KPI (Admin Only) --}}
            <li class="side-menus {{ Request::is('team-kpi*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('team-kpi.index') }}">
                    <i class="fas fa-clipboard-list" aria-hidden="true"></i><span>Team KPI</span>
                </a>
            </li>
            
            {{-- Task Ratings (Admin Only) --}}
            <li class="side-menus {{ Request::is('task-ratings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('task-ratings.index') }}">
                    <i class="fas fa-star" aria-hidden="true"></i><span>Task Ratings</span>
                </a>
            </li>
            
            {{-- Client Ratings (Admin Only) --}}
            <li class="side-menus {{ Request::is('client-ratings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('client-ratings.index') }}">
                    <i class="fas fa-star-half-alt" aria-hidden="true"></i><span>Client Ratings</span>
                </a>
            </li>
        </ul>
    </li>
@endif

{{-- Keep remaining public items as they are --}}
@can('manage_all_tasks')
    <li class="side-menus nav-item dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-tasks"></i>
            <span>{{ __('messages.tasks') }}</span></a>
        <ul class="dropdown-menu side-menus">
            <li class="side-menus {{ Request::is('tasks*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('kanban.index') }}">
                    <i class="fas fa-tasks" aria-hidden="true"></i><span>{{ __('messages.tasks') }}</span>
                </a>
            </li>
            
            @can('manage_status')
                <li class="side-menus {{ Request::is('status*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('status.index') }}">
                        <i class="fas fa-columns" aria-hidden="true"></i><span>{{ __('messages.status.status') }}</span>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

{{-- Days Off Management (Admin Only) --}}
@if(getLoggedInUser()->hasRole('Admin'))
    <li class="side-menus nav-item dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-calendar-times"></i>
            <span>Days Off Management</span></a>
        <ul class="dropdown-menu side-menus">
            <li class="side-menus {{ Request::is('days-off-settings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('days-off-settings.index') }}">
                    <i class="fas fa-cog" aria-hidden="true"></i><span>Settings</span>
                </a>
            </li>
            <li class="side-menus {{ Request::is('days-off-admin*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('days-off-admin.index') }}">
                    <i class="fas fa-tasks" aria-hidden="true"></i><span>Manage Requests</span>
                </a>
            </li>
        </ul>
    </li>
@endif

{{-- Settings (Keep existing structure) --}}
@canany(['manage_activities','manage_tags'])
    <li class="side-menus nav-item dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog" aria-hidden="true"></i>
            <span>{{ __('messages.settings') }}</span></a>
        <ul class="dropdown-menu side-menus">
            @can('manage_tags')
                <li class="side-menus {{ Request::is('tags*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('tags.index') }}">
                        <i class="fas fa-tags" aria-hidden="true"></i><span>{{ __('messages.tags') }}</span>
                    </a>
                </li>
            @endcan
            @can('manage_activities')
                <li class="side-menus {{ Request::is('activity-types*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('activity-types.index') }}">
                        <i class="fas fa-clipboard-list" aria-hidden="true"></i><span>{{ __('messages.activity_types') }}</span>
                    </a>
                </li>
            @endcan
            @can('manage_taxes')
                <li class="side-menus {{ Request::is('taxes*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('taxes.index') }}">
                        <i class="fas fa-percent" aria-hidden="true"></i><span>{{ __('messages.taxes') }}</span>
                    </a>
                </li>
            @endcan
            @can('manage_settings')
                <li class="side-menus {{ Request::is('settings*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('settings.edit') }}">
                        <i class="fas fa-user-cog" aria-hidden="true"></i><span>{{ __('messages.settings') }}</span>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

