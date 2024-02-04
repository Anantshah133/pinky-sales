<?php
include "header.php";
?>

<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h1 class='text-primary text-2xl font-bold'>Complaint / Demo</h1>

            <div class="flex flex-wrap items-center">
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" onclick="location.href='add_complaint_demo.php'">
                    <i class="ri-add-line mr-1"></i> Add Complaint
                </button>
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="exportTable('csv')">
                    <i class="ri-file-line mr-1"></i> CSV
                </button>
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="exportTable('txt')">
                    <i class="ri-file-list-line mr-1"></i> TXT
                </button>
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="exportTable('json')">
                    <i class="ri-braces-line mr-1"></i> JSON
                </button>
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="printTable">
                    <i class="ri-printer-line mr-1"></i> PRINT
                </button>
            </div>
        </div>
        <table id="myTable" class="table-hover whitespace-nowrap"></table>
    </div>
</div>

<!-- script -->

<script>
checkCookies();
function getActions() {
    return `<ul class="flex items-center justify-center gap-4">
        <li>
            <a href="javascript:;" class='text-xl' x-tooltip="View">
                <i class="ri-eye-line text-primary"></i>
            </a>
        </li>
        <li>
            <a href="javascript:;" class='text-xl' x-tooltip="Edit">
                <i class="ri-pencil-line text text-success"></i>
            </a>
        </li>
        <li>
            <a href="javascript:;" class='text-xl' x-tooltip="Delete">
                <i class="ri-delete-bin-line text-danger"></i>
            </a>
        </li>
    </ul>`
}

document.addEventListener('alpine:init', () => {
    Alpine.data('exportTable', () => ({
        datatable: null,
        init() {
            console.log('Initalizing datatable')
            this.datatable = new simpleDatatables.DataTable('#myTable', {
                data: {
                    headings: ['Sr.No.', 'Firstname', 'Lname', 'Email', 'Contact', 'City', 'Zipcode',
                        'Complaint No.', 'Service Type', 'Product Category', 'Dealer Name', 'Date', 'Time', 'Status', 'Action'
                    ],
                    data: [
                        <?php 
                            $stmt = $obj->con1->prepare("select c1.*,s1.name as service_type,p1.name as product_category,c2.ctnm as city from customer_reg c1,service_type s1,product_category p1,city c2 where c1.area=c2.srno and c1.service_type=s1.id and c1.product_category=p1.id order by c1.id desc");
                            $stmt->execute();
                            $Resp=$stmt->get_result();
                            $id=1;
                            while($row = mysqli_fetch_array($Resp)){
                        ?>
                            [
                                <?php echo $id ?>,
                                '<?php echo $row['fname'] ?>',
                                '<?php echo $row['lname'] ?>',
                                '<?php echo $row['email'] ?>',
                                '<?php echo $row['contact'] ?>',
                                '<?php echo $row['city'] ?>',
                                '<?php echo $row['zipcode'] ?>',
                                '<?php echo $row['complaint_no'] ?>',
                                '<?php echo $row['service_type'] ?>',
                                '<?php echo $row['product_category'] ?>',
                                '<?php echo $row['dealer_name'] ?>',
                                '<?php echo $row['date'] ?>',
                                '<?php echo $row['time'] ?>',
                                'New',
                                getActions()
                            ],
                        <?php
                        $id++;
                            }
                        ?>
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
</script>

<?php
include "footer.php";
?>