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

    <!-- service-center table -->

    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Service Center</h5>
        </div>

        <div class=" container flex justify-between items-center  ">
            <div class="">



                <button type="button" class="btn btn-primary" onclick="window.location='service_center_add.php'"> <i
                        class="ri-add-line"></i>Add</button>

            </div>
            <div class="flex  space-x-4  ">
                <div class="">
                    <button type="button" class="btn btn-primary mb-5 ">
                        <i class="ri-printer-line"></i> Print
                    </button>
                </div>
                <div class="">
                    <button type="button" class="btn btn-primary mb-5 ">
                        <i class="ri-file-3-line"></i> Export into CSV
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Adress</th>
                        <th class="text-center">Area</th>
                        <th>Status</th>
                        <th>Date Time</th>
                        <th class="text-center">Action </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="data in tableData" :key="data.id">
                        <tr>
                            <td x-text="data.name" class="whitespace-nowrap"></td>
                            <td x-text="data.date"></td>
                            <td x-text="data.sale"></td>
                            <td class="text-center whitespace-nowrap"
                                :class="{'text-success': data.status === 'Complete', 'text-secondary': data.status === 'Pending', 'text-info': data.status === 'In Progress', 'text-danger': data.status === 'Canceled'}"
                                x-text="data.status"></td>
                            <td class="text-center">
                                <button type="button" x-tooltip="Delete">
                                    <svg> ... </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>




    <!-- script -->
    <script>
    document.addEventListener("alpine:init", () => {
                Alpine.data("form", () => ({
                        tableData: [{
                                id: 1,
                                name: 'John Doe',
                                email: 'johndoe@yahoo.com',
                                date: '10/08/2020',
                                sale: 120,
                                status: 'Complete',
                                register: '5 min ago',
                                progress: '40%',
                                position: 'Developer',
                                office: 'London'
                            },
                            ......
                        }));
                });
    </script>



    <?php
include "footer.php";
?>