<div class="page-body">
	<div class="container-xl">
		<div class="row justify-content-center mb-4">
			<div class="col-md-8 text-center">
				<div class="card bg-primary-lt shadow-sm">
					<div class="card-body">
						<h2 class="mb-2">Input Scan Pickup</h2>
						<div class="input-group">
							<input type="text" id="scan_input" class="form-control form-control-lg fw-bold"
								style="font-size: 24px; border: 2px solid var(--tblr-primary);"
								placeholder="Scan AWB di sini..." autofocus autocomplete="off">
							<button class="btn btn-primary" type="button" id="btn-camera" data-bs-toggle="modal" data-bs-target="#modal-scanner">
								<?= tabler_icon('camera', 'icon-lg') ?>
							</button>
						</div>
						<div id="loading_scan" class="mt-2 d-none">
							<div class="spinner-border spinner-border-sm text-primary"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="card" style="height: 70vh;">
					<div class="card-header bg-dark text-white">
						<h3 class="card-title">1. Daftar Tunggu Pickup</h3>
						<div class="card-actions">
							<span class="badge bg-yellow text-dark" id="count_pending"><?= count($pending_list) ?> Resi</span>
						</div>
					</div>
					<div class="list-group list-group-flush overflow-auto" id="pending_list_container">
						<?php if ($pending_list): foreach ($pending_list as $p): ?>
								<div class="list-group-item" id="pending-<?= $p->no_resi ?>">
									<div class="row align-items-center">
										<div class="col">
											<strong class="d-block"><?= $p->no_resi ?></strong>
											<small class="text-muted d-block"><?= $p->agent_name ?></small>
											<div class="mt-1 small">
												<span class="badge bg-secondary-lt"><?= $p->koli ?> Koli</span>
												<span class="badge bg-secondary-lt"><?= $p->chargeable_weight ?> KG</span>
												<span class="text-truncate d-inline-block align-bottom ms-1" style="max-width: 150px;">
													- <?= $p->commodity_detail ?>
												</span>
											</div>
										</div>
										<div class="col-auto">
											<i class="bi bi-hourglass-split text-warning"></i>
										</div>
									</div>
								</div>
							<?php endforeach;
						else: ?>
							<div class="p-5 text-center text-muted" id="empty_pending">
								<i class="bi bi-check-circle display-4"></i><br>Semua barang sudah di-pickup!
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="card border-success" style="height: 70vh;">
					<div class="card-header bg-success text-white">
						<h3 class="card-title">2. Berhasil Pickup</h3>
						<div class="card-actions">
							<span class="badge bg-white text-success" id="count_success">0 Resi</span>
						</div>
					</div>
					<div class="list-group list-group-flush overflow-auto" id="scan_history">
						<div class="list-group-item text-center text-muted py-5" id="empty_history">
							Belum ada barang yang di-scan.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-scanner" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Scan Barcode / QR</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close-scanner"></button>
			</div>
			<div class="modal-body p-0 overflow-hidden">
				<div id="reader" style="width: 100%;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
			</div>
		</div>
	</div>
</div>

<audio id="sound_success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>
<audio id="sound_error" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3"></audio>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const input = document.getElementById('scan_input');
		const history = document.getElementById('scan_history');
		const pendingContainer = document.getElementById('pending_list_container');
		let successCount = 0;

		// --- 1. LOGIC SCANNER KAMERA ---
		const html5QrCode = new Html5Qrcode("reader");
		const qrConfig = {
			fps: 10,
			qrbox: {
				width: 250,
				height: 250
			}
		};

		document.getElementById('btn-camera').addEventListener('click', function() {
			html5QrCode.start({
					facingMode: "environment"
				}, qrConfig, onScanSuccess)
				.catch(err => {
					Swal.fire('Error', 'Kamera tidak diizinkan atau tidak ditemukan', 'error');
				});
		});

		function onScanSuccess(decodedText, decodedResult) {
			// Bunyi beep sukses dulu biar user tau udah kena
			document.getElementById('sound_success').play();

			// Stop kamera & tutup modal
			html5QrCode.stop().then(() => {
				$('#modal-scanner').modal('hide');
				processScan(decodedText); // Kirim hasil scan ke fungsi utama
			});
		}

		$('#modal-scanner').on('hidden.bs.modal', function() {
			if (html5QrCode.isScanning) {
				html5QrCode.stop();
			}
		});

		// --- 2. LOGIC INPUT MANUAL ---
		document.addEventListener('click', () => input.focus());

		input.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				const val = this.value.trim();
				if (val === '') return;
				this.value = '';
				processScan(val);
			}
		});

		// --- 3. FUNGSI UTAMA PROSES DATA (AJAX) ---
		function processScan(scannedVal) {
			$('#loading_scan').removeClass('d-none');

			$.post("<?= site_url('shipment/ajax_update_pickup') ?>", {
				no_resi: scannedVal
			}, function(res) {
				$('#loading_scan').addClass('d-none');

				if (res.status) {
					// Hapus dari kiri
					const pendingItem = document.getElementById('pending-' + res.data.no_resi);
					if (pendingItem) {
						pendingItem.remove();
						updatePendingCount();
					}
					// Tambah ke kanan
					addHistory(res.data);
					// Play sound success kalau belum diputar di onScanSuccess
					if (!html5QrCode.isScanning) document.getElementById('sound_success').play();

				} else {
					document.getElementById('sound_error').play();
					Swal.fire({
						icon: 'error',
						title: 'Gagal',
						text: res.message,
						timer: 2000,
						showConfirmButton: false
					});
				}
			}, 'json');
		}

		function addHistory(data) {
			const emptyHistory = document.getElementById('empty_history');
			if (emptyHistory) emptyHistory.remove();

			if (document.getElementById('success-' + data.no_resi)) return;

			successCount++;
			document.getElementById('count_success').innerText = successCount + ' Resi';

			const item = `
            <div class="list-group-item bg-success-lt animate__animated animate__fadeIn" id="success-${data.no_resi}">
                <div class="row align-items-center">
                    <div class="col">
                        <strong class="text-success">${data.no_resi}</strong>
                        <div class="small text-dark">
                            <b>Penerima:</b> ${data.penerima} (${data.tujuan})<br>
                            <b>Info:</b> ${data.total_koli} Koli
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success text-white border-0">TERANGKUT</span>
                    </div>
                </div>
            </div>`;
			history.insertAdjacentHTML('afterbegin', item);
		}

		function updatePendingCount() {
			const count = pendingContainer.querySelectorAll('.list-group-item').length;
			document.getElementById('count_pending').innerText = count + ' Resi';
			if (count === 0) {
				pendingContainer.innerHTML = '<div class="p-5 text-center text-muted"><i class="bi bi-check-circle display-4"></i><br>Semua barang sudah di-pickup!</div>';
			}
		}
	});
</script>
