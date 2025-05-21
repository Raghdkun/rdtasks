<div class="preview-wrapper">
    <div class="section-header">
        <h1 class="page__heading">{{ __('messages.report.report_details') }}</h1>
        <div class="filter-container section-header-breadcrumb d-md-flex justify-content-end">
            <button class="btn btn-primary save-btn"
                    data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">{{ __('messages.common.save') }}</button>
            <a class="btn btn-light ml-2 cancel-btn" href="#">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
    <div class="section-body">
        @include('flash::message')
        <div class="row">
            <div class="col-lg-12">
                @include('reports.report_format')
                @include('tasks.task_details')
            </div>
        </div>
    </div>
</div>
<script src="{{ mix('assets/js/task/task_time_entry.js') }}"></script>
