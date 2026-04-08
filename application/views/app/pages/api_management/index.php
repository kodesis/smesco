<div class="page-header">
	<div class="container-xl d-flex justify-content-between align-items-center">
		<h2 class="page-title">API Access Management</h2>
		<a href="<?= site_url('api_management/create') ?>" class="btn btn-primary">
			<?= tabler_icon('plus') ?> Register New App
		</a>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter card-table">
					<thead>
						<tr>
							<th>Agent / Client</th>
							<th>API Key</th>
							<th>IP Whitelist</th>
							<th>Status</th>
							<th class="w-1">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($clients)): ?>
							<tr>
								<td colspan="5" class="text-center py-4">
									<div class="empty">
										<?= tabler_icon('key-off') ?>
										<p class="empty-title">Belum ada API Client</p>
										<p class="empty-subtitle text-muted">Daftarkan aplikasi pertama yang akan mengakses sistem ini.</p>
										<div class="empty-action">
											<a href="<?= site_url('api_management/create') ?>" class="btn btn-primary">
												<?= tabler_icon('plus') ?> Register New App
											</a>
										</div>
									</div>
								</td>
							</tr>
						<?php else: ?>
							<?php foreach ($clients as $c): ?>
								<tr>
									<td>
										<div class="fw-bold"><?= $c->agent_name ?></div>
										<div class="text-muted small"><?= $c->client_name ?></div>
									</td>
									<td>
										<div class="input-group input-group-flat" style="max-width: 200px;">
											<input type="password" class="form-control form-control-sm border-0 bg-transparent api-key-field"
												value="<?= $c->api_key ?>" readonly>
											<span class="input-group-text bg-transparent border-0">
												<a href="javascript:void(0)" class="link-secondary btn-show-key" title="Show Key">
													<?= tabler_icon('eye') ?>
												</a>
											</span>
											<span class="input-group-text bg-transparent border-0">
												<a href="javascript:void(0)" class="link-secondary btn-copy-key" title="Copy Key">
													<?= tabler_icon('copy') ?>
												</a>
											</span>
										</div>
									</td>
									<td>
										<span class="badge bg-dark-lt"><?= $c->ip_whitelist ?: 'Any IP (Public)' ?></span>
									</td>
									<td>
										<span class="badge <?= $c->is_active ? 'bg-success' : 'bg-danger' ?>-lt">
											<?= $c->is_active ? 'Active' : 'Revoked' ?>
										</span>
									</td>
									<td>
										<div class="btn-list flex-nowrap">
											<form method="POST" action="<?= site_url('api_management/regenerate/' . $c->id) ?>"
												onsubmit="return confirm('Ganti API Key lama?')">
												<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
													value="<?= $this->security->get_csrf_hash() ?>">
												<button type="submit" class="btn btn-white btn-sm">
													<?= tabler_icon('refresh') ?>
												</button>
											</form>
											<form method="POST" action="<?= site_url('api_management/delete/' . $c->id) ?>"
												onsubmit="return confirm('Hapus akses API?')">
												<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
													value="<?= $this->security->get_csrf_hash() ?>">
												<button type="submit" class="btn btn-danger btn-sm">
													<?= tabler_icon('trash') ?>
												</button>
											</form>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
$icon_eye = json_encode(tabler_icon("eye", "text-success"));
$icon_eye_off = json_encode(tabler_icon("eye-off", "text-success"));
?>
<script>
	var iconEye = <?= $icon_eye ?>;
	var iconEyeOff = <?= $icon_eye_off ?>;

	$(document).on('click', '.btn-show-key', function() {
		let input = $(this).closest('.input-group').find('.api-key-field');
		if (input.attr('type') === 'password') {
			input.attr('type', 'text');
			$(this).html(iconEyeOff);
		} else {
			input.attr('type', 'password');
			$(this).html(iconEye);
		}
	});

	$(document).on('click', '.btn-copy-key', function() {
		let key = $(this).closest('.input-group').find('.api-key-field').val();
		navigator.clipboard.writeText(key).then(() => {
			Swal.fire({
				toast: true,
				position: 'top-end',
				icon: 'success',
				title: 'API Key copied!',
				showConfirmButton: false,
				timer: 2000,
				timerProgressBar: true
			});
		});
	});
</script>
