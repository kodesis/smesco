<form method="POST" action="<?= base_url('partner/processTopUpSaldo/' . $partner['Id']) ?>" autocomplete="off" enctype="multipart/form-data">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6 col-12 mb-3">
                <label for="nominal" class="form-label">Nominal</label>
                <input type="text" name="nominal" id="nominal" class="form-control angka" placeholder="Masukkan nominal topup" required>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <label for="tanggal" class="form-label">Tanggal transfer</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" required>
            </div>
            <div class="col-12 mb-3">
                <label for="bukti_transfer" class="form-label">Bukti transfer</label>
                <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control">
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <div class="d-flex mt-5">
            <button type="button" class="btn me-auto" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
            <button type="submit" class="btn btn-primary btn-submit ms-auto">
                Simpan
            </button>
        </div>
    </div>
</form>