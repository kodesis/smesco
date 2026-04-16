<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-navy fw-800">
					<?= tabler_icon('receipt-2', 'me-2 text-primary') ?>
					Verifikasi Bukti Transfer
				</h2>
				<div class="text-muted mt-1">Daftar resi yang menunggu validasi pembayaran dari customer.</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">


		<?php if ($this->session->flashdata('success')): ?>
			<div class="alert alert-success alert-dismissible mb-4" role="alert">
				<div class="d-flex align-items-center">
					<div><?= tabler_icon('circle-check', 'me-2') ?></div>
					<div><?= $this->session->flashdata('success') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>
		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger alert-dismissible mb-4" role="alert">
				<div class="d-flex align-items-center">
					<div><?= tabler_icon('alert-circle', 'me-2') ?></div>
					<div><?= $this->session->flashdata('error') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>

		<div class="row row-cards mb-4">
			<div class="col-sm-6 col-lg-4">
				<div class="card card-sm border-top border-3 border-warning shadow-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-warning text-white avatar">
									<?= tabler_icon('clock-hour-4') ?>
								</span>
							</div>
							<div class="col">
								<div class="font-weight-medium">Menunggu Verifikasi</div>
								<div class="text-muted"><?= $total ?> Resi Pending</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-4">
				<div class="card card-sm border-top border-3 border-primary shadow-sm">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-primary text-white avatar">
									<?= tabler_icon('currency-rupiah') ?>
								</span>
							</div>
							<div class="col">
								<div class="font-weight-medium">Potensi Pendapatan</div>
								<div class="text-muted">Rp <?= number_format($total_pending_amount, 0, ',', '.') ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card shadow-sm">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h3 class="card-title fw-bold">Daftar Antrean</h3>

				<form action="<?= site_url('finance/verifikasi') ?>" method="GET" class="d-flex gap-2">
					<input type="text" name="q" class="form-control" placeholder="Cari No Resi / Nama..." value="<?= $filters['q'] ?? '' ?>">
					<button type="submit" class="btn btn-dark"><?= tabler_icon('search') ?></button>
					<?php if (!empty($filters['q'])): ?>
						<a href="<?= site_url('finance/verifikasi') ?>" class="btn btn-outline-secondary">Reset</a>
					<?php endif; ?>
				</form>
			</div>

			<div class="table-responsive">
				<table class="table table-vcenter table-hover card-table">
					<thead class="bg-light">
						<tr>
							<th>Detail Resi</th>
							<th>Pengirim</th>
							<th>Rute</th>
							<th>Nominal Tagihan</th>
							<th>Status Pembayaran</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($shipments)): ?>
							<tr>
								<td colspan="6" class="text-center py-5">
									<i class="bi bi-check-circle display-4 text-success mb-3 d-block"></i>
									<h4 class="text-muted">Kerja bagus! Tidak ada resi yang menunggu verifikasi saat ini.</h4>
								</td>
							</tr>
						<?php else: ?>
							<?php foreach ($shipments as $s): ?>
								<tr>
									<td>
										<div class="fw-bold text-primary"><?= $s->no_resi ?></div>
										<div class="text-muted small"><?= date('d M Y, H:i', strtotime($s->created_at)) ?></div>
									</td>
									<td>
										<div class="fw-bold text-dark"><?= $s->sender_name ?></div>
										<div class="text-muted small">
											<a href="https://wa.me/<?= $s->sender_phone ?>" target="_blank" class="text-decoration-none text-success">
												<i class="bi bi-whatsapp"></i> <?= $s->sender_phone ?>
											</a>
										</div>
									</td>
									<td>
										<div class="fw-bold"><?= $s->origin ?> <i class="bi bi-arrow-right text-muted mx-1"></i> <?= $s->destination ?></div>
										<div class="text-muted small"><?= $s->category ?></div>
									</td>
									<td>
										<div class="h4 mb-0 text-danger fw-900">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></div>
									</td>
									<td>
										<span class="badge bg-warning-lt text-warning fw-bold px-3 py-2">
											<?= tabler_icon('upload', 'icon-sm me-1') ?> PERLU DICEK
										</span>
									</td>
									<td class="text-center">
										<div class="btn-group">
											<button type="button" class="btn btn-primary btn-sm btn-view-proof"
												data-img="<?= base_url($s->payment_proof) ?>"
												data-resi="<?= $s->no_resi ?>"
												data-amount="<?= number_format($s->total_amount, 0, ',', '.') ?>">
												<i class="bi bi-eye"></i> Cek Struk
											</button>

											<button type="button" class="btn btn-success btn-sm btn-approve" data-resi="<?= $s->no_resi ?>">
												<?= tabler_icon('check') ?>
											</button>

											<button type="button" class="btn btn-danger btn-sm btn-reject" data-resi="<?= $s->no_resi ?>">
												<?= tabler_icon('x') ?>
											</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<?php if (!empty($pagination)): ?>
				<div class="card-footer d-flex align-items-center justify-content-center">
					<?= $pagination ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</div>

