<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('agents') ?>">Agen</a></li>
						<li class="breadcrumb-item active"><?= ($agent->name) ?></li>
					</ol>
				</nav>
				<h2 class="page-title">Detail Agen</h2>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('agents/edit/' . $agent->id) ?>" class="btn btn-primary">
					<?= tabler_icon('pencil', 'me-1') ?>Edit Agen
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php if ($this->session->flashdata('success')): ?>
			<div class="alert alert-success alert-dismissible mb-4" role="alert">
				<div class="d-flex align-items-center">
					<div><?= tabler_icon('circle-check', 'me-2') ?></div>
					<div><?= $this->session->flashdata('success') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>
		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger alert-dismissible mb-4" role="alert">
				<div class="d-flex align-items-center">
					<div><?= tabler_icon('alert-circle', 'me-2') ?></div>
					<div><?= $this->session->flashdata('error') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>

		<div class="row row-deck row-cards">

			<div class="col-md-4">
				<div class="row row-cards">

					<div class="col-12">
						<div class="card">
							<div class="card-status-top bg-blue"></div>
							<div class="card-body text-center">
								<span class="avatar avatar-xl bg-blue-lt text-blue mb-3 rounded">
									<?= tabler_icon('building') ?>
									<span class="badge <?= $agent->is_active ? 'bg-success' : 'bg-danger' ?> badge-blink"></span>
								</span>
								<h3 class="card-title mb-1"><?= ($agent->name) ?></h3>
								<div class="mb-3">
									<span class="badge bg-blue-lt" title="Kode Agen"><?= ($agent->code) ?></span>
								</div>
								<div class="text-muted mb-3">
									<?= ($agent->regency_name) ?><?= $agent->province_name ? ', ' . ($agent->province_name) : '' ?>
								</div>
								<div>
									<span class="badge <?= $agent->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
										<?= $agent->is_active ? 'Status: Aktif' : 'Status: Nonaktif' ?>
									</span>
								</div>
							</div>

							<div class="d-flex">
								<a href="<?= site_url('agents/toggle_status/' . $agent->id) ?>"
									class="card-btn"
									onclick="return confirm('Ubah status agen ini?')">
									<?= tabler_icon('refresh', 'me-2 text-muted') ?> Status
								</a>
								<a href="<?= site_url('agents/delete/' . $agent->id) ?>"
									class="card-btn"
									onclick="return confirm('Hapus agen ini? Tindakan tidak dapat dibatalkan.')">
									<?= tabler_icon('trash', 'me-2 text-danger') ?> <span class="text-danger">Hapus</span>
								</a>
							</div>
						</div>
					</div>

					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Informasi Kontak</h3>
							</div>
							<div class="card-body">
								<div class="datagrid">
									<?php if ($agent->address): ?>
										<div class="datagrid-item">
											<div class="datagrid-title">Alamat Lengkap</div>
											<div class="datagrid-content"><?= ($agent->address) ?></div>
										</div>
									<?php endif; ?>

									<?php if ($agent->phone): ?>
										<div class="datagrid-item">
											<div class="datagrid-title">Telepon</div>
											<div class="datagrid-content">
												<a href="tel:<?= ($agent->phone) ?>" class="text-reset">
													<?= tabler_icon('phone', 'icon-sm me-1 text-muted') ?><?= ($agent->phone) ?>
												</a>
											</div>
										</div>
									<?php endif; ?>

									<?php if ($agent->email): ?>
										<div class="datagrid-item">
											<div class="datagrid-title">Email</div>
											<div class="datagrid-content">
												<a href="mailto:<?= ($agent->email) ?>" class="text-reset">
													<?= tabler_icon('mail', 'icon-sm me-1 text-muted') ?><?= ($agent->email) ?>
												</a>
											</div>
										</div>
									<?php endif; ?>

									<div class="datagrid-item">
										<div class="datagrid-title">Tanggal Terdaftar</div>
										<div class="datagrid-content">
											<?= tabler_icon('calendar', 'icon-sm me-1 text-muted') ?>
											<?= date('d M Y', strtotime($agent->created_at)) ?>
										</div>
									</div>

									<div class="datagrid-item">
										<div class="datagrid-title">Kapasitas Sistem</div>
										<div class="datagrid-content">
											<span class="badge bg-purple-lt"><?= number_format($agent->total_users) ?> user terdaftar</span>
										</div>
									</div>

									<div class="datagrid-item">
										<div class="datagrid-title">Hub Operasional (Cargo City)</div>
										<div class="datagrid-content">
											<span class="badge bg-purple text-white">
												<?= tabler_icon('map-pin', 'me-1') ?> <?= $agent->cargo_city_name ?? 'Belum di-mapping' ?>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('users', 'me-2') ?>User di Agen Ini</h3>
						<div class="card-actions">
							<a href="<?= site_url('users/create') ?>" class="btn btn-sm btn-primary">
								<?= tabler_icon('plus', 'me-1') ?>Tambah User
							</a>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table">
							<thead>
								<tr>
									<th>User</th>
									<th>Role</th>
									<th>Status</th>
									<th class="w-1">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($users): ?>
									<?php foreach ($users as $u):
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
														style="background-color: <?= $ac['bg'] ?>; color: <?= $ac['color'] ?>; font-size: 12px; position: relative;">
														<?= $initials ?>
														<span class="badge <?= $u->is_active ? 'bg-success' : 'bg-danger' ?>"
															style="position:absolute; bottom:1px; right:1px; width:8px; height:8px; border-radius:50%; padding:0; border:1.5px solid #fff;"></span>
													</span>
													<div>
														<div class="font-weight-medium text-body"><?= ($u->name) ?></div>
														<div class="text-muted small"><?= ($u->email) ?></div>
													</div>
												</div>
											</td>
											<td>
												<?php
												$role_color = [
													'superadmin'    => 'red',
													'admin-kribo'   => 'orange',
													'finance-kribo' => 'yellow',
													'admin-mitra'   => 'blue',
													'staff-mitra'   => 'azure',
													'finance-mitra' => 'cyan',
													'tracker-mitra' => 'indigo',
												][$u->role_slug] ?? 'secondary';
												?>
												<span class="badge bg-<?= $role_color ?>-lt"><?= ($u->role_name) ?></span>
											</td>
											<td>
												<span class="badge <?= $u->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
													<?= $u->is_active ? 'Aktif' : 'Nonaktif' ?>
												</span>
											</td>
											<td>
												<a href="<?= site_url('users/detail/' . $u->id) ?>"
													class="btn btn-sm btn-outline-secondary" title="Lihat Detail User">
													<?= tabler_icon('eye') ?>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="4" class="text-center py-5">
											<div class="empty">
												<div class="empty-icon">
													<?= tabler_icon('users-off', 'icon-lg') ?>
												</div>
												<p class="empty-title">Belum ada user</p>
												<p class="empty-subtitle text-muted">
													Agen ini belum memiliki user yang terdaftar di dalam sistem.
												</p>
												<div class="empty-action">
													<a href="<?= site_url('users/create') ?>" class="btn btn-primary">
														<?= tabler_icon('plus', 'me-2') ?>Tambahkan User Baru
													</a>
												</div>
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
