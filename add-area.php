<?php
include "header.php";
?>
<div class="panel">
    <div class="mb-5 flex items-center justify-between">
       <h5 class="text-lg font-semibold dark:text-white-light">Form groups</h5>
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
</div>
</div>
</div>
</form>
<?php
include "footer.php";
?>
