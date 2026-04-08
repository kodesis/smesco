<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('master/services') ?>">Layanan</a></li>
						<li class="breadcrumb-item active">Tambah Layanan</li>
					</ol>
				</nav>
				<h2 class="page-title">Tambah Layanan Baru</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">
			<div class="col-md-8 col-lg-6">
				<?= form_open('master/create_service', ['class' => 'card']) ?>
				<div class="card-body">
					<?php if (validation_errors()): ?>
						<div class="alert alert-danger mb-3">
							<?= validation_errors('<div class="small">', '</div>') ?>
						</div>
					<?php endif; ?>

					<div class="mb-3">
						<label class="form-label required">Kode Layanan</label>
						<input type="text" name="code" class="form-control" value="<?= set_value('code') ?>" placeholder="Contoh: REG, EXP, CARGO" required>
						<small class="form-hint">Maksimal 20 karakter. Kode harus unik.</small>
					</div>
					<div class="mb-3">
						<label class="form-label required">Nama Layanan</label>
						<input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" placeholder="Contoh: Reguler Service" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Deskripsi</label>
						<textarea name="description" class="form-control" rows="3" placeholder="Opsional..."><?= set_value('description') ?></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Status</label>
						<label class="form-check form-switch">
							<input class="form-check-input" type="checkbox" name="is_active" value="1" <?= set_checkbox('is_active', '1', true) ?>>
							<span class="form-check-label">Aktif</span>
						</label>
					</div>
				</div>
				<div class="card-footer text-end">
					<a href="<?= site_url('master/services') ?>" class="btn btn-link me-2">Batal</a>
					<button type="submit" class="btn btn-primary">Simpan Layanan</button>
				</div>
				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>
