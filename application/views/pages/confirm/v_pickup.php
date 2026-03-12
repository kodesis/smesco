<div class="card-body">
    <h2 class="h2 text-center mb-4"><?= $title ?></h2>

    <?= $this->session->flashdata('message_name'); ?>

    <?php
    if ($status == '1') {
    ?>
        <form method="POST" action="<?= base_url('confirm/finishPickup') ?>" autocomplete="off" novalidate enctype="multipart/form-data">
            <div class="mb-3">
                <label for="kode_booking" class="form-label">Kode Booking</label>
                <input type="text" name="kode_booking" id="kode_booking" class="form-control" value="<?= $no_booking ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Bukti foto</label>
                <input type="file" name="file_upload" id="file_upload" class="form-control">
                <?= form_error('file_upload', '<small class="text-danger">', '</small>'); ?>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-submit w-100">Submit</button>
            </div>
        </form>
    <?php
    } ?>

</div>