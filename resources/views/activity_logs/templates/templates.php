<script id="activityLogsTemplate" type="text/x-jsrender">
    <div class="activity position-relative">
        <div class="activity-icon bg-white shadow-primary">
            <i class="activity-icon-size {{:subject_type}}"></i>
        </div>
        <div class="activity-detail">
            <div class="mb-2 d-flex">
                <span class="text-job text-primary">{{:created_at}}</span>
                <span class="ml-auto position-relative">{{:created_by}}</span>
                <button  data-id="{{:id}}" class="btn activityData" title="<?php echo __('messages.common.delete') ?>">&nbsp; <i class="fa fa-trash text-danger"></i></button>
            </div>
            <span class="post-id"">{{:description}}</span>
        </div>
    </div>




</script>
