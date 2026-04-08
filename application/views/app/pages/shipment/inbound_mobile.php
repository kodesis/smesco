<div class="page-body m-0 p-2">
	<div class="container-xl">

		<div class="d-flex justify-content-between align-items-center mb-3">
			<div>
				<h3 class="m-0 text-uppercase">Inbound <?= $my_city ?></h3>
				<div class="text-muted small">Scan barang yang baru turun kargo</div>
			</div>
			<span class="badge bg-primary-lt" id="count_badge"><?= count($pending_list) ?> Antrean</span>
		</div>

		<div class="card mb-3 border-primary shadow-sm">
			<div class="card-body p-2 text-center">
				<div class="input-group">
					<input type="text" id="hid_input" class="form-control form-control-lg text-center fw-bold border-2"
						placeholder="Tembak Piece ID..." autofocus autocomplete="off">
					<button class="btn btn-primary" type="button" id="btn-camera" data-bs-toggle="modal" data-bs-target="#modal-scanner">
						<?= tabler_icon('camera') ?>
					</button>
				</div>
				<div class="mt-2 small text-muted">Pastikan fokus pada kotak input atau gunakan kamera</div>
			</div>
		</div>

		<div class="row g-2">
			<div class="col-md-6">
				<div class="text-muted small mb-2 text-uppercase fw-bold">1. Daftar Barang Masuk</div>
				<div id="list-container" class="overflow-auto" style="max-height: 55vh;">
					<?php if ($pending_list): foreach ($pending_list as $p): ?>
							<div class="card mb-2 shipment-item shadow-none border" id="item-<?= $p->no_resi ?>" data-total="<?= $p->koli ?>" data-received="0">
								<div class="card-body p-2">
									<div class="row align-items-center">
										<div class="col">
											<div class="fw-bold text-primary"><?= $p->no_resi ?></div>
											<div class="small text-muted">Asal: <?= $p->origin_agent ?></div>
										</div>
										<div class="col-auto text-end">
											<div class="h4 m-0 counter-label text-yellow">0 / <?= $p->koli ?></div>
											<div class="small text-muted" style="font-size: 10px;">Koli</div>
										</div>
									</div>
									<div class="progress progress-xs mt-2">
										<div class="progress-bar bg-primary" style="width: 0%"></div>
									</div>
								</div>
							</div>
						<?php endforeach;
					else: ?>
						<div class="text-center py-5 text-muted small border rounded bg-light" id="empty-pending">Belum ada barang arah kota ini.</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-md-6">
				<div class="text-muted small mb-2 text-uppercase fw-bold text-success">2. Berhasil Masuk Gudang</div>
				<div id="success-container" class="overflow-auto" style="max-height: 55vh;">
					<div class="text-center py-5 text-muted small border rounded bg-light" id="empty-success">Belum ada scan.</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-scanner" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Scan Inbound Koli</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-0 overflow-hidden" style="background: #000;">
				<div id="reader" style="width: 100%;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
			</div>
		</div>
	</div>
</div>

<audio id="beep_success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

<script src="https://unpkg.com/html5-qrcode"></script>


<script>
	document.addEventListener("DOMContentLoaded", function() {
		const hidInput = document.getElementById('hid_input');
		const html5QrCode = new Html5Qrcode("reader");
		const qrConfig = {
			fps: 15,
			qrbox: {
				width: 250,
				height: 250
			}
		};

		// --- LOGIC KAMERA ---
		document.getElementById('btn-camera').addEventListener('click', function() {
			html5QrCode.start({
				facingMode: "environment"
			}, qrConfig, (decodedText) => {
				// Beep & Stop Kamera
				document.getElementById('beep_success').play();
				html5QrCode.stop().then(() => {
					$('#modal-scanner').modal('hide');
					processInbound(decodedText); // Kirim ke fungsi AJAX lu
				});
			}).catch(err => {
				console.error(err);
				Swal.fire('Error', 'Kamera tidak ditemukan atau izin ditolak', 'error');
			});
		});

		// Matikan kamera kalau modal ditutup manual
		$('#modal-scanner').on('hide.bs.modal', function() {
			// Pindah fokus DULU sebelum modal benar-benar tertutup
			hidInput.focus();
		});

		$('#modal-scanner').on('hidden.bs.modal', function() {
			if (html5QrCode.isScanning) {
				html5QrCode.stop();
			}
		});

		// --- LOGIC INPUT MANUAL (Keyboard/Scanner Gun) ---
		hidInput.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				processInbound(this.value);
				this.value = '';
			}
		});

		// --- FUNGSI PROSES AJAX (Tetap Sama) ---
		function processInbound(scannedVal) {
			$.post("<?= site_url('shipment/ajax_process_inbound') ?>", {
				no_resi: scannedVal
			}, function(res) {
				if (res.status) {
					document.getElementById('beep_success').play();
					const item = document.getElementById('item-' + res.data.no_resi);
					if (item) {
						let total = parseInt(item.getAttribute('data-total'));
						let received = res.data.received;

						item.setAttribute('data-received', received);
						item.querySelector('.counter-label').innerText = received + ' / ' + total;
						item.querySelector('.progress-bar').style.width = (received / total * 100) + '%';

						if (res.is_complete) {
							moveToSuccess(item);
						}
					}

					// Toast notif biar keren
					Swal.fire({
						toast: true,
						position: 'top-end',
						icon: 'success',
						title: 'Diterima: ' + scannedVal,
						showConfirmButton: false,
						timer: 1500
					});
				} else {
					Swal.fire('Gagal', res.message, 'error');
				}
			}, 'json');
		}

		// Fungsi moveToSuccess & updateBadge lu tetep pake yang lama bro...
	});
</script>
