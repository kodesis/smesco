<?php defined('BASEPATH') or exit('No direct script access allowed');
$rp    = function ($v) {
	return 'Rp ' . number_format((float) $v, 0, ',', '.');
};
$badge = ['PENDING' => 'bg-yellow-lt', 'VERIFIED' => 'bg-green-lt', 'REJECTED' => 'bg-red-lt'];
?>
<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<h2 class="page-title"><?= tabler_icon('cash', 'me-2') ?> Setoran Agen Kecamatan</h2>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<div class="row row-cards mb-4">
			<div class="col-6 col-lg-3">
				<a href="<?= site_url('finance/settlements?status=PENDING') ?>" class="card card-sm card-link text-decoration-none">
					<div class="card-body">
						<div class="text-muted small">Menunggu verifikasi</div>
						<div class="h2 mb-0"><?= (int) $pending_sum['n'] ?> setoran</div>
						<div class="small text-muted"><?= $rp($pending_sum['amount']) ?></div>
					</div>
				</a>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-body py-2">
				<form method="GET" action="<?= site_url('finance/settlements') ?>">
					<div class="row g-2 align-items-end">
						<div class="col-12 col-md-5">
							<label class="form-label small mb-1">Kata kunci</label>
							<input type="text" name="q" class="form-control" placeholder="No. setoran / nama agen / kode agen" value="<?= html_escape($filters['q']) ?>">
						</div>
						<div class="col-6 col-md-4">
							<label class="form-label small mb-1">Status</label>
							<select name="status" class="form-select">
								<?php foreach (['PENDING' => 'Menunggu verifikasi', 'VERIFIED' => 'Terverifikasi', 'REJECTED' => 'Ditolak', 'ALL' => 'Semua'] as $k => $v): ?>
									<option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-6 col-md-3 d-flex gap-2">
							<button class="btn btn-primary flex-fill">Cari</button>
							<a href="<?= site_url('finance/settlements') ?>" class="btn btn-sm btn-link">Reset</a>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="text-muted small mb-2">Menampilkan <strong><?= count($rows) ?></strong> dari <strong><?= (int) $total ?></strong> setoran</div>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-hover">
					<thead>
						<tr>
							<th>No. Setoran</th>
							<th>Agen</th>
							<th>Dikirim</th>
							<th>Tgl Transfer</th>
							<th class="text-center">Resi</th>
							<th class="text-end">Total</th>
							<th>Status</th>
							<th class="w-1"></th>
						</tr>
					</thead>
					<tbody>
						<?php if ($rows): foreach ($rows as $r): ?>
								<tr>
									<td class="fw-bold"><?= html_escape($r->settlement_no) ?></td>
									<td>
										<?= html_escape($r->agent_name) ?>
										<div class="small text-muted"><?= html_escape($r->agent_code) ?></div>
									</td>
									<td class="small"><?= date('d/m/Y H:i', strtotime($r->submitted_at)) ?></td>
									<td class="small"><?= date('d/m/Y', strtotime($r->transfer_date)) ?></td>
									<td class="text-center"><?= (int) $r->item_count ?></td>
									<td class="text-end fw-bold"><?= $rp($r->total_amount) ?></td>
									<td><span class="badge <?= $badge[$r->status] ?? 'bg-secondary-lt' ?>"><?= html_escape($r->status) ?></span></td>
									<td>
										<a href="<?= site_url('finance/settlement_detail/' . (int) $r->id) ?>" class="btn btn-sm btn-outline-primary">
											<?= $r->status === 'PENDING' ? 'Periksa' : 'Detail' ?>
										</a>
									</td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="8" class="text-center text-muted py-5">Tidak ada setoran.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<?php $this->load->view('app/layouts/_pagination', compact('total', 'page', 'per_page', 'offset', 'total_pages', 'base_url')); ?>
		</div>
	</div>
</div>
