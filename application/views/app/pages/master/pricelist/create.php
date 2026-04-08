<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('master/pricelist') ?>">Pricelist</a></li>
						<li class="breadcrumb-item active">Tambah Pricelist</li>
					</ol>
				</nav>
				<h2 class="page-title">Tambah Pricelist Baru</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">

			<div class="col-md-8 col-lg-7">

				<?= form_open('master/create_pricelist/create', ['class' => 'card']) ?>

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
										<option value="<?= $ori->name ?>" <?= set_select('origin', $ori->name) ?>>
											<?= $ori->code . ' - ' . $ori->name ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if (form_error('origin')): ?>
									<div class="invalid-feedback"><?= form_error('origin') ?></div>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label class="form-label required">Kota Tujuan (Destination)</label>
								<select name="destination" id="destination" class="form-select <?= form_error('destination') ? 'is-invalid' : '' ?>">
									<option value="">— Pilih kota tujuan —</option>
									<?php foreach ($cities as $dest): ?>
										<option value="<?= $dest->name ?>" <?= set_select('destination', $dest->name) ?>>
											<?= $dest->code . ' - ' . $dest->name ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if (form_error('destination')): ?>
									<div class="invalid-feedback"><?= form_error('destination') ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label required">Tipe Layanan (Service Type)</label>
						<select name="service_type_id" id="service_type_id" class="form-select <?= form_error('service_type_id') ? 'is-invalid' : '' ?>">
							<option value="">— Pilih Service —</option>
							<?php foreach ($services as $srv): ?>
								<option value="<?= $srv->id ?>" <?= set_select('service_type_id', $srv->id) ?>>
									<?= $srv->code . ' - ' . $srv->name . ' (' . $srv->description . ')' ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (form_error('service_type_id')): ?>
							<div class="invalid-feedback"><?= form_error('service_type_id') ?></div>
						<?php endif; ?>
					</div>

					<div class="row">
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label for="price_per_kg" class="form-label required">Harga per kg</label>
								<div class="input-group input-group-flat">
									<span class="input-group-text">Rp</span>

									<input type="text" id="price_per_kg_display"
										class="form-control <?= form_error('price_per_kg') ? 'is-invalid' : '' ?>"
										value="<?= set_value('price_per_kg') ?>"
										placeholder="0">

									<input type="hidden" name="price_per_kg" id="price_per_kg" value="<?= set_value('price_per_kg') ?>">

								</div>
								<?php if (form_error('price_per_kg')): ?>
									<div class="text-danger small mt-1"><?= form_error('price_per_kg') ?></div>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="mb-3">
								<label for="min_weight_kg" class="form-label required">Minimum Berat</label>
								<div class="input-group input-group-flat">
									<input type="number" step="0.01" name="min_weight_kg" id="min_weight_kg"
										class="form-control <?= form_error('min_weight_kg') ? 'is-invalid' : '' ?>"
										value="<?= set_value('min_weight_kg', '10') ?>"
										placeholder="1.00">
									<span class="input-group-text">Kg</span>
								</div>
								<?php if (form_error('min_weight_kg')): ?>
									<div class="text-danger small mt-1"><?= form_error('min_weight_kg') ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label">Status Pricelist</label>
						<label class="form-check form-switch">
							<input class="form-check-input" type="checkbox" name="is_active" value="1" <?= set_checkbox('is_active', '1', true) ?>>
							<span class="form-check-label">Aktif (Dapat digunakan untuk transaksi)</span>
						</label>
					</div>

				</div>

				<div class="card-footer text-end">
					<div class="d-flex">
						<a href="<?= site_url('master/pricelist') ?>" class="btn btn-link">Batal</a>
						<button type="submit" class="btn btn-primary ms-auto" id="btn-submit">
							<?= tabler_icon('device-floppy', 'me-2') ?>Simpan Pricelist
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>

			<div class="col-md-4 col-lg-5">
				<div class="card bg-green-lt border-0">
					<div class="card-body">
						<h4 class="card-title"><?= tabler_icon('info-circle', 'me-2') ?>Panduan Pricelist</h4>
						<ul class="list-unstyled mt-2 small text-muted">
							<li class="mb-2">
								<strong>Kombinasi Unik:</strong> Pastikan kombinasi antara <b>Kota Asal</b>, <b>Kota Tujuan</b>, dan <b>Tipe Layanan</b> belum pernah dibuat sebelumnya agar tidak terjadi duplikasi harga.
							</li>
							<li class="mb-2">
								<strong>Harga per kg:</strong> Masukkan nominal angka saja tanpa titik/koma (contoh: 15000).
							</li>
							<li class="mb-2">
								<strong>Minimum Berat:</strong> Default adalah 1.00 Kg. Jika pengiriman ke rute ini mewajibkan charge minimal 5 Kg, ubah nilainya menjadi 5.00.
							</li>
							<li class="mb-0">
								<strong>Status Aktif:</strong> Pricelist yang dinonaktifkan tidak akan muncul saat kurir/admin membuat resi pengiriman baru.
							</li>
						</ul>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const displayInput = document.getElementById('price_per_kg_display');
		const hiddenInput = document.getElementById('price_per_kg');

		// Fungsi untuk nambahin titik (Format Rupiah)
		function formatRupiah(value) {
			// Hapus semua karakter selain angka
			let number_string = value.replace(/[^0-9]/g, '');
			// Tambahkan titik setiap 3 digit
			return number_string.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		}

		// 1. Format otomatis saat halaman pertama kali diload (jika ada value dari set_value)
		if (displayInput.value) {
			displayInput.value = formatRupiah(displayInput.value);
		}

		// 2. Format otomatis saat user mengetik
		displayInput.addEventListener('input', function(e) {
			// Format tampilan inputnya
			this.value = formatRupiah(this.value);

			// Simpan angka murni (tanpa titik) ke hidden input untuk dikirim ke database
			hiddenInput.value = this.value.replace(/\./g, '');
		});
	});
</script>
