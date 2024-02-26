<?php
include "db_connect.php";
$obj = new DB_Connect();
date_default_timezone_set('Asia/Kolkata');
session_start();

if (!isset($_SESSION['type'])) {
    header("location:login.php");
    exit;
}

$allowed_pages = array();

if ($_SESSION['type'] == "admin") {
    $allowed_pages = array(
        "index.php",
        "user_profile.php",
        "complaint_demo.php",
        "call_allocation.php",
        "state.php",
        "city.php",
        "area_pincode.php",
        "service_center.php",
        "technician.php",
        "service_type.php",
        "product_category.php",
        "product_service.php",
        "report.php",
        "privacy.php",
        "add_complaint_demo.php",
        "add_call_allocation.php",
        "add_call_history.php",
        "add_state.php",
        "add_city.php",
        "add_area_pincode.php",
        "add_service_center.php",
        "add_technician.php",
        "add_service_type.php",
        "add_product_category.php",
        "add_product_service.php",
        "add_privacy.php"
    );
} elseif ($_SESSION['type'] == "center") {
    $allowed_pages = array(
        "index.php",
        "user_profile.php",
        "call_allocation.php",
        "complaint_demo.php",
        "technician.php",
        "add_call_allocation.php",
        "add_complaint_demo.php",
        "add_technician.php",
        "add_call_history.php"
    );
}

if (!in_array(basename($_SERVER['PHP_SELF']), $allowed_pages)) {
    header("location:pages_error_404.php");
    exit;
}

