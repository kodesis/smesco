<!-- ── PAGE HEADER ── -->
<section class="page-header">
	<div class="container">
		<nav aria-label="breadcrumb" class="mb-3">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
				<li class="breadcrumb-item active">Tracking</li>
			</ol>
		</nav>
		<h1>Lacak Pengiriman</h1>
		<p>Pantau posisi paketmu secara realtime dari Jakarta Hub.</p>
	</div>
</section>

<!-- ── MAIN CONTENT ── -->
<div class="container pb-5">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card-tracking bg-white">

				<div class="search-wrap">
					<form action="<?= site_url('home/tracking') ?>" method="GET">
						<label class="search-label">
							<i class="bi bi-box-seam me-1"></i> Nomor Resi / AWB
						</label>
						<div class="search-input-group">
							<i class="bi bi-search search-icon"></i>
							<input name="awb"
								type="text"
								class="search-input"
								placeholder="Contoh: SMC2604120001"
								value="<?= $awb ?>"
								autocomplete="off"
								required>
							<button type="submit" class="search-btn">
								<i class="bi bi-search me-1"></i>
								<span>Lacak</span>
							</button>
						</div>
						<div class="search-hint">
							<i class="bi bi-info-circle me-1"></i>
							Masukkan nomor resi lengkap yang tertera pada bukti pengiriman kamu.
						</div>
					</form>
				</div>

				<?php if ($awb && $shipment): ?>

					<!-- Shipment Info Banner -->
					<div class="shipment-banner">
						<div class="row g-3 align-items-center">
							<div class="col-sm-3">
								<span class="info-label">Nomor Resi</span>
								<div class="info-value" style="font-size:1rem;"><?= $shipment['no_resi'] ?></div>
							</div>
							<div class="col-sm-4">
								<span class="info-label">Rute</span>
								<div class="d-flex align-items-center">
									<span class="info-value"><?= $shipment['origin'] ?></span>
									<i class="bi bi-arrow-right route-arrow"></i>
									<span class="info-value"><?= $shipment['destination'] ?></span>
								</div>
							</div>
							<div class="col-sm-3">
								<span class="info-label">Layanan</span>
								<div class="info-value" style="font-size:0.85rem;"><?= $shipment['service_name'] ?></div>
							</div>
							<div class="col-sm-2 text-sm-end">
								<span class="status-pill"><?= strtoupper($shipment['status']) ?></span>
							</div>
						</div>
					</div>

					<!-- Timeline -->
					<div class="section-title">
						<i class="bi bi-clock-history"></i> Riwayat Pengiriman
					</div>

					<?php if (!empty($history)): ?>
						<ul class="v-timeline">
							<?php foreach ($history as $key => $log): ?>
								<li class="t-item <?= $key === 0 ? 'active' : '' ?>">
									<div class="t-time">
										<?= date('d M Y, H:i', strtotime($log['created_at'])) ?> WIB
									</div>
									<div class="t-status"><?= $log['status'] ?></div>
									<?php if ($log['note']): ?>
										<div class="t-desc"><?= $log['note'] ?></div>
									<?php endif; ?>
									<!-- <?php if ($log['location']): ?>
										<div class="t-location">
											<i class="bi bi-geo-alt me-1"></i><?= $log['location'] ?>
										</div>
									<?php endif; ?> -->
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<div class="empty-state">
							<div class="icon-wrap bg-warning bg-opacity-10">
								<i class="bi bi-clock text-warning"></i>
							</div>
							<h4>Belum Ada Riwayat</h4>
							<p>Data tracking untuk resi ini belum tersedia.<br>Coba cek kembali beberapa saat lagi.</p>
						</div>
					<?php endif; ?>

				<?php elseif ($awb && !$shipment): ?>

					<!-- Not Found -->
					<div class="empty-state">
						<div class="icon-wrap" style="background: rgba(239,68,68,0.08);">
							<i class="bi bi-shield-exclamation" style="color: #ef4444;"></i>
						</div>
						<h4>Resi Tidak Ditemukan</h4>
						<p>
							Nomor resi <strong><?= $awb ?></strong> tidak terdaftar di sistem kami.<br>
							Pastikan tidak ada typo dan coba lagi ya.
						</p>
					</div>

				<?php else: ?>

					<!-- Default / Empty -->
					<div class="empty-state">
						<div class="icon-wrap" style="background: rgba(25,59,92,0.06);">
							<i class="bi bi-box-seam" style="color: var(--navy);"></i>
						</div>
						<h4>Siap Melacak?</h4>
						<p>Masukkan nomor resi Smesco Express di form atas<br>untuk memantau posisi paketmu.</p>
					</div>

				<?php endif; ?>

			</div>
		</div>
	</div>
</div>
