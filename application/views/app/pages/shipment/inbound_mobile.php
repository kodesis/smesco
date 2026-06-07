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
	<input type="file" id="camera_input" accept="image/*" capture="environment" class="d-none">
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

		// --- LOGIC KAMERA QR READER ---
		document.getElementById('btn-camera').addEventListener('click', function() {
			html5QrCode.start({
				facingMode: "environment"
			}, qrConfig, (decodedText) => {
				document.getElementById('beep_success').play();
				html5QrCode.stop().then(() => {
					$('#modal-scanner').modal('hide');
					processInbound(decodedText);
				});
			}).catch(err => {
				console.error(err);
				Swal.fire('Error', 'Kamera tidak ditemukan atau izin ditolak', 'error');
			});
		});

		$('#modal-scanner').on('hide.bs.modal', function() {
			hidInput.focus();
		});

		$('#modal-scanner').on('hidden.bs.modal', function() {
			if (html5QrCode.isScanning) {
				html5QrCode.stop();
			}
		});

		// --- LOGIC INPUT MANUAL / SCANNER GUN ---
		hidInput.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				processInbound(this.value);
				this.value = '';
			}
		});

		let tempBarcode = ''; // Variabel penampung

		// --- FUNGSI TRIGGER SCAN & BUKA KAMERA NATIVE ---
		function processInbound(scannedVal) {
			tempBarcode = scannedVal;
			document.getElementById('beep_success').play();

			if ($('#modal-scanner').is(':visible')) {
				html5QrCode.stop().then(() => {
					$('#modal-scanner').modal('hide');
					$('#camera_input').click();
				});
			} else {
				$('#camera_input').click();
			}
		}

		// --- KETIKA SELESAI FOTO & PROSES AJAX ---
		$('#camera_input').on('change', function() {
			// Memastikan file benar-benar tertangkap dari hardware kamera HP
			if (!this.files || this.files.length === 0) {
				return;
			}

			let fileTarget = this.files;
			if (!fileTarget) return;

			Swal.fire({
				title: 'Mengupload...',
				text: 'Menyimpan data inbound dan bukti foto',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			// Masukkan ke FormData secara aman
			let formData = new FormData();
			formData.append('no_resi', tempBarcode); // tempBarcode berisi nomor resi/karung aktif
			formData.append('photo', fileTarget[0]); // Menyuntikkan file gambar asli

			$.ajax({
				url: "<?= site_url('shipment/ajax_process_inbound') ?>",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(res) {
					$('#camera_input').val(''); // Reset input kamera agar bisa dipakai scan ulang

					if (res.status) {
						Swal.fire({
							toast: true,
							position: 'top-end',
							icon: 'success',
							title: res.message,
							showConfirmButton: false,
							timer: 1500
						});

						if (res.is_koli) {
							setTimeout(() => {
								location.reload();
							}, 1500);
						} else {
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
						}
						hidInput.focus();
					} else {
						Swal.fire('Gagal', res.message, 'error');
					}
				},
				error: function() {
					$('#camera_input').val('');
					Swal.fire('Error', 'Koneksi ke server terputus saat upload foto.', 'error');
				}
			});
		});

		// --- FUNGSI UI MINDAN CARD KE LIST SUKSES ---
		function moveToSuccess(itemNode) {
			const successContainer = document.getElementById('success-container');
			const emptySuccess = document.getElementById('empty-success');

			if (emptySuccess) emptySuccess.remove();

			// Ubah style card jadi hijau (Sukses)
			itemNode.querySelector('.progress-bar').classList.remove('bg-primary');
			itemNode.querySelector('.progress-bar').classList.add('bg-success');
			itemNode.classList.add('border-success');
			itemNode.classList.remove('border');

			// Pindah elemen HTML ke div sebelah kanan
			successContainer.prepend(itemNode);

			updateBadge();
		}

		// --- FUNGSI UPDATE ANGKA BADGE ANTREAN ---
		function updateBadge() {
			const pendingCount = document.querySelectorAll('#list-container .shipment-item').length;
			document.getElementById('count_badge').innerText = pendingCount + ' Antrean';

			if (pendingCount === 0) {
				document.getElementById('list-container').innerHTML = '<div class="text-center py-5 text-muted small border rounded bg-light" id="empty-pending">Semua barang telah diterima.</div>';
			}
		}

	});
</script>
