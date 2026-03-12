<div class="card-body">
    <h2 class="h2 text-center mb-4"><?= $title ?></h2>

    <?= $this->session->flashdata('message_name'); ?>

    <?php
    if ($status == '1') {
    ?>
        <form method="POST" action="<?= base_url('confirm/finishDelivery') ?>" autocomplete="off" novalidate enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="resi" class="form-label">No. Resi</label>
                        <input type="text" name="resi" id="resi" class="form-control" value="<?= $resi['no_resi'] ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="jenis_barang" class="form-label">Jenis barang</label>
                        <input type="text" name="jenis_barang" id="jenis_barang" class="form-control" value="<?= $commodity ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="qty" class="form-label">Qty</label>
                        <input type="text" name="qty" id="qty" class="form-control" value="<?= $resi['qty'] ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="chargeable" class="form-label">Chargeable</label>
                        <input type="text" name="chargeable" id="chargeable" class="form-control" value="<?= $resi['chargeable'] ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-12">
                    <div class="mb-3">
                        <label for="nama_penerima" class="form-label">Nama penerima</label>
                        <input type="text" name="nama_penerima" id="nama_penerima" class="form-control" value="<?= $resi['nama_penerima'] ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-12">
                    <div class="mb-3">
                        <label for="alamat_penerima" class="form-label">Alamat penerima</label>
                        <input type="text" name="alamat_penerima" id="alamat_penerima" class="form-control" value="<?= $resi['alamat_penerima'] ?>" readonly>
                    </div>
                </div>
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