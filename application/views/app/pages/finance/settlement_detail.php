<?php defined('BASEPATH') or exit('No direct script access allowed');
$rp    = function ($v) {
	return 'Rp ' . number_format((float) $v, 0, ',', '.');
};
$badge = ['PENDING' => 'bg-yellow-lt', 'VERIFIED' => 'bg-green-lt', 'REJECTED' => 'bg-red-lt'];
$sum   = 0;
foreach ($items as $it) {
	$sum += (float) $it->amount;
}
$mismatch = abs($sum - (float) $st->total_amount) >= 0.01 || abs((float) $st->transfer_amount - (float) $st->total_amount) >= 0.01;
$ext   = strtolower(pathinfo($st->proof_file, PATHINFO_EXTENSION));
$proof = site_url('uploads/settlement/' . (int) $st->id);
?>
<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title"><?= html_escape($st->settlement_no) ?>
					<span class="badge <?= $badge[$st->status] ?? 'bg-secondary-lt' ?> ms-2"><?= html_escape($st->status) ?></span>
				</h2>
			</div>
			<div class="col-auto ms-auto">
				<a href="<?= site_url('finance/settlements') ?>" class="btn btn-outline-secondary">Kembali</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php if ($mismatch): ?>
			<div class="alert alert-danger">Total setoran, jumlah transfer, dan jumlah item tidak sama. Jangan diverifikasi, hubungi developer.</div>
		<?php endif; ?>

		<div class="row row-cards">
			<div class="col-lg-8">
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">Resi dalam setoran (<?= count($items) ?>)</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>No. Resi</th>
									<th>Tujuan</th>
									<th>Pengirim</th>
									<th class="text-end">Tagihan</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($items as $it): ?>
									<tr>
										<td class="fw-bold"><?= html_escape($it->no_resi) ?></td>
										<td><?= html_escape($it->destination) ?></td>
										<td class="small"><?= html_escape($it->sender_name) ?></td>
										<td class="text-end"><?= $rp($it->amount) ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3" class="text-end">Total</th>
									<th class="text-end"><?= $rp($sum) ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Bukti transfer</h3>
						<div class="card-actions"><a href="<?= $proof ?>" target="_blank" class="btn btn-sm btn-outline-primary">Buka di tab baru</a></div>
					</div>
					<div class="card-body text-center">
						<?php if ($ext === 'pdf'): ?>
							<iframe src="<?= $proof ?>" style="width:100%;height:600px;border:0"></iframe>
						<?php else: ?>
							<a href="<?= $proof ?>" target="_blank"><img src="<?= $proof ?>" alt="Bukti transfer" class="img-fluid rounded border" style="max-height:600px"></a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="col-lg-4">
				<div class="card mb-3">
					<div class="card-body">
						<div class="text-muted small">Agen</div>
						<div class="fw-bold"><?= html_escape($st->agent_name) ?> <span class="text-muted">(<?= html_escape($st->agent_code) ?>)</span></div>

						<div class="text-muted small mt-3">Nominal yang harus cocok di mutasi bank</div>
						<div class="h1 mb-0"><?= $rp($st->total_amount) ?></div>

						<div class="text-muted small mt-3">Tanggal transfer (pengakuan agen)</div>
						<div><?= date('d/m/Y', strtotime($st->transfer_date)) ?></div>

						<div class="text-muted small mt-3">Dikirim oleh</div>
						<div><?= html_escape($st->submitted_name) ?>, <?= date('d/m/Y H:i', strtotime($st->submitted_at)) ?></div>

						<?php if ($st->status !== 'PENDING'): ?>
							<div class="text-muted small mt-3"><?= $st->status === 'VERIFIED' ? 'Diverifikasi oleh' : 'Ditolak oleh' ?></div>
							<div><?= html_escape($st->verified_name) ?>, <?= $st->verified_at ? date('d/m/Y H:i', strtotime($st->verified_at)) : '-' ?></div>
						<?php endif; ?>
						<?php if ($st->status === 'REJECTED'): ?>
							<div class="text-muted small mt-3">Alasan penolakan</div>
							<div class="text-danger"><?= nl2br(html_escape($st->reject_note)) ?></div>
						<?php endif; ?>
					</div>
				</div>

				<?php if ($st->status === 'PENDING' && $can_verify): ?>
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Keputusan</h3>
						</div>
						<div class="card-body">
							<div class="small text-muted mb-3">Cocokkan dengan mutasi bank: nominal, tanggal, dan rekening tujuan. Keputusan berlaku untuk seluruh resi di setoran ini.</div>

							<?= form_open('finance/settlement_verify', ['id' => 'form-verify']) ?>
							<input type="hidden" name="id" value="<?= (int) $st->id ?>">
							<button type="submit" class="btn btn-success w-100 mb-2" <?= $mismatch ? 'disabled' : '' ?>>Verifikasi (LUNAS)</button>
							<?= form_close() ?>

							<?= form_open('finance/settlement_reject', ['id' => 'form-reject']) ?>
							<input type="hidden" name="id" value="<?= (int) $st->id ?>">
							<input type="hidden" name="reject_note" id="reject_note">
							<button type="button" class="btn btn-outline-danger w-100" id="btn-reject">Tolak</button>
							<?= form_close() ?>
						</div>
					</div>
				<?php elseif ($st->status === 'PENDING'): ?>
					<div class="alert alert-info">Role kamu hanya bisa melihat. Verifikasi dilakukan oleh finance.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
	$('#form-verify').on('submit', function(e) {
		e.preventDefault();
		const form = this;
		Swal.fire({
			title: 'Verifikasi setoran?',
			text: 'Semua resi di dalamnya menjadi LUNAS ke Smesco. Ini tidak bisa dibatalkan dari sini.',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Ya, verifikasi',
			cancelButtonText: 'Batal',
			confirmButtonColor: '#2fb344'
		}).then(r => {
			if (r.isConfirmed) form.submit();
		});
	});

	$('#btn-reject').on('click', function() {
		Swal.fire({
			title: 'Tolak setoran',
			input: 'textarea',
			inputLabel: 'Alasan (akan dikirim ke agen)',
			inputPlaceholder: 'Contoh: nominal di mutasi bank tidak ditemukan',
			showCancelButton: true,
			confirmButtonText: 'Tolak setoran',
			cancelButtonText: 'Batal',
			confirmButtonColor: '#d63939',
			inputValidator: v => (!v || v.trim().length < 5) ? 'Alasan minimal 5 karakter' : null
		}).then(r => {
			if (!r.isConfirmed) return;
			$('#reject_note').val(r.value.trim());
			$('#form-reject').trigger('submit');
		});
	});
</script>
