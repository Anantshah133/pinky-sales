<?php
include "header.php";
?>
<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-lg font-semibold dark:text-white-light">Service Type- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5">
                <div>
                    <label for="groupFname"> Name</label>
                    <input id="groupFname" type="text" class="form-input" />
                </div>
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex">
                        <input type="radio" name="default_radio" class="form-radio" checked />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex">
                        <input type="radio" name="default_radio" class="form-radio text-danger" />
                        <span>Disable</span>
                    </label>
                </div>
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="button" class="btn btn-primary">Save </button>
                        <button type="button" class="btn  btn-warning ">Close</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>