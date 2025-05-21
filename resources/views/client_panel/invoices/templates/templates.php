<script id="invoiceItemTemplate" type="text/x-jsrender">
    <tr>
        <td><input type="text" name="item[]"   class="form-control item-name input-group__icon" required="" placeholder="Enter task">
            <input type="text" name="task_id[]" class="form-control task-id" hidden>
            <input type="text" name="item_project_id[]" class="form-control item_project_id" hidden>
        </td>
        <td><input type="text" name="hours[]" class="form-control hours" required="" min="0" value="0"></td>
        <td class="text-right">
            <input type="text" name="task_amount[]" value="0" min="0" class="form-control task-amount text-align-right-invoice" required="" >
        </td>
        <td><a href="#" class="remove-invoice-item text-danger"><i class="far fa-trash-alt"></i></a></td>
    </tr>



</script>

<script id="optionsTemplate" type="text/x-jsrender">
    <option class="new-option" value="{{:value}}">{{:label}}</option>



</script>
