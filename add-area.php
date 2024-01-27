<?php
include "header.php";
?>
<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-lg font-semibold dark:text-white-light">Service Area- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5">
                <div>
                    <label for="groupFname"> Name</label>
                    <input id="groupFname" type="text" class="form-input" />
                    <div class="relative inline-flex align-middle">
                        <button type="button" class="btn btn-primary">Save
                        </button>&nbsp;&nbsp;
                        <button type="button" class="btn  btn-warning ">Close</button>
                    </div>
                </div>
</form>
        </div>
    </div>
</div>

</form>
<?php
include "footer.php";
?>