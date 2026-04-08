<!-- v_mitra.php -->
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-uppercase ls-1">
					Dashboard <?= htmlspecialchars($agent_info->name ?? 'Mitra') ?>
				</h2>
				<div class="text-muted small mt-1">
					<?= $agent_info->code ?? '' ?> · <?= $agent_info->regency_name ?? '' ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

		<!-- Agent Header Card -->
		<div class="row mb-4">
			<div class="col-sm-6 col-xl-9">
				<div class="card card-body bg-primary-lt border-primary">
					<div class="row align-items-center">
						<div class="col-auto">
							<span class="avatar avatar-lg bg-primary text-white">
								<?= tabler_icon('building') ?>
							</span>
						</div>
						<div class="col">
							<h3 class="mb-0"><?= ($agent_info->name ?? '—') ?></h3>
							<div class="text-muted">
								Kode: <strong><?= ($agent_info->code ?? '—') ?></strong>
								· <?= ($agent_info->regency_name ?? '—') ?>
								<?php if (!empty($agent_info->province_name)): ?>
									, <?= ($agent_info->province_name) ?>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-auto">
							<?php if (!empty($agent_info->is_active)): ?>
								<span class="badge bg-success">Aktif</span>
							<?php else: ?>
								<span class="badge bg-danger">Nonaktif</span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-purple text-white avatar"><?= tabler_icon('package-import') ?></span>
							</div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->inbound_pending ?? 0) ?></div>
								<div class="text-muted small">Menunggu Inbound</div>
							</div>
						</div>
					</div>
					<?php if ($city_name): ?>
						<div class="card-footer p-2">
							<a href="<?= site_url('shipment/inbound_scan') ?>" class="btn btn-sm btn-purple w-100">
								<?= tabler_icon('scan', 'me-1') ?> Buka Scanner Inbound
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>


		<!-- Stats -->
		<div class="row row-deck row-cards mb-4">
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-blue text-white avatar"><?= tabler_icon('users') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium"><?= number_format($total_users) ?></div>
								<div class="text-muted">Total User Agen</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-green text-white avatar"><?= tabler_icon('user-check') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium"><?= number_format($users_active) ?></div>
								<div class="text-muted">User Aktif</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-yellow text-white avatar"><?= tabler_icon('package') ?></span>
							</div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->shipment_today ?? 0) ?></div>
								<div class="text-muted small">Paket Hari Ini</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-green text-white avatar"><?= tabler_icon('cash') ?></span>
							</div>
							<div class="col">
								<div class="fw-bold">Rp <?= number_format($shipment_stats->omzet_bulan_ini ?? 0, 0, ',', '.') ?></div>
								<div class="text-muted small">Omzet Bulan Ini</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- User List Agen -->
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('users', 'me-2') ?>Anggota Tim</h3>
						<?php if ($current_user['role_slug'] === 'admin-mitra'): ?>
							<div class="card-options">
								<a href="<?= site_url('users/create') ?>" class="btn btn-sm btn-primary">
									<?= tabler_icon('plus', 'me-1') ?> Tambah User
								</a>
							</div>
						<?php endif; ?>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>Nama</th>
									<th>Role</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($agent_users): ?>
									<?php foreach ($agent_users as $u):

										$role_color = [
											'superadmin'    => 'red',
											'admin-kribo'   => 'orange',
											'finance-kribo' => 'yellow',
											'admin-mitra'   => 'blue',
											'staff-mitra'   => 'azure',
											'finance-mitra' => 'cyan',
											'tracker-mitra' => 'indigo',
										][$u->role_slug] ?? 'secondary';

										$words    = explode(' ', trim($u->name));
										$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

										$avatar_colors = [
											'red'       => ['bg' => '#fce8e8', 'color' => '#a52a2a'],
											'orange'    => ['bg' => '#fef0e6', 'color' => '#9d4a00'],
											'yellow'    => ['bg' => '#fdf5d3', 'color' => '#8a6800'],
											'blue'      => ['bg' => '#e6f1fb', 'color' => '#0c447c'],
											'azure'     => ['bg' => '#e8f4fd', 'color' => '#0d5d94'],
											'cyan'      => ['bg' => '#e3f5f9', 'color' => '#0a5f74'],
											'indigo'    => ['bg' => '#eaecfb', 'color' => '#3a3f9e'],
											'secondary' => ['bg' => '#f1f3f5', 'color' => '#6c757d'],
										];
										$ac = $avatar_colors[$role_color]; ?>
										<tr>
											<td>
												<div class="d-flex align-items-center">
													<span class="avatar avatar-sm me-3 text-uppercase fw-medium"
														style="background-color:<?= $ac['bg'] ?>;color:<?= $ac['color'] ?>;font-size:12px;position:relative;">
														<?= $initials ?>
														<span class="badge <?= $u->is_active ? 'bg-success' : 'bg-danger' ?>"
															style="position:absolute;bottom:1px;right:1px;width:8px;height:8px;border-radius:50%;padding:0;border:1.5px solid #fff;"></span>
													</span>
													<div>
														<a href="<?= site_url('users/detail/' . $u->id) ?>" class="text-body fw-semibold">
															<?= htmlspecialchars(trim($u->name)) ?>
														</a>
														<div class="text-muted small"><?= htmlspecialchars(trim($u->email)) ?></div>
													</div>
												</div>
											</td>
											<td><span class="badge bg-purple-lt"><?= ($u->role_name) ?></span></td>
											<td>
												<?= $u->is_active
													? '<span class="badge bg-success-lt">Aktif</span>'
													: '<span class="badge bg-danger-lt">Nonaktif</span>' ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="4" class="text-center text-muted py-4">
											Belum ada user terdaftar di agen ini
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
