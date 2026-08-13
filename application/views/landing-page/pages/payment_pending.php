<?php

/**
 * landing-page/pages/payment_success.php
 *
 * $shipment = array asosiatif (M_Shipment::getResi() pakai row_array()).
 *
 * payment_status di DB cuma 3 state nyata:
 *   NULL    -> belum pernah diproses (masih nunggu callback Duitku)
 *   'PAID'  -> confirmDuitkuPayment() sukses
 *   'FAILED'-> markDuitkuFailed() (gateway nolak/gagal)
 * 'expired' BUKAN value kolom -> dihitung manual dari payment_expired_at.
 */

$rawStatus = $shipment['payment_status'] ?? null;

$isExpired = $rawStatus === null
	&& !empty($shipment['payment_expired_at'])
	&& strtotime($shipment['payment_expired_at']) < time();

if ($rawStatus === 'PAID') {
	$viewStatus = 'paid';
} elseif ($rawStatus === 'FAILED') {
	$viewStatus = 'failed';
} elseif ($isExpired) {
	$viewStatus = 'expired';
} else {
	// $rawStatus === null dan belum lewat payment_expired_at -> masih menunggu callback
	$viewStatus = 'waiting';
}

$statusMeta = [
	'paid'    => ['icon' => 'circle-check',  'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#86efac', 'title' => 'Pembayaran Berhasil!'],
	'waiting' => ['icon' => 'hourglass-half', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'title' => 'Menunggu Konfirmasi Pembayaran'],
	'failed'  => ['icon' => 'circle-xmark',  'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'title' => 'Pembayaran Gagal'],
	'expired' => ['icon' => 'clock',         'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'title' => 'Pembayaran Kedaluwarsa'],
];
$meta = $statusMeta[$viewStatus];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="container py-5">
	<div class="row justify-content-center">
		<div class="col-lg-6">

			<!-- ── STATUS CARD ── -->
			<div class="success-card" style="border-color:<?= $meta['border'] ?>;background:<?= $meta['bg'] ?>;">
				<div class="success-icon" style="color:<?= $meta['color'] ?>;">
					<i class="fa-solid fa-<?= $meta['icon'] ?>"></i>
				</div>
				<h2 class="success-title" style="color:<?= $meta['color'] ?>;"><?= $meta['title'] ?></h2>

				<?php if ($viewStatus === 'waiting'): ?>
					<p class="success-sub">
						Kami sedang menunggu konfirmasi dari sistem pembayaran untuk resi
						<strong><?= $shipment['no_resi'] ?></strong>. Halaman ini akan
						<strong>otomatis update</strong> begitu pembayaran terkonfirmasi — jangan tutup dulu.
					</p>
					<div id="waitingBadge" style="font-size:0.75rem;color:var(--grey);margin-top:-4px;margin-bottom:16px;">
						<i class="fa-solid fa-spinner fa-spin"></i> Mengecek status pembayaran...
					</div>
				<?php elseif ($viewStatus === 'paid'): ?>
					<p class="success-sub">
						Pembayaran untuk resi <strong><?= $shipment['no_resi'] ?></strong> sudah kami terima.
						Paket akan segera diproses.
					</p>
				<?php elseif ($viewStatus === 'expired'): ?>
					<p class="success-sub">
						Batas waktu pembayaran untuk resi <strong><?= $shipment['no_resi'] ?></strong> sudah lewat.
						Silakan hubungi admin untuk melakukan rebooking.
					</p>
				<?php else: // failed 
				?>
					<p class="success-sub">
						Pembayaran untuk resi <strong><?= $shipment['no_resi'] ?></strong> tidak berhasil diproses.
						Silakan hubungi admin atau coba ulang pembayaran.
					</p>
				<?php endif; ?>

				<!-- Ringkasan Pesanan -->
				<div class="order-info-card text-start mb-4">
					<div class="order-info-head">
						<i class="fa-solid fa-receipt" style="color:var(--yellow);"></i> Ringkasan Pesanan
					</div>
					<div class="order-info-body">
						<div class="info-row">
							<span class="lbl">No. Resi</span>
							<span class="val resi"><?= $shipment['no_resi'] ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Layanan</span>
							<span class="val"><?= $shipment['service_name'] ?? '-' ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Rute</span>
							<span class="val"><?= $shipment['origin'] ?> → <?= $shipment['destination'] ?></span>
						</div>
						<div class="info-row">
							<span class="lbl">Pengirim</span>
							<span class="val"><?= htmlspecialchars($shipment['sender_name']) ?></span>
						</div>
						<div class="info-row" style="background:#f9fafb;margin:-0px -18px;padding:12px 18px;margin-top:8px;border-radius:0 0 14px 14px;">
							<span class="lbl" style="font-size:0.8rem;font-weight:700;color:var(--navy);">Total Pembayaran</span>
							<span class="val" style="font-size:1.1rem;color:var(--navy-deep);">
								Rp <?= number_format((float) $shipment['total_amount'], 0, ',', '.') ?>
							</span>
						</div>
					</div>
				</div>

				<?php if (in_array($viewStatus, ['failed', 'expired'], true)): ?>
					<a href="https://wa.me/628xxxxxxxxxx?text=Halo+admin,+resi+<?= $shipment['no_resi'] ?>+bermasalah,+bisa+minta+bantuan?"
						target="_blank" class="btn-wa">
						<i class="fa-brands fa-whatsapp"></i> Hubungi Admin via WhatsApp
					</a>
				<?php endif; ?>
			</div>

		</div>
	</div>
</div>

<?php if ($viewStatus === 'waiting'): ?>
	<script>
		// Polling reload sederhana selagi masih waiting. Kasar buat traffic tinggi
		// (reload full page tiap 5 detik x jumlah user nunggu bersamaan) — kalau
		// volume kecil aman, kalau nanti concurrent user banyak ganti ke endpoint
		// AJAX status-check yang cuma return JSON, bukan reload seluruh halaman.
		(function() {
			const POLL_INTERVAL_MS = 5000;
			const MAX_POLLS = 60; // auto-stop setelah 5 menit biar nggak infinite loop
			let count = 0;

			setInterval(function() {
				count++;
				if (count > MAX_POLLS) return;
				window.location.reload();
			}, POLL_INTERVAL_MS);
		})();
	</script>
<?php endif; ?>
