<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					<?= tabler_icon('package', 'me-2') ?> Data Shipment
				</h2>
				<div class="text-muted mt-1">Kelola semua data pengiriman dan resi.</div>
			</div>

			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">

					<a href="<?= site_url('shipment/preview_manifest') ?>" class="btn btn-warning">
						<?= tabler_icon('file-spreadsheet', 'me-1') ?>
						Buat Manifest
					</a>

					<a href="<?= site_url('shipment/create') ?>" class="btn btn-primary">
						<?= tabler_icon('plus', 'me-1') ?>
						Buat Shipment Baru
					</a>

				</div>
			</div>

		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<!-- SUMMARY CARDS (data dari $stats) -->
		<div class="row row-cards mb-4">
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
		</div>

		<!-- FILTER -->
		<div class="card mb-4">
			<div class="card-body py-2">
				<form action="<?= site_url('shipment') ?>" method="GET">
					<div class="row g-2 align-items-end">
						<div class="col-12 col-md-3">
							<label class="form-label small mb-1">Kata Kunci</label>
							<input type="text" name="q" class="form-control form-control-sm" placeholder="Resi / nama..." value="<?= $filters['q'] ?>">
						</div>
						<div class="col-6 col-md-2">
							<label class="form-label small mb-1">Status</label>
							<select name="status" class="form-select form-select-sm">
								<option value="">Semua</option>
								<?php
								$statuses = [
									'BOOKED',
									'READY_TO_PICKUP',
									'PICKED_UP',
									'RECEIVED_ORIGIN',
									'OFFLOADED',
									'CONSOLIDATED',
									'MANIFESTED',
									'DEPARTED',
									'ARRIVED',
									'RECEIVED_DESTINATION',
									'DELIVERED',
									'CANCELLED'
								];
								foreach ($statuses as $st):
									$sel = ($filters['status'] == $st) ? 'selected' : '';
								?>
									<option value="<?= $st ?>" <?= $sel ?>><?= str_replace('_', ' ', $st) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-6 col-md-2">
							<label class="form-label small mb-1">Tgl Dari</label>
							<input type="date" name="start" class="form-control form-control-sm" value="<?= $filters['start'] ?>">
						</div>
						<div class="col-6 col-md-2">
							<label class="form-label small mb-1">Tgl Sampai</label>
							<input type="date" name="end" class="form-control form-control-sm" value="<?= $filters['end'] ?>">
						</div>
						<div class="col-6 col-md-3 d-flex gap-2">
							<button type="submit" class="btn btn-sm btn-primary">Cari</button>
							<a href="<?= site_url('shipment') ?>" class="btn btn-sm btn-link px-1">Reset</a>
							<button type="button" id="btn-bulk-manifest" class="btn btn-sm btn-dark d-none ms-auto">
								<?= tabler_icon('plane-departure', 'me-1') ?> Manifest (<span id="selected-count">0</span>)
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- INFO ROW -->
		<div class="d-flex justify-content-between align-items-center mb-2 px-1">
			<small class="text-muted">
				Menampilkan <strong><?= count($shipments) ?></strong> dari <strong><?= $total ?></strong> transaksi
			</small>
		</div>

		<!-- ===== DESKTOP TABLE (hidden on mobile) ===== -->
		<div class="card shadow-sm d-none d-md-block">
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-hover">
					<thead class="bg-light">
						<tr>
							<!-- <th style="width:20px"><input type="checkbox" id="check-all" class="form-check-input"></th> -->
							<th class="w-1">No</th>
							<th>No. Resi (AWB)</th>
							<th>Rute & Layanan</th>
							<th>Pengirim & Penerima</th>
							<th>Biaya & Qty</th>
							<th>Status</th>
							<th class="w-1">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($shipments)): $no = 1;
							foreach ($shipments as $s): ?>
								<tr>
									<!-- <td>
										<?php if ($s->status == 'RECEIVED_ORIGIN' && $role_slug == 'admin-kribo'): ?>
											<input type="checkbox" class="form-check-input shipment-check" value="<?= $s->id ?>">
										<?php endif; ?>
									</td> -->
									<td class="text-muted small"><?= $no++ ?></td>
									<td>
										<div class="d-flex align-items-center gap-1">
											<a href="<?= site_url('shipment/detail/' . $s->id) ?>"><span class="fw-bold text-primary"><?= $s->no_resi ?></span></a>
											<?php if ($s->is_valuable): ?>
												<span class="text-danger" title="Barang Berharga"><?= tabler_icon('shield-check', 'icon-sm') ?></span>
											<?php endif; ?>
										</div>
										<small class="text-muted"><?= $s->created_by_name ?> • <?= date('d/m/Y H:i', strtotime($s->created_at)) ?></small>
									</td>
									<td>
										<div class="d-flex align-items-center small mb-1">
											<span class="badge bg-blue-lt me-1"><?= $s->origin ?></span>
											<?= tabler_icon('arrow-right', 'text-muted mx-1') ?>
											<span class="badge bg-green-lt"><?= $s->destination ?></span>
										</div>
										<span class="badge bg-purple-lt small text-uppercase"><?= $s->service_code ?></span>
									</td>
									<td>
										<div class="small mb-1"><span class="text-muted">Dari:</span> <strong><?= htmlspecialchars($s->sender_name) ?></strong></div>
										<div class="small"><span class="text-muted">Ke:</span> <strong><?= htmlspecialchars($s->receiver_name) ?></strong></div>
									</td>
									<td>
										<div class="fw-bold text-success small">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></div>
										<div class="small text-muted"><?= $s->koli ?> Koli | <?= floatval($s->chargeable_weight) ?> Kg</div>
									</td>
									<td><?= shipment_status_badge($s->status) ?></td>
									<td>
										<div class="btn-list flex-nowrap">
											<?php if ($s->status == 'BOOKED'): ?>
												<?php if ($s->payment_type !== 'TRANSFER'): ?>

													<button type="button" class="btn btn-sm btn-success btn-confirm-paid"
														data-id="<?= $s->id ?>" data-resi="<?= $s->no_resi ?>">
														<?= tabler_icon('cash', 'me-1') ?> Lunas
													</button>

												<?php else: ?>

													<span class="btn btn-sm btn-warning disabled pe-none" style="opacity: 1;" title="Menunggu Konfirmasi Finance Pusat">
														<?= tabler_icon('clock-hour-4', 'me-1') ?> Pending
													</span>

												<?php endif; ?>
											<?php endif; ?>
											<a href="<?= site_url('shipment/detail/' . $s->id) ?>" class="btn btn-sm btn-icon btn-outline-primary" title="Detail"><?= tabler_icon('eye') ?></a>
											<?php if ($s->category === 'INTERNATIONAL'): ?>
												<button type="button" class="btn btn-sm btn-icon btn-outline-azure btn-set-vendor"
													data-id="<?= $s->id ?>"
													data-resi="<?= $s->no_resi ?>"
													data-vendor="<?= $s->vendor ?>"
													data-connote="<?= $s->vendor_connote ?>"
													title="Set Vendor Connote">
													<?= tabler_icon('world-upload') ?>
												</button>
											<?php endif; ?>
											<?php if ($s->status == 'RECEIVED_DESTINATION'): ?>
												<button type="button" class="btn btn-sm btn-success btn-trigger-delivery"
													data-id="<?= $s->id ?>"
													data-resi="<?= $s->no_resi ?>">
													<?= tabler_icon('truck-delivery', 'me-1') ?> Kirim
												</button>
											<?php endif; ?>
											<?php if ($s->status !== 'BOOKED' && $s->status !== 'CANCELLED'): ?>
												<a href="<?= site_url('shipment/print_label/' . $s->no_resi) ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" title="Cetak Label"><?= tabler_icon('printer') ?></a>
											<?php endif; ?>
											<!-- <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-cancel-shipment"
												data-id="<?= $s->id ?>" data-resi="<?= $s->no_resi ?>" title="Batalkan"><?= tabler_icon('trash') ?></button> -->
											<?php if ($role_slug !== 'staff-mitra'): ?>
												<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-cancel-shipment"
													data-id="<?= $s->id ?>" data-resi="<?= $s->no_resi ?>" title="Batalkan">
													<?= tabler_icon('trash') ?>
												</button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="8" class="text-center py-5">
									<div class="empty">
										<div class="empty-icon text-muted"><?= tabler_icon('mood-empty') ?></div>
										<p class="empty-title">Data Kosong</p>
										<p class="empty-subtitle text-muted">Tidak ada transaksi yang sesuai filter.</p>
									</div>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<?php $this->load->view('app/layouts/_pagination', compact('total', 'page', 'per_page', 'offset', 'total_pages', 'base_url')); ?>
		</div>

		<!-- ===== MOBILE CARD LIST (hidden on desktop) ===== -->
		<div class="d-md-none">
			<?php if (!empty($shipments)): foreach ($shipments as $s): ?>
					<?php
					$bg = 'bg-secondary';
					if ($s->status == 'BOOKED')                $bg = 'bg-cyan';
					if ($s->status == 'READY_TO_PICKUP')       $bg = 'bg-yellow text-dark';
					if ($s->status == 'PICKED_UP')             $bg = 'bg-teal';
					if ($s->status == 'RECEIVED_ORIGIN')       $bg = 'bg-indigo';
					if ($s->status == 'MANIFESTED')            $bg = 'bg-blue';
					if ($s->status == 'DEPARTED')              $bg = 'bg-orange';
					if ($s->status == 'ARRIVED')               $bg = 'bg-purple';
					if ($s->status == 'RECEIVED_DESTINATION')  $bg = 'bg-cyan';
					if ($s->status == 'DELIVERED')             $bg = 'bg-success';
					if ($s->status == 'CANCELLED')             $bg = 'bg-danger';
					?>
					<div class="card mb-2">
						<div class="card-body p-3">
							<!-- Header: Resi + Status -->
							<div class="d-flex justify-content-between align-items-start mb-2">
								<div>
									<div class="fw-bold text-primary"><?= $s->no_resi ?><?php if ($s->is_valuable): ?> <span class="text-danger"><?= tabler_icon('shield-check', 'icon-sm') ?></span><?php endif; ?></div>
									<small class="text-muted"><?= $s->created_by_name ?> • <?= date('d/m/Y H:i', strtotime($s->created_at)) ?></small>
								</div>
								<span class="badge <?= $bg ?> fw-bold"><?= str_replace('_', ' ', $s->status) ?></span>
							</div>
							<!-- Info Grid -->
							<div class="row g-2 small mb-2">
								<div class="col-6">
									<div class="text-muted">Rute</div>
									<div class="fw-semibold"><?= $s->origin ?> → <?= $s->destination ?> <span class="badge bg-purple-lt ms-1"><?= $s->service_code ?></span></div>
								</div>
								<div class="col-6">
									<div class="text-muted">Biaya</div>
									<div class="fw-bold text-success">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></div>
									<div class="text-muted"><?= $s->koli ?> Koli | <?= floatval($s->chargeable_weight) ?> Kg</div>
								</div>
								<div class="col-6">
									<div class="text-muted">Pengirim</div>
									<div class="fw-semibold text-truncate"><?= htmlspecialchars($s->sender_name) ?></div>
								</div>
								<div class="col-6">
									<div class="text-muted">Penerima</div>
									<div class="fw-semibold text-truncate"><?= htmlspecialchars($s->receiver_name) ?></div>
								</div>
							</div>
							<!-- Actions -->
							<div class="d-flex gap-2 flex-wrap">
								<?php if ($s->status == 'BOOKED'): ?>
									<?php if ($s->payment_type !== 'TRANSFER'): ?>

										<button type="button" class="btn btn-sm btn-success btn-confirm-paid"
											data-id="<?= $s->id ?>" data-resi="<?= $s->no_resi ?>">
											<?= tabler_icon('cash', 'me-1') ?> Lunas
										</button>

									<?php else: ?>

										<span class="btn btn-sm btn-warning disabled pe-none" style="opacity: 1;" title="Menunggu Konfirmasi Finance Pusat">
											<?= tabler_icon('clock-hour-4', 'me-1') ?> Pending
										</span>

									<?php endif; ?>
								<?php endif; ?>
								<a href="<?= site_url('shipment/detail/' . $s->id) ?>" class="btn btn-sm btn-outline-primary"><?= tabler_icon('eye', 'me-1') ?> Detail</a>
								<?php if ($s->category === 'INTERNATIONAL'): ?>
									<button type="button" class="btn btn-sm btn-outline-azure btn-set-vendor"
										data-id="<?= $s->id ?>"
										data-resi="<?= $s->no_resi ?>"
										data-vendor="<?= $s->vendor ?>"
										data-connote="<?= $s->vendor_connote ?>"
										title="Set Vendor Connote">
										<?= tabler_icon('world-upload', 'me-1') ?> Vendor
									</button>
								<?php endif; ?>
								<?php if ($s->status == 'RECEIVED_DESTINATION'): ?>
									<button type="button" class="btn btn-sm btn-success btn-trigger-delivery"
										data-id="<?= $s->id ?>"
										data-resi="<?= $s->no_resi ?>">
										<?= tabler_icon('truck-delivery', 'me-1') ?> Kirim
									</button>
								<?php endif; ?>
								<a href="<?= site_url('shipment/print_label/' . $s->no_resi) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><?= tabler_icon('printer') ?></a>
								<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-void-shipment"
									data-id="<?= $s->id ?>"
									data-resi="<?= $s->no_resi ?>"
									title="Void Transaksi">
									<?= tabler_icon('ban') ?> </button>
							</div>
						</div>
					</div>
				<?php endforeach;
			else: ?>
				<div class="card">
					<div class="card-body text-center py-5 text-muted">
						<?= tabler_icon('mood-empty', 'display-6') ?><p class="mt-2 mb-0">Tidak ada data transaksi.</p>
					</div>
				</div>
			<?php endif; ?>

			<!-- Pagination mobile -->
			<?php $this->load->view('app/layouts/_pagination', compact('total', 'page', 'per_page', 'offset', 'total_pages', 'base_url')); ?>
		</div>

	</div>
