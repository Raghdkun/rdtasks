<div class="card-body">
    <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
    <div class="row">
        <div class="col-lg-3 col-sm-6 form-group">
            <span class="font-weight-bold">{{ __('messages.report.name').':' }} </span>
            <p class="d-table-cell">{{ $invoice->name }}</p>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{ __('messages.project.client').':' }} </span>
            <p class="d-table-cell">
                @forelse($invoice->invoiceClients->pluck('name') as $client)
                    {{$loop->first ? '':', '}}
                    <span>{{$client}}</span>
                @empty
                    <span>{{ __('messages.common.n/a') }}</span>
                @endforelse
            </p>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{ __('messages.invoice.invoice_number').':' }} </span>
            <p class="d-table-cell">{{ __('messages.invoice.invoice_prefix') }}{{ $invoice->invoice_number }}</p>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{ __('messages.task.project').':' }} </span>
            <p class=" d-table-cell">
                @forelse($invoice->invoiceProjects->pluck('name') as $project)
                    {{$loop->first ? '':', '}}
                    <span>{{$project}}</span>
                @empty
                    <span>{{ __('messages.common.n/a') }}</span>
                @endforelse
            </p>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{ __('messages.invoice.issue_date').':' }} </span><br>
            <span
                class=" d-table-cell">{{ isset($invoice->issue_date) ? Carbon\Carbon::parse($invoice->issue_date)->translatedFormat('jS M, Y') : 'N/A' }}</span>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{ __('messages.invoice.due_date').':' }} </span><br>
            <span
                class=" d-table-cell">{{ isset($invoice->due_date) ? Carbon\Carbon::parse($invoice->due_date)->translatedFormat('jS M, Y') : 'N/A' }}</span>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{ __('messages.tax.tax').':' }} </span>
            <p class=" d-table-cell">{{ isset($invoice->tax_id) ? $invoice->tax->name.'('.$invoice->tax->tax.'%)' : 'N/A' }}</p>
        </div>
        <div class="col-sm-3 form-group">
            <span class="font-weight-bold">{{__('messages.common.created_on').':'}} </span><br>
            <span data-toggle="tooltip" data-placement="right"
                  title="{{ date('jS M, Y', strtotime($invoice->created_at)) }}">{{ $invoice->created_at->diffForHumans() }}</span>
        </div>
        <div class="col-sm-3 form-group">
            <b class="font-weight-bold">{{__('messages.common.last_updated').':'}} </b><br>
            <span data-toggle="tooltip" data-placement="right"
                  title="{{ date('jS M, Y', strtotime($invoice->updated_at)) }}">{{ $invoice->updated_at->diffForHumans() }}</span>
        </div>
        <div class="col-sm-3 form-group">
            <b class="font-weight-bold">{{__('messages.invoice.status').':'}} </b><br>
            @if($invoice->status == \App\Models\Invoice::STATUS_DRAFT)
                <span class="text-danger">{{ $invoice->statusText }}</span>
            @elseif($invoice->status == \App\Models\Invoice::STATUS_SENT)
                <span class="text-primary">{{ $invoice->statusText }}</span>
            @else
                <span class="text-success">{{ $invoice->statusText }}</span>
            @endif
        </div>
        <div class="col-sm-12 form-group">
            <b class="font-weight-bold">{{ __('messages.invoice.notes').':' }} </b>
            <p>{!! isset($invoice->notes) ? nl2br(e($invoice->notes)) : 'N/A' !!}</p>
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
                    <th>{{ __('messages.time_entry.task') }}</th>
                    <th class="small-column">{{ __('messages.invoice.hours') }}</th>
                    <th class="small-column text-right">{{ __('messages.invoice.task_amount') }}</th>
                </tr>
                </thead>
                <tbody class="items-container">
                @foreach($invoice->invoiceItems as $item)
                    <tr>
                        <td><span>{{ isset($item->item_name) ? $item->item_name : '' }}</span></td>
                        <td><span>{{ isset($item->hours) ? $item->hours : '0.0' }}</span></td>
                        <td class="text-right">
                            <span>
                                 @if($item->task_amount != '0')
                                    <i class="@if(count($invoice->invoiceProjects) > 0){{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }} @else {{ \App\Models\Project::getCurrencyClass(1) }}
                                    @endif"></i>
                                @endif
                                {{ ($item->task_amount != '0') ? number_format($item->task_amount, 2) : __('messages.invoice.fix_rate') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="p-0">
                        <table class="float-right text-right invoice-footer-table">
                            <tr>
                                <td colspan="2"
                                    class="font-weight-bold">{{ __('messages.invoice.sub_total').':' }} </td>
                                <td class="footer-numbers sub-total"><i
                                        class="@if(count($invoice->invoiceProjects) > 0) {{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }}  @else
                                        {{ \App\Models\Project::getCurrencyClass(1) }}
                                        @endif "></i>
                                    <span
                                        id="subTotal">{{ number_format($invoice->sub_total, 2) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-weight-bold">{{ __('messages.invoice.tax').':' }} </td>
                                <td class="footer-numbers invoice-tax"><span id="invoiceTax">{{ isset($invoice->tax_id) ? $invoice->tax->tax : '0' }}%</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-weight-bold">{{ __('messages.invoice.discount').':' }} </td>
                                <td class="footer-numbers"><i
                                        class=" @if(count($invoice->invoiceProjects) > 0)
                                        {{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }}
                                        @else
                                        {{ \App\Models\Project::getCurrencyClass(1) }}
                                        @endif
                                            "></i><span>
                                @if(isset($invoice->discount))
                                            {{ number_format($invoice->discount, 2) }}
                                        @else
                                            {{null}}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="font-size-20px">{{ __('messages.invoice.total').':' }} </td>
                                <td class="footer-numbers"><i
                                        class="@if(count($invoice->invoiceProjects) > 0){{ \App\Models\Project::getCurrencyClass($invoice->invoiceProjects[0]->currency) }}  @else  {{ \App\Models\Project::getCurrencyClass(1) }}
                                        @endif font-size-20px"></i>
                                    <span
                                        id="netTotal"
                                        class="font-size-20px">{{ isset($invoice->amount) ? number_format($invoice->amount, 2) : null }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
