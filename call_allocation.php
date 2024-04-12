<?php
include "header.php";
setcookie("editId", "", time() - 3600);
setcookie("viewId", "", time() - 3600);

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
        setcookie("msg", "data_del", time() + 3600, "/");
    }
    header("location:call_allocation.php");
}
?>


<div class='p-6' x-data='callAllocationTable'>
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h1 class='text-primary text-2xl font-semibold'>Call Allocation</h1>
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

<script>
    function getActions(id, complaint_no) {
        return `<ul class="flex items-center justify-center gap-4">
            <li>
                <a href="javascript:viewRecord('${id}', 'add_call_allocation.php')" class='text-xl' x-tooltip="View">
                    <i class="ri-eye-line text-primary"></i>
                </a>
            </li>
            <li>
                <a href="javascript:updateRecord('${id}', 'add_call_allocation.php')" class='text-xl' x-tooltip="Edit">
                    <i class="ri-pencil-line text text-success"></i>
                </a>
            </li>
            <li>
                <a href="javascript:;" class='text-xl' x-tooltip="Delete"  @click="showAlert(${id},'${complaint_no}')">
                    <i class="ri-delete-bin-line text-danger"></i>
                </a>
            </li>
        </ul>`;
    }


    function updateRecord(id) {
        document.cookie = "editId=" + id;
        window.location = "add_call_allocation.php";
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('callAllocationTable', () => ({
            datatable: null,
            init() {
                console.log('Initalizing datatable')
                this.datatable = new simpleDatatables.DataTable('#call-table', {
                    data: {
                        headings: ['Sr.No.', 'Complaint No.','Customer Name', 'Customer Contact', 'Product Category', 'Service Center', 'Technician', 'Allocation Date Time', 'Warranty Status', 'Call Status', 'Action'],
                        data: [
                            <?php
                            if (isset($_SESSION['type_center']) && $_SESSION['type_center']) {
                                $center_id = $_SESSION['scid'];
                                $stmt = $obj->con1->prepare("SELECT tbl.*,t1.name AS technician_name FROM (SELECT c1.*,s1.name AS service_center_name,p1.name AS product_category_name, c2.warranty AS warranty_status, CONCAT(c2.fname,' ',c2.lname) AS customer_name,c2.contact AS customer_contact, CONCAT(c1.allocation_date, ' ', c1.allocation_time) AS allocation_datetime FROM call_allocation c1,customer_reg c2,service_center s1,product_category p1 WHERE c1.complaint_no=c2.complaint_no AND c2.warranty!=2 AND c1.service_center_id=s1.id AND c2.product_category=p1.id AND s1.id=?) AS tbl LEFT JOIN technician t1 on tbl.technician=t1.id order by tbl.id DESC");
                                $stmt->bind_param("i", $center_id);
                                $stmt->execute();
                                $Resp = $stmt->get_result();
                                $stmt->close();
                                $i = 1;
                            } else {
                                $stmt = $obj->con1->prepare("SELECT tbl.*,t1.name AS technician_name FROM (SELECT c1.*,s1.name AS service_center_name,p1.name AS product_category_name, c2.warranty AS warranty_status, CONCAT(c2.fname,' ',c2.lname) AS customer_name, c2.contact AS customer_contact, CONCAT(c1.allocation_date, ' ', c1.allocation_time) AS allocation_datetime FROM call_allocation c1, customer_reg c2, service_center s1, product_category p1 WHERE c1.complaint_no=c2.complaint_no AND c2.warranty!=2 AND c1.service_center_id=s1.id AND c2.product_category=p1.id) AS tbl LEFT JOIN technician t1 ON tbl.technician=t1.id ORDER BY tbl.id DESC");
                                $stmt->execute();
                                $Resp = $stmt->get_result();
                                $stmt->close();
                                $i = 1;
                            }
                            while ($row = mysqli_fetch_array($Resp)) {
                            ?>
                                [
                                    '<?php echo $i ?>', 
                                    '<?php echo $row['complaint_no'] ?>',
                                    '<?php echo $row['customer_name'] ?>',
                                    '<?php echo $row['customer_contact'] ?>',
                                    '<?php echo $row['product_category_name'] ?>',
                                    '<?php echo $row['service_center_name'] ?>',
                                    '<?php echo $row['technician_name'] ?>',
                                    '<?php
                                        $fetch_date = trim($row['allocation_datetime']) != "" ? date_format(date_create($row['allocation_datetime']), "d-m-Y h:i A") : '';
                                        echo $fetch_date;
                                    ?>',
                                    `<span class="badge badge-outline-<?php echo $row['warranty_status'] == 1 ? 'success' : ($row['warranty_status'] == 3 ? 'secondary' : 'danger') ?>">
                                        <?php echo $row['warranty_status'] == 1 ? 'In-Warranty' : ($row['warranty_status'] == 3 ? 'N / A' : 'Out-of-Warranty') ?>
                                    </span>`,
                                    `<span class="badge badge-outline-<?php
                                        switch ($row["status"]) {
                                            case 'new':
                                                echo 'secondary';
                                                break;
                                            case 'allocated':
                                                echo 'warning';
                                                break;
                                            case 'closed':
                                                echo 'success';
                                                break;
                                            case 'cancelled':
                                                echo 'danger';
                                                break;
                                            case 'pending':
                                                echo 'dark';
                                                break;
                                            default:
                                                echo 'primary';
                                                break;
                                        }
                                    ?>">
                                        <?php echo ucfirst($row["status"]); ?>
                                    </span>`,
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
                    }],
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

    async function showAlert(id, complaint_no) {
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