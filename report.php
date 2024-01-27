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
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Report</h5>
        </div>

        <form x-data="form" class="space-y-5">
            <div class="flex">
                <div class="w-6/12 px-2">
                    <lable for="basic">Start Date</lable>
                    <input id="basic" x-model="date1" class="form-input" />
                </div>
                <div class="w-6/12 px-2">
                    <lable for="basic">End Date</lable>
                    <input id="basic" x-model="date1" class="form-input" />
                </div>
            </div>

            <div class="flex">
                <div class="w-6/12 px-2">
                    <label for="groupFname"> Service Center</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>MIRA BHAYANDER</option>
                        <option>N H SERVICE</option>
                        <option>NO SERVICE</option>
                        <option>PALGHAR</option>
                        <option>Test Service center</option>
                        <option>VIRAR NSP VASAI</option>
                    </select>
                </div>

                <div class="w-6/12 px-2">
                    <label for="groupFname"> Technician</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>MIRA BHAYANDER</option>
                        <option>N H SERVICE</option>
                        <option>NO SERVICE</option>
                        <option>PALGHAR</option>
                        <option>Test Service center</option>
                        <option>VIRAR NSP VASAI</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="gridStatus">Status</label>
                <div class="flex gap-5 items-center">
                    <div class="">
                        <label class="">
                            <input type="radio" name="default_radio" class="form-radio" checked />
                            <span>Pending</span>
                        </label>
                    </div>
                    <div class="">
                        <label class="">
                            <input type="radio" name="default_radio" class="form-radio text-primary" />
                            <span>Allocated</span>
                        </label>
                    </div>
                    <div class="">
                        <label class="">
                            <input type="radio" name="default_radio" class="form-radio text-primary" />
                            <span>Closed</span>
                        </label>
                    </div>
                    <div class="">
                        <label class="">
                            <input type="radio" name="default_radio" class="form-radio text-primary" />
                            <span>Cancelled</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex">
                <div class="w-6/12 px-2">
                    <label for="groupFname"> Complaint No</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>MIRA BHAYANDER</option>
                        <option>N H SERVICE</option>
                        <option>NO SERVICE</option>
                        <option>PALGHAR</option>
                        <option>Test Service center</option>
                        <option>VIRAR NSP VASAI</option>
                    </select>
                </div>

                <div class="w-6/12 px-2">
                    <label for="groupFname"> Contact No</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>MIRA BHAYANDER</option>
                        <option>N H SERVICE</option>
                        <option>NO SERVICE</option>
                        <option>PALGHAR</option>
                        <option>Test Service center</option>
                        <option>VIRAR NSP VASAI</option>
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<!-- script -->
<script src="assets/js/flatpickr.js"></script>
<script>
document.addEventListener("alpine:init", () => {
    Alpine.data("form", () => ({
        date1: '2022-07-05',
        init() {
            console.log('jsdjfsdfhkjḍ')
            flatpickr(document.getElementById('basic'), {
                dateFormat: 'Y-m-d',
                defaultDate: this.date1,
            })
        }
    }));
});
</script>

<?php
include "footer.php";
?>