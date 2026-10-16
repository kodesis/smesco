<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('app/pages/agent_kecamatan/_mobile_css');

$agent_name = $agent_info->name ?? 'Agen Kecamatan';
$agent_code = $agent_info->code ?? '—';
$location   = implode(' · ', array_filter([
	$agent_region->nama_kecamatan ?? '',
	$agent_region->nama_kota ?? '',
	$agent_region->nama_provinsi ?? '',
]));

$st = $shipment_stats ?? [];
$n  = function ($k) use ($st) {
	return (int) ($st[$k] ?? 0);
};

$cards = [
	['Hari Ini',         $n('today'),               'package',        'primary', site_url('agentkecamatan')],
	['Belum Dibayar',    $n('unpaid'),              'clock',          'warning', site_url('agentkecamatan?status=BOOKED')],
	['Siap Diserahkan',  $n('ready_for_handover'),  'package-export', 'success', site_url('agentkecamatan?status=READY_FOR_HANDOVER')],
	['Di Indah Kargo',   $n('handover_to_courier'), 'truck-delivery', 'info',    site_url('agentkecamatan?status=HANDOVER_TO_COURIER')],
	['Diproses Jakarta', $n('ready_for_forwarding'), 'plane-departure', 'purple', site_url('agentkecamatan?status=READY_FOR_FORWARDING')],
	['Total Pengiriman', $n('total'),               'list',           'secondary', site_url('agentkecamatan')],
];
?>

<div class="page-header d-print-none">
	<div class="container-xl">
		<h2 class="page-title"><?= html_escape($agent_name) ?></h2>
		<div class="text-muted small mt-1">
			<?= html_escape($agent_code) ?><?= $location ? ' · ' . html_escape($location) : '' ?>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<a href="<?= site_url('agentkecamatan/create') ?>" class="btn btn-primary btn-lg w-100 mb-3">
			<?= tabler_icon('plus', 'me-1') ?> Kirim Barang
		</a>

		<div class="row row-cards mb-3">
			<?php foreach ($cards as [$label, $val, $icon, $color, $url]): ?>
				<div class="col-6 col-md-4 col-xl-2">
					<a href="<?= $url ?>" class="card card-sm card-link text-decoration-none h-100">
						<div class="card-body">
							<span class="avatar avatar-sm bg-<?= $color ?>-lt text-<?= $color ?> mb-2">
								<?= tabler_icon($icon) ?>
							</span>
							<div class="h1 mb-0"><?= number_format($val) ?></div>
							<div class="text-muted small"><?= $label ?></div>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="d-flex gap-2 mb-4">
			<a href="<?= site_url('agentkecamatan') ?>" class="btn btn-outline-primary flex-fill">
				<?= tabler_icon('list', 'me-1') ?> Semua Pengiriman
			</a>
			<a href="<?= site_url('agentkecamatan/manifest') ?>" class="btn btn-outline-secondary flex-fill">
				<?= tabler_icon('clipboard-list', 'me-1') ?> Daftar Serah-Terima
			</a>
		</div>

		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?= tabler_icon('package', 'me-2') ?> Pengiriman Terbaru</h3>
				<div class="card-options">
					<a href="<?= site_url('agentkecamatan') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
				</div>
			</div>

			<?php if (!empty($recent_shipments)): ?>

				<!-- DESKTOP -->
				<div class="table-responsive d-none d-md-block">
					<table class="table table-vcenter card-table">
						<thead>
							<tr>
								<th>Resi</th>
								<th>Penerima</th>
								<th>Tujuan</th>
								<th>Berat</th>
								<th>Pembayaran</th>
								<th>Status</th>
								<th class="w-1"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($recent_shipments as $s): ?>
								<tr>
									<td>
										<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="fw-semibold"><?= html_escape($s->no_resi) ?></a>
										<div class="text-muted small"><?= date('d/m/Y H:i', strtotime($s->created_at)) ?></div>
									</td>
									<td><?= html_escape($s->receiver_name ?? '—') ?></td>
									<td><?= html_escape($s->destination ?? '—') ?></td>
									<td><?= number_format((float) $s->chargeable_weight, 2, ',', '.') ?> kg</td>
									<td>
										<?php if (($s->payment_status ?? '') === 'PAID'): ?>
											<span class="badge bg-success-lt">Sudah Dibayar</span>
										<?php else: ?>
											<span class="badge bg-warning-lt">Belum Dibayar</span>
										<?php endif; ?>
									</td>
									<td><?= shipment_status_badge($s->status) ?></td>
									<td>
										<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="btn btn-sm btn-icon btn-ghost-secondary">
											<?= tabler_icon('chevron-right') ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- MOBILE -->
				<div class="list-group list-group-flush d-md-none">
					<?php foreach ($recent_shipments as $s): ?>
						<a href="<?= site_url('agentkecamatan/detail/' . $s->id) ?>" class="list-group-item list-group-item-action">
							<div class="d-flex justify-content-between align-items-start mb-1">
								<div>
									<div class="fw-bold"><?= html_escape($s->no_resi) ?></div>
									<div class="text-muted small"><?= date('d/m/Y H:i', strtotime($s->created_at)) ?></div>
								</div>
								<?= shipment_status_badge($s->status) ?>
							</div>
							<div class="small">
								<span class="text-muted">Ke:</span> <?= html_escape($s->receiver_name ?? '—') ?>
								· <?= html_escape($s->destination ?? '—') ?>
							</div>
							<div class="d-flex justify-content-between small mt-1">
								<span class="text-muted"><?= number_format((float) $s->chargeable_weight, 2, ',', '.') ?> kg</span>
								<?php if (($s->payment_status ?? '') === 'PAID'): ?>
									<span class="text-success">Sudah dibayar</span>
								<?php else: ?>
									<span class="text-warning">Belum dibayar</span>
								<?php endif; ?>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

			<?php else: ?>
				<div class="card-body text-center text-muted py-5">
					<?= tabler_icon('package-off', 'mb-2') ?>
					<div>Belum ada pengiriman.</div>
				</div>
			<?php endif; ?>
		</div>

	</div>
</div>
