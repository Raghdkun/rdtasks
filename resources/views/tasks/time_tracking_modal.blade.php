<div class="modal fade" id="timeTrackingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.task.time_tracking') }}</h5>
                <button type="button" class="close outline-none" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body overflow-auto">
                <table class="table table-hover time-tracker-table">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('messages.task.note') }}</th>
                        <th scope="col">{{ __('messages.task.activity') }}</th>
                        <th scope="col">{{ __('messages.task.start_time') }}</th>
                        <th scope="col">{{ __('messages.task.end_time') }}</th>
                        <th scope="col" class="text-nowrap text-center">{{ __('messages.task.tracked_by') }}</th>
                        <th scope="col" class="text-nowrap text-center">{{ __('messages.task.time') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($task->timeEntries as $entry)
                        <tr>
                            <td>{!! html_entity_decode($entry->note) !!}</td>
                            <td>{{$entry->activityType->name}}</td>
                            <td>{{\Carbon\Carbon::parse($entry->start_time)->translatedFormat('Y-m-d H:i:s')}}</td>
                            <td>{{\Carbon\Carbon::parse($entry->end_time)->translatedFormat('Y-m-d H:i:s')}}</td>
                            <td class="text-nowrap text-center"><img src="{{$entry->user->image_path}}" width="40px">
                            </td>
                            <td class="text-nowrap text-center">{{roundToQuarterHour($entry->duration)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
