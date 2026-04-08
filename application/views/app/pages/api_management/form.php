<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('api_management') ?>">API Management</a></li>
						<li class="breadcrumb-item active">Register Aplikasi</li>
					</ol>
				</nav>
				<h2 class="page-title">Register API Client</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">

			<div class="col-md-8 col-lg-7">
				<?= form_open('api_management/create', ['class' => 'card']) ?>

				<div class="card-header">
					<h3 class="card-title"><?= tabler_icon('key', 'me-2') ?>Informasi Aplikasi</h3>
				</div>

				<div class="card-body">

					<?php if (validation_errors()): ?>
						<div class="alert alert-danger mb-3">
							<div class="d-flex align-items-center">
								<div><?= tabler_icon('alert-circle', 'me-2 flex-shrink-0') ?></div>
								<div><?= validation_errors('<div class="small">', '</div>') ?></div>
							</div>
						</div>
					<?php endif; ?>

					<div class="mb-3">
						<label class="form-label required">Agen</label>
						<select name="agent_id" class="form-select <?= form_error('agent_id') ? 'is-invalid' : '' ?>">
							<option value="">— Pilih Agen —</option>
							<?php foreach ($agents as $a): ?>
								<option value="<?= $a->id ?>" <?= set_select('agent_id', $a->id) ?>>
									<?= $a->name ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (form_error('agent_id')): ?>
							<div class="invalid-feedback"><?= form_error('agent_id') ?></div>
						<?php endif; ?>
					</div>

					<div class="mb-3">
						<label class="form-label required">Nama Aplikasi</label>
						<input type="text" name="client_name"
							class="form-control <?= form_error('client_name') ? 'is-invalid' : '' ?>"
							value="<?= set_value('client_name') ?>"
							placeholder="contoh: Aplikasi Mobile Mitra">
						<?php if (form_error('client_name')): ?>
							<div class="invalid-feedback"><?= form_error('client_name') ?></div>
						<?php endif; ?>
						<small class="form-hint">Nama ini hanya untuk identifikasi internal.</small>
					</div>

					<div class="mb-3">
						<label class="form-label">IP Whitelist</label>
						<input type="text" name="ip_whitelist"
							class="form-control"
							value="<?= set_value('ip_whitelist') ?>"
							placeholder="192.168.1.1, 10.0.0.5">
						<small class="form-hint">Pisahkan dengan koma. Kosongkan untuk mengizinkan semua IP.</small>
					</div>

					<div class="mb-3">
						<label class="form-label">Hit Limit per Hari</label>
						<div class="input-group">
							<input type="number" name="hit_limit"
								class="form-control"
								value="<?= set_value('hit_limit', 1000) ?>"
								min="1">
							<span class="input-group-text">request / hari</span>
						</div>
						<small class="form-hint">Default 1000. Sesuaikan dengan kebutuhan integrasi.</small>
					</div>

				</div>

				<div class="card-footer">
					<div class="d-flex">
						<a href="<?= site_url('api_management') ?>" class="btn btn-link">Batal</a>
						<button type="submit" class="btn btn-primary ms-auto">
							<?= tabler_icon('key', 'me-2') ?>Generate API Key
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>

			<div class="col-md-4 col-lg-5">
				<div class="card bg-blue-lt border-0">
					<div class="card-body">
						<h4 class="card-title"><?= tabler_icon('info-circle', 'me-2') ?>Panduan Integrasi</h4>
						<ul class="list-unstyled mt-2 small">
							<li class="mb-3 text-muted">
								<?= tabler_icon('key', 'me-1 text-blue') ?>
								<strong>API Key</strong> akan di-generate otomatis setelah form disimpan. Simpan key ini di tempat aman — tidak bisa dilihat ulang secara plaintext.
							</li>
							<li class="mb-3 text-muted">
								<?= tabler_icon('shield-lock', 'me-1 text-green') ?>
								<strong>IP Whitelist</strong> membatasi akses hanya dari IP yang terdaftar. Kosongkan jika aplikasi mengakses dari IP dinamis.
							</li>
							<li class="mb-3 text-muted">
								<?= tabler_icon('clock', 'me-1 text-orange') ?>
								<strong>Hit Limit</strong> adalah batas maksimal request per hari. Jika tercapai, API akan mengembalikan response <code>429 Too Many Requests</code>.
							</li>
							<li class="mb-0 text-muted">
								<?= tabler_icon('refresh', 'me-1 text-red') ?>
								API Key bisa di-<strong>regenerate</strong> kapan saja dari halaman daftar, namun key lama akan langsung tidak valid.
							</li>
						</ul>
						<hr>
						<div class="small text-muted">
							<?= tabler_icon('alert-triangle', 'me-1 text-yellow') ?>
							Jangan bagikan API Key ke pihak yang tidak berwenang.
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
