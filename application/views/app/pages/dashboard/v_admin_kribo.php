<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-3">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-uppercase ls-1">Dashboard Admin Kribo</h2>
				<div class="text-muted small mt-1">Monitoring operasional pengiriman & manajemen mitra.</div>
			</div>
			<div class="col-auto text-muted small"><?= date('l, d F Y') ?></div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<!-- ===== ROW 1: STAT CARDS USER & AGENT ===== -->
		<div class="row row-deck row-cards mb-3">
			<div class="col-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-blue text-white avatar"><?= tabler_icon('building') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($total_agents) ?></div>
								<div class="text-muted small">Total Agen</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-green text-white avatar"><?= tabler_icon('map-pin') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($agents_active) ?></div>
								<div class="text-muted small">Agen Aktif</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-teal text-white avatar"><?= tabler_icon('users') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($total_users) ?></div>
								<div class="text-muted small">Total User</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-red text-white avatar"><?= tabler_icon('user-off') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($users_inactive) ?></div>
								<div class="text-muted small">User Nonaktif</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ===== ROW 2: STAT CARDS SHIPMENT ===== -->
		<div class="row row-deck row-cards mb-3">
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-azure text-white avatar"><?= tabler_icon('package') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->shipment_today ?? 0) ?></div>
								<div class="text-muted small">Booking Hari Ini</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-cyan text-white avatar"><?= tabler_icon('clock-pause') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->pending_payment ?? 0) ?></div>
								<div class="text-muted small">Pending Bayar</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- TAMBAHKAN INI setelah card "Pending Bayar" -->
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-lime text-white avatar"><?= tabler_icon('truck') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->proses_pickup ?? 0) ?></div>
								<div class="text-muted small">Proses Pickup</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-yellow text-white avatar"><?= tabler_icon('list-check') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->ready_manifest ?? 0) ?></div>
								<div class="text-muted small">Siap Manifest</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-orange text-white avatar"><?= tabler_icon('plane-departure') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->in_transit ?? 0) ?></div>
								<div class="text-muted small">Dalam Transit</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-success text-white avatar"><?= tabler_icon('circle-check') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->delivered ?? 0) ?></div>
								<div class="text-muted small">Delivered</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-xl">
				<div class="card card-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-indigo text-white avatar"><?= tabler_icon('receipt') ?></span></div>
							<div class="col">
								<div class="fw-bold"><?= number_format($shipment_stats->total_shipment ?? 0) ?></div>
								<div class="text-muted small">Total Semua</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ===== ROW 3: RECENT SHIPMENT + AGENT LIST ===== -->
		<div class="row row-deck row-cards mb-3">

			<!-- Recent Shipments -->
			<div class="col-lg-8">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('package', 'me-2') ?>Booking Terbaru</h3>
						<div class="card-options">
							<a href="<?= site_url('shipment') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table table-hover">
							<thead class="bg-light">
								<tr>
									<th>No. Resi</th>
									<th>Agen</th>
									<th>Rute</th>
									<th>Biaya</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($recent_shipments): foreach ($recent_shipments as $s):
										$bg = 'bg-secondary';
										if ($s->status == 'BOOKED')           $bg = 'bg-cyan';
										if ($s->status == 'READY_TO_PICKUP')  $bg = 'bg-yellow text-dark';
										if ($s->status == 'PICKED_UP')             $bg = 'bg-teal';
										if ($s->status == 'RECEIVED_ORIGIN')  $bg = 'bg-indigo';
										if ($s->status == 'MANIFESTED')       $bg = 'bg-blue';
										if ($s->status == 'DEPARTED')              $bg = 'bg-orange';
										if ($s->status == 'ARRIVED')               $bg = 'bg-purple';
										if ($s->status == 'RECEIVED_DESTINATION')  $bg = 'bg-cyan';
										if ($s->status == 'DELIVERED')        $bg = 'bg-success';
										if ($s->status == 'CANCELLED')        $bg = 'bg-danger';
								?>
										<tr>
											<td>
												<a href="<?= site_url('shipment/detail/' . $s->id) ?>" class="fw-bold text-primary"><?= $s->no_resi ?></a>
												<div class="small text-muted"><?= date('d/m/Y H:i', strtotime($s->created_at)) ?></div>
											</td>
											<td class="small"><?= htmlspecialchars($s->agent_name ?? '—') ?></td>
											<td>
												<div class="d-flex align-items-center gap-1 small">
													<span class="badge bg-blue-lt"><?= $s->origin ?></span>
													<i class="bi bi-arrow-right text-muted"></i>
													<span class="badge bg-green-lt"><?= $s->destination ?></span>
												</div>
												<div class="small text-muted"><?= $s->service_code ?></div>
											</td>
											<td class="fw-bold text-success small">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></td>
											<td><span class="badge <?= $bg ?> fw-bold"><?= str_replace('_', ' ', $s->status) ?></span></td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="5" class="text-center text-muted py-4">Belum ada data shipment</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Daftar Agen -->
			<div class="col-lg-4">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('building', 'me-2') ?>Daftar Agen</h3>
						<div class="card-options">
							<a href="<?= site_url('agents') ?>" class="btn btn-sm btn-outline-primary">Kelola</a>
						</div>
					</div>
					<div class="list-group list-group-flush overflow-auto" style="max-height: 380px;">
						<?php if ($agents_list): foreach ($agents_list as $a): ?>
								<div class="list-group-item">
									<div class="row align-items-center">
										<div class="col">
											<a href="<?= site_url('agents/detail/' . $a->id) ?>" class="fw-semibold text-body"><?= trim($a->name) ?></a>
											<div class="text-muted small"><?= trim($a->code) ?> · <?= trim($a->regency_name) ?></div>
										</div>
										<div class="col-auto d-flex align-items-center gap-2">
											<span class="badge bg-blue-lt"><?= $a->total_users ?> user</span>
											<span class="badge <?= $a->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
												<?= $a->is_active ? 'Aktif' : 'Off' ?>
											</span>
										</div>
									</div>
								</div>
							<?php endforeach;
						else: ?>
							<div class="list-group-item text-center text-muted py-4">Belum ada agen</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

		</div>

		<!-- ===== ROW 4: USER TERBARU ===== -->
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?= tabler_icon('users', 'me-2') ?>User Terbaru</h3>
				<div class="card-options">
					<a href="<?= site_url('users') ?>" class="btn btn-sm btn-outline-primary">Kelola User</a>
				</div>
			</div>
			<div class="list-group list-group-flush">
				<?php if ($recent_users):
					$role_color_map = [
						'superadmin'    => 'red',
						'admin-kribo'   => 'orange',
						'finance-kribo' => 'yellow',
						'admin-mitra'   => 'blue',
						'staff-mitra'   => 'azure',
						'finance-mitra' => 'cyan',
						'tracker-mitra' => 'indigo',
					];
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
					foreach ($recent_users as $u):
						$role_color = $role_color_map[$u->role_slug] ?? 'secondary';
						$ac         = $avatar_colors[$role_color];
						$words      = explode(' ', trim($u->name));
						$initials   = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
				?>
						<div class="list-group-item">
							<div class="row align-items-center">
								<div class="col-auto">
									<span class="avatar avatar-sm fw-medium text-uppercase"
										style="background:<?= $ac['bg'] ?>;color:<?= $ac['color'] ?>;font-size:12px;position:relative;">
										<?= $initials ?>
										<span class="badge <?= $u->is_active ? 'bg-success' : 'bg-danger' ?>"
											style="position:absolute;bottom:1px;right:1px;width:8px;height:8px;border-radius:50%;padding:0;border:1.5px solid #fff;"></span>
									</span>
								</div>
								<div class="col text-truncate">
									<a href="<?= site_url('users/detail/' . $u->id) ?>" class="text-body d-block"><?= trim($u->name) ?></a>
									<div class="text-muted small text-truncate">
										<?= trim($u->role_name) ?>
										<?php if (!empty($u->agent_name)): ?> · <?= trim($u->agent_name) ?><?php endif; ?>
									</div>
								</div>
								<div class="col-auto">
									<span class="badge bg-<?= $role_color ?>-lt"><?= trim($u->role_name) ?></span>
								</div>
							</div>
						</div>
					<?php endforeach;
				else: ?>
					<div class="list-group-item text-center text-muted py-4">Belum ada user</div>
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
