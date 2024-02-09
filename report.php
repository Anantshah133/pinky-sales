<?php
include "header.php";
?>

<div class='p-6'>
    <!-- Service Center - Add form -->
    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Report</h5>
        </div>

        <form x-data="form" class="space-y-5">

            <div class="flex">
                <div class="w-6/12 px-4">
                    <label for="groupFname"> Service Center</label>

                    <select class="form-select text-white-dark" name="service_center" required>
                        <option value="">Choose...</option>
                        <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `service_center` WHERE status='enable'"
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                        <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                        <?php 
                            } 
                        ?>
                    </select>
                </div>

                <div class="w-6/12 px-4 ">
                    <label for="groupFname"> Technician</label>

                    <select class="form-select text-white-dark" name="" required>
                        <option value="">Choose...</option>
                        <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `technician` WHERE status='enable'"
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                        <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                        <?php 
                            } 
                        ?>
                    </select>
                </div>
            </div>
            <div class="flex">
                <div class="w-6/12 px-4">
                    <label for="range-calendar">Date Range</label>
                    <input id="range-calendar" x-model="date3" class="form-input" />
                </div>

                <div class="w-6/12  px-4 ">
                    <label for="gridStatus">Status</label>
                    <div class="flex gap-5 items-center mt-3">
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
            </div>


            <div class="flex">
                <div class="w-6/12 px-4">
                    <label for="groupFname"> Complaint No</label>

                    <select class="form-select text-white-dark" name="" required>
                        <option value="">Choose...</option>
                        <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `complaint` "
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                        <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                        <?php 
                            } 
                        ?>
                    </select>
                </div>

                <div class="w-6/12 px-4 ">
                    <label for="groupFname"> Contact No</label>

                    <select class="form-select text-white-dark" name="" required>
                        <option value="">Choose...</option>
                        <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `contact` "
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                        <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                        <?php 
                            } 
                        ?>
                    </select>
                </div>
            </div>
            <div class="px-4">
                <button type="button" class="btn btn-success ">Submit</button>
            </div>
        </form>
    </div>
</div>

<div class='p-6 ' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h1 class='text-primary text-2xl font-bold'>Report</h1>
            <div class="flex flex-wrap items-center">
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="printTable">
                    <i class="ri-printer-line mr-1"></i> PRINT
                </button>
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="exportTable('csv')">
                    <i class="ri-file-line mr-1"></i> CSV
                </button>
            </div>
        </div>
        <table id="call-table" class="table-hover whitespace-nowrap"></table>
    </div>
</div>


<!-- script -->
<script src="assets/js/flatpickr.js"></script>
<script>
checkCookies();
function getActions(id) {
    return `<ul class="flex items-center justify-center gap-4">
        <li>
            <a href="add_complaint_demo.php?viewId=${id}" class='text-xl' x-tooltip="View">
                <i class="ri-eye-line text-primary"></i>
            </a>
        </li>
    </ul>`
}
document.addEventListener('alpine:init', () => {
    Alpine.data('exportTable', () => ({
        datatable: null,
        init() {
            console.log('Initalizing datatable')
            this.datatable = new simpleDatatables.DataTable('#call-table', {
                data: {
                    headings: ['Sr.No.', 'Complaint No.', 'Customer Name', 'Customer Contact', 'Service Center', 'Technician', 'Allocation Date',
                        , 'Allocation Time', 'Status', 'Action'],
                    data: [
                        
                        ['Sr.No.', 'Complaint No.', 'Customer Name', 'Customer Contact', 'Service Center', 'Technician', 'Allocation Date',
                        , 'Allocation Time', 'Status', 'Action'],
                    ],
                },
                perPage: 10,
                perPageSelect: [10, 20, 30, 50, 100],
                columns: [{
                        select: 0,
                        sort: 'asc',
                    },
                ],
                firstLast: true,
                firstText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                lastText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                prevText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                nextText: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                labels: {
                    perPage: '{select}',
                },
                layout: {
                    top: '{search}',
                    bottom: '{info}{select}{pager}',
                },
            });
        },

        exportTable(eType) {
            var data = {
                type: eType,
                filename: 'table',
                download: true,
            };

            if (data.type === 'csv') {
                data.lineDelimiter = '\n';
                data.columnDelimiter = ';';
            }
            this.datatable.export(data);
        },

        printTable() {
            this.datatable.print();
        },

        formatDate(date) {
            if (date) {
                const dt = new Date(date);
                const month = dt.getMonth() + 1 < 10 ? '0' + (dt.getMonth() + 1) : dt.getMonth() +
                    1;
                const day = dt.getDate() < 10 ? '0' + dt.getDate() : dt.getDate();
                return day + '/' + month + '/' + dt.getFullYear();
            }
            return '';
        },
    }));
})

document.addEventListener("alpine:init", () => {
    let todayDate = new Date();
    let formattedToday = todayDate.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).split('/').join('-');

    Alpine.data("form", () => ({
        date3: `${formattedToday} to ${formattedToday}`,
        init() {
            flatpickr(document.getElementById('range-calendar'), {
                defaultDate: this.date3,
                dateFormat: 'd-m-Y',
                mode: 'range',
                minDate: formattedToday,
            })
        }
    }));

});
</script>

<?php
include "footer.php";
?>