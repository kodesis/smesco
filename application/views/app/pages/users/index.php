<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Manajemen User</h2>
				<div class="text-muted mt-1">
					<?= number_format($total) ?> user terdaftar
					<?php if ($agent_scope): ?>
						<span class="badge bg-blue-lt ms-2">Scope: Agen Saya</span>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('users/create') ?>" class="btn btn-primary">
					<?= tabler_icon('plus', 'me-2') ?>Tambah User
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-12">
				<div class="card">

					<div class="card-header">
						<div class="d-flex align-items-center gap-2 flex-wrap">
							<h3 class="card-title me-2">Daftar User</h3>

							<!-- Filter Role & Status (auto-submit) -->
							<form method="get" action="<?= site_url('users') ?>" class="d-flex align-items-center gap-2 flex-wrap" id="filter-form">
								<input type="hidden" name="q" value="<?= htmlspecialchars($search ?? '') ?>">

								<!-- <select name="role" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('filter-form').submit()">
									<option value="">Semua Role</option>
									<?php
									$roles = [
										'superadmin'    => 'Superadmin',
										'admin-kribo'   => 'Admin Kribo',
										'finance-kribo' => 'Finance Kribo',
										'admin-mitra'   => 'Admin Mitra',
										'staff-mitra'   => 'Staff Mitra',
										'finance-mitra' => 'Finance Mitra',
										'tracker-mitra' => 'Tracker Mitra',
									];
									foreach ($roles as $slug => $label):
									?>
										<option value="<?= $slug ?>" <?= (($role_filter ?? '') === $slug) ? 'selected' : '' ?>>
											<?= $label ?>
										</option>
									<?php endforeach; ?>
								</select> -->

								<select name="status" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('filter-form').submit()">
									<option value="">Semua Status</option>
									<option value="1" <?= (($status_filter ?? '') === '1') ? 'selected' : '' ?>>Aktif</option>
									<option value="0" <?= (($status_filter ?? '') === '0') ? 'selected' : '' ?>>Nonaktif</option>
								</select>
							</form>
						</div>

						<div class="card-options">
							<!-- Search (preserve filter params) -->
							<form method="get" action="<?= site_url('users') ?>">
								<?php if (!empty($role_filter)): ?>
									<input type="hidden" name="role" value="<?= htmlspecialchars($role_filter) ?>">
								<?php endif; ?>
								<?php if ($status_filter !== '' && $status_filter !== NULL): ?>
									<input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
								<?php endif; ?>
								<div class="input-group">
									<span class="input-group-text bg-transparent border-end-0">
										<?= tabler_icon('search', 'icon text-muted') ?>
									</span>
									<input type="text" name="q" class="form-control form-control-sm border-start-0 ps-0"
										placeholder="Nama, email, atau role..."
										value="<?= htmlspecialchars($search ?? '') ?>"
										style="min-width: 200px;">
									<button type="submit" class="btn btn-sm btn-primary">Cari</button>
									<?php if ($search): ?>
										<a href="<?= site_url('users') ?>" class="btn btn-sm btn-danger" title="Reset">
											<?= tabler_icon('x') ?>
										</a>
									<?php endif; ?>
								</div>
							</form>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table">
							<thead>
								<tr>
									<th>User</th>
									<th>Role</th>
									<th>Agen</th>
									<th>Status</th>
									<th>Bergabung</th>
									<th class="w-1 text-end">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($users): ?>
									<?php foreach ($users as $u): ?>
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
										$ac = $avatar_colors[$role_color];
										?>
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
														<div class="font-weight-medium text-body"><?= htmlspecialchars($u->name) ?></div>
														<div class="text-muted small"><?= htmlspecialchars($u->email) ?></div>
													</div>
												</div>
											</td>
											<td>
												<span class="badge bg-<?= $role_color ?>-lt">
													<?= htmlspecialchars($u->role_name) ?>
												</span>
											</td>
											<td class="text-muted">
												<?= $u->agent_name ? htmlspecialchars($u->agent_name) : '—' ?>
											</td>
											<td>
												<span class="badge <?= $u->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
													<?= $u->is_active ? 'Aktif' : 'Nonaktif' ?>
												</span>
											</td>
											<td class="text-muted small">
												<?= date('d M Y', strtotime($u->created_at)) ?>
											</td>

											<td class="text-end">
												<div class="d-flex align-items-center justify-content-end gap-1">
													<a href="<?= site_url('users/detail/' . $u->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Detail"
														data-bs-toggle="tooltip" data-bs-placement="top">
														<?= tabler_icon('eye') ?>
													</a>
													<a href="<?= site_url('users/edit/' . $u->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Edit"
														data-bs-toggle="tooltip" data-bs-placement="top">
														<?= tabler_icon('pencil') ?>
													</a>
													<a href="<?= site_url('users/toggle_status/' . $u->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Toggle Status"
														data-bs-toggle="tooltip" data-bs-placement="top"
														onclick="return confirm('Ubah status user ini?')">
														<?= tabler_icon('refresh') ?>
													</a>
													<a href="<?= site_url('users/delete/' . $u->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-danger"
														title="Hapus"
														data-bs-toggle="tooltip" data-bs-placement="top"
														onclick="return confirm('Hapus user <?= htmlspecialchars($u->name, ENT_QUOTES) ?>? Tindakan ini tidak dapat dibatalkan.')">
														<?= tabler_icon('trash') ?>
													</a>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="6" class="text-center py-5">
											<div class="empty">
												<div class="empty-icon">
													<?= tabler_icon('users-off', 'icon-lg') ?>
												</div>
												<p class="empty-title">Data tidak ditemukan</p>
												<p class="empty-subtitle text-muted">
													<?= $search
														? 'Tidak ada user yang cocok dengan kata kunci "<strong>' . htmlspecialchars($search) . '</strong>".'
														: 'Belum ada user yang terdaftar di dalam sistem.' ?>
												</p>
												<?php if ($search): ?>
													<div class="empty-action">
														<a href="<?= site_url('users') ?>" class="btn btn-primary">
															<?= tabler_icon('x', 'me-2') ?>Reset Pencarian
														</a>
													</div>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
					<!-- Pagination Footer -->
					<?php $this->load->view('app/layouts/_pagination', compact(
						'total',
						'page',
						'per_page',
						'offset',
						'total_pages',
						'base_url'
					)); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
			new bootstrap.Tooltip(el, {
				trigger: 'hover'
			});
		});
	});
</script>
