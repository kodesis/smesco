<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-3">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Buat Master AWB (Console Penerbangan)</h2>
				<div class="text-muted small mt-1">Langkah awal konsolidasi kargo sebelum pembagian koli/karung.</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card card-md border-primary">
					<div class="card-header bg-primary-lt">
						<h3 class="card-title text-primary"><?= tabler_icon('plane-departure', 'me-2') ?> Data Surat Muatan Udara (SMU) / AWB</h3>
					</div>

					<?= form_open('shipment/save_awb', ['id' => 'form-create-awb']) ?>
					<div class="card-body">
						<div class="row">

							<div class="col-md-6 mb-3">
								<label class="form-label required">Nomor Master AWB / SMU</label>
								<input type="text" name="awb_number" class="form-control" placeholder="Contoh: 126-12345674" oninput="this.value = this.value.toUpperCase()" required>
								<small class="text-muted">Nomor unik dari maskapai kargo.</small>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label required">Maskapai (Airlines)</label>
								<select name="airline_id" class="form-select select2" required>
									<option value="">- Pilih Maskapai -</option>
									<?php foreach ($airlines as $row): ?>
										<option value="<?= $row->id ?>"><?= $row->code ?> - <?= $row->name ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label required">Nomor Penerbangan (Flight No.)</label>
								<input type="text" name="flight_number" class="form-control" placeholder="Contoh: GA-123" oninput="this.value = this.value.toUpperCase()" required>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label required">Jadwal Keberangkatan (ETD)</label>
								<input type="datetime-local" name="departure_date" class="form-control" required>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label required">Bandara Asal (Origin Airport)</label>
								<select name="origin" class="form-select select2" required>
									<option value="">- Pilih Bandara Asal -</option>
									<?php foreach ($airports as $ap): ?>
										<option value="<?= $ap->code ?>"><?= $ap->code ?> - <?= $ap->name ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label required">Bandara Tujuan (Destination Airport)</label>
								<select name="destination" class="form-select select2" required>
									<option value="">- Pilih Bandara Tujuan -</option>
									<?php foreach ($airports as $ap): ?>
										<option value="<?= $ap->code ?>"><?= $ap->code ?> - <?= $ap->name ?></option>
									<?php endforeach; ?>
								</select>
							</div>

						</div>
					</div>

					<div class="card-footer text-end bg-light">
						<a href="<?= site_url('shipment/manifest') ?>" class="btn btn-link link-secondary me-auto">Batal</a>
						<button type="submit" class="btn btn-primary fw-bold">
							<?= tabler_icon('device-floppy', 'me-1') ?> Simpan & Lanjut Bikin Koli
						</button>
					</div>
					<?= form_close() ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		if (jQuery().select2) {
			$('.select2').select2({
				width: '100%'
			});
		}
	});
</script>
