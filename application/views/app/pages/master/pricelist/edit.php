<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('master/pricelist') ?>">Pricelist</a></li>
						<li class="breadcrumb-item active">Edit Pricelist</li>
					</ol>
				</nav>
				<h2 class="page-title">Edit Pricelist</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">
			<div class="col-md-8 col-lg-7">

				<?= form_open('master/edit_pricelist/' . $pricelist->id, ['class' => 'card']) ?>

				<div class="card-header">
					<h3 class="card-title"><?= tabler_icon('currency-rupiah', 'me-2') ?>Informasi Pricelist</h3>
				</div>

				<div class="card-body">
					<?php if (validation_errors()): ?>
						<div class="alert alert-danger mb-3" role="alert">
							<div class="d-flex align-items-center">
								<div><?= tabler_icon('alert-circle', 'me-2 flex-shrink-0') ?></div>
								<div><?= validation_errors('<div class="small">', '</div>') ?></div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($this->session->flashdata('error')): ?>
						<div class="alert alert-danger mb-3" role="alert">
							<div class="d-flex align-items-center">
								<div><?= tabler_icon('alert-circle', 'me-2 flex-shrink-0') ?></div>
								<div><?= $this->session->flashdata('error') ?></div>
							</div>
						</div>
					<?php endif; ?>

					<div class="row">
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label class="form-label required">Kota Asal (Origin)</label>
								<select name="origin" id="origin" class="form-select <?= form_error('origin') ? 'is-invalid' : '' ?>">
									<option value="">— Pilih kota asal —</option>
									<?php foreach ($cities as $ori): ?>
										<option value="<?= $ori->name ?>" <?= set_select('origin', $ori->name, ($pricelist->origin == $ori->name)) ?>>
											<?= $ori->code . ' - ' . $ori->name ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label class="form-label required">Kota Tujuan (Destination)</label>
								<select name="destination" id="destination" class="form-select <?= form_error('destination') ? 'is-invalid' : '' ?>">
									<option value="">— Pilih kota tujuan —</option>
									<?php foreach ($cities as $dest): ?>
										<option value="<?= $dest->name ?>" <?= set_select('destination', $dest->name, ($pricelist->destination == $dest->name)) ?>>
											<?= $dest->code . ' - ' . $dest->name ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label required">Tipe Layanan (Service Type)</label>
						<select name="service_type_id" id="service_type_id" class="form-select <?= form_error('service_type_id') ? 'is-invalid' : '' ?>">
							<option value="">— Pilih Service —</option>
							<?php foreach ($services as $srv): ?>
								<option value="<?= $srv->id ?>" <?= set_select('service_type_id', $srv->id, ($pricelist->service_type_id == $srv->id)) ?>>
									<?= $srv->code . ' - ' . $srv->name . ' (' . $srv->description . ')' ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="row">
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label for="price_per_kg" class="form-label required">Harga per kg</label>
								<div class="input-group input-group-flat">
									<span class="input-group-text">Rp</span>
									<?php
									// Ambil value lama (saat gagal validasi) atau value dari DB 
									$val_price = set_value('price_per_kg', round($pricelist->price_per_kg));
									?>
									<input type="text" id="price_per_kg_display"
										class="form-control <?= form_error('price_per_kg') ? 'is-invalid' : '' ?>"
										value="<?= $val_price ?>" placeholder="0">
									<input type="hidden" name="price_per_kg" id="price_per_kg" value="<?= $val_price ?>">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label for="min_weight_kg" class="form-label required">Minimum Berat</label>
								<div class="input-group input-group-flat">
									<input type="number" step="0.01" name="min_weight_kg" id="min_weight_kg"
										class="form-control <?= form_error('min_weight_kg') ? 'is-invalid' : '' ?>"
										value="<?= set_value('min_weight_kg', floatval($pricelist->min_weight_kg)) ?>" placeholder="1.00">
									<span class="input-group-text">Kg</span>
								</div>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label">Status Pricelist</label>
						<label class="form-check form-switch">
							<input class="form-check-input" type="checkbox" name="is_active" value="1" <?= set_checkbox('is_active', '1', ($pricelist->is_active == 1)) ?>>
							<span class="form-check-label">Aktif (Dapat digunakan untuk transaksi)</span>
						</label>
					</div>

				</div>

				<div class="card-footer text-end">
					<div class="d-flex">
						<a href="<?= site_url('master/detail_pricelist/' . $pricelist->id) ?>" class="btn btn-link">Batal</a>
						<button type="submit" class="btn btn-primary ms-auto">
							<?= tabler_icon('device-floppy', 'me-2') ?>Update Pricelist
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const displayInput = document.getElementById('price_per_kg_display');
		const hiddenInput = document.getElementById('price_per_kg');

		function formatRupiah(value) {
			let number_string = value.toString().replace(/[^0-9]/g, '');
			return number_string.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		}

		if (displayInput.value) {
			displayInput.value = formatRupiah(displayInput.value);
		}

		displayInput.addEventListener('input', function(e) {
			this.value = formatRupiah(this.value);
			hiddenInput.value = this.value.replace(/\./g, '');
		});
	});
</script>