if (isset($_REQUEST['logout'])) {
    setcookie("msg", "logout", time() + 3600, "/");
    session_destroy();
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Onelife - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/perfect-scrollbar.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/quill.snow.css">
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet" />
    <link defer rel="stylesheet" type="text/css" media="screen" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <link rel="stylesheet" href="./style-main.css" />
    <script src="assets/js/mainScript.js"></script>
    <script src="assets/js/simple-datatables.js"></script>
    <script src="assets/js/perfect-scrollbar.min.js"></script>
    <script defer src="assets/js/popper.min.js"></script>
    <script defer src="assets/js/tippy-bundle.umd.min.js"></script>
    <script src="assets/js/sweetalert.min.js"></script>
    <script>
        window.onload = () => {
            checkCookies();
        }
    </script>
</head>

<body x-data="main" class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased"
    :class="[ $store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ?  'dark' : '', $store.app.menu, $store.app.layout,$store.app.rtlClass]">
    <!-- sidebar menu overlay -->
    <div x-cloak class="fixed inset-0 z-50 bg-[black]/60 lg:hidden" :class="{'hidden' : !$store.app.sidebar}"
        @click="$store.app.toggleSidebar()"></div>

    <!-- scroll to top button -->
    <div class="fixed bottom-6 z-50 ltr:right-6 rtl:left-6" x-data="scrollToTop">
        <template x-if="showTopButton">
            <button type="button"
                class="btn btn-outline-primary animate-pulse rounded-full bg-[#fafafa] p-2 dark:bg-[#060818] dark:hover:bg-primary"
                @click="goToTop">
                <svg width="24" height="24" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                        d="M12 20.75C12.4142 20.75 12.75 20.4142 12.75 20L12.75 10.75L11.25 10.75L11.25 20C11.25 20.4142 11.5858 20.75 12 20.75Z"
                        fill="currentColor" />
                    <path
                        d="M6.00002 10.75C5.69667 10.75 5.4232 10.5673 5.30711 10.287C5.19103 10.0068 5.25519 9.68417 5.46969 9.46967L11.4697 3.46967C11.6103 3.32902 11.8011 3.25 12 3.25C12.1989 3.25 12.3897 3.32902 12.5304 3.46967L18.5304 9.46967C18.7449 9.68417 18.809 10.0068 18.6929 10.287C18.5768 10.5673 18.3034 10.75 18 10.75L6.00002 10.75Z"
                        fill="currentColor" />
                </svg>
            </button>
        </template>
    </div>

    <div class="main-container min-h-screen text-black dark:text-white-dark" :class="[$store.app.navbar]">
        <!-- start sidebar section -->
        <div :class="{'dark text-white-dark' : $store.app.semidark}">
            <nav x-data="sidebar"
                class="sidebar fixed top-0 bottom-0 z-50 h-full min-h-screen w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300">
                <div class="h-full bg-white dark:bg-[#0e1726]">
                    <div class="flex items-center justify-between px-4 py-3">
                        <a href="index.php" class="main-logo flex shrink-0 items-center justify-center">
                            <img class="ml-[5px] logo flex-none" src="assets/images/one_life/logo.png" alt="image" />
                        </a>
                        <a href="javascript:;"
                            class="collapse-icon flex h-8 w-8 items-center rounded-full transition duration-300 hover:bg-gray-500/10 rtl:rotate-180 dark:text-white-light dark:hover:bg-dark-light/10"
                            @click="$store.app.toggleSidebar()">
                            <svg class="m-auto h-5 w-5" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>
                    <ul class='perfect-scrollbar relative h-[calc(100vh-80px)] space-y-0.5 overflow-y-auto overflow-x-hidden p-4 py-0 font-semibold'>
                        <h2 class="-mx-4 mb-1 flex items-center bg-white-light/30 py-3 px-7 font-extrabold uppercase dark:bg-dark dark:bg-opacity-[0.08]">
                            <svg class="hidden h-5 w-4 flex-none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Dashboard</span>
                        </h2>

                        <!------ Both Visible ------>
                        <li class="menu nav-item">
                            <a href="index.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "index.php" ? "active" : "" ?>">
                                <div class="flex items-center">
                                    <svg class="shrink-0  mb-1" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.5"
                                            d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                            fill="currentColor" />
                                        <path
                                            d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span
                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Dashboard</span>
                                </div>
                            </a>
                        </li>
                        <h2 class='-mx-4 mb-1 flex items-center bg-white-light/30 py-3 px-7 font-extrabold uppercase dark:bg-dark dark:bg-opacity-[0.08]'>
                            Complaint And Calls
                        </h2>
                        <li class="menu nav-item">
                            <a href="complaint_demo.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "complaint_demo.php" ? "active" : "" ?>">
                                <div class="flex items-center">
                                    <svg class="shrink-0 " width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle opacity="0.5" cx="15" cy="6" r="3" fill="currentColor"></circle>
                                        <ellipse opacity="0.5" cx="16" cy="17" rx="5" ry="3" fill="currentColor">
                                        </ellipse>
                                        <circle cx="9.00098" cy="6" r="4" fill="currentColor"></circle>
                                        <ellipse cx="9.00098" cy="17.001" rx="7" ry="4" fill="currentColor"></ellipse>
                                    </svg>
                                    <span
                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Complaint
                                        / Demo</span>
                                </div>
                            </a>
                        </li>
                        <li class="menu nav-item">
                            <a href="call_allocation.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "call_allocation.php" ? "active" : "" ?>">
                                <div class="flex items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M18 14C18 18.4183 14.4183 22 10 22C8.76449 22 7.5944 21.7199 6.54976 21.2198C6.19071 21.0479 5.78393 20.9876 5.39939 21.0904L4.17335 21.4185C3.20701 21.677 2.32295 20.793 2.58151 19.8267L2.90955 18.6006C3.01245 18.2161 2.95209 17.8093 2.7802 17.4502C2.28008 16.4056 2 15.2355 2 14C2 9.58172 5.58172 6 10 6C14.4183 6 18 9.58172 18 14ZM6.5 15C7.05228 15 7.5 14.5523 7.5 14C7.5 13.4477 7.05228 13 6.5 13C5.94772 13 5.5 13.4477 5.5 14C5.5 14.5523 5.94772 15 6.5 15ZM10 15C10.5523 15 11 14.5523 11 14C11 13.4477 10.5523 13 10 13C9.44772 13 9 13.4477 9 14C9 14.5523 9.44772 15 10 15ZM13.5 15C14.0523 15 14.5 14.5523 14.5 14C14.5 13.4477 14.0523 13 13.5 13C12.9477 13 12.5 13.4477 12.5 14C12.5 14.5523 12.9477 15 13.5 15Z"
                                            fill="currentColor"></path>
                                        <path opacity="0.6"
                                            d="M17.9842 14.5084C18.0921 14.4638 18.1986 14.4163 18.3035 14.3661C18.5952 14.2264 18.9257 14.1774 19.2381 14.261L20.2343 14.5275C21.0194 14.7376 21.7377 14.0193 21.5277 13.2342L21.2611 12.238C21.1775 11.9256 21.2266 11.595 21.3662 11.3033C21.7726 10.4545 22.0001 9.50385 22.0001 8.5C22.0001 4.91015 19.09 2 15.5001 2C12.7901 2 10.4674 3.6585 9.4917 6.0159C9.65982 6.00535 9.82936 6 10.0001 6C14.4184 6 18.0001 9.58172 18.0001 14C18.0001 14.1708 17.9948 14.3403 17.9842 14.5084Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span
                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Call
                                        Allocation</span>
                                </div>
                            </a>
                        </li>
                        <!------ Both Visible ------>
                        
                        <h2 class='-mx-4 mb-1 flex items-center bg-white-light/30 py-3 px-7 font-extrabold uppercase dark:bg-dark dark:bg-opacity-[0.08]'>
                            Admin Controls
                        </h2>
                        <?php if ($_SESSION['type'] == "admin") { ?>
                                <li class="menu nav-item">
                                    <a href="state.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "state.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <circle opacity="0.5" cx="12" cy="10" r="7" fill="#1C274C"/>
                                                <path d="M9.60212 8.21316C9.47104 6.75421 8.34593 5.39474 7.79976 4.89737L7.49805 4.63933C8.71505 3.61626 10.2854 3 11.9998 3C13.5491 3 14.9809 3.50337 16.1405 4.3555C16.3044 4.85287 15.9923 5.89211 15.6646 6.38947C15.5459 6.56963 15.2767 6.79329 14.9817 7.0053C14.3163 7.48334 13.4767 7.71978 13.0498 8.6C12.9277 8.85162 12.9329 9.09758 12.9916 9.31138C13.0338 9.46509 13.0608 9.63217 13.0612 9.79558C13.0626 10.324 12.5282 10.7058 11.9998 10.7C10.6248 10.685 9.72465 9.57688 9.60212 8.21316Z" fill="#1C274C"/>
                                                <path d="M13.0057 14.3935C13.6974 13.0901 16.003 13.0901 16.003 13.0901C18.4053 13.065 18.7299 11.6064 18.9468 10.8691C18.5585 14.0061 16.0948 16.4997 12.9722 16.9335C12.7463 16.4582 12.4788 15.3865 13.0057 14.3935Z" fill="#1C274C"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.0035 1.49982C18.2797 1.19118 18.7539 1.16491 19.0625 1.44116C21.3246 3.4658 22.75 6.41044 22.75 9.687C22.75 15.4384 18.3612 20.1647 12.75 20.6996V21.25H14C14.4142 21.25 14.75 21.5858 14.75 22C14.75 22.4142 14.4142 22.75 14 22.75H10C9.58579 22.75 9.25001 22.4142 9.25001 22C9.25001 21.5858 9.58579 21.25 10 21.25H11.25V20.7415C8.14923 20.621 5.37537 19.2236 3.44116 17.0625C3.16491 16.7539 3.19118 16.2797 3.49982 16.0035C3.80847 15.7272 4.28261 15.7535 4.55886 16.0622C6.31098 18.0198 8.85483 19.25 11.687 19.25C16.9685 19.25 21.25 14.9685 21.25 9.687C21.25 6.85483 20.0198 4.31098 18.0622 2.55886C17.7535 2.28261 17.7272 1.80847 18.0035 1.49982Z" fill="#1C274C"/>
                                            </svg>
                                            <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">State </span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="city.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "city.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg class="shrink-0 " width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.5"
                                                    d="M19.7165 20.3624C21.143 19.5846 22 18.5873 22 17.5C22 16.3475 21.0372 15.2961 19.4537 14.5C17.6226 13.5794 14.9617 13 12 13C9.03833 13 6.37738 13.5794 4.54631 14.5C2.96285 15.2961 2 16.3475 2 17.5C2 18.6525 2.96285 19.7039 4.54631 20.5C6.37738 21.4206 9.03833 22 12 22C15.1066 22 17.8823 21.3625 19.7165 20.3624Z"
                                                    fill="currentColor"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5 8.51464C5 4.9167 8.13401 2 12 2C15.866 2 19 4.9167 19 8.51464C19 12.0844 16.7658 16.2499 13.2801 17.7396C12.4675 18.0868 11.5325 18.0868 10.7199 17.7396C7.23416 16.2499 5 12.0844 5 8.51464ZM12 11C13.1046 11 14 10.1046 14 9C14 7.89543 13.1046 7 12 7C10.8954 7 10 7.89543 10 9C10 10.1046 10.8954 11 12 11Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                            <span
                                                class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">City </span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="area_pincode.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "area_pincode.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C16.714 2 19.0711 2 20.5355 3.46447C21.0394 3.96833 21.3699 4.57786 21.5867 5.3527L5.3527 21.5867C4.57786 21.3699 3.96833 21.0394 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2ZM5.5 8.75732C5.5 10.5422 6.61708 12.625 8.35997 13.3698C8.76626 13.5434 9.23374 13.5434 9.64003 13.3698C11.3829 12.625 12.5 10.5422 12.5 8.75732C12.5 6.95835 10.933 5.5 9 5.5C7.067 5.5 5.5 6.95835 5.5 8.75732Z" fill="#1C274C"/>
                                                <path d="M10.5 9C10.5 9.82843 9.82843 10.5 9 10.5C8.17157 10.5 7.5 9.82843 7.5 9C7.5 8.17157 8.17157 7.5 9 7.5C9.82843 7.5 10.5 8.17157 10.5 9Z" fill="#1C274C"/>
                                                <g opacity="0.5">
                                                <path d="M21.8893 7.17188C22.0002 8.43338 22.0002 10.0059 22.0002 12.0002C22.0002 16.1339 22.0002 18.4552 21.0128 19.9515L15.0613 14L21.8893 7.17188Z" fill="#1C274C"/>
                                                <path d="M19.9523 21.0123L14.0006 15.0607L7.17188 21.8893C8.43338 22.0002 10.0059 22.0002 12.0002 22.0002C16.1346 22.0002 18.4559 22.0002 19.9523 21.0123Z" fill="#1C274C"/>
                                                </g>
                                            </svg>
                                            <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Area
                                                    Pincode</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="service_center.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "service_center.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10 2H9C6.17157 2 4.75736 2 3.87868 2.87868C3 3.75736 3 5.17157 3 8V21.25H16V17.75V16.25V8C16 5.17157 16 3.75736 15.1213 2.87868C14.2426 2 12.8284 2 10 2Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M3 21.25H2C1.58579 21.25 1.25 21.5858 1.25 22C1.25 22.4142 1.58579 22.75 2 22.75H17.25C17.6642 22.75 18 22.4142 18 22C18 21.5858 17.6642 21.25 17.25 21.25H16H3Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M10.1431 10.386C10.3562 10.0308 10.2411 9.5701 9.88587 9.35699C9.53069 9.14388 9.06999 9.25906 8.85688 9.61424L7.35688 12.1142C7.21786 12.3459 7.21422 12.6345 7.34735 12.8696C7.48048 13.1048 7.7298 13.2501 8 13.2501H9.67536L8.85688 14.6142C8.64377 14.9694 8.75894 15.4301 9.11413 15.6432C9.46931 15.8563 9.93001 15.7412 10.1431 15.386L11.6431 12.886C11.7821 12.6543 11.7858 12.3657 11.6527 12.1306C11.5195 11.8955 11.2702 11.7501 11 11.7501H9.32464L10.1431 10.386Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M16 17.75H17.5714C17.9462 17.75 18.25 18.0538 18.25 18.4286V18.5C18.25 19.7427 19.2574 20.75 20.5 20.75C21.7426 20.75 22.75 19.7427 22.75 18.5V7.60177C22.75 7.44511 22.75 7.33702 22.7441 7.23161C22.6884 6.24063 22.2422 5.31214 21.5031 4.64962C21.4245 4.57915 21.3401 4.51163 21.2178 4.41378L19.9685 3.41438C19.6451 3.15562 19.1731 3.20806 18.9143 3.53151C18.6556 3.85495 18.708 4.32692 19.0315 4.58568L20.2646 5.57215C20.4091 5.68774 20.4585 5.72768 20.5019 5.76653C20.9453 6.16405 21.2131 6.72114 21.2465 7.31573C21.2497 7.37384 21.25 7.43743 21.25 7.62247V8H20.5C19.6716 8 19 8.67157 19 9.5V11.9189C19 12.5645 19.4131 13.1377 20.0257 13.3419L21.25 13.75V18.5C21.25 18.9142 20.9142 19.25 20.5 19.25C20.0858 19.25 19.75 18.9142 19.75 18.5V18.4286C19.75 17.2254 18.7746 16.25 17.5714 16.25H16V17.75Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                            <span
                                                class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Service
                                                Center</span>
                                        </div>
                                    </a>
                                </li>
                        <?php } ?>

                        <!------ Technician Page ------>
                        <li class="menu nav-item">
                            <a href="technician.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "technician.php" ? "active" : "" ?>">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M6 4.5C6 3.56538 6 3.09808 6.20096 2.75C6.33261 2.52197 6.52197 2.33261 6.75 2.20096C7.09808 2 7.56538 2 8.5 2H15.5C16.4346 2 16.9019 2 17.25 2.20096C17.478 2.33261 17.6674 2.52197 17.799 2.75C18 3.09808 18 3.56538 18 4.5C18 5.43462 18 5.90192 17.799 6.25C17.6674 6.47803 17.478 6.66739 17.25 6.79904C16.9019 7 16.4346 7 15.5 7H8.5C7.56538 7 7.09808 7 6.75 6.79904C6.52197 6.66739 6.33261 6.47803 6.20096 6.25C6 5.90192 6 5.43462 6 4.5Z"
                                            fill="#1C274C" />
                                        <path
                                            d="M10 16V20C10 20.9428 10 21.4142 10.2929 21.7071C10.5858 22 11.0572 22 12 22C12.9428 22 13.4142 22 13.7071 21.7071C14 21.4142 14 20.9428 14 20V16C14 15.0572 14 14.5858 13.7071 14.2929C13.4142 14 12.9428 14 12 14C11.0572 14 10.5858 14 10.2929 14.2929C10 14.5858 10 15.0572 10 16Z"
                                            fill="#1C274C" />
                                        <path opacity="0.5"
                                            d="M19.0451 5.25C19.4997 5.25 19.8049 5.25037 20.0414 5.26579C20.2716 5.2808 20.3841 5.30776 20.4581 5.3369C20.7808 5.46395 21.0362 5.71937 21.1633 6.04208C21.1924 6.11609 21.2194 6.22858 21.2344 6.45878C21.2498 6.6953 21.2502 7.00044 21.2502 7.4551C21.2502 8.29243 21.2396 8.52185 21.1788 8.69392C21.0745 8.98918 20.8632 9.23455 20.5867 9.38148C20.4256 9.4671 20.2002 9.5116 19.3722 9.63581L15.2488 10.2543C14.4761 10.3702 13.8275 10.4675 13.315 10.6116C12.772 10.7643 12.2915 10.9923 11.9164 11.4278C11.3356 12.1023 11.2719 12.9787 11.255 13.9875C11.2549 13.9945 11.2549 14.0015 11.2549 14.0085C11.4643 14 11.7098 14 12 14C12.2947 14 12.5433 14 12.7548 14.0089C12.7728 12.9462 12.8717 12.6172 13.053 12.4067C13.1625 12.2796 13.3305 12.1654 13.7211 12.0556C14.132 11.94 14.6863 11.8555 15.5181 11.7307L19.7139 11.1014C20.3646 11.0044 20.8715 10.9288 21.2905 10.7061C21.8989 10.3829 22.3638 9.84304 22.5932 9.19346C22.7512 8.74599 22.7508 8.23353 22.7503 7.57559L22.7502 7.43099C22.7502 7.00662 22.7502 6.65233 22.7312 6.3612C22.7115 6.05823 22.6689 5.77171 22.559 5.49258C22.2795 4.78261 21.7176 4.22069 21.0076 3.94117C20.7285 3.83128 20.442 3.78872 20.139 3.76897C19.8478 3.74999 19.4936 3.74999 19.0692 3.75H18.0002V5.25H19.0451Z"
                                            fill="#1C274C" />
                                        <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.75 4.5C4.75 4.08579 5.08579 3.75 5.5 3.75H6V5.25H5.5C5.08579 5.25 4.75 4.91421 4.75 4.5Z"
                                            fill="#1C274C" />
                                    </svg>
                                    <span
                                        class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Technician</span>
                                </div>
                            </a>
                        </li>
                        <!------ Technician Page ------>

                        <?php if ($_SESSION['type'] == "admin") { ?>
                                <li class="menu nav-item">
                                    <a href="service_type.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "service_type.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M20.3116 12.6473L20.8293 10.7154C21.4335 8.46034 21.7356 7.3328 21.5081 6.35703C21.3285 5.58657 20.9244 4.88668 20.347 4.34587C19.6157 3.66095 18.4881 3.35883 16.2331 2.75458C13.978 2.15033 12.8504 1.84821 11.8747 2.07573C11.1042 2.25537 10.4043 2.65945 9.86351 3.23687C9.27709 3.86298 8.97128 4.77957 8.51621 6.44561C8.43979 6.7254 8.35915 7.02633 8.27227 7.35057L8.27222 7.35077L7.75458 9.28263C7.15033 11.5377 6.84821 12.6652 7.07573 13.641C7.25537 14.4115 7.65945 15.1114 8.23687 15.6522C8.96815 16.3371 10.0957 16.6392 12.3508 17.2435L12.3508 17.2435C14.3834 17.7881 15.4999 18.0873 16.415 17.9744C16.5152 17.9621 16.6129 17.9448 16.7092 17.9223C17.4796 17.7427 18.1795 17.3386 18.7203 16.7612C19.4052 16.0299 19.7074 14.9024 20.3116 12.6473Z"
                                                    fill="#1C274C" />
                                                <path opacity="0.5"
                                                    d="M16.4149 17.9745C16.2064 18.6128 15.8398 19.1903 15.347 19.6519C14.6157 20.3368 13.4881 20.6389 11.2331 21.2432C8.97798 21.8474 7.85044 22.1496 6.87466 21.922C6.10421 21.7424 5.40432 21.3383 4.86351 20.7609C4.17859 20.0296 3.87647 18.9021 3.27222 16.647L2.75458 14.7152C2.15033 12.4601 1.84821 11.3325 2.07573 10.3568C2.25537 9.5863 2.65945 8.88641 3.23687 8.3456C3.96815 7.66068 5.09569 7.35856 7.35077 6.75431C7.7774 6.64 8.16369 6.53649 8.51621 6.44534C8.51618 6.44545 8.51624 6.44524 8.51621 6.44534C8.43979 6.72513 8.3591 7.02657 8.27222 7.35081L7.75458 9.28266C7.15033 11.5377 6.84821 12.6653 7.07573 13.6411C7.25537 14.4115 7.65945 15.1114 8.23687 15.6522C8.96815 16.3371 10.0957 16.6393 12.3508 17.2435C14.3833 17.7881 15.4999 18.0873 16.4149 17.9745Z"
                                                    fill="#1C274C" />
                                            </svg>
                                            <span
                                                class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Service Type</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="product_category.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "product_category.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M22 5C22 6.65685 20.6569 8 19 8C17.3431 8 16 6.65685 16 5C16 3.34315 17.3431 2 19 2C20.6569 2 22 3.34315 22 5Z"
                                                    fill="#1C274C" />
                                                <path opacity="0.5"
                                                    d="M15.612 2.03826C14.59 2 13.3988 2 12 2C7.28595 2 4.92893 2 3.46447 3.46447C2 4.92893 2 7.28595 2 12C2 16.714 2 19.0711 3.46447 20.5355C4.92893 22 7.28595 22 12 22C16.714 22 19.0711 22 20.5355 20.5355C22 19.0711 22 16.714 22 12C22 10.6012 22 9.41 21.9617 8.38802C21.1703 9.08042 20.1342 9.5 19 9.5C16.5147 9.5 14.5 7.48528 14.5 5C14.5 3.86584 14.9196 2.82967 15.612 2.03826Z"
                                                    fill="#1C274C" />
                                                <path
                                                    d="M3.46451 20.5355C4.92902 22 7.28611 22 12.0003 22C16.7145 22 19.0716 22 20.5361 20.5355C21.8931 19.1785 21.9927 17.0551 22 13H18.8402C17.935 13 17.4824 13 17.0846 13.183C16.6868 13.3659 16.3922 13.7096 15.8031 14.3968L15.1977 15.1032C14.6086 15.7904 14.314 16.1341 13.9162 16.317C13.5183 16.5 13.0658 16.5 12.1606 16.5H11.84C10.9348 16.5 10.4822 16.5 10.0844 16.317C9.68655 16.1341 9.392 15.7904 8.80291 15.1032L8.19747 14.3968C7.60837 13.7096 7.31382 13.3659 6.91599 13.183C6.51815 13 6.06555 13 5.16035 13H2C2.0073 17.0551 2.10744 19.1785 3.46451 20.5355Z"
                                                    fill="#1C274C" />
                                            </svg>
                                            <span
                                                class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Product
                                                Category</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="product_service.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "product_service.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M8.42229 20.6181C10.1779 21.5395 11.0557 22.0001 12 22.0001V12.0001L2.63802 7.07275C2.62423 7.09491 2.6107 7.11727 2.5974 7.13986C2 8.15436 2 9.41678 2 11.9416V12.0586C2 14.5834 2 15.8459 2.5974 16.8604C3.19479 17.8749 4.27063 18.4395 6.42229 19.5686L8.42229 20.6181Z" fill="#1C274C"/>
                                                <path opacity="0.7" d="M17.5774 4.43152L15.5774 3.38197C13.8218 2.46066 12.944 2 11.9997 2C11.0554 2 10.1776 2.46066 8.42197 3.38197L6.42197 4.43152C4.31821 5.53552 3.24291 6.09982 2.6377 7.07264L11.9997 12L21.3617 7.07264C20.7564 6.09982 19.6811 5.53552 17.5774 4.43152Z" fill="#1C274C"/>
                                                <path opacity="0.5" d="M21.4026 7.13986C21.3893 7.11727 21.3758 7.09491 21.362 7.07275L12 12.0001V22.0001C12.9443 22.0001 13.8221 21.5395 15.5777 20.6181L17.5777 19.5686C19.7294 18.4395 20.8052 17.8749 21.4026 16.8604C22 15.8459 22 14.5834 22 12.0586V11.9416C22 9.41678 22 8.15436 21.4026 7.13986Z" fill="#1C274C"/>
                                                <path d="M6.32334 4.48382C6.35617 4.46658 6.38926 4.44922 6.42261 4.43172L7.91614 3.64795L17.0169 8.65338L21.0406 6.64152C21.1783 6.79745 21.298 6.96175 21.4029 7.13994C21.5525 7.39396 21.6646 7.66352 21.7487 7.96455L17.7503 9.96373V13.0002C17.7503 13.4144 17.4145 13.7502 17.0003 13.7502C16.5861 13.7502 16.2503 13.4144 16.2503 13.0002V10.7137L12.7503 12.4637V21.9042C12.4934 21.9682 12.2492 22.0002 12.0003 22.0002C11.7515 22.0002 11.5072 21.9682 11.2503 21.9042V12.4637L2.25195 7.96455C2.33601 7.66352 2.44813 7.39396 2.59771 7.13994C2.70264 6.96175 2.82232 6.79745 2.96001 6.64152L12.0003 11.1617L15.3865 9.46857L6.32334 4.48382Z" fill="#1C274C"/>
                                            </svg>
                                        <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Product-Service</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="report.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "report.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path opacity="0.5"
                                                    d="M3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447Z"
                                                    fill="#1C274C" />
                                                <path
                                                    d="M8.96383 6.86264C11.2024 4.71245 14.8198 4.71245 17.0583 6.86264C19.3139 9.02918 19.3139 12.5548 17.0583 14.7213L14.8535 16.8391C13.5891 18.0536 11.5511 18.0536 10.2867 16.8391C9.0053 15.6083 9.0053 13.5997 10.2867 12.3689L12.0506 10.6747C12.3493 10.3877 12.8241 10.3973 13.111 10.696C13.398 10.9947 13.3884 11.4695 13.0897 11.7564L11.3258 13.4507C10.6589 14.0912 10.6589 15.1168 11.3258 15.7573C12.0097 16.4142 13.1305 16.4142 13.8144 15.7573L16.0192 13.6395C17.6603 12.0633 17.6603 9.52069 16.0192 7.94444C14.3612 6.35185 11.661 6.35185 10.0029 7.94444L7.79812 10.0622C6.40063 11.4045 6.40063 13.5679 7.79812 14.9102C8.09685 15.1972 8.1064 15.6719 7.81947 15.9707C7.53253 16.2694 7.05775 16.279 6.75902 15.992C4.74699 14.0594 4.74699 10.913 6.75902 8.98043L8.96383 6.86264Z"
                                                    fill="#1C274C" />
                                            </svg>
                                            <span
                                                class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Report</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="menu nav-item">
                                    <a href="privacy.php" class="nav-link group <?php echo basename($_SERVER["PHP_SELF"]) == "privacy_policy.php" ? "active" : "" ?>">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <g opacity="0.5">
                                                    <path
                                                        d="M14 2.75C15.9068 2.75 17.2615 2.75159 18.2892 2.88976C19.2952 3.02503 19.8749 3.27869 20.2981 3.7019C20.7852 4.18904 20.9973 4.56666 21.1147 5.23984C21.2471 5.9986 21.25 7.08092 21.25 9C21.25 9.41422 21.5858 9.75 22 9.75C22.4142 9.75 22.75 9.41422 22.75 9L22.75 8.90369C22.7501 7.1045 22.7501 5.88571 22.5924 4.98199C22.417 3.97665 22.0432 3.32568 21.3588 2.64124C20.6104 1.89288 19.6615 1.56076 18.489 1.40314C17.3498 1.24997 15.8942 1.24998 14.0564 1.25H14C13.5858 1.25 13.25 1.58579 13.25 2C13.25 2.41421 13.5858 2.75 14 2.75Z"
                                                        fill="#1C274C" />
                                                    <path
                                                        d="M2.00001 14.25C2.41422 14.25 2.75001 14.5858 2.75001 15C2.75001 16.9191 2.75289 18.0014 2.88529 18.7602C3.00275 19.4333 3.21477 19.811 3.70191 20.2981C4.12512 20.7213 4.70476 20.975 5.71085 21.1102C6.73852 21.2484 8.09318 21.25 10 21.25C10.4142 21.25 10.75 21.5858 10.75 22C10.75 22.4142 10.4142 22.75 10 22.75H9.94359C8.10583 22.75 6.6502 22.75 5.51098 22.5969C4.33856 22.4392 3.38961 22.1071 2.64125 21.3588C1.95681 20.6743 1.58304 20.0233 1.40762 19.018C1.24992 18.1143 1.24995 16.8955 1.25 15.0964L1.25001 15C1.25001 14.5858 1.58579 14.25 2.00001 14.25Z"
                                                        fill="#1C274C" />
                                                    <path
                                                        d="M22 14.25C22.4142 14.25 22.75 14.5858 22.75 15L22.75 15.0963C22.7501 16.8955 22.7501 18.1143 22.5924 19.018C22.417 20.0233 22.0432 20.6743 21.3588 21.3588C20.6104 22.1071 19.6615 22.4392 18.489 22.5969C17.3498 22.75 15.8942 22.75 14.0564 22.75H14C13.5858 22.75 13.25 22.4142 13.25 22C13.25 21.5858 13.5858 21.25 14 21.25C15.9068 21.25 17.2615 21.2484 18.2892 21.1102C19.2952 20.975 19.8749 20.7213 20.2981 20.2981C20.7852 19.811 20.9973 19.4333 21.1147 18.7602C21.2471 18.0014 21.25 16.9191 21.25 15C21.25 14.5858 21.5858 14.25 22 14.25Z"
                                                        fill="#1C274C" />
                                                    <path
                                                        d="M9.94359 1.25H10C10.4142 1.25 10.75 1.58579 10.75 2C10.75 2.41421 10.4142 2.75 10 2.75C8.09319 2.75 6.73852 2.75159 5.71085 2.88976C4.70476 3.02503 4.12512 3.27869 3.70191 3.7019C3.21477 4.18904 3.00275 4.56666 2.88529 5.23984C2.75289 5.9986 2.75001 7.08092 2.75001 9C2.75001 9.41422 2.41422 9.75 2.00001 9.75C1.58579 9.75 1.25001 9.41422 1.25001 9L1.25 8.90369C1.24995 7.10453 1.24992 5.8857 1.40762 4.98199C1.58304 3.97665 1.95681 3.32568 2.64125 2.64124C3.38961 1.89288 4.33856 1.56076 5.51098 1.40314C6.65019 1.24997 8.10584 1.24998 9.94359 1.25Z"
                                                        fill="#1C274C" />
                                                </g>
                                                <path
                                                    d="M12 10.75C11.3096 10.75 10.75 11.3096 10.75 12C10.75 12.6904 11.3096 13.25 12 13.25C12.6904 13.25 13.25 12.6904 13.25 12C13.25 11.3096 12.6904 10.75 12 10.75Z"
                                                    fill="#1C274C" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.89243 14.0598C5.29747 13.3697 5 13.0246 5 12C5 10.9754 5.29748 10.6303 5.89242 9.94021C7.08037 8.56222 9.07268 7 12 7C14.9273 7 16.9196 8.56222 18.1076 9.94021C18.7025 10.6303 19 10.9754 19 12C19 13.0246 18.7025 13.3697 18.1076 14.0598C16.9196 15.4378 14.9273 17 12 17C9.07268 17 7.08038 15.4378 5.89243 14.0598ZM9.25 12C9.25 10.4812 10.4812 9.25 12 9.25C13.5188 9.25 14.75 10.4812 14.75 12C14.75 13.5188 13.5188 14.75 12 14.75C10.4812 14.75 9.25 13.5188 9.25 12Z"
                                                    fill="#1C274C" />
                                            </svg>
                                            <span
                                                class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Privacy
                                                Policy</span>
                                        </div>
                                    </a>
                                </li>
                        <?php } ?>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- end sidebar section -->

        <div class="main-content">
            <!-- start header section -->
            <header>
                <div class="shadow-sm">
                    <div class="relative flex w-full items-center bg-white px-5 py-2.5 dark:bg-[#0e1726]">
                        <div class="horizontal-logo flex items-center justify-between ltr:mr-2 rtl:ml-2 lg:hidden">
                            <a href="index.php" class="main-logo flex shrink-0 items-center justify-center">
                                <img class="ml-[5px] logo flex-none" src="assets/images/one_life/logo.png" alt="image" />
                            </a>

                            <a href="javascript:;"
                                class="collapse-icon flex flex-none rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary ltr:ml-2 rtl:mr-2 dark:bg-dark/40 dark:text-[#d0d2d6] dark:hover:bg-dark/60 dark:hover:text-primary lg:hidden"
                                @click="$store.app.toggleSidebar()">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 7L4 7" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path opacity="0.5" d="M20 12L4 12" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M20 17L4 17" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                </svg>
                            </a>
                        </div>
                        <div x-data="header" class="flex justify-between items-center ltr:ml-auto rtl:mr-auto rtl:space-x-reverse dark:text-[#d0d2d6] sm:flex-1 ltr:sm:ml-0 sm:rtl:mr-0">
                            <div class="sm:rtl:ml-auto" x-data="{ search: false }" @click.outside="search = false">
                                <div>
                                    <p class="flex items-center text-base font-bold text-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M6.94028 2C7.35614 2 7.69326 2.32421 7.69326 2.72414V4.18487C8.36117 4.17241 9.10983 4.17241 9.95219 4.17241H13.9681C14.8104 4.17241 15.5591 4.17241 16.227 4.18487V2.72414C16.227 2.32421 16.5641 2 16.98 2C17.3958 2 17.733 2.32421 17.733 2.72414V4.24894C19.178 4.36022 20.1267 4.63333 20.8236 5.30359C21.5206 5.97385 21.8046 6.88616 21.9203 8.27586L22 9H2.92456H2V8.27586C2.11571 6.88616 2.3997 5.97385 3.09665 5.30359C3.79361 4.63333 4.74226 4.36022 6.1873 4.24894V2.72414C6.1873 2.32421 6.52442 2 6.94028 2Z" fill="#1C274C"/>
                                            <path opacity="0.5" d="M21.9995 14.0001V12.0001C21.9995 11.161 21.9963 9.66527 21.9834 9H2.00917C1.99626 9.66527 1.99953 11.161 1.99953 12.0001V14.0001C1.99953 17.7713 1.99953 19.6569 3.1711 20.8285C4.34267 22.0001 6.22829 22.0001 9.99953 22.0001H13.9995C17.7708 22.0001 19.6564 22.0001 20.828 20.8285C21.9995 19.6569 21.9995 17.7713 21.9995 14.0001Z" fill="#1C274C"/>
                                            <path d="M18 17C18 17.5523 17.5523 18 17 18C16.4477 18 16 17.5523 16 17C16 16.4477 16.4477 16 17 16C17.5523 16 18 16.4477 18 17Z" fill="#1C274C"/>
                                            <path d="M18 13C18 13.5523 17.5523 14 17 14C16.4477 14 16 13.5523 16 13C16 12.4477 16.4477 12 17 12C17.5523 12 18 12.4477 18 13Z" fill="#1C274C"/>
                                            <path d="M13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16C12.5523 16 13 16.4477 13 17Z" fill="#1C274C"/>
                                            <path d="M13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12C12.5523 12 13 12.4477 13 13Z" fill="#1C274C"/>
                                            <path d="M8 17C8 17.5523 7.55228 18 7 18C6.44772 18 6 17.5523 6 17C6 16.4477 6.44772 16 7 16C7.55228 16 8 16.4477 8 17Z" fill="#1C274C"/>
                                            <path d="M8 13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13C6 12.4477 6.44772 12 7 12C7.55228 12 8 12.4477 8 13Z" fill="#1C274C"/>
                                        </svg>
                                        <span class="ml-2 mt-1"><?php echo date('d-m-Y') ?></span>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-primary"><?php echo isset($_SESSION["type"]) && $_SESSION["type"] == "center" ? 'Service center' : 'SuperAdmin' ?></h3>
                            </div>
                            <div class="flex gap-2">
                                <div class="dropdown" x-data="dropdown" @click.outside="open = false">
                                    <a href="javascript:;"
                                        class="relative block rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                                        @click="toggle">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <path
                                                d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M12 6V10" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>

                                        <span class="absolute top-0 flex h-3 w-3 ltr:right-0 rtl:left-0">
                                            <span
                                                class="absolute -top-[3px] inline-flex h-full w-full animate-ping rounded-full bg-success/50 opacity-75 ltr:-left-[3px] rtl:-right-[3px]"></span>
                                            <span
                                                class="relative inline-flex h-[6px] w-[6px] rounded-full bg-success"></span>
                                        </span>
                                    </a>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms
                                        class="top-11 w-[300px] divide-y !py-0 text-dark ltr:-right-2 rtl:-left-2 dark:divide-white/10 dark:text-white-dark sm:w-[350px]">
                                        <li>
                                            <div class="flex items-center justify-between px-4 py-2 font-semibold hover:!bg-transparent">
                                                <h4 class="text-lg">Notification</h4>
                                                <template x-if="notifications.length">
                                                    <span class="badge bg-primary/80" x-text="notifications.length + 'New'"></span>
                                                </template>
                                            </div>
                                        </li>
                                        <template x-for="notification in notifications">
                                            <li class="dark:text-white-light/90">
                                                <div class="group flex items-center px-4 py-2" @click.self="toggle">
                                                    <div class="flex flex-auto ltr:pl-3 rtl:pr-3">
                                                        <div class="ltr:pr-3 rtl:pl-3">
                                                            <h6 x-html="notification.message"></h6>
                                                            <span class="block text-xs font-normal dark:text-gray-500"
                                                                x-text="notification.time"></span>
                                                        </div>
                                                        <button type="button"
                                                            class="text-neutral-300 opacity-0 hover:text-danger group-hover:opacity-100 ltr:ml-auto rtl:mr-auto"
                                                            @click="removeNotification(notification.id)">
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <circle opacity="0.5" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="1.5" />
                                                                <path d="M14.5 9.50002L9.5 14.5M9.49998 9.5L14.5 14.5"
                                                                    stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        </template>
                                        <template x-if="notifications.length">
                                            <li>
                                                <div class="p-4">
                                                    <button class="btn btn-primary btn-small block w-full"
                                                        @click="toggle">Read All Notifications</button>
                                                </div>
                                            </li>
                                        </template>
                                        <template x-if="!notifications.length">
                                            <li>
                                                <div
                                                    class="!grid min-h-[200px] place-content-center text-lg hover:!bg-transparent">
                                                    <div
                                                        class="mx-auto mb-4 rounded-full text-primary ring-4 ring-primary/30">
                                                        <svg width="40" height="40" viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.5"
                                                                d="M20 10C20 4.47715 15.5228 0 10 0C4.47715 0 0 4.47715 0 10C0 15.5228 4.47715 20 10 20C15.5228 20 20 15.5228 20 10Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M10 4.25C10.4142 4.25 10.75 4.58579 10.75 5V11C10.75 11.4142 10.4142 11.75 10 11.75C9.58579 11.75 9.25 11.4142 9.25 11V5C9.25 4.58579 9.58579 4.25 10 4.25Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M10 15C10.5523 15 11 14.5523 11 14C11 13.4477 10.5523 13 10 13C9.44772 13 9 13.4477 9 14C9 14.5523 9.44772 15 10 15Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </div>
                                                    No data available.
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div class="dropdown flex-shrink-0" x-data="dropdown" @click.outside="open = false">
                                    <a href="javascript:;" class="group relative" @click="toggle()">
                                        <span>
                                            <img class="h-9 w-9 rounded-full object-cover saturate-50 group-hover:saturate-100"
                                                src="./profile.png" alt="image" />
                                        </span>
                                    </a>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms
                                        class="top-11 w-[230px] !py-0 font-semibold text-dark ltr:right-0 rtl:left-0 dark:text-white-dark dark:text-white-light/90">
                                        <li>
                                            <div class="flex items-center px-4 py-4">
                                                <div class="flex-none">
                                                    <img class="h-10 w-10 rounded-md object-cover"
                                                        src="./profile.png" alt="image" />
                                                </div>
                                                <div class="truncate ltr:pl-4 rtl:pr-4">
                                                    <h4 class="text-base">
                                                        Welcome Admin !
                                                        <span class="rounded bg-success-light px-1 text-xs text-success ltr:ml-2 rtl:ml-2">Pro</span>
                                                    </h4>
                                                    <a class="text-black/60 hover:text-primary dark:text-dark-light/60 dark:hover:text-white"
                                                        href="javascript:;">johndoe@gmail.com</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="user_profile.php" class="dark:hover:text-white" @click="toggle">
                                                <svg class="h-4.5 w-4.5 shrink-0 ltr:mr-2 rtl:ml-2" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5" />
                                                    <path opacity="0.5"
                                                        d="M20 17.5C20 19.9853 20 22 12 22C4 22 4 19.9853 4 17.5C4 15.0147 7.58172 13 12 13C16.4183 13 20 15.0147 20 17.5Z"
                                                        stroke="currentColor" stroke-width="1.5" />
                                                </svg>
                                                Profile</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" class="dark:hover:text-white" @click="toggle">
                                                <svg class="h-4.5 w-4.5 shrink-0 ltr:mr-2 rtl:ml-2" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.5"
                                                        d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12Z"
                                                        stroke="currentColor" stroke-width="1.5" />
                                                    <path
                                                        d="M6 8L8.1589 9.79908C9.99553 11.3296 10.9139 12.0949 12 12.0949C13.0861 12.0949 14.0045 11.3296 15.8411 9.79908L18 8"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                </svg>
                                                Inbox</a>
                                        </li>
                                        <li>
                                            <a href="" class="dark:hover:text-white"
                                                @click="toggle">
                                                <svg class="h-4.5 w-4.5 shrink-0 ltr:mr-2 rtl:ml-2" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2 16C2 13.1716 2 11.7574 2.87868 10.8787C3.75736 10 5.17157 10 8 10H16C18.8284 10 20.2426 10 21.1213 10.8787C22 11.7574 22 13.1716 22 16C22 18.8284 22 20.2426 21.1213 21.1213C20.2426 22 18.8284 22 16 22H8C5.17157 22 3.75736 22 2.87868 21.1213C2 20.2426 2 18.8284 2 16Z"
                                                        stroke="currentColor" stroke-width="1.5" />
                                                    <path opacity="0.5"
                                                        d="M6 10V8C6 4.68629 8.68629 2 12 2C15.3137 2 18 4.68629 18 8V10"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                    <g opacity="0.5">
                                                        <path
                                                            d="M9 16C9 16.5523 8.55228 17 8 17C7.44772 17 7 16.5523 7 16C7 15.4477 7.44772 15 8 15C8.55228 15 9 15.4477 9 16Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M13 16C13 16.5523 12.5523 17 12 17C11.4477 17 11 16.5523 11 16C11 15.4477 11.4477 15 12 15C12.5523 15 13 15.4477 13 16Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M17 16C17 16.5523 16.5523 17 16 17C15.4477 17 15 16.5523 15 16C15 15.4477 15.4477 15 16 15C16.5523 15 17 15.4477 17 16Z"
                                                            fill="currentColor" />
                                                    </g>
                                                </svg>
                                                Lock Screen</a>
                                        </li>
                                        <li class="border-t border-white-light dark:border-white-light/10">
                                            <a href="?logout" class="!py-3 text-danger" @click="toggle">
                                                <svg class="h-4.5 w-4.5 shrink-0 rotate-90 ltr:mr-2 rtl:ml-2" width="18"
                                                    height="18" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.5"
                                                        d="M17 9.00195C19.175 9.01406 20.3529 9.11051 21.1213 9.8789C22 10.7576 22 12.1718 22 15.0002V16.0002C22 18.8286 22 20.2429 21.1213 21.1215C20.2426 22.0002 18.8284 22.0002 16 22.0002H8C5.17157 22.0002 3.75736 22.0002 2.87868 21.1215C2 20.2429 2 18.8286 2 16.0002L2 15.0002C2 12.1718 2 10.7576 2.87868 9.87889C3.64706 9.11051 4.82497 9.01406 7 9.00195"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                    <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                Sign Out
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- end header section -->