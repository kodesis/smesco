<?php defined('BASEPATH') or exit('No direct script access allowed');

$sess = $this->session->userdata('user');

$no_print_statuses = [
	'BOOKED',
	'CANCELLED'
];

$can_print = !in_array(
	$shipment['status'],
	$no_print_statuses
);
?>
<?php $this->load->view('app/pages/agent_kecamatan/_mobile_css'); ?>

<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					Detail Shipment Kecamatan
				</h2>
				<div class="text-muted mt-1">
					Status saat ini:
					<?= shipment_status_badge($shipment['status']) ?>
				</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a href="<?= site_url('agentkecamatan') ?>" class="btn btn-outline-secondary btn-back">
						<?= tabler_icon('arrow-left', 'me-md-1') ?> <span class="d-none d-md-inline">Kembali</span>
					</a>

					<?php if ($can_print): ?>
						<a
							href="<?= site_url('agentkecamatan/print_label/' . (int) $shipment['id']) ?>"
							target="_blank"
							class="btn btn-primary"> <?= tabler_icon('printer', 'me-1') ?> Print Label </a>
					<?php endif; ?>

					<?php if ($shipment['status'] === 'READY_FOR_HANDOVER' && $shipment['status_tagihan_smesco'] == 'PAID'): ?>
						<button
							type="button"
							class="btn btn-primary btn-handover-courier"
							data-id="<?= (int) $shipment['id'] ?>"
							data-resi="<?= html_escape($shipment['no_resi']) ?>">
							<?= tabler_icon('truck-delivery', 'me-1') ?>
							Serahkan ke Courier
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards"><!-- =====================================================
                 KOLOM KIRI
                 ===================================================== -->
			<div class="col-md-8"> <!-- INFORMASI PENGIRIMAN -->
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							Informasi Pengiriman
						</h3>
						<div class="card-options"> <span class="text-muted small"> Dibuat:
								<?= date(
									'd/m/Y H:i',
									strtotime($shipment['created_at'])
								) ?> </span> </div>
					</div>
					<div class="card-body">
						<!-- RESI / KATEGORI / LAYANAN -->
						<div class="row mb-3">
							<div class="col-md-4"> <label class="form-label text-muted small text-uppercase">
									Nomor Resi
								</label>
								<div class="h3 text-primary fw-bold"> <?= html_escape($shipment['no_resi']) ?> </div>
							</div>
							<div class="col-md-4 text-md-center"> <label class="form-label text-muted small text-uppercase">
									Kategori
								</label>
								<div> <span class="badge bg-purple-lt"> <?= html_escape($shipment['category']) ?> </span> </div>
							</div>
							<div class="col-md-4 text-md-end"> <label class="form-label text-muted small text-uppercase">
									Layanan
								</label>
								<div class="h4"> <?= html_escape($shipment['service_name']) ?> </div>
							</div>
						</div>
						<!-- RUTE -->
						<div class="p-2 border-top border-bottom bg-light-lt mb-3">
							<div class="row align-items-center text-center">
								<div class="col">
									<div class="small text-muted">
										ORIGIN
									</div>
									<div class="h4 mb-0 text-primary"> <?= html_escape($shipment['origin']) ?> </div>
								</div>
								<div class="col-auto text-muted"> <i class="bi bi-chevron-double-right"></i> </div>
								<div class="col">
									<div class="small text-muted">
										DESTINATION
									</div>
									<div class="h4 mb-0 text-primary"> <?= html_escape($shipment['destination']) ?> </div>
								</div>
							</div>
						</div>
						<!-- ASAL AGEN -->
						<?php if (!empty($shipment['agent_origin'])): ?>
							<div class="alert alert-info d-flex align-items-center gap-2 py-2">
								<?= tabler_icon('map-pin') ?>
								<div>
									<strong>Asal Agen Kecamatan:</strong>
									<?= html_escape($shipment['agent_origin']) ?>
									<div class="small text-muted"> Barang diterima melalui Agen Kecamatan sebelum diteruskan ke Jakarta. </div>
								</div>
							</div>
						<?php endif; ?>
						<!-- PENGIRIM & PENERIMA -->
						<div class="row g-3 mb-3">
							<div class="col-md-6">
								<div class="bg-light p-3 rounded-3 border h-100">
									<div class="text-muted small fw-bold mb-2"> PENGIRIM </div>
									<div class="fw-bold"> <?= html_escape($shipment['sender_name']) ?> </div>
									<div class="small"> <?= html_escape($shipment['sender_phone']) ?> </div>
									<div class="mt-2 text-muted small"> <?= html_escape($shipment['sender_address']) ?> </div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="bg-blue-lt p-3 rounded-3 border border-primary h-100">
									<div class="text-primary small fw-bold mb-2"> PENERIMA </div>
									<div class="fw-bold"> <?= html_escape($shipment['receiver_name']) ?> </div>
									<div class="small"> <?= html_escape($shipment['receiver_phone']) ?> </div>
									<div class="mt-2 text-muted small"> <?= html_escape($shipment['receiver_address']) ?> </div>
								</div>
							</div>
						</div>
						<!-- DESKRIPSI BARANG -->
						<div class="mb-3">
							<label class="form-label text-muted small fw-bold text-uppercase"> Deskripsi Barang </label>
							<div class="p-3 bg-light rounded border fst-italic">
								"<?= html_escape($shipment['commodity_detail']) ?>"
								<?php if (!empty($shipment['commodity_name'])): ?>
									<span class="text-muted small ms-1"> (<?= html_escape($shipment['commodity_name']) ?>) </span>
								<?php endif; ?>
							</div>
						</div>
						<!-- VALUABLE GOODS -->
						<?php if ($shipment['is_valuable']): ?>
							<div class="alert alert-danger d-flex align-items-center gap-2 py-2">
								<i class="bi bi-shield-check fs-5"></i>
								<div>
									<strong>Valuable Goods</strong> — Estimasi nilai barang: <strong> Rp <?= number_format($shipment['goods_value']) ?> </strong>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div> <!-- =================================================
                     BERAT & BIAYA
                     ================================================= -->
				<div class="card mt-4">
					<div class="card-header">
						<h3 class="card-title"> Berat & Biaya </h3>
					</div>
					<div class="card-body">
						<div class="row text-center mb-4">
							<div class="col-4">
								<div class="small text-muted text-uppercase"> Berat Aktual </div>
								<div class="h3 mb-0"> <?= (float) $shipment['actual_weight'] ?>
									<small class="text-muted fs-6">
										kg
									</small>
								</div>
							</div>
							<div class="col-4">
								<div class="small text-muted text-uppercase"> Berat Volume </div>
								<div class="h3 mb-0"> <?= (float) $shipment['volume_weight'] ?>
									<small class="text-muted fs-6">
										kg
									</small>
								</div>
							</div>
							<div class="col-4">
								<div class="small text-primary fw-bold text-uppercase"> Chargeable </div>
								<div class="h2 mb-0 text-primary"> <?= (float) $shipment['chargeable_weight'] ?>
									<small class="fs-6">
										kg
									</small>
								</div>
							</div>
						</div>
						<!-- DIMENSI --> <?php if (!empty($dimensions)): ?> <div class="table-responsive mb-4">
								<table class="table table-sm table-vcenter">
									<thead>
										<tr>
											<th>Koli</th>
											<th>Dimensi (P × L × T)</th>
											<th class="text-end">
												Volume
											</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($dimensions as $dim): ?>
											<tr>
												<td>
													<?= (int) $dim['qty'] ?>
													Unit
												</td>
												<td>
													<?= (float) $dim['length'] ?> × <?= (float) $dim['width'] ?> × <?= (float) $dim['height'] ?> cm
												</td>
												<td class="text-end">
													<?= number_format(($dim['length'] * $dim['width'] * $dim['height']) / 5000 * $dim['qty'], 2) ?> kg
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
						<!-- BIAYA -->
						<div class="border rounded overflow-hidden">
							<table class="table table-sm mb-0">
								<tbody>
									<tr>
										<td class="text-muted">
											Koli
										</td>
										<td class="text-end fw-bold"> <?= (int) $shipment['koli'] ?> Pcs </td>
									</tr>
									<tr>
										<td class="text-muted">
											Metode Pembayaran
										</td>
										<td class="text-end"> <span class="badge <?= $shipment['payment_type'] === 'CASH'
																									? 'bg-green'
																									: 'bg-blue' ?>-lt"> <?= html_escape($shipment['payment_type']) ?> </span> </td>
									</tr>
									<tr>
										<td class="text-muted">
											Harga / kg
										</td>
										<td class="text-end"> Rp <?= number_format(
																				$shipment['sell_price']
																			) ?> </td>
									</tr>
									<tr>
										<td class="text-muted">
											Biaya Ongkir
										</td>
										<td class="text-end">
											Rp <?= number_format(
													$shipment['chargeable_weight'] *
														$shipment['sell_price']
												) ?> </td>
									</tr>
									<?php if ($shipment['total_addon_fee'] > 0): ?>
										<tr>
											<td class="text-muted">
												Biaya Add-on
											</td>
											<td class="text-end"> Rp <?= number_format(
																					$shipment['total_addon_fee']
																				) ?> </td>
										</tr> <?php endif; ?>
									<?php if ($shipment['tess'] > 0): ?>
										<tr>
											<td class="text-muted">
												TESS
											</td>
											<td class="text-end">
												Rp <?= number_format($shipment['tess']) ?>
											</td>
										</tr> <?php endif; ?>

									<tr class="table-primary">
										<td class="fw-bold h5 mb-0">
											Total Diterima dari Pelanggan
										</td>
										<td class="text-end fw-bold h4 mb-0 text-primary">
											Rp <?= number_format($shipment['total_amount']) ?>
										</td>
									</tr>

									<tr>
										<td class="text-muted">
											Setoran Ke Mitra / Pusat
										</td>
										<td class="text-end text-red">
											Rp <?= number_format($shipment['nominal_tagihan_smesco']) ?>
										</td>
									</tr>

									<tr class="table-success">
										<td class="fw-bold">Margin</td>
										<td class="text-end fw-bold text-success">Rp <?= number_format($shipment['margin_kecamatan']) ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div><!-- =====================================================
                 KOLOM KANAN
                 ===================================================== -->
			<div class="col-md-4"> <!-- PAYMENT STATUS -->
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"> Status Pembayaran </h3>
					</div>
					<div class="card-body text-center">
						<?php if ($shipment['payment_status'] === 'PAID'): ?> <div class="text-success"> <?= tabler_icon('circle-check', 'icon-lg') ?> </div>
							<div class="fw-bold text-success mt-2"> SUDAH LUNAS </div> <?php if (!empty($shipment['payment_confirmed_at'])): ?>
								<div class="small text-muted mt-1">
									<?= date('d/m/Y H:i', strtotime($shipment['payment_confirmed_at'])) ?>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="text-warning"> <?= tabler_icon('clock', 'icon-lg') ?> </div>
							<div class="fw-bold text-warning mt-2"> MENUNGGU PEMBAYARAN </div>
							<div class="small text-muted mt-1"> Pembayaran belum dikonfirmasi. </div>
						<?php endif; ?>

						<?php if (
							$shipment['status'] === 'BOOKED' &&
							$shipment['payment_status'] !== 'PAID'
						): ?>
							<button
								type="button"
								class="btn btn-success btn-confirm-paid mt-3"
								data-id="<?= (int) $shipment['id'] ?>"
								data-resi="<?= html_escape($shipment['no_resi']) ?>">
								<?= tabler_icon('cash', 'me-1') ?>
								Konfirmasi Pembayaran
							</button>
						<?php endif; ?>
					</div>
				</div>
				<?php $tg = $shipment['status_tagihan_smesco'] ?? NULL; ?>
				<div class="card mt-3">
					<div class="card-header">
						<h3 class="card-title">Tagihan ke Smesco</h3>
					</div>
					<div class="card-body text-center">
						<?php if ($tg === 'PAID'): ?>
							<div class="text-success"><?= tabler_icon('circle-check', 'icon-lg') ?></div>
							<div class="fw-bold text-success mt-2">SUDAH LUNAS</div>
							<?php if (!empty($shipment['tagihan_smesco_paid_at'])): ?>
								<div class="small text-muted mt-1"><?= date('d/m/Y H:i', strtotime($shipment['tagihan_smesco_paid_at'])) ?></div>
							<?php endif; ?>
						<?php elseif ($tg === 'PENDING'): ?>
							<div class="text-info"><?= tabler_icon('clock', 'icon-lg') ?></div>
							<div class="fw-bold text-info mt-2">MENUNGGU VERIFIKASI</div>
							<div class="small text-muted mt-1">Setoran sudah dikirim, menunggu finance.</div>
						<?php elseif ($tg === 'UNPAID' && ($shipment['payment_status'] ?? '') !== 'PAID'): ?>
							<div class="text-muted"><?= tabler_icon('clock', 'icon-lg') ?></div>
							<div class="fw-bold mt-2">MENUNGGU PEMBAYARAN CUSTOMER</div>
							<div class="small text-muted mt-1">Belum bisa disetor.</div>
						<?php elseif ($tg === 'UNPAID'): ?>
							<div class="text-warning"><?= tabler_icon('clock', 'icon-lg') ?></div>
							<div class="fw-bold text-warning mt-2">BELUM SETOR</div>
						<?php else: ?>
							<div class="text-muted">Tidak ada tagihan.</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- TRACKING -->
				<div class="card mt-3">
					<div class="card-header">
						<h3 class="card-title"> Tracking Log </h3>
					</div>
					<div class="card-body p-0">
						<ul class="list-group list-group-flush tracking-list" style="max-height:500px;overflow-y:auto;">
							<?php foreach ($history as $key => $h): ?>
								<li class="list-group-item">
									<div class="row">
										<div class="col-auto"> <span
												class="status-dot <?= ($key === 0)
																			? 'status-dot-animated status-green'
																			: '' ?> d-block mt-1"></span> </div>
										<div class="col">
											<div class="text-muted small"> <?= date(
																							'd/m/Y H:i',
																							strtotime($h['created_at'])
																						) ?> </div>
											<div class="fw-bold"> <?= shipment_status_badge(
																				$h['status']
																			) ?> </div>
											<div class="small text-muted mt-1"> <?= html_escape($h['note']) ?> </div>
											<?php if (!empty($h['location'])): ?> <div class="mt-1 small text-muted"> <?= tabler_icon(
																																					'map-pin',
																																					'icon-sm me-1 text-secondary'
																																				) ?> <strong>
														<?= html_escape($h['location']) ?>
													</strong> </div> <?php endif; ?>
											<?php if (!empty($h['photo_proof'])): ?> <?php $folder_path =
																										'uploads/shipments/booking/';
																									if (
																										$h['status'] === 'DELIVERED' ||
																										$h['status'] === 'RECEIVED_DESTINATION'
																									) {
																										$folder_path =
																											'uploads/shipments/pod/';
																									}
																									$full_image_url =
																										base_url(
																											$folder_path .
																												$h['photo_proof']
																										);							?> <div class="mt-2">
													<div
														class="text-muted small mb-1"
														style="font-size:11px;">
														<?= tabler_icon('paperclip', 'icon-sm') ?> Foto Bukti </div>
													<a
														href="javascript:void(0);"
														class="btn-view-pod"
														data-img="<?= $full_image_url ?>"> <img
															src="<?= $full_image_url ?>"
															alt="Proof Image"
															class="img-thumbnail rounded shadow-sm"
															style="max-height:80px;width:auto;object-fit:cover;cursor:pointer;"> </a>
												</div> <?php endif; ?>
										</div>
									</div>
								</li> <?php endforeach; ?>
						</ul>
					</div>
				</div>
				<!-- FOTO BARANG -->
				<?php if (!empty($shipment['shipment_photo'])): ?>
					<div class="card mt-3">
						<div class="card-header">
							<h3 class="card-title"> <?= tabler_icon('photo') ?> Foto Barang </h3>
						</div>
						<div class="card-body p-2">
							<a
								href="<?= base_url($shipment['shipment_photo']) ?>"
								target="_blank"> <img
									src="<?= base_url($shipment['shipment_photo']) ?>"
									class="img-fluid rounded"
									style="width:100%;object-fit:cover;max-height:250px;cursor:zoom-in;"
									alt="Foto Barang <?= html_escape($shipment['no_resi']) ?>"> </a>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>


