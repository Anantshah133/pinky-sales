<?php
include "header.php";
?>

<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h1 class='text-primary text-2xl font-bold'>Call Allocation</h1>

            <div class="flex flex-wrap items-center">
            <button type="button" class="p-2 btn btn-primary btn-sm m-1" onclick="location.href='add-callallocation.php'">
                    <i class="ri-add-line"></i>Add  
                </button>
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
document.addEventListener('alpine:init', () => {
    Alpine.data('exportTable', () => ({
        datatable: null,
        init() {
            console.log('Initalizing datatable')
            this.datatable = new simpleDatatables.DataTable('#myTable', {
                data: {
                    headings: ['Sr.No.', 'Complaint No.', 'Service Center', 'Technician','Allocation Date', 'Allocation Time', 'Status',
                        'Reason', 'Customer Name',
                        'Customer Contact', 'Address', 'product Catagory', 'Action'
                    ],
                    data: [
                        [1, 'ORP2501240003', 'VIRAR NSP VASAI', 'SANJAY SINGH', '26.01.2024',
                            '04:24 pm',
                            'allocated', '', 'mrintunjay', '9137989561','kalkai Apt C/203 nagindas pada nr machhi…' ,'LED TV',
                            ''
                        ],
                        [2, 'ORP2601240005', 'PALGHAR', '', '',
                            '',
                            'new', '', 'PRASANT', '9766381436','404, A wing, lotus Building, Parasnath Nagri,…' ,'LED TV',
                            ''
                        ],
                        [3, 'ORP2601240004', 'N H SERVICE', 'Waris', '26.01.2024',
                            '03:00 pm',
                            'allocated', '', 'Shailesh', '9723726159','Ekta Complex, Khanvel, near petrol pump ' ,'LED TV',
                            ''
                        ],
                        [4, 'ORP2601240003', 'VIRAR NSP VASAI', 'SANJAY SINGH', '26.01.2024',
                            '12:26 pm',
                            'allocated', '', 'riba', '7709480428','flat n. 209 Shabana apt nr Batul nasr nalasopara…' ,'LED TV',
                            ''
                        ],
                        [5, 'ORP2601240002', 'VIRAR NSP VASAI', 'SANJAY SINGH', '26.01.2024',
                            '04:24 pm',
                            'allocated', '', 'tejas', '9168833983','204 dhurvi appt ganpati mandir vs marg virar e' ,'LED TV',
                            ''
                        ],
                        [6, 'ORP2601240001', 'VIRAR NSP VASAI', 'Kadam', '26.01.2024',
                            '10:57 am',
                            'allocated', '', 'RAM TEK', '7058570751','near Sai Baba mandir khairpada waliv naka vasai…' ,'COLLER',
                            ''
                        ],
                        [7, 'ORP2501240003', 'VIRAR NSP VASAI', 'SANJAY SINGH', '25.01.2024',
                            '07:23 pm',
                            'allocated', '', 'dinesh', '7977681353','room 07 shree sai apt moregaon nsp e nr rajiv…' ,'LED TV',
                            ''
                        ],
                        [8, 'ORP2501240002', 'N H SERVICE', 'Waris', '26.01.2024',
                            '02:25 pm',
                            'closed', 'normal setting ok', 'Amin ', '8511672523','Samarvani School Faliya Silvassa' ,'LED TV',
                            ''
                        ],
                        [9, 'ORP2501240001', 'PALGHAR', 'RANAPRATAP', '26.01.2024',
                            '11:58 am',
                            'allocated', '', 'swpnil', '9356169177','aambheghar kasa tel dahanu dist palghar ' ,'HOME THEATER',
                            ''
                        ],
                        [10, 'ORP2401240005', 'VIRAR NSP VASAI', 'Kadam', '26.01.2024',
                            '11:58 am',
                            'allocated', '', 'Santosh', '8483808528','Room - 305, Sai Prerna society, Alkapuri, Achole…' ,'COLLER',
                            ''
                        ],
                        
                    ],
                },
                perPage: 10,
                perPageSelect: [10, 20, 30, 50, 100],
                columns: [{
                        select: 0,
                        sort: 'asc',
                    },
                    // {
                    //     select: 4,
                    //     render: (data, cell, row) => {
                    //         return this.formatDate(data);
                    //     },
                    // },
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