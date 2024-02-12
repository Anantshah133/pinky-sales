<?php
include "header.php";
?>

<div class="p-6">
    <form class="mb-5 panel rounded-md border border-[#ebedf2] bg-white p-4 dark:border-[#191e3a] dark:bg-[#0e1726]">
        <h6 class="mb-5 font-bold text-primary text-xl">General Information</h6>
        <div class="flex flex-col sm:flex-row">
            <div class="grid flex-1 grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="font-bold" for="name">Full Name</label>
                    <input id="name" type="text" placeholder="Jimmy Turner" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" for="profession">Profession</label>
                    <input id="profession" type="text" placeholder="Web Developer" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" for="country">Country</label>
                    <select id="country" class="form-select text-white-dark">
                        <option>All Countries</option>
                        <option selected="">United States</option>
                        <option>India</option>
                        <option>Japan</option>
                        <option>China</option>
                        <option>Brazil</option>
                        <option>Norway</option>
                        <option>Canada</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold" for="address">Address</label>
                    <input id="address" type="text" placeholder="New York" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" for="location">Location</label>
                    <input id="location" type="text" placeholder="Location" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" for="phone">Phone</label>
                    <input id="phone" type="text" placeholder="+1 (530) 555-12121" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" for="email">Email</label>
                    <input id="email" type="email" placeholder="Jimmy@gmail.com" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" for="web">Website</label>
                    <input id="web" type="text" placeholder="Enter URL" class="form-input" />
                </div>
                <div>
                    <label class="font-bold" class="inline-flex cursor-pointer">
                        <input type="checkbox" class="form-checkbox" />
                        <span class="relative text-white-dark checked:bg-none">Make this my default address</span>
                    </label>
                </div>
                <div class="mt-3 sm:col-span-2">
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
include "footer.php";
?>