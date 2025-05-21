<div class="card-body">
    <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
    <div class="row">
        <input type="hidden" id="reportId" value="{{ $report->id }}">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('name', __('messages.report.name').':') }}<span class="required">*</span>
                {{ Form::text('name', __('messages.invoice.invoice_from').' '.\Carbon\Carbon::parse($report->start_date)->format('Y-m-d').' '.__('messages.invoice.to').' '.\Carbon\Carbon::parse($report->end_date)->format('Y-m-d'), ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('project_id', __('messages.invoice.project').':') }}<span class="required">*</span>
                {{ Form::select('project_id[]', $projects, $projectIds, ['class' => 'form-control projects-select-box', 'placeholder' => 'Select Project', 'multiple' => true]) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('client', __('messages.invoice.client').':') }}<span class="required">*</span>
                {{ Form::select('client_id[]', $clients, $clientId, ['class' => 'form-control', 'required', 'id' => 'clientSelectBox', 'placeholder' => 'Select Client', 'multiple' => true]) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('tax_id', __('messages.tax.tax').':') }}
                @if(auth()->user()->can('manage_taxes'))
                    <div class="input-group flex-nowrap">
                    {{ Form::select('tax_id', $taxes, null, ['id' => 'taxId' , 'class' => 'form-control tax-select-box', 'placeholder' => 'Select Tax']) }}
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <a href="#" data-toggle="modal" data-target="#AddModal" title="{{ __('messages.tax.new_tax') }}" ><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                @else
                    {{ Form::select('tax_id', $taxes, null, ['id' => 'taxId' , 'class' => 'form-control tax-select-box', 'placeholder' => 'Select Tax']) }}
                @endif
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('issue_date', __('messages.invoice.issue_date').':') }} <span
                        class="required">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('issue_date', \Carbon\Carbon::today()->format('y-m-d'), ['class' => 'form-control issue-datepicker', 'required', 'autocomplete' => 'off']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('due_date', __('messages.invoice.due_date').':') }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    {{ Form::text('due_date', null, ['class' => 'form-control due-datepicker', 'autocomplete' => 'off']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('invoice_number', __('messages.invoice.invoice_number').':') }}<span
                        class="required">*</span>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('messages.invoice.invoice_prefix') }}
                        </div>
                    </div>
                    {{ Form::text('invoice_number', isset($invoice->invoice_number) ? $invoice->invoice_number : generateUniqueInvoiceNumber(), ['class' => 'form-control', 'required', 'id' => 'invoiceNumber']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('discount_apply', __('messages.invoice.discount_apply').':') }}<span
                        class="required">*</span><br>
                {{ Form::select('discount_type', $discountType, null, ['class' => 'form-control', 'id' => 'discountTypeSelect', 'placeholder' => 'Select Discount type','required']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('discount', __('messages.invoice.discount')) }}(<i
                        class="{{ \App\Models\Project::getCurrencyClass($project->currency) }}"></i>)
                <div class="input-group">
                    {{ Form::text('discount', null, ['class' => 'form-control', 'id' => 'discount']) }}
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="{{ \App\Models\Project::getCurrencyClass($project->currency) }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('notes', __('messages.invoice.notes').':') }}
                {{ Form::textarea('notes', null, ['class' => 'form-control invoice-notes textarea-sizing']) }}
            </div>
        </div>
    </div>
</div>
<hr>
<br>
<div>
    <div class="col-12">
        <div class="row justify-content-between">
            <div class="form-group col-md-5 col-lg-3  col-sm-12">

            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="items-container-heading">
                <tr>
                    <th class="invoice-time-entry-task">{{ __('messages.time_entry.task') }}<span
                                class="required">*</span></th>
                    <th class="small-column">{{ __('messages.invoice.hours') }}<span class="required">*</span></th>
                    <th class="text-right invoice-mobile-text-width"><span>{{ __('messages.invoice.task_amount') }}<span
                                    class="required">*</span></span>
                    </th>
                    <th class="invoice-add-item"><a href="#" id="itemAddBtn" title="{{ __('messages.task.add_task') }}"><i class="fas fa-plus"></i></a></th>
                </tr>
                </thead>
                <tbody class="items-container">
                @if($report->report_type != \App\Models\Report::STATIC_REPORT)
                    @foreach($tasks as  $task)
                      
                        @if(count($task->timeEntries))
                            <tr>
                                <td><a href="{{ url('tasks',$task->project->prefix.-$task->task_number) }}"
                                       target="_blank"><input
                                                type="text" name="item[]"
                                                value="{{ isset($task->title) ? html_entity_decode($task->title) : '' }} ({{ html_entity_decode($task->project->name) }})"
                                                class="form-control item-name input-group__icon" required=""
                                                readonly></a>
                                    <input type="text" name="task_id[]"
                                           value="{{ isset($task->id) ? $task->id : '' }}"
                                           class="form-control task-id" hidden>
                                    <input type="text" hidden name="item_project_id[]"
                                           value="{{ isset($task->project_id) ? $task->project_id : '' }}"
                                           class="item_project_id form-control">
                                </td>
                                <td><input type="text" name="hours[]"
                                           value="{{ isset($task->task_hours) ? roundToQuarterHour($task->task_total_minutes) : '1' }}"
                                           class="form-control hours"
                                           min="0" readonly disabled></td>
                                <td class="text-right">
                                    <input type="text" hidden name="task_amount[]"
                                           value="{{ ($task->project->budget_type == \App\Models\Project::FIXED_COST)?'0':$task->taskTotalHours * $task->project->price }}"
                                           class="task-amount">
                                    @if($task->project->budget_type == \App\Models\Project::HOURLY)
                                        <i class="{{ \App\Models\Project::getCurrencyClass($task->project->currency) }}"></i>
                                    @endif {{ ($task->project->budget_type == \App\Models\Project::FIXED_COST)?__('messages.invoice.fix_rate'):$task->taskTotalHours * $task->project->price }}
                                </td>
                                <td>
                                    <input type="hidden" name="fix_rate[]"
                                           value="@if($task->project->budget_type == \App\Models\Project::FIXED_COST){{ $task->project->price }}@endif"
                                           class="fix_rate">
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    @foreach($taskMeta as $key => $task)
                        <tr>
                            <td><a href="{{ url('tasks',$task['project']['prefix'].-$task['task_number']) }}"
                                   target="_blank"><input
                                            type="text" name="item[]"
                                            value="{{ isset($task['name']) ? $task['name'] : '' }} ({{ $task['project']['name'] }})"
                                            class="form-control item-name input-group__icon" required=""
                                            readonly></a>
                                <input type="text" name="task_id[]"
                                       value="{{ isset($task['task_id']) ? $task['task_id'] : '' }}"
                                       class="form-control task-id" hidden>
                                <input type="text" hidden name="item_project_id[]"
                                       value="{{ isset($task['project']['id']) ? $task['project']['id'] : '' }}"
                                       class="item_project_id form-control">
                            </td>
                            <td><input type="text" name="hours[]"
                                       value="{{ isset($task['duration']) ? roundToQuarterHour($task['duration']) : '1' }}"
                                       class="form-control hours"
                                       min="0" readonly disabled></td>
                            <td class="text-right">
                                <input type="text" hidden name="task_amount[]"
                                       value="{{ ($task['project']['budget_type'] == \App\Models\Project::FIXED_COST)?'0':$task['task_total_hour'] * $task['project']['price'] }}"
                                       class="task-amount">

                                @if($task['project']['budget_type'] == \App\Models\Project::HOURLY)
                                    <i class="{{ \App\Models\Project::getCurrencyClass($task['project']['currency']) }}"></i>
                                @endif {{ ($task['project']['budget_type'] == \App\Models\Project::FIXED_COST)?__('messages.invoice.fix_rate'):$task['task_total_hour'] * $task['project']['price'] }}
                            </td>
                            <td>
                                <input type="hidden" name="fix_rate[]"
                                       value="@if($task['project']['budget_type'] == \App\Models\Project::FIXED_COST){{ $task['project']['price'] }}@endif"
                                       class="fix_rate">
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="p-0">
                        <table class="float-right text-right invoice-footer-table">
                            <tr>
                                <td colspan="2"
                                    class="font-weight-bold">{{ __('messages.invoice.total').' '.__('messages.invoice.hours').':' }} </td>
                                <td class="footer-numbers total-hour"><span
                                            id="totalHour">{{ $totalHours }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"
                                    class="font-weight-bold">{{ __('messages.invoice.sub_total').':' }} </td>
                                <td class="footer-numbers sub-total"><i
                                            class="{{ \App\Models\Project::getCurrencyClass($tasks[0]->project->currency) }}"></i>
                                    <span
                                            id="subTotal"> 0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-weight-bold">{{ __('messages.invoice.tax').':' }} </td>
                                <td class="footer-numbers invoice-tax"><span id="invoiceTax">0</span>% &nbsp;(<i
                                            class="{{ \App\Models\Project::getCurrencyClass($tasks[0]->project->currency) }} "></i>
                                    <span class="tax-amount"
                                          id="taxAmount"> 0</span>)
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-size-20px">{{ __('messages.invoice.total').':' }} </td>
                                <td class="footer-numbers"><i
                                            class="{{ \App\Models\Project::getCurrencyClass($tasks[0]->project->currency) }} font-size-20px"></i>
                                    <span
                                            id="netTotal" class="font-size-20px"> 0</span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tfoot>
            </table>
            <input type="text" hidden value="{{ $fixRate }}" id="fixRateAmount">
        </div>
    </div>
    <br>
    <div class="form-group col-sm-12">
        <div class="btn-group dropup open">
            {{ Form::button(__('messages.invoice.save_and_send'), ['class' => 'btn btn-primary', 'id' => 'saveAndSend', 'data-status' => '1']) }}
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-left">
                <li>
                    <a href="#" class="dropdown-item" id="saveAsDraft"
                       data-status="0">{{ __('messages.invoice.save_as_draft') }}</a>
                </li>
            </ul>

        </div>
        <a href="{{ route('invoices.index') }}"
           class="btn btn-light ml-1">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
