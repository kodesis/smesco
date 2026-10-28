<?php defined('BASEPATH') or exit('No direct script access allowed');
$statuses = ['BOOKED', 'READY_FOR_HANDOVER', 'HANDOVER_TO_COURIER', 'READY_FOR_FORWARDING', 'MANIFESTED', 'DEPARTED', 'ARRIVED', 'RECEIVED_DESTINATION', 'DELIVERED', 'CANCELLED'];
?>
<?php $this->load->view('app/pages/agent_kecamatan/_mobile_css'); ?>
<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title"><?= tabler_icon('plane-departure', 'me-2') ?> Pengiriman Internasional</h2>
			</div>

			<div class="col-auto ms-auto btn-list">
				<button type="button" class="btn btn-dark btn-bulk-handover bulk-ui bulk-off d-none d-md-inline-flex">
					<?= tabler_icon('truck-delivery', 'me-1') ?> Serahkan ke Forwarder (<span class="selected-count">0</span>)
				</button>
				<a href="<?= site_url('agentkecamatan/create') ?>" class="btn btn-primary">
					<?= tabler_icon('plus', 'me-1') ?> Kirim Barang
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php
		$rp  = function ($v) {
			return 'Rp ' . number_format((float) $v, 0, ',', '.');
		};
		$qk  = $filters['quick'] ?? '';
		$st  = $filters['status'] ?? '';
		$cards = [
			[
				'label'  => 'Hari Ini',
				'value'  => number_format($stats['today']),
				'url'    => site_url('agentkecamatan?quick=today'),
				'active' => $qk === 'today',
				'sub'    => '',
			],
			[
				'label'  => 'Belum Lunas',
				'value'  => number_format($stats['unpaid']),
				'url'    => site_url('agentkecamatan?quick=unpaid'),
				'active' => $qk === 'unpaid',
				'sub'    => '',
			],
			[
				'label'  => 'Siap Diserahkan',
				'value'  => number_format($stats['ready_for_handover']),
				'url'    => site_url('agentkecamatan?status=READY_FOR_HANDOVER'),
				'active' => $st === 'READY_FOR_HANDOVER' && $qk === '',
				'sub'    => '',
			],
			[
				'label'  => 'Outstanding',
				'value'  => $rp($outstanding['outstanding'] ?? 0),
				'url' => $can_settle ? site_url('agentkecamatan/settlement') : '#',
				'active' => false,
				'sub'    => 'Siap disetor: ' . (!empty($outstanding['settleable']) ? $rp($outstanding['settleable']) : 'Rp 0')
					. ((float) ($outstanding['pending'] ?? 0) > 0 ? ' · Menunggu verifikasi: ' . (!empty($outstanding['pending']) ? $rp($outstanding['pending']) : 'Rp 0') : ''),
			],
		];
		?>
		<div class="row row-cards mb-4">
			<?php foreach ($cards as $c): ?>
				<div class="col-6 col-lg-3">
					<a href="<?= $c['url'] ?>" class="card card-sm card-link text-decoration-none <?= $c['active'] ? 'border-primary' : '' ?>">
						<div class="card-body">
							<div class="text-muted small"><?= $c['label'] ?></div>
							<div class="h2 mb-0"><?= $c['value'] ?></div>
							<?php if ($c['sub'] !== ''): ?>
								<div class="small text-muted mt-1"><?= html_escape($c['sub']) ?></div>
							<?php endif; ?>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="card mb-4">
			<div class="card-body py-2">
				<form method="GET" action="<?= site_url('agentkecamatan') ?>">
					<input type="hidden" name="quick" value="<?= html_escape($filters['quick']) ?>">
					<div class="row g-2 align-items-end">
						<div class="col-12 col-md-5">
							<label class="form-label small mb-1">Kata Kunci</label>
							<input type="text" name="q" class="form-control" placeholder="Resi / pengirim / penerima / tujuan" value="<?= html_escape($filters['q']) ?>">
						</div>
						<div class="col-6 col-md-4">
							<label class="form-label small mb-1">Status</label>
							<select name="status" class="form-select">
								<option value="">Semua</option>
								<?php foreach ($statuses as $st): ?>
									<option value="<?= $st ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>><?= str_replace('_', ' ', $st) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-6 col-md-3 d-flex gap-2">
							<button class="btn btn-primary flex-fill">Cari</button>
							<a href="<?= site_url('agentkecamatan') ?>" class="btn btn-sm btn-link">Reset</a>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="text-muted small mb-2">
			Menampilkan <strong><?= count($shipments) ?></strong> dari <strong><?= (int) $total ?></strong> pengiriman
		</div>

		<?php if ($filters['quick']): ?>
			<div class="mb-2">
				<span class="badge bg-blue-lt">Filter: <?= $filters['quick'] === 'today' ? 'Hari ini' : 'Belum lunas' ?></span>
				<a href="<?= site_url('agentkecamatan') ?>" class="small ms-1">hapus</a>
			</div>
		<?php endif; ?>

		<div class="card d-none d-md-block">
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-hover">
					<thead>
						<tr>
							<th class="w-1"><input type="checkbox" class="form-check-input check-all"></th>
							<th>No. Resi</th>
							<th>Tujuan</th>
							<th>Pengirim / Penerima</th>
							<th>Biaya</th>
							<th>Status</th>
							<th class="w-1">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($shipments): foreach ($shipments as $s): ?>
								<tr>
									<td>
										<?php if ($s->status === 'READY_FOR_HANDOVER'): ?>
											<input type="checkbox" class="form-check-input shipment-check" value="<?= (int) $s->id ?>">
										<?php endif; ?>
									</td>
									<td>
										<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="fw-bold"><?= html_escape($s->no_resi) ?></a>
										<div class="small text-muted"><?= date('d/m/Y H:i', strtotime($s->created_at)) ?></div>
									</td>
									<td>
										<?= html_escape($s->destination) ?>
										<div class="small text-muted"><?= html_escape($s->service_name) ?></div>
									</td>
									<td class="small">
										<div><span class="text-muted">Dari:</span> <?= html_escape($s->sender_name) ?></div>
										<div><span class="text-muted">Ke:</span> <?= html_escape($s->receiver_name) ?></div>
									</td>
									<td>
										<div class="fw-bold">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></div>
										<div class="small text-muted"><?= (int) $s->koli ?> Koli | <?= (float) $s->chargeable_weight ?> Kg</div>
										<?php if ($s->payment_status !== 'PAID'): ?>
											<span class="badge bg-yellow-lt">Belum lunas</span>
										<?php endif; ?>
									</td>
									<td><?= shipment_status_badge($s->status) ?></td>
									<td>
										<div class="btn-list flex-nowrap">
											<?php if ($s->status === 'BOOKED' && $s->payment_status !== 'PAID'): ?>
												<button type="button" class="btn btn-sm btn-success btn-confirm-paid"
													data-id="<?= (int) $s->id ?>" data-resi="<?= html_escape($s->no_resi) ?>">
													<?= tabler_icon('cash', 'me-1') ?> Lunas
												</button>
											<?php endif; ?>
											<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="btn btn-sm btn-icon btn-outline-primary" title="Detail"><?= tabler_icon('eye') ?></a>
											<?php if ($s->payment_status === 'PAID'): ?>
												<a href="<?= site_url('agentkecamatan/print_label/' . (int) $s->id) ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" title="Cetak Label"><?= tabler_icon('printer') ?></a>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="7" class="text-center text-muted py-5">Belum ada pengiriman.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<?php $this->load->view('app/layouts/_pagination', compact('total', 'page', 'per_page', 'offset', 'total_pages', 'base_url')); ?>
		</div>
		<div class="d-md-none">
			<?php if ($shipments): ?>
				<div class="form-check mb-3">
					<label class="form-check-label small text-muted">
						<input type="checkbox" class="form-check-input check-all me-2 chk-lg"> Pilih semua yang siap diserahkan
					</label>
				</div>

				<?php foreach ($shipments as $s): ?>
					<div class="card mb-2">
						<div class="card-body p-3">
							<div class="d-flex justify-content-between align-items-start mb-2">
								<div class="d-flex align-items-start gap-2">
									<?php if ($s->status === 'READY_FOR_HANDOVER'): ?>
										<input type="checkbox" class="form-check-input shipment-check chk-lg mt-1" value="<?= (int) $s->id ?>">
									<?php endif; ?>
									<div>
										<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="fw-bold"><?= html_escape($s->no_resi) ?></a>
										<div class="small text-muted"><?= date('d/m/Y H:i', strtotime($s->created_at)) ?></div>
									</div>
								</div>
								<?= shipment_status_badge($s->status) ?>
							</div>

							<div class="row g-2 small mb-3">
								<div class="col-6">
									<div class="text-muted">Tujuan</div>
									<div class="fw-semibold"><?= html_escape($s->destination) ?></div>
								</div>
								<div class="col-6">
									<div class="text-muted">Biaya</div>
									<div class="fw-bold">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></div>
									<div class="text-muted"><?= (int) $s->koli ?> Koli | <?= (float) $s->chargeable_weight ?> Kg</div>
								</div>
								<div class="col-6">
									<div class="text-muted">Pengirim</div>
									<div class="fw-semibold text-truncate"><?= html_escape($s->sender_name) ?></div>
								</div>
								<div class="col-6">
									<div class="text-muted">Penerima</div>
									<div class="fw-semibold text-truncate"><?= html_escape($s->receiver_name) ?></div>
								</div>
							</div>

							<?php if ($s->payment_status !== 'PAID'): ?>
								<span class="badge bg-yellow-lt mb-2">Belum lunas</span>
							<?php endif; ?>

							<div class="d-flex gap-2">
								<?php if ($s->status === 'BOOKED' && $s->payment_status !== 'PAID'): ?>
									<button type="button" class="btn btn-success flex-fill btn-confirm-paid"
										data-id="<?= (int) $s->id ?>" data-resi="<?= html_escape($s->no_resi) ?>">
										<?= tabler_icon('cash', 'me-1') ?> Lunas
									</button>
								<?php endif; ?>
								<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="btn btn-outline-primary flex-fill">
									<?= tabler_icon('eye', 'me-1') ?> Detail
								</a>
								<?php if ($s->payment_status === 'PAID'): ?>
									<a href="<?= site_url('agentkecamatan/print_label/' . (int) $s->id) ?>" target="_blank" class="btn btn-outline-secondary btn-icon">
										<?= tabler_icon('printer') ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="card">
					<div class="card-body text-center text-muted py-5">Belum ada pengiriman.</div>
				</div>
			<?php endif; ?>
			<?php $this->load->view('app/layouts/_pagination', compact('total', 'page', 'per_page', 'offset', 'total_pages', 'base_url')); ?>
		</div>

		<div class="sticky-bar d-md-none bulk-ui bulk-off">
			<button type="button" class="btn btn-dark w-100 btn-bulk-handover">
				<?= tabler_icon('truck-delivery', 'me-1') ?> Serahkan ke Forwarder (<span class="selected-count">0</span>)
			</button>
		</div>
	</div>
