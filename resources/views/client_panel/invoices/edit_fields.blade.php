<div class="card-body">
    <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
    <div class="row">
        <input type="hidden" id="hdnInvoiceId" value="{{$invoice->id}}">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('name', __('messages.report.name').':') }}<span class="required">*</span>
                {{ Form::text('name', $invoice->name, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('project_id[]', __('messages.invoice.project').':') }}<span class="required">*</span>
                <select name="project_id" required multiple class="form-control projects-select-box">
                    <option value="">Select Project</option>
                    @foreach($invoice->invoiceClients as $client)
                        @foreach($client->projects as $project)
                            <option class="new-option"
                                    value="{{ $project->id }}" {{ $invoice->invoiceProjects->contains($project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('client', __('messages.invoice.client').':') }}<span class="required">*</span>
                {{ Form::select('client_id[]', $invoiceSyncList['clients'], $invoiceSyncList['clientIds'], ['class' => 'form-control', 'required', 'id' => 'clientSelectBox', 'multiple' => true]) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('tax_id', __('messages.tax.tax').':') }}
                {{ Form::select('tax_id', $invoiceSyncList['taxes'], isset($invoice->tax_id) ? $invoice->tax_id : null, ['class' => 'form-control tax-select-box', 'placeholder' => 'Select Tax']) }}
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
                    {{ Form::text('issue_date', isset($invoice->issue_date) ? $invoice->issue_date : null, ['class' => 'form-control issue-datepicker', 'required', 'autocomplete' => 'off']) }}
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
                    {{ Form::text('due_date', isset($invoice->due_date) ? $invoice->due_date : null, ['class' => 'form-control due-datepicker', 'autocomplete' => 'off']) }}
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
                {{ Form::select('discount_type', $invoiceSyncList['discountType'], isset($invoice->discount_type) ? $invoice->discount_type : null, ['class' => 'form-control', 'id' => 'discountTypeSelect', 'placeholder' => 'Select Discount type','required']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('discount', __('messages.invoice.discount')) }}(<i
                    class="@if(count($invoice->invoiceProjects) > 0){{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else
                    {{ getCurrencyIcon(1) }}
                    @endif "></i>)
                <div class="input-group">
                    {{ Form::text('discount', isset($invoice->discount) ? $invoice->discount : null, ['class' => 'form-control', 'id' => 'discount']) }}
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="@if(count($invoice->invoiceProjects) > 0){{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else
                            {{ getCurrencyIcon(1) }}
                            @endif"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('notes', __('messages.invoice.notes').':') }}
                {{ Form::textarea('notes', isset($invoice->notes) ? $invoice->notes : null, ['class' => 'form-control textarea-sizing invoice-notes']) }}
            </div>
        </div>
    </div>
</div>
<hr>
<br>
<div>
    <div class="col-12">
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
                    <th class="invoice-add-item"><a href="#" id="itemAddBtn"><i class="fas fa-plus"></i></a></th>
                </tr>
                </thead>
                <tbody class="items-container">
                @foreach($invoice->invoiceItems as $invoiceItem)
                    <tr>
                        <td>
                                <input type="text" name="item[]"
                                       value="{{ isset($invoiceItem->item_name) ? $invoiceItem->item_name : '' }}"
                                       class="form-control item-name" required="" readonly>
                            <input type="text" name="task_id[]"
                                   value="{{ isset($invoiceItem->task_id) ? $invoiceItem->task_id : '' }}"
                                   class="form-control task-id" hidden>
                            <input type="text" name="item_project_id[]"
                                   value="{{ isset($invoiceItem->item_project_id) ? $invoiceItem->item_project_id : '' }}"
                                   class="form-control item_project_id" hidden>
                        </td>
                        <td><input type="text" name="hours[]"
                                   value="{{ isset($invoiceItem->hours) ? ($invoiceItem->hours) : '1' }}"
                                   class="form-control hours"
                                   min="0" readonly disabled>
                        </td>
                        <td class="text-right">
                            <input type="text" hidden name="task_amount[]"
                                   value="{{ $invoiceItem->task_amount }}" class="task-amount">
                            @if($invoiceItem->task_amount != 0)
                                <i class="@if(count($invoice->invoiceProjects) > 0) {{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else
                                {{ \App\Models\Project::getCurrencyClass(1) }}
                                @endif "></i>
                            @endif
                            {{ ($invoiceItem->task_amount == 0)?__('messages.invoice.fix_rate'):$invoiceItem->task_amount}}
                        </td>
                        <td><input type="hidden" name="fix_rate[]"
                                   value="{{ isset($invoiceItem->fix_rate)?$invoiceItem->fix_rate:''}}"
                                   class="fix_rate"></td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="p-0">
                        <table class="float-right text-right invoice-footer-table">
                            <tr>
                                <td colspan="2"
                                    class="font-weight-bold">{{ __('messages.invoice.total').' '.__('messages.invoice.hours').':' }} </td>
                                <td class="footer-numbers total-hour"><span
                                        id="totalHour">{{ $invoice->total_hour }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"
                                    class="font-weight-bold">{{ __('messages.invoice.sub_total').':' }} </td>
                                <td class="footer-numbers sub-total"><i
                                        class="@if(count($invoice->invoiceProjects) > 0) {{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else
                                        {{ \App\Models\Project::getCurrencyClass(1) }}
                                        @endif "></i>
                                    <span
                                        id="subTotal"> {{ $invoice->sub_total }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-weight-bold">{{ __('messages.invoice.tax').':' }} </td>
                                <td class="footer-numbers invoice-tax"><span id="invoiceTax">0</span>% &nbsp;(<i
                                        class="@if(count($invoice->invoiceProjects) > 0) {{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else
                                        {{ \App\Models\Project::getCurrencyClass(1) }}
                                        @endif"></i>
                                    <span class="tax-amount"
                                          id="taxAmount"> 0</span>)
                                </td>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-size-20px">{{ __('messages.invoice.total').':' }} </td>
                                <td class="footer-numbers"><i
                                        class="@if(count($invoice->invoiceProjects) > 0) {{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else
                                        {{ \App\Models\Project::getCurrencyClass(1) }}
                                        @endif font-size-20px"></i>
                                    <span
                                            id="netTotal" class="font-size-20px">{{ $invoice->amount }}</span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tfoot>
            </table>
            <input type="text" hidden value="{{ $countFixRate }}" id="fixRateAmount">
        </div>
    </div>
    <br>
    <div class="form-group col-sm-12">
        <div class="btn-group dropup open">
            @if($invoice->status == \App\Models\Invoice::STATUS_SENT)
                {{ Form::button(__('messages.common.save'), ['class' => 'btn btn-primary', 'id' => 'editSaveAndSend', 'data-status' => '1', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
            @else
                {{ Form::button(__('messages.common.save'), ['class' => 'btn btn-primary', 'id' => 'editSaveAndSend', 'data-status' => '2', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
            @endif
        </div>
        <a href="{{ route('client.invoices.index') }}"
           class="btn btn-secondary text-dark ml-2">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
