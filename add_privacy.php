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
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Privacy Policy Add</h5>
        </div>
        <form class="space-y-5">
            <lable for="">Detail</lable>
            <textarea id="ctnTextarea" rows="3" class="form-textarea" placeholder="" required></textarea>
            <div>
                <label for="groupFname"> Type</label>

                <select class="form-select text-white-dark">
                    <option>User</option>
                    <option>Service-center</option>
                    <option>Technician</option>
                </select>
                <div class="relative inline-flex align-middle gap-5 mt-4">
                <button type="button" class="btn btn-primary">Save 
</button>
                <button type="button" class="btn  btn-warning ">Close</button>
            </div>
            </div>

        
</form>




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