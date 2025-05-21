@extends('layouts.app')
@section('title')
    Task Reports
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Task Reports</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('task.report.export') }}" class="btn btn-primary form-btn mr-3">
                    Export All Tasks <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('task.report.export') }}" method="GET">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="project_id">Project:</label>
                                <select name="project_id" id="project_id" class="form-control">
                                    <option value="">All Projects</option>
                                    @foreach($projects as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="status">Status:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="priority">Priority:</label>
                                <select name="priority" id="priority" class="form-control">
                                    <option value="">All Priorities</option>
                                    @foreach($priorities as $key => $priority)
                                        <option value="{{ $key }}">{{ $priority }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="user_id">Assigned To:</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Date Range:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="date_range" id="date_range">
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_js')
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
    <script>
        $(function() {
            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
            });
        });
    </script>
@endsection