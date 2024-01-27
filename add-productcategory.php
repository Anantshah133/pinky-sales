<?php
include "header.php";
?>
<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-lg font-semibold dark:text-white-light">Product Category- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5">
                <div>
                    <label for="groupFname"> Name</label>
                    <input id="groupFname" type="text" placeholder="Enter First Name" class="form-input" />
                </div>
                <div class="mb-2 flex items-center gap-2 pl-1">
                    <button type="button" class="btn btn-primary !mt-6 ">Save & Return</button>
                    <button type="button" class="btn btn-primary !mt-6">Save & New</button>
                    <button type="button" class="btn btn-primary !mt-6">Save & Edit</button>
                    <button type="button" class="btn btn-primary !mt-6">Return</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
include "footer.php";
?>