<div class="modal modal-blur fade" id="modal-proof" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">
			<div class="modal-header">
				<h5 class="modal-title fw-bold">Bukti Transfer <span id="proof-resi" class="text-primary"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body text-center bg-light">
				<div class="mb-3">
					<span class="text-muted">Nominal yang harus dibayar:</span>
					<div class="h2 text-danger fw-900 mb-0" id="proof-amount">Rp 0</div>
				</div>

				<div class="border rounded bg-white p-2 d-inline-block shadow-sm">
					<img id="proof-img" src="" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 500px; object-fit: contain;">
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-danger btn-reject-modal">
					<?= tabler_icon('xbox-x') ?> Tolak (Tidak Valid)
				</button>
				<button type="button" class="btn btn-success btn-approve-modal">
					<?= tabler_icon('circle-check') ?> Terima & Proses Resi
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {

		let currentResi = '';

		// --- 1. LOGIC NAMPILIN MODAL GAMBAR ---
		document.querySelectorAll('.btn-view-proof').forEach(btn => {
			btn.addEventListener('click', function() {
				currentResi = this.dataset.resi;

				// Ganti isi text dan src gambar di modal
				document.getElementById('proof-resi').innerText = currentResi;
				document.getElementById('proof-amount').innerText = 'Rp ' + this.dataset.amount;
				document.getElementById('proof-img').src = this.dataset.img;

				// Munculin Modalnya
				var modal = new bootstrap.Modal(document.getElementById('modal-proof'));
				modal.show();
			});
		});

		// --- 2. LOGIC APPROVE (TERIMA) ---
		function actionApprove(resi) {
			Swal.fire({
				title: 'Verifikasi Pembayaran?',
				text: `Anda yakin nominal transfer untuk resi ${resi} sudah masuk dan sesuai?`,
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Ya, Approve!',
				cancelButtonText: 'Batal',
				confirmButtonColor: '#28a745'
			}).then((result) => {
				if (result.isConfirmed) {
					// Arahkan ke endpoint Controller (nanti kita bikin)
					window.location.href = `<?= site_url('finance/approve_payment/') ?>${resi}`;
				}
			});
		}

		// --- 3. LOGIC REJECT (TOLAK) ---
		function actionReject(resi) {
			Swal.fire({
				title: 'Tolak Bukti Transfer?',
				text: `Bukti transfer untuk resi ${resi} akan ditolak. Customer harus mengupload ulang.`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, Tolak!',
				cancelButtonText: 'Batal',
				confirmButtonColor: '#dc3545'
			}).then((result) => {
				if (result.isConfirmed) {
					// Arahkan ke endpoint Controller (nanti kita bikin)
					window.location.href = `<?= site_url('finance/reject_payment/') ?>${resi}`;
				}
			});
		}

		// Pasang event klik ke tombol Approve/Reject yang ada di Tabel
		document.querySelectorAll('.btn-approve').forEach(btn => {
			btn.addEventListener('click', function() {
				actionApprove(this.dataset.resi);
			});
		});
		document.querySelectorAll('.btn-reject').forEach(btn => {
			btn.addEventListener('click', function() {
				actionReject(this.dataset.resi);
			});
		});

		// Pasang event klik ke tombol Approve/Reject yang ada di dalam Modal Gambar
		document.querySelector('.btn-approve-modal').addEventListener('click', function() {
			actionApprove(currentResi);
		});
		document.querySelector('.btn-reject-modal').addEventListener('click', function() {
			actionReject(currentResi);
		});

	});
</script>
