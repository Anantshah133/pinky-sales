<?php
include "header.php";
?>
<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-lg font-semibold dark:text-white-light">Area Pincode- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5">
                <div>
                    <label for="groupFname"> Service Area Id</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>Test state</option>
                        <option>Gujarat</option>
                        <option>Bhayander</option>
                        <option>VIRAR NSP VASAI</option>
                        <option>Thane</option>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Pincode </label>
                    <input id="groupFname" type="text" class="form-input" />
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="button" class="btn btn-primary">Save </button>
                        <button type="button" class="btn  btn-warning ">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>