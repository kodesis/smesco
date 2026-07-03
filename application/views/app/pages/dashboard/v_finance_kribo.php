<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					Detail User
				</h2>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<!-- Header info -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="alert alert-info alert-dismissible" role="alert">
					<?= tabler_icon('info-circle', 'me-2') ?>
					<strong>Finance</strong> — Modul keuangan penuh akan tersedia di Phase 3.
					Berikut ringkasan data yang tersedia saat ini.
					<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
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
								<span class="bg-green text-white avatar"><?= tabler_icon('building') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium"><?= number_format($total_agents) ?></div>
								<div class="text-muted">Total Agen</div>
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
								<span class="bg-blue text-white avatar"><?= tabler_icon('users') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium"><?= number_format($total_users) ?></div>
								<div class="text-muted">Total User Mitra</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm border-dashed">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-yellow text-white avatar"><?= tabler_icon('cash') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium text-muted">—</div>
								<div class="text-muted">Total Tagihan <span class="badge bg-yellow-lt">Phase 3</span></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm border-dashed">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-red text-white avatar"><?= tabler_icon('receipt') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium text-muted">—</div>
								<div class="text-muted">Transaksi Bulan Ini <span class="badge bg-yellow-lt">Phase 3</span></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Agen Summary -->
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('building', 'me-2') ?>Ringkasan per Agen</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>Kode</th>
									<th>Nama Agen</th>
									<th>Kota</th>
									<th>Total User</th>
									<th>Status</th>
									<!-- <th class="text-muted">Tagihan <small>(Phase 3)</small></th> -->
								</tr>
							</thead>
							<tbody>
								<?php if ($agents_list): ?>
									<?php foreach ($agents_list as $a): ?>
										<tr>
											<td><span class="badge bg-blue-lt"><?= ($a->code) ?></span></td>
											<td><strong><?= ($a->name) ?></strong></td>
											<td class="text-muted"><?= ($a->regency_name) ?></td>
											<td><?= $a->total_users ?></td>
											<td>
												<?= $a->is_active
													? '<span class="badge bg-success-lt">Aktif</span>'
													: '<span class="badge bg-danger-lt">Nonaktif</span>' ?>
											</td>
											<!-- <td class="text-muted">—</td> -->
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="6" class="text-center text-muted py-4">Belum ada data agen</td>
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