<!-- ============================================================
     MODAL FOTO
============================================================ -->
<div
	class="modal fade"
	id="modal-preview-pod"
	tabindex="-1"
	role="dialog"
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div
			class="modal-content"
			style="background:transparent;border:none;">
			<div class="modal-body p-0 text-center position-relative">
				<button
					type="button"
					class="btn-close btn-close-white position-absolute top-0 end-0 m-2"
					data-bs-dismiss="modal"
					style="z-index:999;">
				</button>
				<img
					id="pod-large-preview"
					src=""
					class="img-fluid rounded shadow-lg"
					style="max-height:85vh;width:auto;">
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click', '.btn-confirm-paid', function() {
		const id = $(this).data('id');
		const resi = $(this).data('resi');

		Swal.fire({
			title: 'Konfirmasi Pembayaran',
			text: `Sudah menerima pembayaran untuk resi ${resi}?`,
			icon: 'info',
			showCancelButton: true,
			confirmButtonText: 'Ya, Sudah Lunas!',
			cancelButtonText: 'Belum',
			confirmButtonColor: '#2fb344'
		}).then(result => {

			if (!result.isConfirmed) {
				return;
			}

			$.post(
				"<?= site_url('agentkecamatan/confirm_payment') ?>", {
					id: id
				},
				function(res) {

					if (res.status) {
						Swal.fire(
							'Sip!',
							'Pembayaran berhasil dikonfirmasi.',
							'success'
						).then(() => {
							location.reload();
						});

					} else {
						Swal.fire(
							'Gagal',
							res.message || 'Gagal mengkonfirmasi pembayaran.',
							'error'
						);
					}

				},
				'json'
			).fail(function(xhr) {

				let message = 'Terjadi kesalahan saat mengkonfirmasi pembayaran.';

				if (xhr.responseJSON && xhr.responseJSON.message) {
					message = xhr.responseJSON.message;
				}

				Swal.fire(
					'Gagal',
					message,
					'error'
				);
			});
		});
	});
	$(document).on('click', '.btn-view-pod', function() {
		const imageUrl = $(this).data('img');
		$('#pod-large-preview')
			.attr('src', imageUrl);
		$('#modal-preview-pod').modal('show');
	});

	$(document).on('click', '.btn-handover-courier', function() {
		openHandover([$(this).data('id')]);
	});
</script>

<?php $this->load->view('app/pages/agent_kecamatan/_handover_modal'); ?>
