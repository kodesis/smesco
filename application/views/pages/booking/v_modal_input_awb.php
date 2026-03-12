<form method='POST' action='<?= base_url('booking/updateAwb/' . $id) ?>'>
    <div class='row'>
        <div class="col-md-12 col-12">
            <div class="mb-3">
                <label for="Titik jemput" class="form-label">AWB</label>
                <input type="text" name="awb" id="awb" class="form-control" placeholder="Masukkan awb..." value="">
            </div>
        </div>
    </div>
    <div class='row mt-2'>
        <div class='col-12 text-end'><button type='submit' class='btn btn-primary btn-submit'>Update AWB</button></div>
    </div>
</form>