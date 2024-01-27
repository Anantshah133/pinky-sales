<?php
include "header.php";
?>

<div class='p-6'>
    <!-- <div>
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Progress Table</h5>
        </div>
    </div>
    <div class="table-responsive border mb-5">
        <table>
            <thead class='border-b'>
                <tr class=''>
                    <th>#</th>
                    <th>Name</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody x-data='complaint'>
                <template x-for="item in tableData" :key="item.id">
                    <tr class='bg-white'>
                        <td x-text="item.id"></td>
                        <td x-text="item.name" class="whitespace-nowrap"></td>
                        <td class="p-3 border-b border-[#ebedf2] dark:border-[#191e3a] text-center">
                            <button type="button" x-tooltip="Edit">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" x-tooltip="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
   
        </table>
    </div> -->

    <!-- Service Center - Add form -->
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
                <button type="button" class="btn btn-primary ltr:rounded-r-none rtl:rounded-l-none">Save &
                    Return</button>
                <button type="button" class="btn btn-success rounded-none">Save & New</button>
                <button type="button" class="btn  btn-dark rounded-none">Save & Edit</button>
                <button type="button" class="btn  btn-warning ltr:rounded-l-none rtl:rounded-r-none">Return</button>

            </div>
        </form>


    </div>










    <?php
include "footer.php";
?>