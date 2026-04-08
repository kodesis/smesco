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

<div class="row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?= tabler_icon('pencil', 'me-2') ?>Edit: <?= ($agent->name) ?></h3>
			</div>
			<div class="card-body">

				<?php if (validation_errors()): ?>
					<div class="alert alert-danger mb-3">
						<?= validation_errors('<div>', '</div>') ?>
					</div>
				<?php endif; ?>
				<?php if ($this->session->flashdata('error')): ?>
					<div class="alert alert-danger mb-3"><?= $this->session->flashdata('error') ?></div>
				<?php endif; ?>

				<?= form_open('agents/edit/' . $agent->id) ?>

				<div class="row mb-3">
					<div class="col-md-8">
						<label class="form-label required">Nama Agen</label>
						<input type="text" name="name"
							class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
							value="<?= set_value('name', $agent->name) ?>">
						<?php if (form_error('name')): ?><div class="invalid-feedback"><?= form_error('name') ?></div><?php endif; ?>
					</div>
					<div class="col-md-4">
						<label class="form-label required">Kode Agen</label>
						<input type="text" name="code"
							class="form-control <?= form_error('code') ? 'is-invalid' : '' ?>"
							value="<?= set_value('code', $agent->code) ?>"
							style="text-transform:uppercase">
						<?php if (form_error('code')): ?><div class="invalid-feedback"><?= form_error('code') ?></div><?php endif; ?>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label required">Kota</label>
						<input type="text" name="city"
							class="form-control <?= form_error('city') ? 'is-invalid' : '' ?>"
							value="<?= set_value('city', $agent->city) ?>">
						<?php if (form_error('city')): ?><div class="invalid-feedback"><?= form_error('city') ?></div><?php endif; ?>
					</div>
					<div class="col-md-6">
						<label class="form-label">Provinsi</label>
						<input type="text" name="province" class="form-control"
							value="<?= set_value('province', $agent->province) ?>">
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label">Alamat Lengkap</label>
					<textarea name="address" class="form-control" rows="3"><?= set_value('address', $agent->address) ?></textarea>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label">Nomor Telepon</label>
						<input type="text" name="phone" class="form-control"
							value="<?= set_value('phone', $agent->phone) ?>">
					</div>
					<div class="col-md-6">
						<label class="form-label">Email Agen</label>
						<input type="email" name="email"
							class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
							value="<?= set_value('email', $agent->email) ?>">
						<?php if (form_error('email')): ?><div class="invalid-feedback"><?= form_error('email') ?></div><?php endif; ?>
					</div>
				</div>

				<div class="row mt-4">
					<div class="col">
						<a href="<?= site_url('agents/detail/' . $agent->id) ?>"
							class="btn btn-outline-secondary w-100">Batal</a>
					</div>
					<div class="col">
						<button type="submit" class="btn btn-primary w-100">
							<?= tabler_icon('device-floppy', 'me-1') ?>Simpan Perubahan
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-3">Info Agen</h4>
				<dl class="row small">
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
					class="btn btn-outline-warning w-100 mb-2 btn-sm"
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
