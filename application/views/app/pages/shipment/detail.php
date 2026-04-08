<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Detail Shipment</h2>
				<div class="text-muted mt-1">Status saat ini:
					<span class="badge bg-blue-lt"><?= $shipment['status'] ?></span>
				</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a href="<?= site_url('shipment') ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-2"></i> Kembali
					</a>
					<a href="<?= site_url('shipment/print_label/' . $shipment['no_resi']) ?>" target="_blank" class="btn btn-primary">
						<i class="bi bi-printer me-2"></i> Cetak Label
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Informasi Pengiriman</h3>
					</div>
					<div class="card-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<label class="form-label text-muted small uppercase">Nomor Resi (AWB)</label>
								<div class="h3 text-primary fw-bold"><?= $shipment['no_resi'] ?></div>
							</div>
							<div class="col-md-6">
								<label class="form-label text-muted small uppercase">Layanan</label>
								<div class="h4"><?= $shipment['service_name'] ?></div>
							</div>
						</div>

						<hr>

						<div class="row g-3">
							<div class="col-md-6">
								<div class="bg-light p-3 rounded-3 border">
									<div class="text-muted small fw-bold mb-2">PENGIRIM</div>
									<div class="fw-bold"><?= $shipment['sender_name'] ?></div>
									<div class="small"><?= $shipment['sender_phone'] ?></div>
									<div class="mt-2 text-muted small"><?= $shipment['sender_address'] ?></div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="bg-blue-lt p-3 rounded-3 border border-primary">
									<div class="text-primary small fw-bold mb-2">PENERIMA</div>
									<div class="fw-bold"><?= $shipment['receiver_name'] ?></div>
									<div class="small"><?= $shipment['receiver_phone'] ?></div>
									<div class="mt-2 text-muted small"><?= $shipment['receiver_address'] ?></div>
								</div>
							</div>
						</div>

						<div class="mt-4">
							<label class="form-label text-muted small fw-bold">DESKRIPSI BARANG</label>
							<div class="p-3 bg-light rounded border italic">
								"<?= $shipment['commodity_detail'] ?>" (<?= $shipment['commodity_name'] ?>)
							</div>
						</div>
					</div>
				</div>

				<div class="card mt-4">
					<div class="card-table table-responsive">
						<table class="table table-vcenter">
							<thead>
								<tr>
									<th>Koli</th>
									<th>Dimensi (P x L x T)</th>
									<th>Volume (kg)</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($dimensions as $dim): ?>
									<tr>
										<td><?= $dim['qty'] ?> Unit</td>
										<td><?= (float)$dim['length'] ?> x <?= (float)$dim['width'] ?> x <?= (float)$dim['height'] ?> cm</td>
										<td class="text-end"><?= number_format((($dim['length'] * $dim['width'] * $dim['height']) / 5000) * $dim['qty'], 2) ?> kg</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="bg-light fw-bold">
								<tr>
									<td colspan="2">Chargeable Weight (Timbangan Charge)</td>
									<td class="text-blue text-end h3 mb-0"><?= $shipment['chargeable_weight'] ?> KG</td>
								</tr>
								<tr>
									<td colspan="2">Total Amount (Biaya Kirim)</td>
									<td class="text-success text-end h3 mb-0">Rp <?= number_format($shipment['total_amount']) ?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Tracking Log</h3>
					</div>
					<div class="card-body p-0">
						<ul class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
							<?php foreach ($history as $key => $h): ?>
								<div class="list-group-item">
									<div class="row">
										<div class="col-auto">
											<span class="status-dot <?= ($key === 0) ? 'status-dot-animated status-green' : '' ?> d-block"></span>
										</div>
										<div class="col">
											<div class="text-truncate small text-muted"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></div>
											<div class="fw-bold text-navy"><?= $h['status'] ?></div>
											<div class="small text-muted"><?= $h['note'] ?></div>
											<?php if ($h['location']): ?>
												<div class="mt-1 small"><i class="bi bi-geo-alt me-1"></i><?= $h['location'] ?></div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
