<?php
include "header.php";
if (isset($_REQUEST["flg"]) && $_REQUEST["flg"] == "del") {
    try {
        $stmt_del = $obj->con1->prepare(
            "delete from call_allocation where id='" . $_REQUEST["n_pcategoryid"] . "'"
        );
        $Resp = $stmt_del->execute();
        if (!$Resp) {
            if (strtok($obj->con1->error, ":") == "Cannot delete or update a parent row") {
                throw new Exception("City is already in use!");
            }
        }
        $stmt_del->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data_del",time()+3600,"/");
    }
    header("location:call_allocation.php");
}
?>


<div class='p-6' x-data='callAllocationTable'>
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h1 class='text-primary text-2xl font-bold'>Call Allocation</h1>
            <div class="flex flex-wrap items-center">
                <button type="button" class="p-2 btn btn-primary btn-sm m-1"
                    onclick="location.href='add_call_allocation.php'">
                    <i class="ri-add-line mr-1"></i> Add Call
                </button>
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

<script>
checkCookies();

function getActions(id,complaint_no) {
    return `<ul class="flex items-center justify-center gap-4">
        <li>
            <a href="add_call_allocation.php?viewId=${id}" class='text-xl' x-tooltip="View">
                <i class="ri-eye-line text-primary"></i>
            </a>
        </li>
        <li>
            <a href="add_call_allocation.php?editId=${id}" class='text-xl' x-tooltip="Edit">
                <i class="ri-pencil-line text text-success"></i>
            </a>
        </li>
        <li>
            <a href="javascript:;" class='text-xl' x-tooltip="Delete"  @click="showAlert(${id},'${complaint_no}')">
                <i class="ri-delete-bin-line text-danger"></i>
            </a>
        </li>
    </ul>`
}


document.addEventListener('alpine:init', () => {
    Alpine.data('callAllocationTable', () => ({
        datatable: null,
        init() {
            console.log('Initalizing datatable')
            this.datatable = new simpleDatatables.DataTable('#call-table', {
                data: {
                    headings: ['Sr.No.', 'Complaint No.', 'Service Center', 'Technician',
                        'Allocation Date', 'Allocation Time', 'Status',
                        'Reason', 'Customer Name', 'Customer Contact',
                        'Product Category', 'Action'
                    ],
                    data: [
                        <?php 
                            $stmt = $obj->con1->prepare("select tbl.*,t1.name as technician_name from (select c1.*,s1.name as service_center_name,p1.name as product_category_name,CONCAT(c2.fname,' ',c2.lname) as customer_name,c2.contact as customer_contact FROM call_allocation c1,customer_reg c2,service_center s1,product_category p1 where c1.complaint_no=c2.complaint_no and c1.service_center_id=s1.id and c2.product_category=p1.id ) as tbl LEFT JOIN technician t1 on tbl.technician=t1.id order by tbl.id desc");
                            $stmt->execute();
                            $Resp=$stmt->get_result();
                            $i = 1;
                            while($row = mysqli_fetch_array($Resp)){
                        ?>
                            [
                                '<?php echo $i ?>', '<?php echo $row['complaint_no'] ?>',
                                '<?php echo $row['service_center_name'] ?>',
                                '<?php echo $row['technician_name'] ?>',
                                '<?php echo $row['allocation_date'] ?>',
                                '<?php echo $row['allocation_time'] ?>',
                                '<?php echo $row['status'] ?>',
                                '<?php echo $row['reason'] ?>',
                                '<?php echo $row['customer_name'] ?>',
                                '<?php echo $row['customer_contact'] ?>',
                                '<?php echo $row['product_category_name'] ?>',
                                getActions(<?php echo $row['id'] ?>, '<?php echo $row['complaint_no'] ?>')
                            ],
                        <?php
                            $i++;
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
                filename: 'call_allocation',
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

async function showAlert(id,complaint_no) {
    new window.Swal({
        title: 'Are you sure?',
        text: `You want to delete Call allocation for:- ${complaint_no}!`,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        padding: '2em',
    }).then((result) => {
        console.log(result)
        if (result.isConfirmed) {
            var loc = "call_allocation.php?flg=del&n_pcategoryid=" + id;
            window.location = loc;
        }
    });
}
</script>


<?php
include "footer.php";
?>