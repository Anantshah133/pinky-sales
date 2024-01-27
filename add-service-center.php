<?php
include "header.php";
?>

<div class='p-6'>
    
    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Service Center Add</h5>
        </div>

        <form class="space-y-5">
            <div>
                <label for="groupFname"> Name</label>
                <input id="groupFname" type="text" placeholder="Enter First Name" class="form-input" />
            </div>
            <div>
                <label for="ctnEmail">Email address</label>
                <input id="ctnEmail" type="email" placeholder="name@example.com" class="form-input" required />
            </div>
            <div>
                <label for="groupFname">Contact</label>
                <input id="groupFname" type="text" placeholder="" class="form-input" />
            </div>

            <div>
                <label for="gridAddress1">Address</label>
                <input id="gridAddress1" type="text" placeholder="Enter Address" value="" class="form-input" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div>
                    <label for="gridState">State</label>
                    <select id="gridState" class="form-select text-white-dark">
                        <option>Choose...</option>
                        <option>...</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="gridCity">City</label>
                    <input id="gridCity" type="text" placeholder="Enter City" class="form-input" />
                </div>

                <div>
                    <label for="gridZip">Zip</label>
                    <input id="gridZip" type="text" placeholder="Enter Zip" class="form-input" />
                </div>
            </div>
            <div>
                <label for="gridUID">Userid</label>
                <input type="text" placeholder="" class="form-input" required />
            </div>
            <div>
                <label for="gridpass">Password</label>
                <input type="password" placeholder="Enter Password" class="form-input" required />
            </div>

            <div>
                <label for="gridStatus">Status</label>
                <label class="inline-flex">
                    <input type="radio" name="default_radio" class="form-radio" checked />
                    <span>Enable</span>
                </label>
                <label class="">
                    <input type="radio" name="default_radio" class="form-radio text-success" />
                    <span>Disable</span>
                </label>
            </div>

            <div class="relative inline-flex align-middle">
                <button type="button" class="btn btn-primary">Save 
                    </button>&nbsp;&nbsp;
                <button type="button" class="btn  btn-warning ">Close</button>
            </div>
        </form>


    </div>








</div>

    <?php
include "footer.php";
?>