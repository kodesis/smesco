<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('users') ?>">Users</a></li>
						<li class="breadcrumb-item">
							<a href="<?= site_url('users/detail/' . $user->id) ?>"><?= html_escape($user->name) ?></a>
						</li>
						<li class="breadcrumb-item active">Edit</li>
					</ol>
				</nav>
				<h2 class="page-title">Edit User</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('user-edit', 'me-2') ?>Edit Data: <?= html_escape($user->name) ?></h3>
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

						<?= form_open('users/edit/' . $user->id) ?>

						<div class="row mb-3">
							<div class="col-md-6">
								<label class="form-label required">Nama Lengkap</label>
								<input type="text" name="name"
									class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
									value="<?= set_value('name', $user->name) ?>" required>
								<?php if (form_error('name')): ?><div class="invalid-feedback"><?= form_error('name') ?></div><?php endif; ?>
							</div>
							<div class="col-md-6">
								<label class="form-label required">Email</label>
								<input type="email" name="email"
									class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
									value="<?= set_value('email', $user->email) ?>" required>
								<?php if (form_error('email')): ?><div class="invalid-feedback"><?= form_error('email') ?></div><?php endif; ?>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label class="form-label required">Role Akses</label>
								<select name="role_id" class="form-select <?= form_error('role_id') ? 'is-invalid' : '' ?>" required>
									<option value="">- Pilih Role -</option>
									<?php foreach ($roles as $role_id => $role_name): ?>
										<option value="<?= $role_id ?>" <?= set_select('role_id', $role_id, ($user->role_id == $role_id)) ?>>
											<?= html_escape($role_name) ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if (form_error('role_id')): ?><div class="invalid-feedback"><?= form_error('role_id') ?></div><?php endif; ?>
							</div>

							<?php if ($agent_id === NULL): ?>
								<div class="col-md-6">
									<label class="form-label">Cabang / Agen</label>
									<select name="agent_id" class="form-select <?= form_error('agent_id') ? 'is-invalid' : '' ?>">
										<option value="">-- Pusat / Head Office --</option>
										<?php foreach ($agents as $a_id => $a_name): ?>
											<option value="<?= $a_id ?>" <?= set_select('agent_id', $a_id, ($user->agent_id == $a_id)) ?>>
												<?= html_escape($a_name) ?>
											</option>
										<?php endforeach; ?>
									</select>
									<?php if (form_error('agent_id')): ?><div class="invalid-feedback"><?= form_error('agent_id') ?></div><?php endif; ?>
									<small class="form-hint">Biarkan Pusat jika user ini staff internal pusat.</small>
								</div>
							<?php endif; ?>

							<div class="col-md-6">
								<label class="form-label">No. HP</label>
								<input type="text" name="phone"
									class="form-control <?= form_error('phone') ? 'is-invalid' : '' ?>"
									value="<?= set_value('phone', $user->phone) ?>" required>
								<?php if (form_error('phone')): ?><div class="invalid-feedback"><?= form_error('phone') ?></div><?php endif; ?>
							</div>
						</div>

						<div class="hr-text">Keamanan Akun</div>

						<div class="mb-4">
							<label class="form-label">Password Baru (Opsional)</label>
							<input type="password" name="password" class="form-control" placeholder="Ketik password baru jika ingin mengubah">
							<small class="form-hint text-primary">Biarkan kosong jika tidak ingin mengganti password saat ini.</small>
						</div>

						<div class="row mt-4">
							<div class="col">
								<a href="<?= site_url('users/detail/' . $user->id) ?>" class="btn btn-outline-secondary w-100">Batal</a>
							</div>
							<div class="col">
								<button type="submit" class="btn btn-primary w-100">
									<?= tabler_icon('device-floppy', 'me-1') ?> Simpan Perubahan
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
						<h4 class="card-title mb-3">Info User</h4>
						<dl class="row small mb-0">
							<dt class="col-5 text-muted mb-2">Status Akun</dt>
							<dd class="col-7 mb-2">
								<?= $user->is_active
									? '<span class="badge bg-success-lt">Aktif</span>'
									: '<span class="badge bg-danger-lt">Nonaktif</span>' ?>
							</dd>

							<dt class="col-5 text-muted mb-2">Terdaftar Pada</dt>
							<dd class="col-7 mb-2"><?= date('d M Y, H:i', strtotime($user->created_at)) ?></dd>

							<dt class="col-5 text-muted">Akses Terakhir</dt>
							<dd class="col-7"><?= $user->last_login ? date('d M Y, H:i', strtotime($user->last_login)) : 'Belum pernah login' ?></dd>
						</dl>
					</div>

					<div class="card-footer bg-light-lt">
						<div class="row g-2">
							<div class="col-6">
								<a href="<?= site_url('users/toggle_status/' . $user->id) ?>"
									class="btn btn-outline-warning w-100 btn-sm"
									onclick="return confirm('Ubah status aktif user ini?')">
									<?= tabler_icon('refresh', 'me-1') ?> Toggle Status
								</a>
							</div>
							<div class="col-6">
								<a href="<?= site_url('users/delete/' . $user->id) ?>"
									class="btn btn-outline-danger w-100 btn-sm"
									onclick="return confirm('Yakin ingin menghapus user ini secara permanen?')">
									<?= tabler_icon('trash', 'me-1') ?> Hapus
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
