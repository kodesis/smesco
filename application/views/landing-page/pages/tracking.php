<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"> -->

<style>
	/* Pakai Token Warna Lu Bro */
	:root {
		--navy: #193b5c;
		--navy-deep: #0f2740;
		--yellow: #ffcf26;
		--off: #f4f6f9;
		--border: #e2e6eb;
		--grey: #4d545e;
	}

	body {
		font-family: 'Inter', sans-serif;
		background: var(--off);
	}

	/* ── BREADCRUMB HEADER ── */
	.page-header {
		position: relative;
		background: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2070&auto=format&fit=crop');
		/* Gambar Gudang/Logistik */
		background-size: cover;
		background-position: center;
		padding: 100px 0 60px;
		color: white;
	}

	.page-header::before {
		content: '';
		position: absolute;
		inset: 0;
		background: linear-gradient(to right, rgba(15, 39, 64, 0.95), rgba(15, 39, 64, 0.7));
		z-index: 1;
	}

	.page-header .container {
		position: relative;
		z-index: 2;
	}

	.breadcrumb-item+.breadcrumb-item::before {
		color: rgba(255, 255, 255, 0.5);
	}

	.breadcrumb-item a {
		color: var(--yellow);
		text-decoration: none;
		font-weight: 600;
	}

	.breadcrumb-item.active {
		color: white;
		opacity: 0.8;
	}

	/* ── TIMELINE ── */
	.card-tracking {
		border: none;
		border-radius: 20px;
		margin-top: -40px;
		box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
		z-index: 10;
		position: relative;
	}

	.v-timeline {
		position: relative;
		padding-left: 40px;
		list-style: none;
	}

	.v-timeline::before {
		content: '';
		position: absolute;
		left: 15px;
		top: 10px;
		bottom: 10px;
		width: 2px;
		background: var(--border);
	}

	.t-item {
		position: relative;
		margin-bottom: 30px;
	}

	.t-item::before {
		content: '';
		position: absolute;
		left: -33px;
		top: 5px;
		width: 14px;
		height: 14px;
		background: white;
		border: 3px solid var(--border);
		border-radius: 50%;
		transition: 0.3s;
	}

	.t-item.active::before {
		background: var(--yellow);
		border-color: var(--navy);
		box-shadow: 0 0 0 5px rgba(255, 207, 38, 0.2);
	}

	.t-status {
		font-weight: 800;
		color: var(--navy);
		font-size: 1rem;
	}

	.t-time {
		font-size: 0.75rem;
		color: var(--grey);
		font-weight: 600;
		margin-bottom: 5px;
	}

	.t-desc {
		font-size: 0.85rem;
		color: var(--grey);
		opacity: 0.8;
	}

	.btn-back {
		border-radius: 10px;
		font-weight: 700;
		transition: 0.3s;
	}

	.btn-back:hover {
		background: var(--navy);
		color: white;
	}

	ol,
	ul {
		padding-left: 0;
	}

	dl,
	ol,
	ul {
		margin-top: 0;
		margin-bottom: 0;
	}
</style>

<section class="page-header">
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
				<li class="breadcrumb-item active" aria-current="page">Tracking</li>
			</ol>
		</nav>
		<h1 class="display-5 fw-900">Lacak Pengiriman</h1>
		<p class="text-white-50">Cek posisi paketmu secara realtime dari Jakarta Hub.</p>
	</div>
</section>

<div class="container pb-5">
	<div class="row justify-content-center">
		<div class="col-lg-9">
			<div class="card card-tracking bg-white p-4 p-md-5">

				<div class="search-container mb-5 pb-4 border-bottom">
					<form action="<?= site_url('home/tracking') ?>" method="GET">
						<div class="row g-2 align-items-end">
							<div class="col">
								<label class="field-label mb-2">Lacak Resi Lainnya</label>
								<input name="awb" class="field-input" type="text" placeholder="Masukkan nomor resi..." value="<?= $awb ?>" required />
							</div>
							<div class="col-auto">
								<button type="submit" class="btn-dark" style="padding: 13px 25px; border-radius: 10px;">
									<i class="bi bi-search me-2"></i> Lacak
								</button>
							</div>
						</div>
					</form>
				</div>

				<?php if ($awb && $shipment): ?>
					<div class="row mb-5 align-items-center bg-light p-4 rounded-4 g-3 border-start border-4 border-navy">
						<div class="col-md-3">
							<label class="small text-uppercase fw-700 text-muted d-block">Nomor Resi</label>
							<span class="h5 fw-800 text-navy mb-0"><?= $shipment['no_resi'] ?></span>
						</div>

						<div class="col-md-5">
							<label class="small text-uppercase fw-700 text-muted d-block">Rute Pengiriman</label>
							<div class="d-flex align-items-center">
								<span class="h6 fw-800 text-navy mb-0"><?= $shipment['origin'] ?></span>
								<i class="bi bi-arrow-right mx-3 text-warning fw-bold" style="font-size: 1.2rem;"></i>
								<span class="h6 fw-800 text-navy mb-0"><?= $shipment['destination'] ?></span>
							</div>
						</div>

						<div class="col-md-2">
							<label class="small text-uppercase fw-700 text-muted d-block">Layanan</label>
							<span class="h6 fw-700 text-navy mb-0"><?= $shipment['service_name'] ?></span>
						</div>

						<div class="col-md-2 text-md-end">
							<span class="badge bg-navy text-yellow px-3 py-2 rounded-pill fw-700">
								<?= strtoupper($shipment['status']) ?>
							</span>
						</div>
					</div>

					<ul class="v-timeline">
						<?php if (!empty($history)): ?>
							<?php foreach ($history as $key => $log): ?>
								<li class="t-item <?= ($key == 0) ? 'active' : '' ?>">
									<div class="t-time"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?> WIB</div>
									<div class="t-status <?= ($key == 0) ? 'text-navy' : '' ?>">
										<?= $log['status'] ?>
									</div>
									<div class="t-desc">
										<?= $log['note'] ?>
										<?php if ($log['location']): ?>
											<br><small class="text-muted"><i class="bi bi-geo-alt"></i> <?= $log['location'] ?></small>
										<?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						<?php else: ?>
							<div class="text-center py-4">
								<p class="text-muted italic">Data riwayat belum tersedia.</p>
							</div>
						<?php endif; ?>
					</ul>

				<?php elseif ($awb && !$shipment): ?>
					<div class="text-center py-5">
						<div class="bg-warning bg-opacity-10 p-5 rounded-5">
							<i class="bi bi-shield-exclamation display-2 text-warning mb-4"></i>
							<h3 class="fw-800 text-navy">Resi Tidak Terdaftar</h3>
							<p class="mb-0">Maaf bro, nomor resi <strong><?= $awb ?></strong> nggak ada di sistem Jakarta Hub. Coba cek lagi typo-nya ya.</p>
						</div>
					</div>

				<?php else: ?>
					<div class="text-center py-5">
						<i class="bi bi-box-seam display-2 text-light mb-4"></i>
						<h3 class="fw-800 text-navy">Siap Melacak?</h3>
						<p>Masukkan nomor resi Smesco Express pada form di atas untuk memantau paketmu.</p>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>
