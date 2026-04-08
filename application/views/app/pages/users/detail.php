<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('users') ?>">User</a></li>
						<li class="breadcrumb-item active"><?= htmlspecialchars($user->name) ?></li>
					</ol>
				</nav>
				<h2 class="page-title">Detail User</h2>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('users/edit/' . $user->id) ?>" class="btn btn-primary">
					<?= tabler_icon('pencil', 'me-1') ?>Edit User
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php if ($this->session->flashdata('success')): ?>
			<div class="alert alert-success alert-dismissible mb-4" role="alert">
				<div class="d-flex">
					<div><?= tabler_icon('circle-check', 'me-2') ?></div>
					<div><?= $this->session->flashdata('success') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>

		<div class="row row-deck row-cards">

			<div class="col-md-4">
				<div class="row row-cards">

					<div class="col-12">
						<div class="card">
							<div class="card-status-top bg-primary"></div>
							<div class="card-body text-center">
								<div class="mb-3">
									<span class="avatar avatar-xl bg-blue-lt text-blue mb-3 rounded">
										<?= tabler_icon('user') ?>
										<span class="badge <?= $user->is_active ? 'bg-success' : 'bg-danger' ?> badge-blink"></span>
									</span>
								</div>
								<h3 class="card-title mb-1"><?= htmlspecialchars($user->name) ?></h3>
								<div class="text-muted mb-3"><?= htmlspecialchars($user->email) ?></div>

								<?php
								$role_color = [
									'superadmin'    => 'red',
									'admin-kribo'   => 'orange',
									'finance-kribo' => 'yellow',
									'admin-mitra'   => 'blue',
									'staff-mitra'   => 'azure',
									'finance-mitra' => 'cyan',
									'tracker-mitra' => 'indigo',
								][$user->role_slug] ?? 'secondary';
								?>
								<div>
									<span class="badge bg-<?= $role_color ?>-lt"><?= htmlspecialchars($user->role_name) ?></span>
									<span class="badge <?= $user->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?> ms-1">
										<?= $user->is_active ? 'Aktif' : 'Nonaktif' ?>
									</span>
								</div>
							</div>

							<div class="d-flex">
								<a href="<?= site_url('users/toggle_status/' . $user->id) ?>"
									class="card-btn"
									onclick="return confirm('Ubah status user ini?')">
									<?= tabler_icon('refresh', 'me-2 text-muted') ?> Status
								</a>
								<a href="<?= site_url('users/delete/' . $user->id) ?>"
									class="card-btn"
									onclick="return confirm('Hapus user ini? Tindakan tidak dapat dibatalkan.')">
									<?= tabler_icon('trash', 'me-2 text-danger') ?> <span class="text-danger">Hapus</span>
								</a>
							</div>
						</div>
					</div>

					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Informasi Tambahan</h3>
							</div>
							<div class="card-body">
								<div class="datagrid">
									<div class="datagrid-item">
										<div class="datagrid-title">Role Scope</div>
										<div class="datagrid-content">
											<span class="status status-azure">
												<?= htmlspecialchars($user->role_scope) ?>
											</span>
										</div>
									</div>

									<?php if ($user->agent_name): ?>
										<div class="datagrid-item">
											<div class="datagrid-title">Agen / Mitra</div>
											<div class="datagrid-content">
												<a href="<?= site_url('agents/detail/' . $user->agent_id) ?>" class="text-reset">
													<strong><?= htmlspecialchars($user->agent_name) ?></strong>
												</a>
											</div>
										</div>
									<?php endif; ?>

									<div class="datagrid-item">
										<div class="datagrid-title">Tanggal Bergabung</div>
										<div class="datagrid-content">
											<?= tabler_icon('calendar', 'icon-sm me-1 text-muted') ?>
											<?= date('d M Y', strtotime($user->created_at)) ?>
										</div>
									</div>

									<?php if ($user->updated_at): ?>
										<div class="datagrid-item">
											<div class="datagrid-title">Terakhir Diperbarui</div>
											<div class="datagrid-content">
												<?= date('d M Y, H:i', strtotime($user->updated_at)) ?>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('history', 'me-2') ?>Log Aktivitas Terakhir</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table">
							<thead>
								<tr>
									<th class="w-1">Waktu</th>
									<th>Aksi</th>
									<th>Keterangan</th>
									<th>IP Address</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($logs): ?>
									<?php foreach ($logs as $log): ?>
										<tr>
											<td class="text-muted small text-nowrap">
												<?= date('d M Y', strtotime($log->created_at)) ?><br>
												<span class="text-muted"><?= date('H:i', strtotime($log->created_at)) ?></span>
											</td>
											<td>
												<span class="badge bg-azure-lt"><?= htmlspecialchars($log->action) ?></span>
											</td>
											<td class="text-secondary"><?= htmlspecialchars($log->description) ?></td>
											<td class="text-muted small">
												<?= tabler_icon('network', 'icon-sm me-1 text-muted') ?>
												<?= htmlspecialchars($log->ip_address) ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="4" class="text-center py-5">
											<div class="empty">
												<div class="empty-icon">
													<?= tabler_icon('mood-empty', 'icon-lg') ?>
												</div>
												<p class="empty-title">Belum ada aktivitas</p>
												<p class="empty-subtitle text-muted">
													User ini belum melakukan aktivitas apapun yang terekam di sistem.
												</p>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
