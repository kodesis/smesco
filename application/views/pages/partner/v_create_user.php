<form method="POST" action="<?= base_url('auth/processReview/' . $partner['Id']) ?>" autocomplete="off">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <select name="hasil_review" id="hasil_review" class="form-select" required>
                    <option value="">:: Pilih hasil review</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <div class="d-flex mt-5">
            <button type="button" class="btn btn-secondary ms-auto" data-bs-dismiss="modal" aria-label="Close">Tutup</button>

            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            <button type="submit" class="btn btn-primary btn-submit ms-1">
                Simpan
            </button>
        </div>
    </div>
</form>