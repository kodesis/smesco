<?php
$today = date('Y-m-d');

$tanggal_pickup = $awb['tanggal_pickup'];
$min = (!$tanggal_pickup) ? "min='$today'" : '';
?>
<form method='POST' action='<?= base_url('booking/setPickup/' . $awb['awb']) ?>'>
    <div class='row'>
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label for="Titik jemput" class="form-label">Titik jemput</label>
                <input type="text" name="titik_jemput" id="titik_jemput" class="form-control" value="<?= $awb['alamat_pickup'] ?>" readonly>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label for="Gudang tujuan" class="form-label">Gudang tujuan</label>
                <input type="text" name="gudang_tujuan" id="gudang_tujuan" class="form-control" value="<?= $awb['gudang_tujuan'] ?>" readonly>
            </div>
        </div>
        <div class='col-md-6 col-12'>
            <div class='mb-3'>
                <label class='form-label'>Driver</label>
                <select class='form-select' name='driver_id' required>
                    <option value=''>:: Pilih driver pickup</option>

                    <?php
                    foreach ($drivers as $d) {
                        $driver_id = htmlspecialchars($d->Id, ENT_QUOTES, 'UTF-8');
                        $driver_name = htmlspecialchars($d->name, ENT_QUOTES, 'UTF-8');
                        $selected = ($driver_id == $awb['id_driver']) ? 'selected' : '';
                    ?>
                        <option <?= $selected ?> value='<?= $driver_id ?>'><?= $driver_name ?></option>
                    <?php
                    }
                    ?>

                </select>
            </div>
        </div>
        <div class='col-md-6 col-12'>
            <div class='mb-3'>
                <label class='form-label'>Tanggal pickup</label>
                <input type='date' class='form-control' name='tanggal_pickup' <?= $min ?> value='<?= $tanggal_pickup ?>' required>
            </div>
        </div>
    </div>

    <div class='row mt-2'>
        <div class='col-12 text-end'><button type='submit' class='btn btn-primary btn-submit'>Set Pickup</button></div>
    </div>
</form>