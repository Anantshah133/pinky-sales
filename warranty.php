<?php
include "header.php";
setcookie("editId", "", time() - 3600);
setcookie("viewId", "", time() - 3600);

if (isset($_REQUEST["flg"]) && $_REQUEST["flg"] == "del") {
    $complaint_id = $_REQUEST["n_complaintid"];

    try {
        $stmt = $obj->con1->prepare("SELECT complaint_no FROM `customer_reg` WHERE id=?");
        $stmt->bind_param("s", $complaint_id);
        $stmt->execute();
        $Result = $stmt->get_result()->fetch_assoc();
        $complaint_no = $Result['complaint_no'];

        if(!$Result){
            throw new Exception("Problem in deleting ! " . strtok($obj->con1->error, '('));
        }

        $stmt_del = $obj->con1->prepare("DELETE FROM call_allocation WHERE complaint_no=?");
        $stmt_del->bind_param("s", $complaint_no);
        $Res = $stmt_del->execute();

        if(!$Res){
            throw new Exception("Problem in deleting ! " . strtok($obj->con1->error, '('));
        }

        $stmt_del = $obj->con1->prepare("DELETE FROM customer_reg WHERE id=?");
        $stmt_del->bind_param("s", $complaint_id);
        $Resp = $stmt_del->execute();
        if (!$Resp) {
            if (strtok($obj->con1->error, ":") == "Cannot delete or update a parent row") {
                throw new Exception("Data is already in use");
            }
        }
        $stmt_del->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
        setcookie("msg", "fail", time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data_del", time() + 3600, "/");
        header("location:warranty.php");
    }
}
?>

<div class='p-6' x-data='exportTable'>
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h1 class='text-primary text-2xl font-semibold'>Warranty Details</h1>

            <div class="flex flex-wrap items-center">
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="printTable">
                    <i class="ri-printer-line mr-1"></i> PRINT
                </button>
                <button type="button" class="p-2 btn btn-primary btn-sm m-1" @click="exportTable('csv')">
                    <i class="ri-file-line mr-1"></i> CSV
                </button>
            </div>
        </div>
        <table id="myTable" class="table-hover whitespace-nowrap"></table>
    </div>
</div>

<!-- script -->
<script>
    
    function getActions(id, name) {
        return `<ul class="flex items-center gap-4">
        <li>
            <a href="javascript:viewRecord(${id}, 'edit_view_warranty.php')" class='text-xl' x-tooltip="View">
                <i class="ri-eye-line text-primary"></i>
            </a>
        </li>
        <li>
            <a href="javascript:updateRecord(${id}, 'edit_view_warranty.php');" class='text-xl' x-tooltip="Edit">
                <i class="ri-pencil-line text text-success"></i>
            </a>
        </li>
        <li>
            <a href="javascript:;" class='text-xl' x-tooltip="Delete" @click="showAlert(${id}, '${name}')">
                <i class="ri-delete-bin-line text-danger"></i>
            </a>
        </li>
    </ul>`
    }

    function showAlert(id, number) {
        new window.Swal({
            title: 'Are you sure?',
            text: `You want to delete Call :- ${number} !`,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            padding: '2em',
        }).then((result) => {
            console.log(result)
            if (result.isConfirmed) {
                var loc = "warranty.php?flg=del&n_complaintid=" + id;
                window.location = loc;
            }
        });
    }
    
    document.addEventListener('alpine:init', () => {
        Alpine.data('exportTable', () => ({
            datatable: null,
            init() {
                console.log('Initalizing datatable')
                this.datatable = new simpleDatatables.DataTable('#myTable', {
                    data: {
                        headings: ['Sr.No.', 'Customer Name', 'Warranty No.', 'Product Name', 'Barcode', 'Warranty Starting Date', 'Source', 'Action'],
                        data: [
                            <?php
                                if(isset($_SESSION['type_center'])){
                                    $center_id = $_SESSION['scid'];
                                    $stmt = $obj->con1->prepare("SELECT p1.name AS product, c1.id, CONCAT(c1.fname, ' ', c1.lname) AS customer_name, c1.source, c1.date, c1.complaint_no, c1.barcode FROM customer_reg c1, product_category p1, call_allocation ca1 WHERE warranty=2 AND c1.complaint_no=ca1.complaint_no AND ca1.service_center_id=? AND p1.id=c1.product_category ORDER BY id DESC");
                                    $stmt->bind_param("i", $center_id);
                                    $stmt->execute();
                                    $Resp = $stmt->get_result();
                                    $stmt->close();
                                } else if(isset($_SESSION['type_admin'])){
                                    $stmt = $obj->con1->prepare("SELECT p1.name AS product, c1.id, CONCAT(c1.fname, ' ', c1.lname) AS customer_name, c1.source, c1.date, c1.complaint_no, c1.barcode FROM customer_reg c1, product_category p1 WHERE warranty=2 AND p1.id=c1.product_category ORDER BY id DESC");
                                    $stmt->execute();
                                    $Resp = $stmt->get_result();
                                    $stmt->close();
                                }
                                $i = 1;
                                while ($row = mysqli_fetch_array($Resp)) {
                            ?>
                                [
                                    <?php echo $i; ?>,
                                    '<?php echo $row["customer_name"]; ?>',
                                    '<?php echo $row["complaint_no"]; ?>',
                                    '<?php echo $row["product"]; ?>',
                                    `<strong><?php echo $row["barcode"]; ?></strong>`,
                                    `<span class="badge badge-outline-secondary">
                                        <?php echo date("d-m-Y", strtotime($row["date"])); ?>
                                    </span>`,
                                    `<span class="badge badge-outline-<?php echo $row['source'] == 'web' ? 'secondary' : 'danger' ?>">
                                        <?php echo ucfirst($row['source']) ?>
                                    </span>`,
                                    getActions(<?php echo $row["id"]; ?>, '<?php echo $row["complaint_no"]; ?>')
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
                    filename: 'state',
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