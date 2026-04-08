<div class="page-body m-0 p-2">
	<div class="container-xl">

		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="m-0">Acceptance Warehouse</h3>
			<span class="badge bg-primary-lt" id="count_badge"><?= count($pending_list) ?> Antrean</span>
		</div>

		<div class="card mb-3 shadow-sm border-primary">
			<div class="card-body p-2">
				<ul class="nav nav-pills nav-fill mb-2" role="tablist">
					<li class="nav-item">
						<a class="nav-link active p-2" data-bs-toggle="pill" href="#tab-scanner" id="btn-mode-scanner">
							<?= tabler_icon('barcode', 'me-1') ?> Scanner
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link p-2" data-bs-toggle="pill" href="#tab-camera" id="btn-mode-camera">
							<?= tabler_icon('camera', 'me-1') ?> Kamera
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade show active" id="tab-scanner">
						<input type="text" id="hid_input" class="form-control form-control-lg text-center fw-bold border-2"
							placeholder="Tembak Piece ID di sini..." autofocus autocomplete="off">
					</div>
					<div class="tab-pane fade" id="tab-camera">
						<div id="reader" style="width: 100%;" class="rounded overflow-hidden"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="row g-2">
			<div class="col-md-6">
				<div class="text-muted small mb-2 text-uppercase fw-bold">1. Antrean Inbound (Expected)</div>
				<div id="list-container" class="overflow-auto" style="max-height: 60vh;">
					<?php if ($pending_list): foreach ($pending_list as $p): ?>
							<div class="card mb-2 shipment-item shadow-none border" id="item-<?= $p->no_resi ?>" data-total="<?= $p->koli ?>" data-received="0">
								<div class="card-body p-2">
									<div class="row align-items-center">
										<div class="col">
											<div class="fw-bold text-primary"><?= $p->no_resi ?></div>
											<div class="small text-muted"><?= $p->destination ?></div>
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
						<div class="text-center py-5 text-muted small border rounded bg-light" id="empty-pending">
							Semua barang sudah diterima.
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-md-6">
				<div class="text-muted small mb-2 text-uppercase fw-bold text-success">2. Berhasil Diterima (Success)</div>
				<div id="success-container" class="overflow-auto" style="max-height: 60vh;">
					<div class="text-center py-5 text-muted small border rounded bg-light" id="empty-success">
						Belum ada barang di-scan.
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<audio id="beep_success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>
<audio id="beep_error" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3"></audio>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
	let html5QrCode;

	document.addEventListener("DOMContentLoaded", function() {
		const hidInput = document.getElementById('hid_input');

		// Jaga fokus input alat scanner
		document.addEventListener('click', () => {
			if (document.getElementById('tab-scanner').classList.contains('active')) hidInput.focus();
		});

		// Handle Input Alat Scanner
		hidInput.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				processScan(this.value);
				this.value = '';
			}
		});

		// Handle Kamera
		document.getElementById('btn-mode-camera').addEventListener('click', startCamera);
		document.getElementById('btn-mode-scanner').addEventListener('click', stopCamera);

		function startCamera() {
			html5QrCode = new Html5Qrcode("reader");
			html5QrCode.start({
				facingMode: "environment"
			}, {
				fps: 10,
				qrbox: 250
			}, (text) => {
				processScan(text);
				stopCamera();
				setTimeout(startCamera, 1500); // Jeda biar gak double scan
			});
		}

		function stopCamera() {
			if (html5QrCode && html5QrCode.isScanning) html5QrCode.stop();
		}

		function processScan(scannedVal) {
			$.post("<?= site_url('shipment/ajax_process_acceptance') ?>", {
				no_resi: scannedVal
			}, function(res) {
				if (res.status) {
					document.getElementById('beep_success').play();

					const noResi = res.data.no_resi;
					const item = document.getElementById('item-' + noResi);

					if (item) {
						let total = parseInt(item.getAttribute('data-total'));
						let received = parseInt(item.getAttribute('data-received'));

						received++;
						item.setAttribute('data-received', received);

						// Update UI Counter & Progress
						item.querySelector('.counter-label').innerText = received + ' / ' + total;
						let percent = (received / total) * 100;
						item.querySelector('.progress-bar').style.width = percent + '%';

						// Jika sudah lengkap, PINDAHKAN ke kolom kanan
						if (received >= total) {
							moveToSuccess(item);
						}
					}

					Toastify({
						text: "BOX " + scannedVal + " OK!",
						backgroundColor: "green"
					}).showToast();
				} else {
					document.getElementById('beep_error').play();
					Swal.fire({
						icon: 'error',
						title: 'Gagal',
						text: res.message,
						timer: 1500,
						showConfirmButton: false
					});
				}
			}, 'json');
		}

		function moveToSuccess(element) {
			// Sembunyikan empty state kolom kanan
			const emptySuccess = document.getElementById('empty-success');
			if (emptySuccess) emptySuccess.remove();

			// Animasi Keluar
			element.classList.add('animate__animated', 'animate__fadeOutLeft');

			setTimeout(() => {
				element.classList.remove('animate__fadeOutLeft', 'shadow-none', 'border');
				element.classList.add('animate__animated', 'animate__fadeInRight', 'border-success', 'bg-success-lt');

				<?php $icon_check = json_encode(tabler_icon("check", "text-success")); ?>
				// Update tampilan koli jadi centang
				element.querySelector('.counter-label').innerHTML = '<?= $icon_check ?>';
				element.querySelector('.counter-label').classList.remove('text-yellow');

				// Pindah Container
				document.getElementById('success-container').prepend(element);

				updateBadgeCount();
			}, 400);
		}

		function updateBadgeCount() {
			let pendingCount = document.getElementById('list-container').querySelectorAll('.shipment-item').length;
			$('#count_badge').text(pendingCount + ' Pending');

			if (pendingCount === 0) {
				document.getElementById('list-container').innerHTML = '<div class="text-center py-5 text-muted small border rounded bg-light">Semua barang sudah diterima.</div>';
			}
		}
	});
</script>