</div>

<?php $this->load->view('app/pages/agent_kecamatan/_handover_modal'); ?>

<script>
	function selectedIds() {
		return [...new Set($('.shipment-check:checked').map((i, el) => el.value).get())];
	}

	function updateBulk() {
		const n = selectedIds().length;
		$('.selected-count').text(n);
		$('.bulk-ui').toggleClass('bulk-off', n === 0);
	}

	$(document).on('change', '.shipment-check', function() {
		$('.shipment-check[value="' + this.value + '"]').prop('checked', this.checked); // sync tabel & card
		updateBulk();
	});

	$(document).on('change', '.check-all', function() {
		$('.check-all').prop('checked', this.checked);
		$('.shipment-check').prop('checked', this.checked);
		updateBulk();
	});

	$(document).on('click', '.btn-bulk-handover', function() {
		openHandover(selectedIds());
	});

	$(document).on('click', '.btn-confirm-paid', function() {
		const id = $(this).data('id'),
			resi = $(this).data('resi');
		Swal.fire({
			title: 'Konfirmasi Pembayaran',
			text: `Sudah menerima pembayaran untuk resi ${resi}?`,
			icon: 'info',
			showCancelButton: true,
			confirmButtonText: 'Ya, Sudah Lunas!',
			cancelButtonText: 'Belum',
			confirmButtonColor: '#2fb344'
		}).then(r => {
			if (!r.isConfirmed) return;
			$.post("<?= site_url('agentkecamatan/confirm_payment') ?>", {
				id
			}, function(res) {
				if (res.status) Swal.fire('Sip!', res.message, 'success').then(() => location.reload());
				else Swal.fire('Gagal', res.message, 'error');
			}, 'json').fail(function(xhr) {
				Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan.', 'error');
			});
		});
	});
</script>
