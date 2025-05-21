<script id="attachment" type="text/x-jsrender">
<div class="file-attachment col-sm-6" id="file-attachment{{:id}}">
                                                            <div class="">
                                                                <a class="fancybox preview-image-thumb" target="_blank" href="{{:url}}"
                                                                   alt="#"> <img class="x-image"  src="{{:image}}" />
                                                               </a>
                                                            </div>
                                                            <div class="x-details">
                                                                <div><span class="font-weight-600">
                   {{:username}}</span> <br><small>{{:updated_at}}</small></div>
                                                                <div class="x-actions"><strong>
                                                                        <a href="{{:downloadTask}}/media/{{:id}} " class="download-attachment"><?php echo __('messages.expense.download')?> <span class="x-icons"><i class="ti-download"></i></span></a></strong>
                                                                    <span>
                                                                    {{if createdId == loginUserId}} |
                                                                    <a href="javascript:void(0)"
                                                                                class="text-danger delete-attachment" data-id="{{:id}}"><?php echo __('messages.common.delete') ?> </a> </span>
                                                                    {{/if}}
                                                                </div>
                                                            </div>
                                                      </div>




</script>

<script id="comment" type="text/x-jsrender">
    <div class="d-flex flex-row comment-row" id="comment{{:id}}">
                                                            <div class="p-2 comment-avatar">
                                                                <img src="{{:userImage}}"
                                                                     class="img-circle" alt="Steven">
                                                            </div>
                                                            <div class="comment-text w-100 js-hover-actions">
                                                                <div class="row">
                                                                    <div class="col-sm-6 x-name"><b>{{:userName}}</b>
                                                                    </div>
                                                                    <div class="col-sm-6 x-meta text-right">
                                                                        <span class="x-date mr-3"><small>{{:updated_at}}</small></span>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                   <div class="p-t-4 col-sm-10 comments-data{{:id}}">
                                                                    <p>{{:comment}}</p>
                                                                   </div>
                                                                    <div class="col-sm-2">
                                                                {{if userId == loginUserId}}
                                                                        <a class="dropdown dropdown-list-toggle">
                                                                        <a href="javascript:void(0)" data-toggle="dropdown" class="notification-toggle
                                                                    action-dropdown d-none position-xs-bottom ml-3 commentToggle">
                                                                        <i class="fas fa-ellipsis-v action-toggle-mr"></i>
                                                                        </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <div class="dropdown-list-content dropdown-list-icons">
                                                                        <a href="#" class="dropdown-item dropdown-item-desc edit-comment" data-id="{{:id}}"><i class="fas fa-edit mr-2 card-edit-icon"></i><?php echo __('messages.common.edit') ?> </a>
                                                                    <a href="#" class="dropdown-item dropdown-item-desc delete-comment" data-id="{{:id}}"><i class="fas fa-trash mr-2 card-delete-icon"></i><?php echo __('messages.common.delete') ?> </a>
                                                                    </div>
                                                                    </div>
                                                                    </a>
                                                                {{/if}}
                                                                    </div>
                                                                    </div>
                                                            </div>
                                                        </div>



</script>

<script id="edit-due-date" type="text/x-jsrender">
   <input type="text" name="task_date_due" id="task_date_due" autocomplete="off" value="{{:due_date}}">{{:due_date}}

</script>

<script id="status-text" type="text/x-jsrender">
<span data-toggle="popover" id="status-data" data-placement="bottom" data-html="true">{{:status}}</span>

</script>

<script id="priority-text" type="text/x-jsrender">
 <span data-toggle="popover" id="priority-data" data-placement="bottom" data-html="true">{{:priority}}</span>

</script>

<script id="task-assignee" type="text/x-jsrender">
    <figure class="avatar mr-2 avatar-sm" data-toggle="tooltip" title="{{:name}}"><img class="img-circle avatar-xsmall" src="{{:avatar}}"/></figure>

</script>

<script id="more-assignee" type="text/x-jsrender">
    <figure class="avatar ml-1 avatar-sm more-avatar">+{{:moreAssignee}}</figure>

</script>

<script id="add-assignee" type="text/x-jsrender">
<span data-id="{{:id}}" data-toggle="popover" id="edit-task-assignee-data" data-placement="bottom" data-html="true"><img class="assignee__avatar p-1 assignee" title="<?php echo __('messages.project.edit_assignee') ?>" src="assets/img/add.svg"></span>
</script>

<script id="status-popover" type="text/x-jsrender">
    <div class="d-flex status-header">
        <span><?php echo __('messages.task.status') ?></span>
        <a href="#" class="font-size-20px text-dark ml-auto btn-close-status text-decoration-none"><span>&times;</span></a>
    </div>
    <hr class="mb-0">

</script>

<script id="priority-popover" type="text/x-jsrender">
    <div class="d-flex">
        <span><?php echo __('messages.task.priority') ?></span>
        <a href="#" class="font-size-20px text-dark ml-auto btn-close-priority text-decoration-none"><span>&times;</span></a>
    </div>
    <hr class="mb-0">

</script>

<script id="assignee-popover" type="text/x-jsrender">
    <div class="d-flex">
        <span><?php echo __('messages.task.assignee') ?></span>
        <a href="#" class="font-size-20px text-dark ml-auto btn-close-assignee text-decoration-none"><span>&times;</span></a>
    </div>
    <hr class="mb-0">

</script>
