<?php
include "header.php";
setcookie("editId", "", time() - 3600);
setcookie("viewId", "", time() - 3600);
?>
<main class='p-6' x-data="sales">
    <div class="pt-5">
        <div class="mb-6 grid grid-cols-1 gap-6 text-white sm:grid-cols-2 xl:grid-cols-4">
            <!-- Users Visit -->
            <div class="panel bg-gradient-to-r from-cyan-500 to-cyan-400">
                <div class="flex justify-between">
                    <div class="text-md font-semibold ltr:mr-1 rtl:ml-1">New calls</div>
                </div>
                <div class="mt-5 flex items-center">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">35</div>
                    <div class="badge bg-white/30">+ 2.35%</div>
                </div>
                <div class="mt-5 flex items-center font-semibold">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0 ltr:mr-2 rtl:ml-2">
                        <path opacity="0.5"
                            d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                        <path
                            d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                    </svg>
                    Last Week 169
                </div>
            </div>

            <!-- Sessions -->
            <div class="panel bg-gradient-to-r from-violet-500 to-violet-400">
                <div class="flex justify-between">
                    <div class="text-md font-semibold ltr:mr-1 rtl:ml-1">Allocated Call</div>
                </div>
                <div class="mt-5 flex items-center">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">24</div>
                    <div class="badge bg-white/30">- 2.35%</div>
                </div>
                <div class="mt-5 flex items-center font-semibold">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0 ltr:mr-2 rtl:ml-2">
                        <path opacity="0.5"
                            d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                        <path
                            d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                    </svg>
                    Last Week 120
                </div>
            </div>

            <!-- Time On-Site -->
            <div class="panel bg-gradient-to-r from-blue-500 to-blue-400">
                <div class="flex justify-between">
                    <div class="text-md font-semibold ltr:mr-1 rtl:ml-1">Pending Call</div>
                </div>
                <div class="mt-5 flex items-center">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">148</div>
                    <div class="badge bg-white/30">+ 1.35%</div>
                </div>
                <div class="mt-5 flex items-center font-semibold">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0 ltr:mr-2 rtl:ml-2">
                        <path opacity="0.5"
                            d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                        <path
                            d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                    </svg>
                    Last Week 170
                </div>
            </div>

            <!-- Bounce Rate -->
            <div class="panel bg-gradient-to-r from-fuchsia-500 to-fuchsia-400">
                <div class="flex justify-between">
                    <div class="text-md font-semibold ltr:mr-1 rtl:ml-1">Closed call</div>
                </div>
                <div class="mt-5 flex items-center">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">70</div>
                    <div class="badge bg-white/30">- 0.35%</div>
                </div>
                <div class="mt-5 flex items-center font-semibold">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0 ltr:mr-2 rtl:ml-2">
                        <path opacity="0.5"
                            d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                        <path
                            d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                            stroke="currentColor" stroke-width="1.5"></path>
                    </svg>
                    Last Week 170
                </div>
            </div>
        </div>
    </div>
    <div class='mb-6 grid gap-6 xl:grid-cols-3'>
        <div class="panel xl:col-span-2 shadow-md">
            <div class="mb-5 flex items-center">
                <h5 class="text-2xl text-primary font-bold dark:text-white-light">
                    Daily Calls
                </h5>
                <!-- <span class="block text-sm font-normal text-white-dark">Go to columns for details.</span> -->
                <div class="relative ltr:ml-auto rtl:mr-auto">
                    <div
                        class="grid h-11 w-11 place-content-center rounded-full bg-[#ffeccb] text-warning dark:bg-warning dark:text-[#ffeccb]">
                        <i class="ri-phone-line text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden">
                <div x-ref="dailySales" class="rounded-lg bg-white dark:bg-black">
                    <!-- loader -->
                    <div
                        class="grid min-h-[175px] place-content-center bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08]">
                        <span
                            class="inline-flex h-5 w-5 animate-spin rounded-full border-2 border-black !border-l-transparent dark:border-white"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel shadow-md">
            <div class="mb-5 flex items-center">
                <h5 class="text-2xl text-primary font-bold dark:text-white-light">Calls Data</h5>
            </div>
            <div class="overflow-hidden">
                <div x-ref="salesByCategory" class="rounded-lg bg-white dark:bg-black">
                    <!-- loader -->
                    <div
                        class="grid min-h-[353px] place-content-center bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08]">
                        <span
                            class="inline-flex h-5 w-5 animate-spin rounded-full border-2 border-black !border-l-transparent dark:border-white"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('sales', () => ({
        init() {
            isDark = this.$store.app.theme === 'dark' || this.$store.app.isDarkMode ? true : false;
            isRtl = this.$store.app.rtlClass === 'rtl' ? true : false;

            const revenueChart = null;
            const salesByCategory = null;
            const dailySales = null;
            const totalOrders = null;

            // revenue
            setTimeout(() => {
                this.salesByCategory = new ApexCharts(this.$refs.salesByCategory, this
                    .salesByCategoryOptions);
                this.$refs.salesByCategory.innerHTML = '';
                this.salesByCategory.render();

                // daily sales
                this.dailySales = new ApexCharts(this.$refs.dailySales, this
                    .dailySalesOptions);
                this.$refs.dailySales.innerHTML = '';
                this.dailySales.render();
            }, 300);

            this.$watch('$store.app.theme', () => {
                isDark = this.$store.app.theme === 'dark' || this.$store.app.isDarkMode ?
                    true : false;
                this.dailySales.updateOptions(this.dailySalesOptions);
                this.salesByCategory.updateOptions(this.salesByCategoryOptions);
            });
        },

        get salesByCategoryOptions() {
            return {
                series: [20, 138, 70, 126],
                chart: {
                    type: 'donut',
                    height: 400,
                    fontFamily: 'Nunito, sans-serif',
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    show: true,
                    width: 25,
                    colors: isDark ? '#0e1726' : '#fff',
                },
                colors: isDark ? ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'] : ['#e2a03f',
                    '#5c1ac3', '#e7515a', '#2eb384'
                ],
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    markers: {
                        width: 10,
                        height: 10,
                        offsetX: -2,
                    },
                    height: 50,
                    offsetY: 20,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            background: 'transparent',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '25px',
                                    offsetY: -10,
                                },
                                value: {
                                    show: true,
                                    fontSize: '26px',
                                    color: isDark ? '#bfc9d4' : undefined,
                                    offsetY: 16,
                                    formatter: (val) => {
                                        return val;
                                    },
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#888ea8',
                                    fontSize: '22px',
                                    formatter: (w) => {
                                        return w.globals.seriesTotals.reduce(function(a,
                                            b) {
                                            return a + b;
                                        }, 0);
                                    },
                                },
                            },
                        },
                    },
                },
                labels: ['New Call', 'Allocated Call', 'Pending Call', 'Closed Call'],
                states: {
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0.15,
                        },
                    },
                    active: {
                        filter: {
                            type: 'none',
                            value: 0.15,
                        },
                    },
                },
            };
        },

        get dailySalesOptions() {
            return {
                series: [{
                        name: 'Calls',
                        data: [44, 55, 41, 67, 22, 43, 21],
                    },
                    {
                        name: 'Last Week',
                        data: [13, 23, 20, 33, 13, 27, 33],
                    },
                ],
                chart: {
                    height: 250,
                    type: 'bar',
                    fontFamily: 'Nunito, sans-serif',
                    toolbar: {
                        show: false,
                    },
                    stacked: true,
                    stackType: '100%',
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    show: true,
                    width: 1,
                },
                colors: ['#2eb384', '#e0e6ed'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0,
                        },
                    },
                }, ],
                xaxis: {
                    labels: {
                        show: false,
                    },
                    categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'],
                },
                yaxis: {
                    show: false,
                },
                fill: {
                    opacity: 1,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '25%',
                    },
                },
                legend: {
                    show: false,
                },
                grid: {
                    show: false,
                    xaxis: {
                        lines: {
                            show: false,
                        },
                    },
                    padding: {
                        top: 10,
                        right: -20,
                        bottom: -20,
                        left: -20,
                    },
                },
            };
        },
    }));
})
</script>

<?php
include "footer.php";
?>