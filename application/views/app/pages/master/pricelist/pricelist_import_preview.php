<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="card-header justify-content-between">
				<h3 class="card-title"><?= tabler_icon('file-search', 'me-2') ?>Konfirmasi Perubahan Data Pricelist</h3>
				<div class="btn-list">
					<a href="<?= site_url('master/pricelist') ?>" class="btn btn-link">Batal</a>
					<a href="<?= site_url('master/confirm_import_pricelist') ?>" class="btn btn-primary shadow-sm">
						<?= tabler_icon('check', 'me-2') ?> Ya, Sinkronkan Data
					</a>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-bordered">
					<thead class="bg-light">
						<tr>
							<th width="10%">Tipe</th>
							<th>Rute & Layanan</th>
							<th width="15%">Kategori</th>
							<th>Rincian Harga (Tiering)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($preview as $p):
							$is_intl = ($p['category'] === 'INTERNATIONAL');
						?>
							<tr>
								<td>
									<span class="badge bg-azure-lt">PROSES</span>
								</td>
								<td>
									<div class="fw-bold text-uppercase"><?= $p['origin'] ?> <?= tabler_icon('arrow-right', 'mx-1') ?> <?= $p['destination'] ?></div>
									<div class="small text-muted">Service ID: <?= $p['service_type_id'] ?></div>
								</td>
								<td>
									<span class="badge <?= $is_intl ? 'bg-purple' : 'bg-azure' ?>-lt">
										<?= $is_intl ? '✈️ INTERNATIONAL' : '🚛 DOMESTIC' ?>
									</span>
								</td>
								<td class="p-0">
									<table class="table table-vcenter table-sm card-table table-borderless m-0">
										<thead class="small border-bottom">
											<tr>
												<th>Range Berat</th>
												<th class="text-end">Modal (Kribo)</th>
												<th class="text-end">Jual (Smesco)</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($p['tiers'] as $t): ?>
												<tr>
													<td class="small text-muted">
														<?php if ($p['is_tiered']): ?>
															<?= $t['tier_min'] ?> - <?= $t['tier_max'] ?> Kg
														<?php else: ?>
															Min. <?= $p['min_weight_kg'] ?> Kg (Flat)
														<?php endif; ?>
													</td>
													<td class="text-end fw-bold">Rp <?= number_format($t['price_kribo']) ?></td>
													<td class="text-end fw-bold text-primary">Rp <?= number_format($t['price_smesco']) ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="card-footer small text-muted">
				* Pastikan range berat tidak tumpang tindih untuk rute Internasional.
			</div>
		</div>
	</div>
</div>
