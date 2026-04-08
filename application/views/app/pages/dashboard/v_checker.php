<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-navy">Warehouse Checker</h2>
				<p class="text-muted small">Cek barang masuk dari kurir ke Warehouse Smesco.</p>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row mb-4">
			<div class="col-12">
				<a href="<?= site_url('shipment/acceptance_scan') ?>" class="card card-link bg-green text-white py-3">
					<div class="card-body text-center">
						<div class="mb-3">
							<?= tabler_icon('scan', 'icon-lg') ?>
						</div>
						<div class="h1 mb-0">SCAN ACCEPTANCE</div>
						<div class="opacity-50">Timbang & Scan koli masuk gudang</div>
					</div>
				</a>
			</div>
		</div>

		<div class="row row-cards mb-4">
			<div class="col-4">
				<div class="card card-sm">
					<div class="card-body text-center">
						<div class="h3 mb-0"><?= number_format($stats->total_expected) ?></div>
						<div class="text-muted small">Total Resi</div>
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="card card-sm">
					<div class="card-body text-center border-bottom border-3 border-azure">
						<div class="h3 mb-0"><?= number_format($stats->in_transit) ?></div>
						<div class="text-muted small">OTW Gudang</div>
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="card card-sm">
					<div class="card-body text-center border-bottom border-3 border-green">
						<div class="h3 mb-0"><?= number_format($stats->accepted) ?></div>
						<div class="text-muted small">Diterima</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Barang Menuju Gudang</h3>
			</div>
			<div class="table-responsive">
				<table class="table table-vcenter card-table">
					<thead>
						<tr>
							<th>Resi</th>
							<th>Tujuan</th>
							<th>Koli</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($pending_inbound)): foreach ($pending_inbound as $i): ?>
								<tr>
									<td class="fw-bold"><?= $i->no_resi ?></td>
									<td><?= $i->destination ?></td>
									<td><span class="badge bg-azure-lt"><?= $i->koli ?> Koli</span></td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="3" class="text-center py-3">Tidak ada barang dalam perjalanan.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
