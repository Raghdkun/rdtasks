<div class="row">
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{__('messages.time_entry.date')}} :</label>
        <p>{{\Carbon\Carbon::parse($expense->date)->translatedFormat('jS M, Y')}}</p>
    </div>
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{ __('messages.invoice.amount')}} :</label>
        <p><i class="{{ \App\Models\Project::getCurrencyClass($expense->project->currency) }}"></i> {{ number_format($expense->amount,2) }}</p>
    </div>
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{__('messages.expense.category')}} :</label>
        <p>{{\App\Models\Expense::CATEGORY[$expense->category]}}</p>
    </div>
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{__('messages.report.client')}} :</label>
        <p>{{html_entity_decode($expense->client->name)}}</p>
    </div>
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{__('messages.report.project')}} :</label>
        <p>{{html_entity_decode($expense->project->name)}}</p>
    </div>
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{__('messages.common.created_by')}} :</label>
        <p>{{!empty($expense->user->name) ? html_entity_decode($expense->user->name) : __('messages.common.n/a')}}</p>
    </div>
    <div class="form-group col-md-4">
        <label class="font-weight-bold">{{__('messages.expense.finance')}} :</label><br>
        @if($expense->billable == 1)
            <p class="badge badge-info font-size-12px pl-2 pr-2">{{__('messages.expense.billable')}}</p>
        @else
            <p class="badge badge-danger font-size-12px pl-2 pr-2">{{__('messages.common.not')}} {{__('messages.expense.billable')}}</p>

        @endif
    </div>
    <div class="form-group col-sm-4">
        {{ Form::label('created_at', __('messages.common.created_on').(':'),['class'=>'font-weight-bold']) }}
        <br>
        <p><span data-toggle="tooltip" data-placement="right"
                 title="{{ \Carbon\Carbon::parse($expense->created_at)->translatedFormat('jS M, Y') }}">{{ $expense->created_at->diffForHumans() }}</span>
        </p>
    </div>
    <div class="form-group col-sm-4">
        {{ Form::label('created_at', __('messages.common.last_updated').(':'),['class'=>'font-weight-bold']) }}
        <br>
        <p><span data-toggle="tooltip" data-placement="right"
                 title="{{ \Carbon\Carbon::parse($expense->updated_at)->translatedFormat('jS M, Y') }}">{{ $expense->updated_at->diffForHumans() }}</span>
        </p>
    </div>
    <div class="form-group col-md-12">
        <label class="font-weight-bold"> {{__('messages.common.description')}} :</label>
        <div class="expense-attachments-row">
            {!! (!empty($expense->description)) ? html_entity_decode($expense->description) : __('messages.common.n/a') !!}
        </div>
    </div>
    <div class="form-group col-md-12 col-sm-12 col-lg-12">
        <label class="font-weight-bold">{{__('messages.task.attachments')}} :</label>
        <div class="row expense-attachments-row">
            @if(count($expense->media) > 0)
                @foreach($expense->media as $media)
                    <div class="col-md-1 col-sm-1 mb-3">
                        <a href="{{ $media->getFullUrl() }}" target="_blank">
                            <img id='previewImage' class="img-thumbnail expense-thumbnail-preview mr-4"
                                 src="{{ mediaUrlEndsWith($media->getFullUrl()) }}"/>
                        </a>
                        <br>
                        <a href="{{ route('expenses.download.attachment', $media->id) }}" download target="_blank"><i
                                    class="fas fa-download text-info" title="{{__('messages.expense.download')}}"></i></a>
                    </div>
                @endforeach
            @else
                <p class="ml-3">{{__('messages.common.n/a')}}</p>
            @endif
        </div>
    </div>
</div>
