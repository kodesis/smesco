<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-3">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-uppercase ls-1">Flight & Manifest Monitoring</h2>
				<div class="text-muted small mt-1">Daftar SMU aktif yang menunggu keberangkatan atau konfirmasi landing.</div>
			</div>

			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a href="<?= site_url('shipment/create_awb') ?>" class="btn btn-primary">
						<?= tabler_icon('plus', 'me-1') ?>
						Buat AWB Baru
					</a>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<!-- FILTER BAR -->
		<div class="card mb-3">
			<div class="card-body py-2">
				<form method="GET" action="<?= site_url('shipment/manifest') ?>">
					<div class="row g-2 align-items-end">
						<div class="col-6 col-md-3">
							<label class="form-label small mb-1">Status</label>
							<select name="status" class="form-select form-select-sm">
								<option value="">Semua Aktif</option>
								<option value="MANIFESTED" <?= ($filters['status'] == 'MANIFESTED') ? 'selected' : '' ?>>Manifested</option>
								<option value="DEPARTED" <?= ($filters['status'] == 'DEPARTED')   ? 'selected' : '' ?>>Departed</option>
							</select>
						</div>
						<div class="col-6 col-md-3 d-flex gap-2">
							<button type="submit" class="btn btn-sm btn-primary">Filter</button>
							<a href="<?= site_url('shipment/manifest') ?>" class="btn btn-sm btn-link px-1">Reset</a>
						</div>
						<div class="col-md-6 text-md-end">
							<small class="text-muted">
								Menampilkan <strong><?= count($manifests) ?></strong> SMU aktif
							</small>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- ===== DESKTOP TABLE ===== -->
		<div class="card shadow-sm d-none d-md-block">
			<div class="table-responsive">
				<table class="table table-vcenter card-table">
					<thead class="bg-light">
						<tr>
							<th>Detail Penerbangan</th>
							<th>Rute</th>
							<th class="text-center">Total Muatan</th>
							<th>Waktu</th>
							<th>Status</th>
							<th class="w-1">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($manifests): foreach ($manifests as $m):
								$dept_ts   = strtotime($m->departure_date);
								$now       = time();
								$diff_min  = ($dept_ts - $now) / 60;
								$is_late   = ($diff_min < 0 && $m->status === 'MANIFESTED');

								if ($diff_min < 0) {
									$time_label = 'Terlambat ' . human_readable_minutes(abs($diff_min));
									$time_class = 'text-danger fw-bold';
								} elseif ($diff_min < 60) {
									$time_label = 'Terbang ' . round($diff_min) . ' menit lagi';
									$time_class = 'text-warning fw-bold';
								} else {
									$time_label = 'Terbang ' . round($diff_min / 60, 1) . ' jam lagi';
									$time_class = 'text-muted';
								}
						?>
								<tr class="<?= $is_late ? 'table-danger' : '' ?>">
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar bg-blue-lt me-3"><?= tabler_icon('plane-departure') ?></span>
											<div>
												<div class="fw-bold text-azure h4 mb-0"><?= $m->smu_number ?></div>
												<div class="text-muted small"><?= $m->flight_number ?></div>
											</div>
										</div>
									</td>
									<td>
										<div class="d-flex align-items-center gap-1 mb-1">
											<span class="badge bg-dark-lt small"><?= $m->origin ?></span>
											<i class="bi bi-arrow-right text-muted"></i>
											<span class="badge bg-green-lt small"><?= $m->destination ?></span>
										</div>
										<?php if ($m->origin_warehouse): ?>
											<div class="small text-muted"><?= tabler_icon('building-warehouse', 'icon-sm me-1') ?><?= $m->origin_warehouse ?></div>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<div class="fw-bold"><?= ($m->total_weight) ? number_format($m->total_weight, 1) : '0.0' ?> KG</div>
										<div class="text-muted small"><?= $m->total_resi ?> Resi / <?= $m->total_koli ?> Koli</div>
									</td>
									<td>
										<div class="small text-muted"><?= date('d M Y H:i', $dept_ts) ?></div>
										<div class="small <?= $time_class ?>"><?= $time_label ?></div>
										<?php if ($is_late): ?>
											<span class="badge bg-danger-lt mt-1">Belum Berangkat!</span>
										<?php endif; ?>
									</td>
									<td>
										<?php
										$badge = 'bg-secondary';
										if ($m->status == 'MANIFESTED') $badge = 'bg-blue';
										if ($m->status == 'DEPARTED')   $badge = 'bg-orange';
										?>
										<span class="badge <?= $badge ?> fw-bold"><?= str_replace('_', ' ', $m->status) ?></span>
									</td>
									<td>
										<a href="<?= site_url('shipment/awb_console/' . $m->awb_id) ?>" class="btn btn-sm btn-light" title="Detail Console / Karung">
											<?= tabler_icon('eye', 'me-1') ?> Detail
										</a>
										<?php if ($m->status == 'DRAFT'): ?>
											<a href="<?= site_url('shipment/awb_console/' . $m->awb_id) ?>" class="btn btn-sm btn-warning">
												<?= tabler_icon('barcode', 'me-1') ?> Pack Items
											</a>
										<?php elseif ($m->status == 'MANIFESTED'): ?>
											<button class="btn btn-sm btn-success btn-depart" data-awb-id="<?= $m->awb_id ?>" data-smu="<?= $m->smu_number ?>">
												<?= tabler_icon('send', 'me-1') ?> Departed
											</button>
										<?php elseif ($m->status == 'DEPARTED'): ?>
											<button class="btn btn-sm btn-azure btn-arrive" data-smu="<?= $m->smu_number ?>">
												<?= tabler_icon('plane-arrival', 'me-1') ?> Arrived
											</button>
										<?php else: ?>
											<span class="text-muted small">Selesai</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="6" class="text-center py-5">
									<div class="empty">
										<div class="empty-icon text-muted"><?= tabler_icon('plane-off') ?></div>
										<p class="empty-title">Tidak ada manifest aktif</p>
										<p class="empty-subtitle text-muted">Semua penerbangan sudah selesai atau belum ada yang di-manifest.</p>
									</div>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- ===== MOBILE CARD LIST ===== -->
		<div class="d-md-none">
			<?php if ($manifests): foreach ($manifests as $m):
					$dept_ts  = strtotime($m->departure_date);
					$now      = time();
					$diff_min = ($dept_ts - $now) / 60;
					$is_late  = ($diff_min < 0 && $m->status === 'MANIFESTED');

					if ($diff_min < 0) {
						$time_label = 'Terlambat ' . human_readable_minutes(abs($diff_min));
						$time_class = 'text-danger fw-bold';
					} elseif ($diff_min < 60) {
						$time_label = 'Terbang ' . round($diff_min) . ' menit lagi';
						$time_class = 'text-warning fw-bold';
					} else {
						$time_label = 'Terbang ' . round($diff_min / 60, 1) . ' jam lagi';
						$time_class = 'text-muted';
					}

					$badge = 'bg-secondary';
					if ($m->status == 'MANIFESTED') $badge = 'bg-blue';
					if ($m->status == 'DEPARTED')   $badge = 'bg-orange';
			?>
					<div class="card mb-2 <?= $is_late ? 'border-danger' : '' ?>">
						<div class="card-body p-3">
							<!-- Header -->
							<div class="d-flex justify-content-between align-items-start mb-2">
								<div>
									<div class="fw-bold text-azure h5 mb-0"><?= $m->smu_number ?></div>
									<div class="small text-muted"><?= $m->flight_number ?></div>
								</div>
								<span class="badge <?= $badge ?> fw-bold"><?= str_replace('_', ' ', $m->status) ?></span>
							</div>
							<!-- Info Grid -->
							<div class="row g-2 small mb-2">
								<div class="col-6">
									<div class="text-muted">Rute</div>
									<div class="fw-semibold"><?= $m->origin ?> → <?= $m->destination ?></div>
								</div>
								<div class="col-6">
									<div class="text-muted">Muatan</div>
									<div class="fw-semibold"><?= number_format($m->total_weight, 1) ?> KG</div>
									<div class="text-muted"><?= $m->total_resi ?> Resi / <?= $m->total_koli ?> Koli</div>
								</div>
								<div class="col-6">
									<div class="text-muted">Jadwal</div>
									<div><?= date('d M Y H:i', $dept_ts) ?></div>
								</div>
								<div class="col-6">
									<div class="text-muted">Countdown</div>
									<div class="<?= $time_class ?>"><?= $time_label ?></div>
								</div>
								<?php if ($m->origin_warehouse): ?>
									<div class="col-12">
										<div class="text-muted">Warehouse</div>
										<div><?= $m->origin_warehouse ?></div>
									</div>
								<?php endif; ?>
							</div>
							<?php if ($is_late): ?>
								<div class="alert alert-danger py-1 px-2 small mb-2">⚠️ Pesawat belum berangkat padahal jadwal sudah lewat!</div>
							<?php endif; ?>
							<!-- Action -->
							<div class="d-grid">
								<?php if ($m->status == 'MANIFESTED'): ?>
									<button class="btn btn-success btn-depart" data-smu="<?= $m->smu_number ?>">
										<?= tabler_icon('send', 'me-1') ?> Confirm Departed
									</button>
								<?php elseif ($m->status == 'DEPARTED'): ?>
									<button class="btn btn-azure btn-arrive" data-smu="<?= $m->smu_number ?>">
										<?= tabler_icon('plane-arrival', 'me-1') ?> Confirm Arrived
									</button>
								<?php else: ?>
									<span class="text-muted small text-center">Flight Selesai</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach;
			else: ?>
				<div class="card">
					<div class="card-body text-center py-5 text-muted">
						<?= tabler_icon('plane-off', 'display-6') ?>
						<p class="mt-2 mb-0">Tidak ada manifest aktif.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>

	</div>
