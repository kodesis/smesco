<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-uppercase ls-1">Daftar Transaksi Shipment</h2>
				<div class="text-muted mt-1">Monitoring & pengolahan data pengiriman kargo.</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('shipment/create') ?>" class="btn btn-primary shadow-sm">
					<?= tabler_icon('plus', 'me-2') ?> Buat Booking Baru
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<!-- SUMMARY CARDS (data dari $stats) -->
		<!-- <div class="row row-cards mb-4">
			<div class="col-6 col-lg-3">
				<div class="card card-sm border-0 border-start border-primary border-3">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-primary text-white avatar shadow-sm"><?= tabler_icon('package') ?></span></div>
							<div class="col">
								<div class="font-weight-medium">Total Booking</div>
								<div class="text-muted small"><?= $stats->total_all ?? 0 ?> Transaksi</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-lg-3">
				<div class="card card-sm border-0 border-start border-yellow border-3">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-yellow text-white avatar shadow-sm"><?= tabler_icon('clock-pause') ?></span></div>
							<div class="col">
								<div class="font-weight-medium">Pending Manifest</div>
								<div class="text-muted small"><?= $stats->total_pending ?? 0 ?> Resi</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-lg-3">
				<div class="card card-sm border-0 border-start border-blue border-3">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-blue text-white avatar shadow-sm"><?= tabler_icon('truck-delivery') ?></span></div>
							<div class="col">
								<div class="font-weight-medium">Dalam Transit</div>
								<div class="text-muted small"><?= $stats->total_transit ?? 0 ?> Resi</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 col-lg-3">
				<div class="card card-sm border-0 border-start border-green border-3">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto"><span class="bg-green text-white avatar shadow-sm"><?= tabler_icon('cash') ?></span></div>
							<div class="col">
								<div class="font-weight-medium">Total Omzet</div>
								<div class="text-muted small">Rp <?= number_format($stats->total_omzet ?? 0, 0, ',', '.') ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="card mb-3">
			<div class="card-body py-3">
				<form action="<?= site_url('reports') ?>" method="GET">
					<div class="row g-2 align-items-end">
						<div class="col-md-3">
							<label class="form-label small">Rentang Tanggal</label>
							<div class="input-group input-group-sm">
								<input type="date" name="start" class="form-control" value="<?= $filters['start'] ?>">
								<span class="input-group-text">s/d</span>
								<input type="date" name="end" class="form-control" value="<?= $filters['end'] ?>">
							</div>
						</div>
						<?php if ($role !== 'admin-mitra'): ?>
							<div class="col-md-2">
								<label class="form-label small">Filter Agen</label>
								<select name="agent_id" class="form-select form-select-sm">
									<option value="">Semua Agen</option>
									<?php foreach ($agents as $ag): ?>
										<option value="<?= $ag->id ?>" <?= ($filters['agent_id'] == $ag->id) ? 'selected' : '' ?>><?= $ag->name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
						<div class="col-md-2">
							<label class="form-label small">Status</label>
							<select name="status" class="form-select form-select-sm">
								<option value="">Semua Status</option>
								<option value="DELIVERED" <?= ($filters['status'] == 'DELIVERED') ? 'selected' : '' ?>>DELIVERED</option>
								<option value="CANCELLED" <?= ($filters['status'] == 'CANCELLED') ? 'selected' : '' ?>>CANCELLED / VOID</option>
							</select>
						</div>
						<div class="col-md-auto">
							<button type="submit" class="btn btn-sm btn-primary"><?= tabler_icon('search', 'me-1') ?> Terapkan</button>
							<a href="<?= site_url('reports') ?>" class="btn btn-sm btn-link">Reset</a>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- FILTER -->
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Rincian Performa Pengiriman</h3>
				<div class="card-options">
					<a href="<?= site_url('reports/export_excel?') . http_build_query($_GET) ?>" class="btn btn-success btn-sm">
						<?= tabler_icon('file-spreadsheet') ?> Export Excel
					</a>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-vcenter table-bordered card-table">
					<thead>
						<tr class="bg-light">
							<th>No. Resi / Tgl</th>
							<th>Status</th> <?php if ($role !== 'admin-mitra'): ?>
								<th>Agen</th>
							<?php endif; ?>
							<th>Destinasi</th>
							<th>Berat (CW)</th>

							<?php if (in_array($role, ['superadmin', 'finance-kribo'])): ?>
								<th>Cost (Kribo)</th>
							<?php endif; ?>

							<?php if (in_array($role, ['superadmin', 'admin-kribo', 'admin-mitra'])): ?>
								<th>Jual (Smesco)</th>
							<?php endif; ?>

							<?php if ($role === 'superadmin'): ?>
								<th class="text-success">Margin</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						$t_weight = 0;
						$t_cost = 0;
						$t_sell = 0;
						$t_margin = 0;

						foreach ($results as $r):
							$is_void = ($r->status === 'CANCELLED');
							$total_cost = $r->chargeable_weight * $r->cost_price;

							// LOGIC: Akumulasi total hanya untuk yang TIDAK CANCELLED
							if (!$is_void) {
								$t_weight += $r->chargeable_weight;
								$t_cost   += $total_cost;
								$t_sell   += $r->total_amount;
								$t_margin += $r->margin_amount;
							}
						?>
							<tr class="<?= $is_void ? 'text-muted bg-light' : '' ?>">
								<td class="<?= $is_void ? 'text-decoration-line-through' : '' ?>">
									<div class="fw-bold"><?= $r->no_resi ?></div>
									<div class="small text-muted"><?= date('d/m/y', strtotime($r->created_at)) ?></div>
								</td>
								<td>
									<span class="badge <?= $is_void ? 'bg-danger' : 'bg-azure' ?>-lt">
										<?= $r->status ?>
									</span>
								</td>
								<?php if ($role !== 'admin-mitra'): ?>
									<td><?= $r->agent_name ?></td>
								<?php endif; ?>
								<td><?= $r->destination ?></td>
								<td class="text-end"><?= $r->chargeable_weight ?> Kg</td>

								<?php if (in_array($role, ['superadmin', 'finance-kribo'])): ?>
									<td class="<?= $is_void ? 'text-decoration-line-through' : '' ?>"><?= number_format($total_cost) ?></td>
								<?php endif; ?>

								<?php if (in_array($role, ['superadmin', 'admin-kribo', 'admin-mitra'])): ?>
									<td class="fw-bold <?= $is_void ? 'text-decoration-line-through' : '' ?> text-end"><?= number_format($r->total_amount) ?></td>
								<?php endif; ?>

								<?php if ($role === 'superadmin'): ?>
									<td class="text-success fw-bold <?= $is_void ? 'text-decoration-line-through' : '' ?> text-end"><?= number_format($r->margin_amount) ?></td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot class="bg-dark text-white fw-bold">
						<tr>
							<td colspan="<?= ($role === 'admin-mitra') ? 3 : 4 ?>">TOTAL (AKTIF)</td>
							<td class="text-end"><?= number_format($t_weight, 2) ?> Kg</td>

							<?php if (in_array($role, ['superadmin', 'finance-kribo'])): ?>
								<td class="text-end"><?= number_format($t_cost) ?></td>
							<?php endif; ?>

							<?php if (in_array($role, ['superadmin', 'admin-kribo', 'admin-mitra'])): ?>
								<td class="text-end"><?= number_format($t_sell) ?></td>
							<?php endif; ?>

							<?php if ($role === 'superadmin'): ?>
								<td class="text-yellow text-end"><?= number_format($t_margin) ?></td>
							<?php endif; ?>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
