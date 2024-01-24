<?php
include "header.php";
?>

<div class='p-6'>
    <button type="button" class="btn btn-primary mb-5 ">
        <i class="ri-add-line"></i>Add Service Area
    </button>
    <button type="button" class="btn btn-primary mb-5 ">
    <i class="ri-printer-line"></i> Print
    </button>
    <button type="button" class="btn btn-primary mb-5 ">
    <i class="ri-file-3-line"></i> Export into CSV
    </button>
    <div class="table-responsive">
        <table>
            <thead>
                <tr class='bg-white'>
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
                            <button type="button" x-tooltip="View">
                                <i class="ri-eye-line"></i>
                            </button>
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