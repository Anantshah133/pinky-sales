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
    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Complaint / Demo</h5>
        </div>
        <div class="mb-5">
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
                    name: 'John Doe',
                },
                {
                    id: 2,
                    name: 'Roshni Rana',
                },
            ]
        }));
    });
    </script>

    <?php
include "footer.php";
?>