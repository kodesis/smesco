<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="card-header justify-content-between">
				<h3 class="card-title">Konfirmasi Perubahan Data</h3>
				<div class="btn-list">
					<a href="<?= site_url('master/pricelist') ?>" class="btn btn-link">Batal</a>
					<a href="<?= site_url('master/confirm_import_pricelist') ?>" class="btn btn-primary">
						<?= tabler_icon('check', 'me-2') ?> Ya, Sinkronkan Data
					</a>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-striped">
					<thead>
						<tr>
							<th>Status</th>
							<th>Rute & Service</th>
							<th>Harga Kribo (Modal)</th>
							<th>Harga Smesco (Jual)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($preview as $p): ?>
							<tr>
								<td>
									<?php if ($p['status'] == 'NEW'): ?>
										<span class="badge bg-success">BARU</span>
									<?php elseif ($p['status'] == 'UPDATE'): ?>
										<span class="badge bg-warning">UPDATE</span>
									<?php else: ?>
										<span class="badge bg-secondary">SAMA</span>
									<?php endif; ?>
								</td>
								<td>
									<strong><?= $p['origin'] ?> → <?= $p['destination'] ?></strong><br>
									<small class="text-muted">Service ID: <?= $p['service_id'] ?></small>
								</td>
								<td>
									<?php if ($p['status'] == 'UPDATE' && $p['price_kribo'] != $p['diff']['old_kribo']): ?>
										<small class="text-danger text-decoration-line-through"><?= number_format($p['diff']['old_kribo']) ?></small> →
									<?php endif; ?>
									<strong><?= number_format($p['price_kribo']) ?></strong>
								</td>
								<td>
									<?php if ($p['status'] == 'UPDATE' && $p['price_smesco'] != $p['diff']['old_smesco']): ?>
										<small class="text-danger text-decoration-line-through"><?= number_format($p['diff']['old_smesco']) ?></small> →
									<?php endif; ?>
									<strong><?= number_format($p['price_smesco']) ?></strong>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
