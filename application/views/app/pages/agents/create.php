<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('agents') ?>">Agen</a></li>
						<li class="breadcrumb-item active">Tambah Agen</li>
					</ol>
				</nav>
				<h2 class="page-title">Tambah Agen Baru</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">

			<div class="col-md-8 col-lg-7">

				<?= form_open('agents/create', ['class' => 'card']) ?>

				<div class="card-header">
					<h3 class="card-title"><?= tabler_icon('building', 'me-2') ?>Informasi Agen</h3>
				</div>

				<div class="card-body">

					<?php if (validation_errors()): ?>
						<div class="alert alert-danger mb-4" role="alert">
							<div class="d-flex">
								<?= tabler_icon('alert-circle', 'me-2 flex-shrink-0') ?>
								<div><?= validation_errors('<div class="small">', '</div>') ?></div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($this->session->flashdata('error')): ?>
						<div class="alert alert-danger mb-4">
							<?= $this->session->flashdata('error') ?>
						</div>
					<?php endif; ?>

					<!-- Nama Agen -->
					<div class="mb-3">
						<label class="form-label required">Nama Agen / Badan Usaha</label>
						<input type="text" name="name"
							class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
							value="<?= set_value('name') ?>"
							placeholder="Contoh: SMESCO Jakarta Timur">
						<?php if (form_error('name')): ?>
							<div class="invalid-feedback"><?= form_error('name') ?></div>
						<?php endif; ?>
					</div>

					<!-- Kode Agen — read-only, auto-generated -->
					<div class="mb-3">
						<label class="form-label">Kode Agen</label>
						<div class="input-group">
							<span class="input-group-text bg-light">
								<?= tabler_icon('barcode', 'text-muted') ?>
							</span>
							<input type="text" id="code_preview" class="form-control bg-light fw-bold"
								value="Pilih provinsi dan kota terlebih dahulu..."
								readonly disabled>
						</div>
						<div class="form-text">
							<?= tabler_icon('info-circle', 'me-1 text-blue') ?>
							Kode agen dibuat otomatis berdasarkan wilayah dan urutan pendaftaran.
							Format: <code>SMC-{kode.kota}.{urutan}</code>
						</div>
					</div>

					<!-- Wilayah: Provinsi → Kota (cascading) -->
					<div class="row mb-3">
						<div class="col-md-6">
							<label class="form-label required">Provinsi</label>
							<select name="province_id" id="province_id"
								class="form-select <?= form_error('province_id') ? 'is-invalid' : '' ?>">
								<option value="">— Pilih Provinsi —</option>
								<?php foreach ($provinces as $pid => $pname): ?>
									<option value="<?= $pid ?>" <?= set_select('province_id', $pid) ?>>
										<?= ($pname) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php if (form_error('province_id')): ?>
								<div class="invalid-feedback"><?= form_error('province_id') ?></div>
							<?php endif; ?>
						</div>

						<div class="col-md-6">
							<label class="form-label required">Kota / Kabupaten</label>
							<select name="regency_id" id="regency_id"
								class="form-select <?= form_error('regency_id') ? 'is-invalid' : '' ?>"
								disabled>
								<option value="">— Pilih Kota/Kab —</option>
							</select>
							<?php if (form_error('regency_id')): ?>
								<div class="invalid-feedback"><?= form_error('regency_id') ?></div>
							<?php endif; ?>
							<div id="loading-kota" class="form-text d-none">
								<?= tabler_icon('loader', 'me-1') ?>Memuat data kota...
							</div>
						</div>
					</div>

					<!-- Alamat -->
					<div class="mb-3">
						<label class="form-label">Alamat Lengkap</label>
						<textarea name="address" class="form-control" rows="3"
							placeholder="Alamat detail agen / titik lokasi..."><?= set_value('address') ?></textarea>
					</div>

					<div class="mb-3">
						<label class="form-label required">Kota Kargo / Hub Operasional</label>
						<select name="city_id" class="form-select <?= form_error('city_id') ? 'is-invalid' : '' ?>">
							<option value="">— Pilih Hub Kargo —</option>
							<?php foreach ($cargo_cities as $c): ?>
								<option value="<?= $c->id ?>" <?= set_select('city_id', $c->id) ?>>
									<?= $c->name ?> (<?= $c->code ?>)
								</option>
							<?php endforeach; ?>
						</select>
						<div class="form-text text-blue">
							<?= tabler_icon('info-circle', 'me-1') ?>
							Pilih Hub terdekat. Ini akan digunakan untuk penentuan harga dan filter barang masuk (Inbound).
						</div>
						<?php if (form_error('city_id')): ?>
							<div class="invalid-feedback"><?= form_error('city_id') ?></div>
						<?php endif; ?>
					</div>

					<!-- Kontak -->
					<div class="row mb-3">
						<div class="col-md-6">
							<label class="form-label">Nomor Telepon</label>
							<div class="input-group">
								<span class="input-group-text"><?= tabler_icon('phone') ?></span>
								<input type="text" name="phone" class="form-control"
									value="<?= set_value('phone') ?>" placeholder="08xx-xxxx-xxxx">
							</div>
						</div>
						<div class="col-md-6">
							<label class="form-label">Email Agen</label>
							<div class="input-group">
								<span class="input-group-text"><?= tabler_icon('mail') ?></span>
								<input type="email" name="email"
									class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
									value="<?= set_value('email') ?>" placeholder="agen@example.com">
							</div>
							<?php if (form_error('email')): ?>
								<div class="invalid-feedback d-block"><?= form_error('email') ?></div>
							<?php endif; ?>
						</div>
					</div>

				</div>

				<div class="card-footer text-end">
					<div class="d-flex">
						<a href="<?= site_url('agents') ?>" class="btn btn-link">Batal</a>
						<button type="submit" class="btn btn-primary ms-auto" id="btn-submit">
							<?= tabler_icon('building-store', 'me-2') ?>Simpan Agen
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>

			<!-- Sidebar -->
			<div class="col-md-4 col-lg-5">
				<!-- Preview kode -->
				<div class="card mb-3" id="card-code-preview">
					<div class="card-body text-center py-4">
						<div class="text-muted small mb-2">Kode Agen yang akan dibuat</div>
						<div id="code-display" class="display-6 fw-bold text-primary font-monospace">
							—
						</div>
						<div id="code-location" class="text-muted small mt-2">
							Pilih wilayah untuk melihat preview kode
						</div>
					</div>
				</div>

				<!-- Tips -->
				<div class="card bg-green-lt border-0">
					<div class="card-body">
						<h4 class="card-title"><?= tabler_icon('info-circle', 'me-2') ?>Tentang Kode Agen</h4>
						<ul class="small mt-2 ps-3 text-muted">
							<li class="mb-2">Kode dibuat <strong>otomatis</strong> berdasarkan kode wilayah Kemendagri</li>
							<li class="mb-2">
								Contoh: Agen di Jakarta Timur (31.75) →
								<span class="badge bg-green-lt font-monospace">SMC-31.75.001</span>
							</li>
							<li class="mb-2">Nomor urut dihitung per kota/kabupaten</li>
							<li class="mb-0 text-danger">Kode hanya dapat diubah oleh superadmin jika benar-benar diperlukan</li>
						</ul>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
	(function() {
		var provinceSelect = document.getElementById('province_id');
		var regencySelect = document.getElementById('regency_id');
		var codePreview = document.getElementById('code_preview');
		var codeDisplay = document.getElementById('code-display');
		var codeLocation = document.getElementById('code-location');
		var loadingKota = document.getElementById('loading-kota');
		var btnSubmit = document.getElementById('btn-submit');

		// Nilai dari form_validation re-populate (jika ada error submit sebelumnya)
		var savedProvinceId = '<?= set_value('province_id') ?>';
		var savedRegencyId = '<?= set_value('regency_id') ?>';

		function setCodePreview(code, location) {
			if (code) {
				codePreview.value = code;
				codeDisplay.textContent = code;
				codeLocation.textContent = location || '';
			} else {
				codePreview.value = 'Pilih provinsi dan kota terlebih dahulu...';
				codeDisplay.textContent = '—';
				codeLocation.textContent = 'Pilih wilayah untuk melihat preview kode';
			}
		}

		function loadRegencies(province_id, selected_regency_id) {
			if (!province_id) {
				regencySelect.innerHTML = '<option value="">— Pilih Kota/Kab —</option>';
				regencySelect.disabled = true;
				setCodePreview(null);
				return;
			}

			// Loading state
			loadingKota.classList.remove('d-none');
			regencySelect.disabled = true;
			regencySelect.innerHTML = '<option value="">Memuat...</option>';

			fetch('<?= site_url('agents/get_regencies/') ?>' + province_id, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(function(res) {
					return res.json();
				})
				.then(function(data) {
					loadingKota.classList.add('d-none');
					regencySelect.innerHTML = '<option value="">— Pilih Kota/Kab —</option>';

					data.forEach(function(item) {
						var opt = document.createElement('option');
						opt.value = item.id;
						opt.dataset.code = item.code_preview;
						opt.dataset.nama = item.nama;
						opt.textContent = item.nama;

						if (selected_regency_id && item.id === selected_regency_id) {
							opt.selected = true;
						}
						regencySelect.appendChild(opt);
					});

					regencySelect.disabled = false;

					// Trigger update preview jika ada selected value
					if (selected_regency_id) {
						updateCodeFromRegency();
					} else {
						setCodePreview(null);
					}
				})
				.catch(function() {
					loadingKota.classList.add('d-none');
					regencySelect.innerHTML = '<option value="">Gagal memuat data</option>';
				});
		}

		function updateCodeFromRegency() {
			var opt = regencySelect.options[regencySelect.selectedIndex];
			if (opt && opt.value) {
				setCodePreview(opt.dataset.code, opt.dataset.nama);
			} else {
				setCodePreview(null);
			}
		}

		// Event: provinsi berubah → muat kota
		provinceSelect.addEventListener('change', function() {
			loadRegencies(this.value, null);
		});

		// Event: kota berubah → update preview kode
		regencySelect.addEventListener('change', updateCodeFromRegency);

		// Init: jika ada re-populate dari form_validation error
		if (savedProvinceId) {
			// Set selected provinsi
			provinceSelect.value = savedProvinceId;
			// Load kota dengan pre-select regency
			loadRegencies(savedProvinceId, savedRegencyId);
		}
	})();
</script>
