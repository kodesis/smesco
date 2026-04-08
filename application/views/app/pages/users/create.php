<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('users') ?>">User</a></li>
						<li class="breadcrumb-item active">Tambah User</li>
					</ol>
				</nav>
				<h2 class="page-title">Tambah User Baru</h2>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row">

			<div class="col-md-8 col-lg-7">

				<?= form_open('users/create', ['class' => 'card']) ?>

				<div class="card-header">
					<h3 class="card-title"><?= tabler_icon('user-plus', 'me-2') ?>Informasi User</h3>
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

					<div class="mb-3">
						<label class="form-label required">Nama Lengkap</label>
						<input type="text" name="name"
							class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
							value="<?= set_value('name') ?>"
							placeholder="Masukkan nama lengkap">
						<?php if (form_error('name')): ?>
							<div class="invalid-feedback"><?= form_error('name') ?></div>
						<?php endif; ?>
					</div>

					<div class="mb-3">
						<label class="form-label required">Email</label>
						<input type="email" name="email"
							class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
							value="<?= set_value('email') ?>"
							placeholder="user@example.com">
						<?php if (form_error('email')): ?>
							<div class="invalid-feedback"><?= form_error('email') ?></div>
						<?php endif; ?>
					</div>

					<div class="mb-3">
						<label class="form-label required">Password</label>
						<div class="input-group input-group-flat">
							<input type="password" name="password" id="password"
								class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
								placeholder="Minimal 8 karakter">
							<span class="input-group-text">
								<a href="#" class="link-secondary" id="togglePw" title="Tampilkan/Sembunyikan password" data-bs-toggle="tooltip">
									<?= tabler_icon('eye') ?>
								</a>
							</span>
						</div>
						<?php if (form_error('password')): ?>
							<div class="invalid-feedback d-block mt-1"><?= form_error('password') ?></div>
						<?php endif; ?>
						<div class="form-text">Minimal 8 karakter.</div>
					</div>

					<div class="mb-3">
						<label class="form-label required">Role</label>
						<select name="role_id" id="role_id"
							class="form-select <?= form_error('role_id') ? 'is-invalid' : '' ?>">
							<option value="">— Pilih Role —</option>
							<?php foreach ($roles as $rid => $label): ?>
								<option value="<?= $rid ?>"
									data-scope="<?= isset($roles_scope[$rid]) ? $roles_scope[$rid] : '' ?>"
									<?= set_select('role_id', $rid) ?>>
									<?= ($label) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (form_error('role_id')): ?>
							<div class="invalid-feedback"><?= form_error('role_id') ?></div>
						<?php endif; ?>
					</div>

					<?php if ($agent_id === NULL): ?>
						<div id="field-agent" class="mb-3" style="display:none;">
							<label class="form-label required" id="label-agent">Agen / Mitra</label>

							<?php if ($agents_empty): ?>
								<div class="alert alert-warning d-flex align-items-center" role="alert">
									<?= tabler_icon('alert-triangle', 'me-3 text-warning flex-shrink-0') ?>
									<div>
										<strong>Belum ada agen terdaftar.</strong><br>
										User dengan role mitra/agen membutuhkan agen yang sudah ada.
										Silakan <a href="<?= site_url('agents/create') ?>" class="alert-link">buat agen terlebih dahulu</a>.
									</div>
								</div>
								<input type="hidden" name="agent_id" value="">
							<?php else: ?>
								<select name="agent_id" id="agent_id"
									class="form-select <?= form_error('agent_id') ? 'is-invalid' : '' ?>">
									<option value="">— Pilih Agen —</option>
									<?php foreach ($agents as $aid => $alabel): ?>
										<option value="<?= $aid ?>" <?= set_select('agent_id', $aid) ?>>
											<?= ($alabel) ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if (form_error('agent_id')): ?>
									<div class="invalid-feedback"><?= form_error('agent_id') ?></div>
								<?php endif; ?>
								<div class="form-text">Pilih agen yang akan mengelola user ini.</div>
							<?php endif; ?>
						</div>

					<?php else: ?>
						<input type="hidden" name="agent_id" value="<?= $agent_id ?>">
					<?php endif; ?>

				</div>

				<div class="card-footer text-end">
					<div class="d-flex">
						<a href="<?= site_url('users') ?>" class="btn btn-link">Batal</a>
						<button type="submit" class="btn btn-primary ms-auto" id="btn-submit">
							<?= tabler_icon('user-plus', 'me-2') ?>Simpan User
						</button>
					</div>
				</div>

				<?= form_close() ?>
			</div>

			<div class="col-md-4 col-lg-5">
				<div class="card bg-blue-lt border-0">
					<div class="card-body">
						<h4 class="card-title"><?= tabler_icon('info-circle', 'me-2') ?>Panduan Role</h4>
						<ul class="list-unstyled mt-2 small">
							<li class="mb-2 text-muted">
								<span class="badge bg-red-lt me-1">Global</span>
								<strong>superadmin</strong> — Akses penuh sistem
							</li>
							<li class="mb-2 text-muted">
								<span class="badge bg-orange-lt me-1">Global</span>
								<strong>admin-kribo</strong> — Admin pusat
							</li>
							<li class="mb-2 text-muted">
								<span class="badge bg-yellow-lt me-1">Global</span>
								<strong>finance-kribo</strong> — Keuangan pusat
							</li>
							<li class="mb-2 text-muted">
								<span class="badge bg-blue-lt me-1">Agen</span>
								<strong>admin-mitra</strong> — Admin agen mitra
							</li>
							<li class="mb-2 text-muted">
								<span class="badge bg-azure-lt me-1">Agen</span>
								<strong>staff-mitra</strong> — Staff operasional
							</li>
							<li class="mb-2 text-muted">
								<span class="badge bg-cyan-lt me-1">Agen</span>
								<strong>finance-mitra</strong> — Keuangan agen
							</li>
							<li class="mb-0 text-muted">
								<span class="badge bg-indigo-lt me-1">Agen</span>
								<strong>tracker-mitra</strong> — Tracker/kurir
							</li>
						</ul>
						<hr>
						<div class="small text-muted">
							<?= tabler_icon('alert-triangle', 'me-1 text-yellow') ?>
							Role dengan label <strong>Agen</strong> wajib dihubungkan ke agen/mitra.
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
	(function() {
		// Map role_id => scope dari PHP
		var rolesScope = <?= json_encode($roles_scope) ?>;

		var roleSelect = document.getElementById('role_id');
		var fieldAgent = document.getElementById('field-agent');
		var agentSelect = document.getElementById('agent_id');

		function updateAgentField() {
			if (!roleSelect || !fieldAgent) return;

			var selectedId = roleSelect.value;
			var selectedScope = rolesScope[selectedId] || '';

			if (selectedScope === 'agent') {
				fieldAgent.style.display = 'block';
				if (agentSelect) agentSelect.setAttribute('required', 'required');
			} else {
				fieldAgent.style.display = 'none';
				if (agentSelect) {
					agentSelect.removeAttribute('required');
					agentSelect.value = '';
				}
			}
		}

		// Eksekusi awal saat halaman dimuat
		updateAgentField();

		if (roleSelect) {
			roleSelect.addEventListener('change', updateAgentField);
		}

		// Toggle show/hide password (diupdate pakai preventDefault krn pakai tag <a>)
		var toggleBtn = document.getElementById('togglePw');
		var pwInput = document.getElementById('password');
		if (toggleBtn && pwInput) {
			toggleBtn.addEventListener('click', function(e) {
				e.preventDefault();
				pwInput.type = pwInput.type === 'password' ? 'text' : 'password';
			});
		}
	})();
</script>
