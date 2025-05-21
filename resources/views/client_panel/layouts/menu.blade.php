<li class="side-menus {{ Request::is('client/projects*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ url('client/projects') }}">
        <i class="fas fa-folder-open" aria-hidden="true"></i><span>{{ __('messages.projects') }}</span>
    </a>
</li>
<li class="side-menus {{ Request::is('client/invoices*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ url('client/invoices') }}">
        <i class="fas fa-file-invoice " aria-hidden="true"></i><span>{{ __('messages.invoices') }}</span>
    </a>
</li>