</div>

<div class="modal modal-blur fade" id="modal-split-depart" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-success-lt">
				<h5 class="modal-title text-success fw-bold"><?= tabler_icon('plane-departure', 'me-1') ?> Konfirmasi Karung Terbang (Flight Out)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-split-depart">
				<input type="hidden" name="awb_id" id="mdl-depart-awb-id">
				<div class="modal-body text-dark">
					<p class="mb-2">Silakan centang karung/koli yang **beneran valid** masuk ke dalam lambung pesawat:</p>

					<div class="list-group list-group-flush border rounded mb-3" id="karung-checkbox-list" style="max-height: 200px; overflow-y: auto;">
					</div>

					<div class="alert alert-warning mb-0 small">
						<?= tabler_icon('info-circle') ?> Karung yang **tidak dicentang** akan otomatis di-set menjadi status **OFFLOADED** (Tertinggal) dan bisa dialihkan ke penerbangan lain nanti.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-success fw-bold"><?= tabler_icon('send', 'me-1') ?> Konfirmasi Terbang!</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	// Ketika tombol Departed diklik
	// Ketika tombol Departed diklik
	$(document).on('click', '.btn-depart', function() {
		const awbId = $(this).data('awb-id'); // Pastikan di tombol aksi lu ganti data-smu jadi data-awb-id="<?= $m->awb_id ?>"
		const smu = $(this).data('smu');

		$('#mdl-depart-awb-id').val(awbId);
		$('#karung-checkbox-list').html('<div class="text-center p-3 text-muted">Memuat daftar karung...</div>');
		$('#modal-split-depart').modal('show');

		// Ambil daftar karung milik AWB ini via AJAX
		$.get("<?= site_url('shipment/ajax_get_koli_by_awb/') ?>" + awbId, function(res) {
			if (res.status) {
				let html = '';
				res.data.forEach(k => {
					html += `
                  <label class="list-group-item d-flex justify-content-between align-items-center py-2 cursor-pointer">
                     <span class="d-flex align-items-center">
                        <input class="form-check-input me-3 check-karung-item" type="checkbox" name="karung_ids[]" value="${k.id}" checked>
                        <div>
                           <strong class="text-azure">${k.koli_number}</strong>
                        </div>
                     </span>
                  </label>
               `;
				});
				$('#karung-checkbox-list').html(html);
			} else {
				$('#karung-checkbox-list').html(`<div class="alert alert-danger m-2 small">${res.message}</div>`);
			}
		}, 'json');
	});


	// <span class = "badge bg-light text-dark font-monospace fw-bold" > $ {k.actual_weight}Kg < /span>

	// Submit Form Split Departed
	$('#form-split-depart').on('submit', function(e) {
		e.preventDefault();

		const totalChecked = $('.check-karung-item:checked').length;
		if (totalChecked === 0) {
			Swal.fire('Peringatan', 'Minimal harus ada 1 karung yang dicentang terbang bro!', 'warning');
			return;
		}

		const btnSubmit = $(this).find('button[type="submit"]');
		btnSubmit.prop('disabled', true).text('Memproses...');

		$.post("<?= site_url('shipment/ajax_confirm_split_departure') ?>", $(this).serialize(), function(res) {
			btnSubmit.prop('disabled', false).html('Konfirmasi Terbang!');
			if (res.status) {
				$('#modal-split-depart').modal('hide');
				Swal.fire('Terbang! ✈️', res.message, 'success').then(() => location.reload());
			} else {
				Swal.fire('Gagal', res.message, 'error');
			}
		}, 'json');
	});

	$(document).on('click', '.btn-arrive', function() {
		const smu = $(this).data('smu');
		Swal.fire({
			title: 'Konfirmasi Landing?',
			text: `SMU ${smu} sudah mendarat di tujuan?`,
			icon: 'info',
			showCancelButton: true,
			confirmButtonText: 'Ya, Sudah Landing!',
			confirmButtonColor: '#4299e1'
		}).then(result => {
			if (result.isConfirmed) {
				$.post("<?= site_url('shipment/ajax_confirm_arrival') ?>", {
					smu_number: smu
				}, function(res) {
					if (res.status) {
						Swal.fire('Landed! 🛬', res.message, 'success').then(() => location.reload());
					} else {
						Swal.fire('Gagal', res.message, 'error');
					}
				}, 'json');
			}
		});
	});
</script>
