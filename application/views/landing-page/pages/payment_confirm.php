<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- ── PROGRESS STEPS ── -->
<div class="pay-progress-bar">
	<div class="container">
		<ul class="pay-steps">
			<li class="pay-step done">
				<span class="pay-step-num"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i></span>
				<span>Booking Dibuat</span>
			</li>
			<div class="pay-step-sep"></div>
			<li class="pay-step active">
				<span class="pay-step-num">2</span>
				<span>Upload Bukti</span>
			</li>
			<div class="pay-step-sep"></div>
			<li class="pay-step">
				<span class="pay-step-num">3</span>
				<span>Verifikasi Admin</span>
			</li>
			<div class="pay-step-sep"></div>
			<li class="pay-step">
				<span class="pay-step-num">4</span>
				<span>Paket Diproses</span>
			</li>
		</ul>
	</div>
</div>

<div class="container py-5">

	<!-- ════════════════════════════════
         STATE: EXPIRED
    ════════════════════════════════ -->
	<?php if (!empty($expired)): ?>
		<div class="row justify-content-center">
			<div class="col-lg-5">
				<div class="expired-card">
					<div class="expired-icon"><i class="fa-solid fa-clock"></i></div>
					<h2 style="font-size:1.4rem;font-weight:800;color:var(--navy-deep);margin-bottom:8px;">Waktu Pembayaran Habis</h2>
					<p style="font-size:0.85rem;color:var(--grey);line-height:1.7;max-width:340px;margin:0 auto 28px;">
						Batas waktu upload bukti transfer untuk resi <strong><?= $shipment->no_resi ?></strong> sudah lewat.
						Silakan hubungi admin untuk melakukan rebooking.
					</p>
					<a href="https://wa.me/628xxxxxxxxxx?text=Halo+admin,+resi+<?= $shipment->no_resi ?>+expired,+bisa+minta+bantuan?"
						target="_blank" class="btn-wa" style="width:auto;display:inline-flex;padding:12px 24px;">
						<i class="fa-brands fa-whatsapp"></i> Hubungi Admin via WhatsApp
					</a>
				</div>
			</div>
		</div>

		<!-- ════════════════════════════════
         STATE: SUCCESS
    ════════════════════════════════ -->
	<?php elseif (!empty($success)): ?>
		<div class="row justify-content-center">
			<div class="col-lg-5">
				<div class="success-card">
					<div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
					<h2 class="success-title">Bukti Transfer Terkirim!</h2>
					<p class="success-sub">
						Bukti transfer untuk resi <strong><?= $shipment->no_resi ?></strong> berhasil diunggah.
						Admin kami akan memverifikasi pembayaran Anda dan melanjutkan proses pengiriman.
					</p>
					<!-- Ringkasan -->
					<div class="order-info-card text-start mb-4">
						<div class="order-info-head">
							<i class="fa-solid fa-receipt" style="color:var(--yellow);"></i> Ringkasan Pesanan
						</div>
						<div class="order-info-body">
							<div class="info-row">
								<span class="lbl">No. Resi</span>
								<span class="val resi"><?= $shipment->no_resi ?></span>
							</div>
							<div class="info-row">
								<span class="lbl">Pengirim</span>
								<span class="val"><?= $shipment->sender_name ?></span>
							</div>
							<div class="info-row">
								<span class="lbl">Rute</span>
								<span class="val"><?= $shipment->origin ?> → <?= $shipment->destination ?></span>
							</div>
							<div class="info-row">
								<span class="lbl">Total</span>
								<span class="val" style="color:#16a34a;font-size:0.9rem;">Rp <?= number_format($shipment->total_amount, 0, ',', '.') ?></span>
							</div>
						</div>
					</div>
					<div class="d-flex flex-column gap-2">
						<div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;font-size:0.75rem;color:#166534;text-align:left;">
							<i class="fa-solid fa-circle-info me-2"></i>
							Konfirmasi akan dikirim via WhatsApp ke nomor <strong><?= substr($shipment->sender_phone, 0, 4) . '****' . substr($shipment->sender_phone, -4) ?></strong> setelah verifikasi selesai.
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ════════════════════════════════
         STATE: FORM UPLOAD
    ════════════════════════════════ -->
	<?php else: ?>

		<!-- Flash error -->
		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="border-radius:12px;font-size:0.82rem;font-weight:600;">
				<i class="fa-solid fa-triangle-exclamation"></i>
				<?= $this->session->flashdata('error') ?>
			</div>
		<?php endif; ?>

		<div class="row g-4">

			<!-- LEFT COL: Info Pesanan + Upload -->
			<div class="col-lg-7">

				<!-- Countdown -->
				<?php if ($shipment->payment_expired_at): ?>
					<div class="countdown-wrap mb-3" id="countdownWrap">
						<div class="countdown-icon"><i class="fa-solid fa-hourglass-half"></i></div>
						<div class="flex-grow-1">
							<div class="countdown-label">Selesaikan pembayaran dalam</div>
							<div class="countdown-timer" id="countdownTimer">--:--</div>
							<div class="countdown-sub">Batas: <?= date('d M Y, H:i', strtotime($shipment->payment_expired_at)) ?> WIB</div>
						</div>
					</div>
				<?php endif; ?>

				<!-- Info Pengiriman -->
				<div class="order-info-card mb-3">
					<div class="order-info-head">
						<i class="fa-solid fa-box" style="color:var(--yellow);"></i> Detail Pengiriman
					</div>
					<div class="order-info-body">
						<div class="info-row">
							<span class="lbl">No. Resi</span>
							<span class="val resi"><?= $shipment->no_resi ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Layanan</span>
							<span class="val"><?= $shipment->service_name ?? '-' ?> <?php if ($shipment->service_code): ?><span style="font-size:0.68rem;background:var(--off);border:1px solid var(--border);border-radius:6px;padding:1px 7px;font-weight:600;color:var(--grey);"><?= $shipment->service_code ?></span><?php endif; ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Rute</span>
							<span class="val"><?= $shipment->origin ?> <i class="fa-solid fa-arrow-right" style="color:var(--yellow);font-size:0.65rem;margin:0 4px;"></i> <?= $shipment->destination ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Pengirim</span>
							<span class="val"><?= $shipment->sender_name ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Penerima</span>
							<span class="val"><?= $shipment->receiver_name ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Koli / Berat</span>
							<span class="val"><?= $shipment->koli ?> koli / <?= number_format($shipment->chargeable_weight, 1) ?> kg (chargeable)</span>
						</div>
						<div class="info-row">
							<span class="lbl">Komoditi</span>
							<span class="val"><?= $shipment->commodity_name ?? '-' ?><?php if ($shipment->commodity_detail): ?> — <span style="font-weight:500;color:var(--grey);"><?= $shipment->commodity_detail ?></span><?php endif; ?></span>
						</div>
						<div class="info-row" style="background:#f9fafb;margin:-0px -18px;padding:12px 18px;margin-top:8px;border-radius:0 0 14px 14px;">
							<span class="lbl" style="font-size:0.8rem;font-weight:700;color:var(--navy);">Total Pembayaran</span>
							<span class="val" style="font-size:1.1rem;color:var(--navy-deep);">Rp <?= number_format($shipment->total_amount, 0, ',', '.') ?></span>
						</div>
					</div>
				</div>

				<!-- Upload Form -->
				<div style="background:white;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;">
					<div class="order-info-head">
						<i class="fa-solid fa-upload" style="color:var(--yellow);"></i> Upload Bukti Transfer
					</div>
					<div class="p-4">
						<form action="<?= base_url('home/confirm_payment/' . $shipment->no_resi) ?>" method="POST" enctype="multipart/form-data" id="uploadForm">

							<div class="upload-zone mb-3" id="uploadZone">
								<input type="file" name="payment_proof" id="proofFile" accept="image/jpeg,image/jpg,image/png" required>
								<div class="upload-icon-wrap" id="uploadIconWrap">
									<i class="fa-solid fa-image"></i>
								</div>
								<div class="upload-title" id="uploadTitle">Klik atau seret foto bukti transfer</div>
								<div class="upload-sub" id="uploadSub">Pastikan nominal dan nama rekening terlihat jelas</div>
								<div class="upload-badge">JPG / PNG · Maks. 2MB</div>
							</div>

							<!-- Preview -->
							<div class="preview-wrap" id="previewWrap">
								<img id="previewImg" src="" alt="Preview">
								<button type="button" class="preview-remove" id="previewRemove" title="Hapus">
									<i class="fa-solid fa-xmark"></i>
								</button>
							</div>

							<!-- Tips -->
							<div style="background:#f8fafc;border:1.5px solid var(--border);border-radius:12px;padding:14px 16px;margin-bottom:20px;">
								<div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--navy);margin-bottom:10px;">
									<i class="fa-solid fa-lightbulb" style="color:var(--yellow);"></i> Tips Foto yang Baik
								</div>
								<div style="display:flex;flex-direction:column;gap:6px;">
									<div style="font-size:0.75rem;color:var(--grey);display:flex;gap:8px;align-items:flex-start;line-height:1.5;">
										<i class="fa-solid fa-check" style="color:#16a34a;margin-top:2px;flex-shrink:0;"></i>
										Screenshot langsung dari aplikasi mobile banking atau internet banking
									</div>
									<div style="font-size:0.75rem;color:var(--grey);display:flex;gap:8px;align-items:flex-start;line-height:1.5;">
										<i class="fa-solid fa-check" style="color:#16a34a;margin-top:2px;flex-shrink:0;"></i>
										Nominal transfer, tanggal, dan nama rekening tujuan harus terlihat
									</div>
									<div style="font-size:0.75rem;color:var(--grey);display:flex;gap:8px;align-items:flex-start;line-height:1.5;">
										<i class="fa-solid fa-xmark" style="color:#dc2626;margin-top:2px;flex-shrink:0;"></i>
										Jangan crop atau edit foto (blur, sticker, dll)
									</div>
								</div>
							</div>

							<button type="submit" class="btn-submit-transfer" id="submitBtn" disabled>
								<i class="fa-solid fa-paper-plane"></i>
								<span>Kirim Bukti Transfer</span>
							</button>
						</form>
					</div>
				</div>
			</div>

			<!-- RIGHT COL: Info Rekening -->
			<div class="col-lg-5">
				<div style="position:sticky;top:80px;">

					<!-- Rekening Card -->
					<div class="rekening-card mb-3">
						<div class="rekening-header">
							<div class="bank-logo-wrap">
								<i class="fa-solid fa-building-columns"></i>
							</div>
							<div>
								<div class="bank-name">Transfer ke rekening</div>
								<div class="bank-title">Bank BNI</div>
							</div>
						</div>

						<div class="rekening-field" style="padding-right:90px;">
							<div class="rekening-field-label">Nama Rekening</div>
							<div class="rekening-field-value" id="namaRekening">PT. Berkah Lima Sekawan</div>
						</div>

						<div class="rekening-field" style="padding-right:90px;">
							<div class="rekening-field-label">Nomor Rekening</div>
							<div class="rekening-field-value" id="noRekening">288-828-0606</div>
							<button class="btn-copy" onclick="copyText('2888280606', this)">
								<i class="fa-regular fa-copy"></i> Salin
							</button>
						</div>

						<div class="rekening-field" style="padding-right:90px;">
							<div class="rekening-field-label">Jumlah Transfer (Harus Tepat)</div>
							<div class="rekening-field-value amount" id="jumlahTransfer">
								Rp <?= number_format($shipment->total_amount, 0, ',', '.') ?>
							</div>
							<button class="btn-copy" onclick="copyText('<?= number_format($shipment->total_amount, 0, '', '') ?>', this)" style="top:auto;bottom:12px;transform:none;">
								<i class="fa-regular fa-copy"></i> Salin
							</button>
						</div>

						<div class="rekening-note">
							<i class="fa-solid fa-circle-exclamation me-1"></i>
							Transfer <strong>sesuai nominal</strong> di atas. Jika lebih/kurang, verifikasi bisa tertunda.
							Gunakan berita: <strong><?= $shipment->no_resi ?></strong>
						</div>
					</div>

					<!-- Bantuan -->
					<div style="background:white;border:1.5px solid var(--border);border-radius:14px;padding:18px 20px;">
						<div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--navy);margin-bottom:12px;">
							<i class="fa-solid fa-headset" style="color:var(--yellow);"></i> Butuh Bantuan?
						</div>
						<p style="font-size:0.78rem;color:var(--grey);line-height:1.6;margin-bottom:14px;">
							Ada kendala saat transfer atau upload bukti? Tim kami siap membantu via WhatsApp.
						</p>
						<a href="https://wa.me/628xxxxxxxxxx?text=Halo+admin,+saya+butuh+bantuan+untuk+resi+<?= $shipment->no_resi ?>"
							target="_blank" class="btn-wa">
							<i class="fa-brands fa-whatsapp"></i> Chat Admin Sekarang
						</a>
					</div>

				</div>
			</div>

		</div>
	<?php endif; ?>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	// ── COUNTDOWN TIMER ──
	<?php if (!empty($shipment->payment_expired_at) && empty($expired) && empty($success)): ?>
			(function() {
				const expiredAt = new Date("<?= date('Y-m-d\TH:i:s', strtotime($shipment->payment_expired_at)) ?>").getTime();
				const timerEl = document.getElementById('countdownTimer');
				const wrapEl = document.getElementById('countdownWrap');

				function tick() {
					const now = Date.now();
					const diff = expiredAt - now;

					if (diff <= 0) {
						timerEl.textContent = '00:00';
						timerEl.classList.add('urgent');
						wrapEl.style.borderColor = '#fecaca';
						wrapEl.style.background = '#fff5f5';
						// optional: disable form
						const form = document.getElementById('uploadForm');
						if (form) form.style.opacity = '0.5';
						return;
					}

					const m = Math.floor(diff / 60000);
					const s = Math.floor((diff % 60000) / 1000);
					timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');

					if (diff < 120000) timerEl.classList.add('urgent');
				}

				tick();
				setInterval(tick, 1000);
			})();
	<?php endif; ?>

	// ── FILE UPLOAD PREVIEW ──
	const fileInput = document.getElementById('proofFile');
	const uploadZone = document.getElementById('uploadZone');
	const previewWrap = document.getElementById('previewWrap');
	const previewImg = document.getElementById('previewImg');
	const previewRem = document.getElementById('previewRemove');
	const uploadIcon = document.getElementById('uploadIconWrap');
	const uploadTitle = document.getElementById('uploadTitle');
	const uploadSub = document.getElementById('uploadSub');
	const submitBtn = document.getElementById('submitBtn');

	function handleFile(file) {
		if (!file) return;
		if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
			alert('Format tidak didukung. Gunakan JPG atau PNG.');
			return;
		}
		if (file.size > 2 * 1024 * 1024) {
			alert('Ukuran file maksimal 2MB.');
			return;
		}

		const reader = new FileReader();
		reader.onload = function(e) {
			previewImg.src = e.target.result;
			previewWrap.style.display = 'block';
			uploadZone.classList.add('has-file');
			uploadTitle.textContent = file.name;
			uploadSub.textContent = (file.size / 1024).toFixed(0) + ' KB';
			submitBtn.disabled = false;
		};
		reader.readAsDataURL(file);
	}

	fileInput?.addEventListener('change', function() {
		handleFile(this.files[0]);
	});

	// Drag & Drop
	uploadZone?.addEventListener('dragover', function(e) {
		e.preventDefault();
		this.classList.add('drag-over');
	});
	uploadZone?.addEventListener('dragleave', function() {
		this.classList.remove('drag-over');
	});
	uploadZone?.addEventListener('drop', function(e) {
		e.preventDefault();
		this.classList.remove('drag-over');
		const dt = e.dataTransfer;
		if (dt.files.length) {
			// assign ke input
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(dt.files[0]);
			fileInput.files = dataTransfer.files;
			handleFile(dt.files[0]);
		}
	});

	// Remove preview
	previewRem?.addEventListener('click', function() {
		previewImg.src = '';
		previewWrap.style.display = 'none';
		uploadZone.classList.remove('has-file');
		uploadTitle.textContent = 'Klik atau seret foto bukti transfer';
		uploadSub.textContent = 'Pastikan nominal dan nama rekening terlihat jelas';
		fileInput.value = '';
		submitBtn.disabled = true;
	});

	// Loading state on submit
	document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
		e.preventDefault();
		const form = this;

		Swal.fire({
			title: 'Kirim Bukti Transfer?',
			text: 'Pastikan foto bukti transfer sudah jelas dan benar sebelum dikirim.',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Ya, Kirim Sekarang!',
			cancelButtonText: 'Cek Lagi',
			reverseButtons: true,
			confirmButtonColor: '#2563eb',
		}).then((result) => {
			if (result.isConfirmed) {
				submitBtn.disabled = true;
				submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Mengunggah...</span>';
				form.submit();
			}
		});
	});

	// ── COPY TO CLIPBOARD ──
	function copyText(text, btn) {
		navigator.clipboard.writeText(text).then(function() {
			const orig = btn.innerHTML;
			btn.innerHTML = '<i class="fa-solid fa-check"></i> Disalin!';
			btn.classList.add('copied');
			setTimeout(function() {
				btn.innerHTML = orig;
				btn.classList.remove('copied');
			}, 2000);
		}).catch(function() {
			// fallback
			const ta = document.createElement('textarea');
			ta.value = text;
			document.body.appendChild(ta);
			ta.select();
			document.execCommand('copy');
			document.body.removeChild(ta);
			const orig = btn.innerHTML;
			btn.innerHTML = '<i class="fa-solid fa-check"></i> Disalin!';
			btn.classList.add('copied');
			setTimeout(function() {
				btn.innerHTML = orig;
				btn.classList.remove('copied');
			}, 2000);
		});
	}
</script>
