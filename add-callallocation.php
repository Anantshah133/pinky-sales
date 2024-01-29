<?php
include "header.php";
?>
<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-lg font-semibold dark:text-white-light">Call Allocation- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5">
                <div>
                    <label for="groupFname"> Complaint No. </label>
                    <input id="groupFname" type="text" class="form-input" />
                </div>
                <div>
                    <label for="groupFname"> Service Center</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>MIRA BHAYANDER</option>
                        <option>N H SERVICE</option>
                        <option>NO SERVICE</option>
                        <option>PALGHAR</option>
                        <option>Test Service center</option>
                        <option>VIRAR NSP VASAI</option>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Product Serial NO. </label>
                    <input id="groupFname" type="text" class="form-input" />
                </div>
                <div>
                    <label for="groupFname"> Serial NO. Image </label>
                    <input type="file" id="myfile" name="myfile">
                </div>
                <div>
                    <label for="groupFname"> Product Model </label>
                    <input id="groupFname" type="text" class="form-input" />
                </div>
                <div>
                    <label for="groupFname"> Product Model Image </label>
                    <input type="file" id="myfile" name="myfile">
                </div>
                <div>
                    <label for="groupFname"> Purchase Date </label>
                    <input id="groupFname" type="text" class="form-input" />
                </div>
                <div>
                    <label for="groupFname"> Purchase Date Image </label>
                    <input type="file" id="myfile" name="myfile">
                </div>
                <div>
                    <label for="groupFname"> Technician </label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>Deepak Kumar</option>
                        <option>Kadam</option>
                        <option>MAHENDRA</option>
                        <option>Nagender Tiwari</option>
                        <option>RANAPRATAP</option>
                        <option>SANJAY SINGH</option>
                        <option>Tech 1</option>
                        <option>VILAS</option>
                        <option>Waris</option>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Status</label>

                    <select class="form-select text-white-dark">
                        <option>-none-</option>
                        <option>New</option>
                        <option>Pending</option>
                        <option>Canclled</option>
                        <option>Closed</option>
                        <option>Allocated</option>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Reason </label>
                    <input id="groupFname" type="text" class="form-input" />
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="button" class="btn btn-primary">Save </button>
                        <button type="button" class="btn  btn-warning ">Close</button>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>

<?php
include "footer.php";
?>