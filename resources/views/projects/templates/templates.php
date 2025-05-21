<script id="attachmentImage" type="text/x-jsrender">
<div class="file-attachment col-sm-4 mb-3" id="file-attachment{{:id}}">
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
