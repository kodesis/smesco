<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('agents') ?>">Agen</a></li>
						<li class="breadcrumb-item">
							<a href="<?= site_url('agents/detail/' . $agent->id) ?>"><?= ($agent->name) ?></a>
						</li>
						<li class="breadcrumb-item active">Edit</li>
					</ol>
				</nav>
				<h2 class="page-title">Edit Agen</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">

			<div class="col-md-8 col-lg-7">

				<?= form_open('agents/edit/' . $agent->id, ['class' => 'card']) ?>

				<div class="card-header">
					<h3 class="card-title"><?= tabler_icon('pencil', 'me-2') ?>Edit: <?= ($agent->name) ?></h3>
				</div>

				<div class="card-body">

					<?php if (validation_errors()): ?>
						<div class="alert alert-danger mb-4">
							<div class="d-flex">
								<?= tabler_icon('alert-circle', 'me-2 flex-shrink-0') ?>
								<div><?= validation_errors('<div class="small">', '</div>') ?></div>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->session->flashdata('error')): ?>
						<div class="alert alert-danger mb-4"><?= $this->session->flashdata('error') ?></div>
					<?php endif; ?>

					<!-- Nama -->
					<div class="mb-3">
						<label class="form-label required">Nama Agen / Badan Usaha</label>
						<input type="text" name="name"
							class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
							value="<?= set_value('name', $agent->name) ?>">
						<?php if (form_error('name')): ?>
							<div class="invalid-feedback"><?= form_error('name') ?></div>
						<?php endif; ?>
					</div>

					<!-- Kode Agen — tampilkan saja, tidak bisa diubah via field biasa -->
					<div class="mb-3">
						<label class="form-label">Kode Agen</label>
						<div class="input-group">
							<span class="input-group-text bg-light">
								<?= tabler_icon('barcode', 'text-muted') ?>
							</span>
							<input type="text" class="form-control bg-light fw-bold font-monospace"
								value="<?= ($agent->code) ?>" readonly disabled
								id="code_display_current">
						</div>
						<?php if ($can_edit_code): ?>
							<div class="form-text text-warning">
								<?= tabler_icon('alert-triangle', 'me-1') ?>
								Kode akan diperbarui otomatis jika Anda mengubah wilayah.
							</div>
						<?php else: ?>
							<div class="form-text text-muted">
								<?= tabler_icon('lock', 'me-1') ?>
								Kode agen hanya dapat diubah oleh superadmin.
							</div>
						<?php endif; ?>
					</div>

					<!-- Wilayah cascading -->
					<div class="row mb-3">
						<div class="col-md-6">
							<label class="form-label required">Provinsi</label>
							<select name="province_id" id="province_id"
								class="form-select <?= form_error('province_id') ? 'is-invalid' : '' ?>">
								<option value="">— Pilih Provinsi —</option>
								<?php foreach ($provinces as $pid => $pname): ?>
									<option value="<?= $pid ?>"
										<?= set_select('province_id', $pid, ($pid == $agent->province_id)) ?>>
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
								class="form-select <?= form_error('regency_id') ? 'is-invalid' : '' ?>">
								<option value="">— Pilih Kota/Kab —</option>
								<?php
								// Pre-populate dari data agen yang sudah tersimpan
								// (sudah diambil controller via get_regencies_by_province)
								foreach ($regencies as $r):
									$selected = ($r->id == $agent->regency_id) ? 'selected' : '';
								?>
									<option value="<?= $r->id ?>" <?= $selected ?>>
										<?= ($r->nama) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php if (form_error('regency_id')): ?>
								<div class="invalid-feedback"><?= form_error('regency_id') ?></div>
							<?php endif; ?>
							<div id="loading-kota" class="form-text d-none">
								<?= tabler_icon('loader', 'me-1') ?>Memuat data kota...
							</div>
						</div>
					</div>

					<!-- Peringatan jika wilayah berubah -->
					<div id="wilayah-warning" class="alert alert-warning mb-3 d-none">
						<?= tabler_icon('alert-triangle', 'me-2') ?>
						<strong>Perhatian:</strong> Mengubah wilayah akan men-<em>generate ulang</em> kode agen secara otomatis.
					</div>

					<!-- Alamat -->
					<div class="mb-3">
						<label class="form-label">Alamat Lengkap</label>
						<textarea name="address" class="form-control" rows="3"><?= set_value('address', $agent->address) ?></textarea>
					</div>

					<div class="mb-3">
						<label class="form-label required">Kota Kargo / Hub Operasional</label>
						<select name="city_id" class="form-select <?= form_error('city_id') ? 'is-invalid' : '' ?>">
							<option value="">— Pilih Hub Kargo —</option>
							<?php foreach ($cargo_cities as $c): ?>
								<option value="<?= $c->id ?>" <?= set_select('city_id', $c->id, ($c->id == $agent->city_id)) ?>>
									<?= $c->name ?> (<?= $c->code ?>)
								</option>
							<?php endforeach; ?>
						</select>
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
									value="<?= set_value('phone', $agent->phone) ?>">
							</div>
						</div>
						<div class="col-md-6">
							<label class="form-label">Email Agen</label>
							<div class="input-group">
								<span class="input-group-text"><?= tabler_icon('mail') ?></span>
								<input type="email" name="email"
									class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
									value="<?= set_value('email', $agent->email) ?>">
							</div>
							<?php if (form_error('email')): ?>
								<div class="invalid-feedback d-block"><?= form_error('email') ?></div>
							<?php endif; ?>
						</div>
					</div>

				</div>

				<div class="card-footer text-end">
					<div class="d-flex">
						<a href="<?= site_url('agents/detail/' . $agent->id) ?>" class="btn btn-link">Batal</a>
						<button type="submit" class="btn btn-primary ms-auto">
							<?= tabler_icon('device-floppy', 'me-2') ?>Simpan Perubahan
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>

			<!-- Sidebar -->
			<div class="col-md-4 col-lg-5">
				<div class="card mb-3">
					<div class="card-body">
						<h4 class="card-title mb-3">Info Agen</h4>
						<dl class="row small">
							<dt class="col-5 text-muted">Kode Saat Ini</dt>
							<dd class="col-7 font-monospace fw-bold"><?= ($agent->code) ?></dd>

							<dt class="col-5 text-muted">Wilayah</dt>
							<dd class="col-7"><?= ($agent->regency_name ?? '—') ?></dd>

							<dt class="col-5 text-muted">Provinsi</dt>
							<dd class="col-7"><?= ($agent->province_name ?? '—') ?></dd>

							<dt class="col-5 text-muted">Status</dt>
							<dd class="col-7">
								<?= $agent->is_active
									? '<span class="badge bg-success-lt">Aktif</span>'
									: '<span class="badge bg-danger-lt">Nonaktif</span>' ?>
							</dd>
							<dt class="col-5 text-muted">Total User</dt>
							<dd class="col-7"><?= $agent->total_users ?></dd>
							<dt class="col-5 text-muted">Terdaftar</dt>
							<dd class="col-7"><?= date('d M Y', strtotime($agent->created_at)) ?></dd>
						</dl>
						<hr>
						<a href="<?= site_url('agents/toggle_status/' . $agent->id) ?>"
							class="btn btn-outline-warning w-100 btn-sm mb-2"
							onclick="return confirm('Ubah status agen ini?')">
							<?= tabler_icon('refresh', 'me-1') ?>Toggle Status
						</a>
						<a href="<?= site_url('agents/delete/' . $agent->id) ?>"
							class="btn btn-outline-danger w-100 btn-sm"
							onclick="return confirm('Hapus agen ini?')">
							<?= tabler_icon('trash', 'me-1') ?>Hapus Agen
						</a>
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
		var loadingKota = document.getElementById('loading-kota');
		var wilayahWarning = document.getElementById('wilayah-warning');
		var codeDisplay = document.getElementById('code_display_current');

		// Simpan nilai asli untuk deteksi perubahan
		var originalRegencyId = '<?= $agent->regency_id ?>';
		var originalProvinceId = '<?= $agent->province_id ?>';

		function loadRegencies(province_id, selected_regency_id) {
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
						opt.textContent = item.nama;
						if (selected_regency_id && item.id === selected_regency_id) {
							opt.selected = true;
						}
						regencySelect.appendChild(opt);
					});

					regencySelect.disabled = false;
					checkWilayahChange();
				})
				.catch(function() {
					loadingKota.classList.add('d-none');
					regencySelect.innerHTML = '<option value="">Gagal memuat data</option>';
				});
		}

		function checkWilayahChange() {
			var currentRegency = regencySelect.value;
			var wilayahBerubah = (currentRegency !== originalRegencyId);

			if (wilayahBerubah && currentRegency) {
				wilayahWarning.classList.remove('d-none');
				// Tampilkan preview kode baru
				var opt = regencySelect.options[regencySelect.selectedIndex];
				if (opt && opt.dataset.code) {
					codeDisplay.value = opt.dataset.code + ' (baru)';
					codeDisplay.classList.add('text-warning');
				}
			} else {
				wilayahWarning.classList.add('d-none');
				codeDisplay.value = '<?= ($agent->code) ?>';
				codeDisplay.classList.remove('text-warning');
			}
		}

		provinceSelect.addEventListener('change', function() {
			// Jika provinsi berubah, muat kota tanpa pre-select
			if (this.value !== originalProvinceId) {
				loadRegencies(this.value, null);
			} else {
				// Kembali ke provinsi asal → muat ulang dengan kota asal ter-select
				loadRegencies(this.value, originalRegencyId);
			}
		});

		regencySelect.addEventListener('change', checkWilayahChange);
	})();
</script>
