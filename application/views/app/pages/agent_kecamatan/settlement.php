<?php defined('BASEPATH') or exit('No direct script access allowed');
$rp   = function ($v) {
	return 'Rp ' . number_format((float) $v, 0, ',', '.');
};
$lock = M_AgentSettlement::SETTLE_ALL_OR_NOTHING;
$badge = ['PENDING' => 'bg-yellow-lt', 'VERIFIED' => 'bg-green-lt', 'REJECTED' => 'bg-red-lt'];
?>
<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title"><?= tabler_icon('cash', 'me-2') ?> Setoran ke Smesco</h2>
			</div>
			<div class="col-auto ms-auto">
				<a href="<?= site_url('agentkecamatan') ?>" class="btn btn-outline-secondary">Kembali</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<div class="row row-cards mb-4">
			<div class="col-6">
				<div class="card card-sm">
					<div class="card-body">
						<div class="text-muted small">Outstanding (siap disetor)</div>
						<div class="h2 mb-0"><?= $rp($summary['outstanding']) ?></div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="card card-sm">
					<div class="card-body">
						<div class="text-muted small">Menunggu verifikasi</div>
						<div class="h2 mb-0"><?= $rp($summary['pending']) ?></div>
					</div>
				</div>
			</div>
		</div>

		<?= form_open_multipart('agentkecamatan/settlement_submit', ['id' => 'form-settle']) ?>
		<div class="card mb-4">
			<div class="card-header">
				<h3 class="card-title">Resi yang harus disetor</h3>
				<?php if ($lock): ?>
					<br>
					<div class="text-muted small">Semua resi yang sudah lunas disetor sekaligus dalam satu transfer.</div>
				<?php endif; ?>
			</div>

			<?php if ($unpaid): ?>
				<div class="table-responsive">
					<table class="table table-vcenter card-table table-hover">
						<thead>
							<tr>
								<th class="w-1"><input type="checkbox" class="form-check-input" id="check-all" <?= $lock ? 'checked onclick="return false"' : '' ?>></th>
								<th>No. Resi</th>
								<th>Tujuan</th>
								<th>Status</th>
								<th class="text-end">Tagihan</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($unpaid as $r): ?>
								<tr>
									<td>
										<input type="checkbox" name="ids[]" value="<?= (int) $r->id ?>"
											class="form-check-input row-check"
											data-amount="<?= (float) $r->nominal_tagihan_smesco ?>"
											<?= $lock ? 'checked onclick="return false"' : '' ?>>
									</td>
									<td>
										<a href="<?= site_url('agentkecamatan/detail/' . $r->id) ?>" class="fw-bold"><?= html_escape($r->no_resi) ?></a>
										<div class="small text-muted"><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></div>
									</td>
									<td><?= html_escape($r->destination) ?></td>
									<td><?= shipment_status_badge($r->status) ?></td>
									<td class="text-end fw-bold"><?= $rp($r->nominal_tagihan_smesco) ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="card-body border-top">
					<div class="row g-3 align-items-end">
						<div class="col-12 col-md-3">
							<div class="text-muted small">Dipilih: <span id="sel-count">0</span> resi</div>
							<div class="h2 mb-0" id="total-text">Rp 0</div>
						</div>
						<div class="col-12 col-md-3">
							<label class="form-label required">Nominal ditransfer</label>
							<input type="number" step="0.01" min="0" name="transfer_amount" id="transfer_amount" class="form-control" required>
							<div class="form-hint">Harus sama persis dengan total.</div>
						</div>
						<div class="col-6 col-md-2">
							<label class="form-label required">Tanggal transfer</label>
							<input type="date" name="transfer_date" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
							<div class="form-hint">&nbsp;</div>
						</div>
						<div class="col-6 col-md-4">
							<label class="form-label required">Bukti transfer</label>
							<input type="file" name="proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
							<div class="form-hint">JPG/PNG/PDF, maks 2 MB.</div>
						</div>
						<div class="col-12">
							<button type="submit" class="btn btn-primary" id="btn-submit" disabled>
								<?= tabler_icon('upload', 'me-1') ?> Kirim Setoran
							</button>
						</div>
					</div>
				</div>
			<?php else: ?>
				<div class="card-body text-center text-muted py-5">Tidak ada resi yang perlu disetor.</div>
			<?php endif; ?>
		</div>
		<?= form_close() ?>

		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Riwayat setoran</h3>
			</div>
			<div class="table-responsive">
				<table class="table table-vcenter card-table">
					<thead>
						<tr>
							<th>No. Setoran</th>
							<th>Tgl Transfer</th>
							<th class="text-end">Total</th>
							<th>Status</th>
							<th>Catatan</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($history): foreach ($history as $h): ?>
								<tr>
									<td class="fw-bold"><?= html_escape($h->settlement_no) ?></td>
									<td><?= date('d/m/Y', strtotime($h->transfer_date)) ?></td>
									<td class="text-end"><?= $rp($h->total_amount) ?></td>
									<td><span class="badge <?= $badge[$h->status] ?? 'bg-secondary-lt' ?>"><?= html_escape($h->status) ?></span></td>
									<td class="small text-muted"><?= $h->status === 'REJECTED' ? html_escape($h->reject_note) : '' ?></td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="5" class="text-center text-muted py-4">Belum ada setoran.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
	function recalc() {
		let t = 0,
			n = 0;
		$('.row-check:checked').each(function() {
			t += parseFloat($(this).data('amount'));
			n++;
		});
		t = Math.round(t * 100) / 100;
		$('#sel-count').text(n);
		$('#total-text').text('Rp ' + t.toLocaleString('id-ID'));
		$('#transfer_amount').val(n ? t : '');
		$('#btn-submit').prop('disabled', n === 0);
	}

	$('#check-all').on('change', function() {
		$('.row-check').prop('checked', this.checked);
		recalc();
	});
	$('.row-check').on('change', function() {
		$('#check-all').prop('checked', $('.row-check:not(:checked)').length === 0);
		recalc();
	});

	$('#form-settle').on('submit', function(e) {
		e.preventDefault();
		const form = this;
		Swal.fire({
			title: 'Kirim setoran?',
			text: $('#sel-count').text() + ' resi, total ' + $('#total-text').text() + '. Setelah dikirim tidak bisa diubah sampai finance memverifikasi.',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Ya, kirim',
			cancelButtonText: 'Batal'
		}).then(r => {
			if (r.isConfirmed) form.submit();
		});
	});

	recalc();
</script>
