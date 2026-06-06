<!-- detail.php -->
<?php defined('BASEPATH') or exit('No direct script access allowed');
$sess     = $this->session->userdata('user');
$is_kribo = in_array($sess['role_slug'], ['superadmin', 'admin-kribo', 'finance-kribo']);

$no_print_statuses = ['BOOKED', 'CANCELLED'];
$can_print = !in_array($shipment['status'], $no_print_statuses);

// $can_edit_statuses = ['BOOKED'];

// $can_edit = in_array($shipment['status'])
?>

<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Detail Shipment</h2>
				<div class="text-muted mt-1">
					Status saat ini: <?= shipment_status_badge($shipment['status']) ?>
				</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a href="<?= site_url('shipment') ?>" class="btn btn-outline-secondary">
						<?= tabler_icon('arrow-left', 'me-1') ?> Kembali
					</a>

					<?php if ($shipment['status'] == 'BOOKED'): ?>
						<?php if ($shipment['payment_type'] !== 'TRANSFER'): ?>

							<button type="button" class="btn btn-success btn-confirm-paid"
								data-id="<?= $shipment['id'] ?>" data-resi="<?= $shipment['no_resi'] ?>">
								<?= tabler_icon('cash', 'me-1') ?> Lunas
							</button>

							<?php else:
							if (!$shipment['payment_proof']) {
								// Belum upload bukti transfer
							?>
								<span class="btn btn-warning disabled pe-none" style="opacity: 1;">
									<?= tabler_icon('clock-hour-4', 'me-1') ?> Menunggu Bukti Transfer
								</span>
							<?php
							} else {
								// Sudah upload bukti transfer, tinggal tunggu konfirmasi Finance
							?>
								<span class="btn btn-info disabled pe-none" style="opacity: 1;">
									<?= tabler_icon('paw', 'me-1') ?> Menunggu Verifikasi Finance
								</span>
							<?php
							} ?>

						<?php endif; ?>
						<a href="<?= base_url('shipment/edit/' . $shipment['id']) ?>" class="btn btn-primary">
							<?= tabler_icon('pencil', 'me-1') ?> Edit
						</a>
					<?php endif; ?>

					<?php if ($can_print): ?>
						<a href="<?= site_url('shipment/print_label/' . $shipment['no_resi']) ?>"
							target="_blank"
							class="btn btn-primary">
							<?= tabler_icon('printer') ?> Print Label
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">

			<!-- ── KOLOM KIRI ── -->
			<div class="col-md-8">

				<!-- Card: Info Pengiriman -->
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Informasi Pengiriman</h3>
						<div class="card-options">
							<span class="text-muted small">Dibuat: <?= date('d/m/Y H:i', strtotime($shipment['created_at'])) ?></span>
						</div>
					</div>
					<div class="card-body">

						<!-- No Resi / Kategori / Layanan -->
						<div class="row mb-3">
							<div class="col-md-4">
								<label class="form-label text-muted small text-uppercase">Nomor Resi</label>
								<div class="h3 text-primary fw-bold"><?= $shipment['no_resi'] ?></div>
							</div>
							<div class="col-md-4 text-md-center">
								<label class="form-label text-muted small text-uppercase">Kategori</label>
								<div>
									<span class="badge <?= $shipment['category'] === 'INTERNATIONAL' ? 'bg-purple' : 'bg-azure' ?>-lt">
										<?= $shipment['category'] ?>
									</span>
								</div>
							</div>
							<div class="col-md-4 text-md-end">
								<label class="form-label text-muted small text-uppercase">Layanan</label>
								<div class="h4"><?= $shipment['service_name'] ?></div>
							</div>
						</div>

						<!-- Rute -->
						<div class="p-2 border-top border-bottom bg-light-lt mb-3">
							<div class="row align-items-center text-center">
								<div class="col">
									<div class="small text-muted">ORIGIN</div>
									<div class="h4 mb-0 text-primary"><?= $shipment['origin'] ?></div>
								</div>
								<div class="col-auto text-muted">
									<i class="bi bi-chevron-double-right"></i>
								</div>
								<div class="col">
									<div class="small text-muted">DESTINATION</div>
									<div class="h4 mb-0 text-primary"><?= $shipment['destination'] ?></div>
								</div>
							</div>
						</div>

						<!-- Pengirim & Penerima -->
						<div class="row g-3 mb-3">
							<div class="col-md-6">
								<div class="bg-light p-3 rounded-3 border h-100">
									<div class="text-muted small fw-bold mb-2">PENGIRIM</div>
									<div class="fw-bold"><?= $shipment['sender_name'] ?></div>
									<div class="small"><?= $shipment['sender_phone'] ?></div>
									<div class="mt-2 text-muted small"><?= $shipment['sender_address'] ?></div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="bg-blue-lt p-3 rounded-3 border border-primary h-100">
									<div class="text-primary small fw-bold mb-2">PENERIMA</div>
									<div class="fw-bold"><?= $shipment['receiver_name'] ?></div>
									<div class="small"><?= $shipment['receiver_phone'] ?></div>
									<div class="mt-2 text-muted small"><?= $shipment['receiver_address'] ?></div>
								</div>
							</div>
						</div>

						<!-- Deskripsi Barang -->
						<div class="mb-3">
							<label class="form-label text-muted small fw-bold text-uppercase">Deskripsi Barang</label>
							<div class="p-3 bg-light rounded border fst-italic">
								"<?= $shipment['commodity_detail'] ?>"
								<span class="text-muted small ms-1">(<?= $shipment['commodity_name'] ?>)</span>
							</div>
						</div>

						<!-- Valuable Goods -->
						<?php if ($shipment['is_valuable']): ?>
							<div class="alert alert-danger d-flex align-items-center gap-2 py-2">
								<i class="bi bi-shield-check fs-5"></i>
								<div>
									<strong>Valuable Goods</strong> — Estimasi nilai barang:
									<strong>Rp <?= number_format($shipment['goods_value']) ?></strong>
								</div>
							</div>
						<?php endif; ?>

						<!-- Info Operasional (Kribo only) -->
						<?php if ($is_kribo && ($shipment['smu_number'] || $shipment['flight_number'] || $shipment['departure_date'])): ?>
							<div class="mt-3 p-3 border rounded bg-yellow-lt">
								<div class="text-muted small fw-bold mb-2 text-uppercase">
									<i class="bi bi-airplane me-1"></i> Info Operasional
								</div>
								<div class="row g-2">
									<?php if ($shipment['smu_number']): ?>
										<div class="col-md-4">
											<div class="small text-muted">No. SMU</div>
											<div class="fw-bold"><?= $shipment['smu_number'] ?></div>
										</div>
									<?php endif; ?>
									<?php if ($shipment['flight_number']): ?>
										<div class="col-md-4">
											<div class="small text-muted">No. Flight</div>
											<div class="fw-bold"><?= $shipment['flight_number'] ?></div>
										</div>
									<?php endif; ?>
									<?php if ($shipment['departure_date']): ?>
										<div class="col-md-4">
											<div class="small text-muted">Tgl Keberangkatan</div>
											<div class="fw-bold"><?= date('d/m/Y H:i', strtotime($shipment['departure_date'])) ?></div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

					</div>
				</div>

				<!-- Card: Berat & Biaya -->
				<div class="card mt-4">
					<div class="card-header">
						<h3 class="card-title">Berat & Biaya</h3>
					</div>
					<div class="card-body">

						<!-- Breakdown Berat -->
						<div class="row text-center mb-4">
							<div class="col-4">
								<div class="small text-muted text-uppercase">Berat Aktual</div>
								<div class="h3 mb-0"><?= (float)$shipment['actual_weight'] ?> <small class="text-muted fs-6">kg</small></div>
							</div>
							<div class="col-4">
								<div class="small text-muted text-uppercase">Berat Volume</div>
								<div class="h3 mb-0"><?= (float)$shipment['volume_weight'] ?> <small class="text-muted fs-6">kg</small></div>
							</div>
							<div class="col-4">
								<div class="small text-primary fw-bold text-uppercase">Chargeable</div>
								<div class="h2 mb-0 text-primary"><?= (float)$shipment['chargeable_weight'] ?> <small class="fs-6">kg</small></div>
							</div>
						</div>

						<!-- Tabel Dimensi -->
						<?php if (!empty($dimensions)): ?>
							<div class="table-responsive mb-4">
								<table class="table table-sm table-vcenter">
									<thead>
										<tr>
											<th>Koli</th>
											<th>Dimensi (P × L × T)</th>
											<th class="text-end">Volume (kg)</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($dimensions as $dim): ?>
											<tr>
												<td><?= $dim['qty'] ?> Unit</td>
												<td><?= (float)$dim['length'] ?> × <?= (float)$dim['width'] ?> × <?= (float)$dim['height'] ?> cm</td>
												<td class="text-end"><?= number_format((($dim['length'] * $dim['width'] * $dim['height']) / 5000) * $dim['qty'], 2) ?> kg</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>

						<!-- Breakdown Biaya -->
						<div class="border rounded overflow-hidden">
							<table class="table table-sm mb-0">
								<tbody>
									<tr>
										<td class="text-muted">Koli</td>
										<td class="text-end fw-bold"><?= $shipment['koli'] ?> Pcs</td>
									</tr>
									<tr>
										<td class="text-muted">Metode Pembayaran</td>
										<td class="text-end">
											<span class="badge <?= $shipment['payment_type'] === 'CASH' ? 'bg-green' : 'bg-blue' ?>-lt">
												<?= $shipment['payment_type'] ?>
											</span>
										</td>
									</tr>

									<?php if ($is_kribo): ?>
										<tr>
											<td class="text-muted">Harga Beli / kg</td>
											<td class="text-end">Rp <?= number_format($shipment['cost_price']) ?></td>
										</tr>
										<tr>
											<td class="text-muted">Harga Jual / kg</td>
											<td class="text-end">Rp <?= number_format($shipment['sell_price']) ?></td>
										</tr>
									<?php endif; ?>

									<tr>
										<td class="text-muted">Biaya Ongkir</td>
										<td class="text-end">Rp <?= number_format($shipment['chargeable_weight'] * $shipment['sell_price']) ?></td>
									</tr>

									<?php if ($shipment['pickup_fee'] > 0): ?>
										<tr>
											<td class="text-muted">Biaya Pickup</td>
											<td class="text-end">Rp <?= number_format($shipment['pickup_fee']) ?></td>
										</tr>
									<?php endif; ?>

									<?php if ($is_kribo): ?>
										<tr class="table-warning">
											<td class="fw-bold">Margin</td>
											<td class="text-end fw-bold text-success">Rp <?= number_format($shipment['margin_amount']) ?></td>
										</tr>
									<?php endif; ?>

									<tr class="table-primary">
										<td class="fw-bold h5 mb-0">Total Biaya</td>
										<td class="text-end fw-bold h4 mb-0 text-primary">Rp <?= number_format($shipment['total_amount']) ?></td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>
				</div>

			</div>

			<!-- ── KOLOM KANAN ── -->
			<div class="col-md-4">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Tracking Log</h3>
					</div>
					<div class="card-body p-0">
						<ul class="list-group list-group-flush" style="max-height:500px;overflow-y:auto;">
							<?php foreach ($history as $key => $h): ?>
								<li class="list-group-item">
									<div class="row">
										<div class="col-auto">
											<span class="status-dot <?= ($key === 0) ? 'status-dot-animated status-green' : '' ?> d-block mt-1"></span>
										</div>
										<div class="col">
											<div class="text-muted small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></div>
											<div class="fw-bold"><?= shipment_status_badge($h['status']) ?></div>
											<div class="small text-muted mt-1"><?= $h['note'] ?></div>
											<!-- <?php if ($h['location']): ?>
												<div class="mt-1 small"><i class="bi bi-geo-alt me-1"></i><?= $h['location'] ?></div>
											<?php endif; ?> -->
										</div>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>

				<?php if (!empty($shipment['shipment_photo'])): ?>
					<div class="card mt-3">
						<div class="card-header">
							<h3 class="card-title">
								<?= tabler_icon('photo') ?> Foto Barang
							</h3>
						</div>
						<div class="card-body p-2">
							<a href="<?= base_url($shipment['shipment_photo']) ?>" target="_blank" title="Klik untuk lihat full size">
								<img src="<?= base_url($shipment['shipment_photo']) ?>"
									class="img-fluid rounded"
									style="width:100%; object-fit:cover; max-height:250px; cursor:zoom-in;"
									alt="Foto Barang <?= $shipment['no_resi'] ?>">
							</a>
							<div class="text-muted small mt-2 text-center">
								<i class="bi bi-zoom-in me-1"></i> Klik foto untuk lihat ukuran penuh
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</div>

<script>
	$(document).on('click', '.btn-confirm-paid', function() {
		const id = $(this).data('id');
		const resi = $(this).data('resi'); // ✅ fix dari data-awb → data-resi

		Swal.fire({
			title: 'Konfirmasi Pembayaran',
			text: `Sudah menerima pembayaran untuk resi ${resi}?`,
			icon: 'info',
			showCancelButton: true,
			confirmButtonText: 'Ya, Sudah Lunas!',
			cancelButtonText: 'Belum',
			confirmButtonColor: '#2fb344'
		}).then(result => {
			if (result.isConfirmed) {
				$.post("<?= site_url('shipment/ajax_confirm_paid') ?>", {
					id
				}, function(res) {
					if (res.status) Swal.fire('Sip!', 'Status berubah jadi Siap Pickup.', 'success').then(() => location.reload());
				}, 'json');
			}
		});
	});
</script>