</div>

<!-- MODAL UPLOAD BUKTI DELIVERED -->
<div class="modal fade" id="modal-delivery" statistical="false" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title fw-bold">Konfirmasi Pengiriman Selesai</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-delivery-submit" enctype="multipart/form-data">
				<div class="modal-body">
					<input type="hidden" name="shipment_id" id="delivery-shipment-id">

					<div class="mb-3">
						<label class="form-label small fw-bold mb-1">No. Resi</label>
						<input type="text" id="delivery-no-resi" class="form-control bg-light fw-bold text-primary" readonly tabindex="-1">
					</div>

					<div class="mb-3">
						<label class="form-label small fw-bold mb-1">Foto Bukti Pengiriman (POD) <span class="text-danger">*</span></label>
						<input type="file" name="pod_image" id="pod_image" class="form-control" accept="image/*" required>
						<small class="text-muted mt-1 d-block">*Ambil foto serah terima barang atau tanda terima fisik.</small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" id="btn-save-delivery" class="btn btn-sm btn-success">Konfirmasi & Delivered</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Manifest (sama seperti sebelumnya) -->
<div class="modal modal-blur fade" id="modal-manifest" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Lengkapi Data Penerbangan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-manifest-bulk">
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label required">Nomor SMU (Master AWB)</label>
						<input type="text" name="smu_number" class="form-control" placeholder="GA-882192" required oninput="this.value=this.value.toUpperCase()">
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label required">Nomor Flight</label>
							<input type="text" name="flight_number" class="form-control" placeholder="GA-142" required oninput="this.value=this.value.toUpperCase()">
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label required">Gudang Asal</label>
							<input type="text" name="origin_warehouse" class="form-control" placeholder="Warehouse CGK" required>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label required">Tanggal & Jam Berangkat</label>
						<input type="datetime-local" name="departure_date" class="form-control" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary">Simpan & Manifestkan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-set-vendor" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title fw-bold">Set Vendor Tracking</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="vendor-shipment-id">
				<div class="mb-2">
					<label class="form-label fw-bold mb-1">No. Resi</label>
					<input type="text" id="vendor-no-resi" class="form-control bg-light fw-bold text-primary" readonly>
				</div>
				<div class="mb-2">
					<label class="form-label fw-bold mb-1">Vendor <span class="text-danger">*</span></label>
					<select id="vendor-name" class="form-select">
						<option value="">-- Pilih Vendor --</option>
						<?php
						foreach ($vendors as $v) : ?>
							<option value="<?= $v->vendor_name ?>"><?= $v->vendor_name ?></option>
						<?php
						endforeach; ?>
					</select>
				</div>
				<div class="mb-2">
					<label class="form-label fw-bold mb-1">Connote / Resi Vendor <span class="text-danger">*</span></label>
					<input type="text" id="vendor-connote" class="form-control" placeholder="Masukkan nomor resi vendor">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="button" id="btn-save-vendor" class="btn btn-sm btn-primary">Simpan</button>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		// --- Checkbox bulk (desktop only) ---
		const checkAll = document.getElementById('check-all');
		const shipmentChecks = document.querySelectorAll('.shipment-check');
		const btnBulk = document.getElementById('btn-bulk-manifest');
		const selectedCount = document.getElementById('selected-count');

		function updateBulkButton() {
			const checked = document.querySelectorAll('.shipment-check:checked');
			selectedCount.innerText = checked.length;
			btnBulk.classList.toggle('d-none', checked.length === 0);
		}

		if (checkAll) {
			checkAll.addEventListener('change', function() {
				shipmentChecks.forEach(c => c.checked = this.checked);
				updateBulkButton();
			});
		}
		shipmentChecks.forEach(c => c.addEventListener('change', updateBulkButton));

		btnBulk?.addEventListener('click', () => $('#modal-manifest').modal('show'));

		// --- Submit Manifest ---
		document.getElementById('form-manifest-bulk')?.addEventListener('submit', function(e) {
			e.preventDefault();
			const selectedIds = Array.from(document.querySelectorAll('.shipment-check:checked')).map(cb => cb.value);
			const formData = new FormData(this);
			formData.append('ids', JSON.stringify(selectedIds));

			Swal.fire({
				title: 'Proses Manifest?',
				text: `${selectedIds.length} resi akan di-update ke MANIFESTED.`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, Proses!'
			}).then(result => {
				if (result.isConfirmed) {
					fetch("<?= site_url('shipment/ajax_bulk_manifest') ?>", {
							method: 'POST',
							body: formData
						})
						.then(r => r.json())
						.then(data => {
							if (data.status) Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
						});
				}
			});
		});

		// --- Konfirmasi Lunas (FIX: data-resi bukan data-awb) ---
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
	});

	$(document).ready(function() {
		// Ketika tombol 'Kirim' diklik, munculkan modal dan set data ID & Resi
		$(document).on('click', '.btn-trigger-delivery', function() {
			var id = $(this).data('id');
			var resi = $(this).data('resi');

			$('#delivery-shipment-id').val(id);
			$('#delivery-no-resi').val(resi);
			$('#pod_image').val(''); // Reset input file

			$('#modal-delivery').modal('show');
		});

		// Handle submit form upload via AJAX
		$('#form-delivery-submit').on('submit', function(e) {
			e.preventDefault();

			var formData = new FormData(this);

			// Mengubah state tombol menjadi loading
			$('#btn-save-delivery').attr('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Memproses...');

			$.ajax({
				url: "<?= site_url('shipment/update_to_delivered') ?>",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				dataType: "JSON",
				success: function(response) {
					if (response.status == 'success') {
						Swal.fire({
							title: "Berhasil!",
							text: response.message,
							icon: "success"
						}).then(function() {
							window.location.reload(); // Reload halaman untuk memperbarui status tabel
						});
					} else {
						Swal.fire({
							title: "Gagal!",
							text: response.message,
							icon: "error"
						});
						$('#btn-save-delivery').attr('disabled', false).text('Konfirmasi & Delivered');
					}
				},
				error: function() {
					Swal.fire({
						title: "Error!",
						text: "Terjadi kesalahan sistem pada server.",
						icon: "error"
					});
					$('#btn-save-delivery').attr('disabled', false).text('Konfirmasi & Delivered');
				}
			});
		});

		// Buka modal set vendor
		$(document).on('click', '.btn-set-vendor', function() {
			$('#vendor-shipment-id').val($(this).data('id'));
			$('#vendor-no-resi').val($(this).data('resi'));
			$('#vendor-name').val($(this).data('vendor') || '');
			$('#vendor-connote').val($(this).data('connote') || '');
			$('#modal-set-vendor').modal('show');
		});

		// Submit
		$('#btn-save-vendor').on('click', function() {
			const id = $('#vendor-shipment-id').val();
			const vendor = $('#vendor-name').val();
			const connote = $('#vendor-connote').val().trim();

			if (!vendor || !connote) {
				Swal.fire('Perhatian', 'Vendor dan connote wajib diisi.', 'warning');
				return;
			}

			$('#btn-save-vendor').attr('disabled', true).text('Menyimpan...');

			$.post("<?= site_url('shipment/ajax_set_vendor') ?>", {
				id,
				vendor,
				connote
			}, function(res) {
				if (res.status) {
					Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
				} else {
					Swal.fire('Gagal', res.message, 'error');
					$('#btn-save-vendor').attr('disabled', false).text('Simpan');
				}
			}, 'json');
		});
	});
</script>
