<?php
include "header.php";
?>

<div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">State Area</h5>
        </div>
        <div class="mb-5">
        <div class="mb-5 flex items-center gap-2">
            <button type="button" class="btn btn-primary mb-5 ">
                <i class="ri-add-line"></i>Add Service Area
            </button>

            <div class="dataTable-search">
            <div class="mb-5 flex items-center gap-2">   
            <button type="button" class="btn btn-primary mb-5 ">
                <i class="ri-printer-line"></i> Print
            </button>
            <button type="button" class="btn btn-primary mb-5 ">
                <i class="ri-file-3-line"></i> Export into CSV
            </button>
</div>
</div>
</div>
            <div class="table-responsive">
                <table class='table-bordered table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th class="!text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody x-data='complaint'>
                        <template x-for="data in tableData" :key="data.id">
                            <tr class='text-lg'>
                                <td x-text='data.id'></td>
                                <td x-text="data.name" class="whitespace-nowrap"></td>
                                <td class="text-center">
                                    <ul class="flex items-center justify-center gap-6">
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
                                    </ul>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- script -->
    <script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("complaint", () => ({
            tableData: [{
                id: 1,
                name: 'test state',
            },
            {
                id: 2,
                name: 'gujarat',
            },
            {
                id: 3,
                name: 'bhayander',
            },
            {
                id: 4,
                name: 'vasai',
            },
            {
                id: 5,
                name: 'thane',
            },
        ]
        }));
    });
    </script>


<?php
include "footer.php";
?>
