<div id="taskKanbanDetailsModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.task.task_details') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="taskId">
                <input type="hidden" id="ThisTaskId">
                <input type="hidden" id="userId" value="{{getLoggedInUserId()}}">
                <div class="alert alert-danger display-none" id="tmAddValidationErrorsBox"></div>
                <div class="row task-kanban">
                    <!--left panel-->
                    <div class="col-lg-8 task-kanban__card-left-panel mt-0" id="card-left-panel">
                        <div class="d-flex">
                            <input type="text" class="task-title-input display-none form-control">
                            <div class="card-title m-b-0" id="task_title"></div>
                            {{--                            <a class="mt-2 ml-1 edit-title" href="#" title="{{ __('messages.common.edit') }}"><i--}}
                            {{--                                        class="fas fa-edit card-edit-icon"></i></a>--}}
                        </div>
                        <div class="card-description" id="card-description">
                            <div class="x-heading  pb-1"><i
                                        class="fas fa-sticky-note  mr-1 mb-1"></i>{{__('messages.task.description')}}:
                                {{--                                <a href="#" class="task_edit_description" title="{{ __('messages.common.edit') }}"><i--}}
                                {{--                                            class="fas fa-edit card-edit-icon mb-1"></i></a>--}}
                                {{--                                <button class="btn btn-primary btn-sm btnDescription ml-2"--}}
                                {{--                                        data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">{{__('messages.common.save')}}</button>--}}
                            </div>
                            <div id="task_no_description" class="text-center"></div>
                            <div class="x-content p-2" id='task_description'></div>
                            <div class="description_input"></div>
                        </div>

                        <div class="card-attachments" id="card-attachments">
                            <div class="x-heading d-flex"><i
                                        class="fas fa-download mr-1 mt-1"></i>{{__('messages.task.attachments')}}:
                                <form method="post" enctype="multipart/form-data" class="mb-0 ml-auto" id="upload">
                                    <label href="#"  class="cursor-pointer font-size-12px btn btn-sm btn-primary mb-0 ml-2 choose-button"><i class="fas fa-plus font-size-12px"></i>&nbsp;{{__('messages.setting_menu.choose')}}
                                        <input type="file" name="files[]" id="upload_attachment" class="d-none" multiple>
                                    </label>
                                    <button type="submit" class="btn btn-sm btn-primary ml-2 btn-upload" data-loading-text="<span class='spinner-border spinner-border-sm'></span> Uploading..."><i class="fas fa-upload font-size-12px"></i>&nbsp;{{__('messages.common.upload')}}</button>
                                </form>
                            </div>
                            <div class="x-content p-2 attachments-content" id="card-attachments-container">
                                <div class="text-center" id="no_attachments"></div>
                                <div class="row" id="attachments">
                                </div>
                            </div>
                        </div>

                        <div class="card-comments" id="card-comments">
                            <div class="x-heading"><i class="fas fa-comments mr-1"></i>{{__('messages.task.comments')}}:
                            </div>
                            <div class="x-content p-2">
                                <input type="text" class="form-control ml-1 comment-input"
                                       placeholder="Add comment..."/>
                                <div class="text-right">
                                    <button class="btn btn-light btn-sm ml-5 mb-2" id="btnCommentClose">{{__('messages.common.cancel')}}</button>
                                    <button class="btn btn-primary btn-sm ml-1 mb-2"  id="btnCommentSave" data-edit-mode="0" data-comment-id="0"  data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">{{__('messages.common.save')}}</button>
                                </div>
                                <div id="comments-input"></div>
                                <div id="card-comments-container" class="comment-content"></div>
                            </div>
                        </div>
                    </div>

                    <!--right panel-->
                    <div class="col-lg-4 task-kanban__card-right-panel" id="card-right-panel">
                        <div class="x-section mb-4">
                            <div class="x-title">
                                <h6>{{__('messages.task.assignee')}}</h6>
                            </div>
                            <span id="task-assigned-container">
                                <span class="x-assigned-user" id="task_assignee"></span>
                            </span>
                            <div id="popover-content-edit-assignee" class="d-none bg-secondary"></div>
                        </div>

                        <div class="task-timer-container">
                            <div class="x-section x-timer m-t-10" id="task-users-task-timer">
                                <div class="x-title  text-left">
                                    <h6 class=" m-b-0">{{__('messages.time_entry.task')}} {{__('messages.time_entry.duration')}}</h6>
                                </div>
                                <span class="x-timer-time timers" id="task_timer_card_16"></span>
                            </div>
                        </div>

                        <div class="x-section">
                            <div class="x-title">
                                <h6>{{__('messages.settings')}}</h6>
                            </div>
                            <!--start date-->
                            <div class="x-element" id="task-start-date"><i class="fas fa-calendar-plus mr-1"></i>
                                <span>{{__('messages.report.start_date')}}:</span>
                                <span class="x-highlight x-editable"><span id="start_date"></span></span>
                                <input type="hidden" name="task_date_start" id="task_date_start">
                            </div>

                            <div class="x-element"><i class="fas fa-calendar-check mr-1"></i>
                                <span>{{__('messages.task.due_date')}}:</span>
                                <span class="x-highlight x-editable" id="due_date"></span>
                            </div>

                            <div class="x-element" id="card-task-status"><i class="fas fa-flag mr-1"></i>
                                <span>{{__('messages.task.status')}}: </span>
                                <span class="x-highlight x-editable js-card-settings-button-static" id="status"></span>
                                <div id="popover-content-status" class="d-none bg-secondary"></div>
                            </div>
                            <div class="x-element" id="card-task-priority"><i class="fas fa-shield-alt mr-1"></i>
                                <span>{{__('messages.task.priority')}}:</span>
                                <span class="x-highlight x-editable js-card-settings-button-static"
                                      id="taskDetailsPriority"></span>
                                <div id="popover-content-priority" class="d-none bg-secondary"></div>
                            </div>
                        </div>

                        <div class="x-section mt-4">
                            <div class="x-title">
                                <h6>{{__('messages.common.information')}}</h6>
                            </div>
                            <div class="x-element x-action">
                                <table class="table  table-sm">
                                    <tbody>
                                    <tr>
                                        <td>{{__('messages.common.created_by')}}:</td>
                                        <td class="td-text" id="created_by"></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.common.created_on')}}:</td>
                                        <td class="td-text" id="created_date"></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.task.time_tracking')}}:</td>
                                        <td class="td-text"><span id="task_timer_all_card_16"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.task.project')}}:</td>
                                        <td style="width: 125px;line-break: anywhere"><strong>
                                                @if(! getLoggedInUser()->hasRole('Admin'))
                                                    <a target="_blank" id="project_id"></a>
                                                @else
                                                    <a  target="_blank" id="project_admin_id"></a>
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
