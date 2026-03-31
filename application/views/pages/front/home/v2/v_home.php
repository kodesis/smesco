<!-- v_home.php -->
<style>
	:root {
		--navy: #193b5c;
		--navy-dark: #0f2740;
		--navy-light: #1e4a73;
		--slate: #4d545e;
		--slate-light: #6b7480;
		--white: #ffffff;
		--off-white: #f7f8fa;
		--border: #e2e6ea;
		--accent: #e8f0f8;
	}

	/* ── HERO ─────────────────────────────────────── */
	.smesco-hero {
		position: relative;
		overflow: hidden;
	}

	.smesco-hero .owl-carousel-item {
		height: 560px;
		background-color: var(--navy-dark);
		background-size: cover;
		background-position: center;
		position: relative;
	}

	.smesco-hero .owl-carousel-item::after {
		content: '';
		position: absolute;
		inset: 0;
		background: linear-gradient(90deg, rgba(15, 39, 64, 0.88) 0%, rgba(15, 39, 64, 0.55) 60%, rgba(15, 39, 64, 0.15) 100%);
	}

	.smesco-hero .hero-content {
		position: absolute;
		inset: 0;
		z-index: 2;
		display: flex;
		align-items: center;
	}

	.smesco-hero .hero-badge {
		display: inline-block;
		background: rgba(255, 255, 255, 0.15);
		border: 1px solid rgba(255, 255, 255, 0.25);
		color: #fff;
		font-size: 11px;
		font-weight: 600;
		letter-spacing: 2px;
		text-transform: uppercase;
		padding: 6px 14px;
		border-radius: 20px;
		margin-bottom: 18px;
	}

	.smesco-hero h1 {
		font-family: 'Roboto', sans-serif;
		font-size: 48px;
		font-weight: 700;
		color: #fff;
		line-height: 1.2;
		margin-bottom: 16px;
	}

	.smesco-hero h1 span {
		color: #7ab8e8;
	}

	.smesco-hero p.lead {
		color: rgba(255, 255, 255, 0.75);
		font-size: 16px;
		line-height: 1.7;
		margin-bottom: 28px;
		max-width: 500px;
	}

	.smesco-hero .owl-dots {
		position: absolute;
		bottom: 24px;
		left: 50%;
		transform: translateX(-50%);
		z-index: 10;
	}

	.smesco-hero .owl-dot span {
		background: rgba(255, 255, 255, 0.4) !important;
		width: 8px !important;
		height: 8px !important;
		margin: 0 4px !important;
		transition: all 0.3s !important;
	}

	.smesco-hero .owl-dot.active span {
		background: #fff !important;
		width: 24px !important;
		border-radius: 4px !important;
	}

	.btn-hero-primary {
		background: #fff;
		color: var(--navy);
		font-weight: 700;
		font-size: 14px;
		padding: 12px 28px;
		border-radius: 4px;
		border: none;
		text-decoration: none;
		transition: all 0.25s;
		display: inline-block;
	}

	.btn-hero-primary:hover {
		background: var(--accent);
		color: var(--navy);
		text-decoration: none;
		transform: translateY(-1px);
	}

	.btn-hero-outline {
		background: transparent;
		color: #fff;
		font-weight: 600;
		font-size: 14px;
		padding: 11px 28px;
		border-radius: 4px;
		border: 1.5px solid rgba(255, 255, 255, 0.5);
		text-decoration: none;
		transition: all 0.25s;
		display: inline-block;
	}

	.btn-hero-outline:hover {
		border-color: #fff;
		color: #fff;
		text-decoration: none;
		background: rgba(255, 255, 255, 0.08);
	}

	/* ── STATS BAR ────────────────────────────────── */
	.stats-bar {
		background: var(--navy);
		padding: 20px 0;
	}

	.stats-bar .stat-item {
		text-align: center;
		padding: 0 24px;
		position: relative;
	}

	.stats-bar .stat-item+.stat-item::before {
		content: '';
		position: absolute;
		left: 0;
		top: 50%;
		transform: translateY(-50%);
		height: 36px;
		width: 1px;
		background: rgba(255, 255, 255, 0.2);
	}

	.stats-bar .stat-number {
		font-family: 'Roboto', sans-serif;
		font-size: 28px;
		font-weight: 700;
		color: #fff;
		line-height: 1;
		margin-bottom: 4px;
	}

	.stats-bar .stat-label {
		font-size: 11px;
		color: rgba(255, 255, 255, 0.6);
		letter-spacing: 0.5px;
	}

	/* ── CEK ONGKIR ───────────────────────────────── */
	.cek-ongkir-section {
		background: var(--off-white);
		padding: 48px 0;
		border-bottom: 1px solid var(--border);
	}

	.cek-ongkir-card {
		background: #fff;
		border-radius: 8px;
		border: 1px solid var(--border);
		padding: 32px 36px;
		box-shadow: 0 2px 16px rgba(25, 59, 92, 0.07);
	}

	.cek-ongkir-card .section-eyebrow {
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 2px;
		text-transform: uppercase;
		color: var(--navy);
		margin-bottom: 4px;
	}

	.cek-ongkir-card h3 {
		font-family: 'Roboto', sans-serif;
		font-size: 22px;
		font-weight: 700;
		color: var(--navy-dark);
		margin-bottom: 24px;
	}

	.cek-ongkir-card .form-control,
	.cek-ongkir-card .form-select {
		border: 1px solid var(--border);
		border-radius: 4px;
		height: 46px;
		font-size: 14px;
		color: var(--slate);
		transition: border-color 0.2s;
	}

	.cek-ongkir-card .form-control:focus,
	.cek-ongkir-card .form-select:focus {
		border-color: var(--navy);
		box-shadow: 0 0 0 3px rgba(25, 59, 92, 0.1);
	}

	.cek-ongkir-card label {
		font-size: 12px;
		font-weight: 600;
		color: var(--slate);
		margin-bottom: 5px;
		display: block;
	}

	.btn-cek {
		background: var(--navy);
		color: #fff;
		font-weight: 700;
		font-size: 14px;
		padding: 11px 28px;
		border-radius: 4px;
		border: none;
		transition: all 0.2s;
		height: 46px;
		white-space: nowrap;
	}

	.btn-cek:hover {
		background: var(--navy-light);
		color: #fff;
		transform: translateY(-1px);
	}

	.btn-detail-link {
		font-size: 13px;
		color: var(--slate-light);
		text-decoration: none;
		transition: color 0.2s;
	}

	.btn-detail-link:hover {
		color: var(--navy);
		text-decoration: underline;
	}

	/* Hasil cek ongkir */
	#hasil-ongkir {
		display: none;
		margin-top: 20px;
		padding: 16px 20px;
		background: var(--accent);
		border-radius: 6px;
		border-left: 4px solid var(--navy);
	}

	#hasil-ongkir.show {
		display: block;
	}

	#hasil-ongkir .hasil-label {
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 1px;
		text-transform: uppercase;
		color: var(--slate);
		margin-bottom: 8px;
	}

	#hasil-ongkir .hasil-rute {
		font-size: 13px;
		color: var(--slate);
		margin-bottom: 12px;
	}

	#hasil-ongkir .hasil-nominal {
		font-family: 'Roboto', sans-serif;
		font-size: 32px;
		font-weight: 700;
		color: var(--navy);
		line-height: 1;
		margin-bottom: 4px;
	}

	#hasil-ongkir .hasil-detail {
		font-size: 12px;
		color: var(--slate-light);
	}

	#hasil-ongkir.error-state {
		border-left-color: #dc3545;
		background: #fff5f5;
	}

	#hasil-ongkir.error-state .hasil-nominal {
		color: #dc3545;
		font-size: 18px;
	}

	/* ── TRACKING ─────────────────────────────────── */
	.tracking-band {
		background: var(--navy-dark);
		padding: 20px 0;
	}

	.tracking-band .tracking-inner {
		display: flex;
		align-items: center;
		gap: 12px;
	}

	.tracking-band label {
		font-size: 13px;
		font-weight: 700;
		color: #fff;
		white-space: nowrap;
		margin: 0;
	}

	.tracking-band .form-control {
		border: 1px solid rgba(255, 255, 255, 0.25);
		background: rgba(255, 255, 255, 0.08);
		color: #fff;
		border-radius: 4px;
		height: 42px;
		font-size: 14px;
		flex: 1;
	}

	.tracking-band .form-control::placeholder {
		color: rgba(255, 255, 255, 0.45);
	}

	.tracking-band .form-control:focus {
		border-color: rgba(255, 255, 255, 0.6);
		background: rgba(255, 255, 255, 0.12);
		box-shadow: none;
		color: #fff;
	}

	.btn-track {
		background: #fff;
		color: var(--navy);
		font-weight: 700;
		font-size: 14px;
		padding: 0 24px;
		border-radius: 4px;
		border: none;
		height: 42px;
		white-space: nowrap;
		transition: all 0.2s;
		text-decoration: none;
		display: inline-flex;
		align-items: center;
	}

	.btn-track:hover {
		background: var(--accent);
		color: var(--navy);
		text-decoration: none;
	}

	/* ── TENTANG KAMI ─────────────────────────────── */
	.about-section {
		padding: 80px 0;
		background: #fff;
	}

	.section-eyebrow {
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 2.5px;
		text-transform: uppercase;
		color: var(--navy);
		margin-bottom: 10px;
		display: block;
	}

	.section-title {
		font-family: 'Roboto', sans-serif;
		font-size: 34px;
		font-weight: 700;
		color: var(--navy-dark);
		line-height: 1.25;
		margin-bottom: 20px;
	}

	.section-body {
		font-size: 15px;
		color: var(--slate);
		line-height: 1.8;
		margin-bottom: 28px;
	}

	.about-feat {
		display: flex;
		gap: 14px;
		align-items: flex-start;
		margin-bottom: 20px;
	}

	.about-feat-icon {
		width: 42px;
		height: 42px;
		background: var(--accent);
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
	}

	.about-feat-icon svg {
		color: var(--navy);
	}

	.about-feat-text h6 {
		font-size: 14px;
		font-weight: 700;
		color: var(--navy-dark);
		margin-bottom: 2px;
	}

	.about-feat-text p {
		font-size: 13px;
		color: var(--slate-light);
		margin: 0;
		line-height: 1.5;
	}

	.btn-navy {
		background: var(--navy);
		color: #fff;
		font-weight: 700;
		font-size: 14px;
		padding: 12px 28px;
		border-radius: 4px;
		border: none;
		text-decoration: none;
		transition: all 0.2s;
		display: inline-block;
	}

	.btn-navy:hover {
		background: var(--navy-light);
		color: #fff;
		text-decoration: none;
		transform: translateY(-1px);
	}

	/* ── LAYANAN — ICON CARDS ─────────────────────── */
	.layanan-icon-section {
		padding: 72px 0 40px;
		background: var(--off-white);
	}

	.layanan-icon-card {
		background: #fff;
		border: 1px solid var(--border);
		border-radius: 8px;
		padding: 28px 20px;
		text-align: center;
		transition: all 0.25s;
		height: 100%;
	}

	.layanan-icon-card:hover {
		border-color: var(--navy);
		transform: translateY(-3px);
		box-shadow: 0 8px 24px rgba(25, 59, 92, 0.1);
	}

	.layanan-icon-card .icon-wrap {
		width: 52px;
		height: 52px;
		background: var(--accent);
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 14px;
		transition: background 0.2s;
	}

	.layanan-icon-card:hover .icon-wrap {
		background: var(--navy);
	}

	.layanan-icon-card:hover .icon-wrap svg {
		stroke: #fff;
	}

	.layanan-icon-card .icon-wrap svg {
		stroke: var(--navy);
		transition: stroke 0.2s;
	}

	.layanan-icon-card h5 {
		font-size: 14px;
		font-weight: 700;
		color: var(--navy-dark);
		margin-bottom: 6px;
	}

	.layanan-icon-card p {
		font-size: 12px;
		color: var(--slate-light);
		line-height: 1.5;
		margin: 0;
	}

	/* ── LAYANAN — IMAGE CARDS ────────────────────── */
	.layanan-img-section {
		padding: 40px 0 72px;
		background: var(--off-white);
	}

	.layanan-img-card {
		border-radius: 8px;
		overflow: hidden;
		border: 1px solid var(--border);
		transition: all 0.25s;
		height: 100%;
		background: #fff;
	}

	.layanan-img-card:hover {
		transform: translateY(-3px);
		box-shadow: 0 8px 28px rgba(25, 59, 92, 0.12);
	}

	.layanan-img-card .img-wrap {
		overflow: hidden;
		height: 180px;
	}

	.layanan-img-card .img-wrap img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform 0.4s;
	}

	.layanan-img-card:hover .img-wrap img {
		transform: scale(1.05);
	}

	.layanan-img-card .card-body-custom {
		padding: 18px 20px 20px;
	}

	.layanan-img-card h5 {
		font-size: 15px;
		font-weight: 700;
		color: var(--navy-dark);
		margin-bottom: 6px;
	}

	.layanan-img-card p {
		font-size: 13px;
		color: var(--slate-light);
		line-height: 1.6;
		margin: 0;
	}

	/* ── JANGKAUAN ────────────────────────────────── */
	.jangkauan-section {
		padding: 80px 0;
		background: #fff;
	}

	.jangkauan-card {
		border-radius: 8px;
		overflow: hidden;
		border: 1px solid var(--border);
	}

	.jangkauan-card .jangkauan-header {
		background: var(--navy);
		padding: 12px 20px;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.jangkauan-card .jangkauan-header h6 {
		color: #fff;
		font-size: 13px;
		font-weight: 700;
		margin: 0;
		letter-spacing: 0.5px;
	}

	.jangkauan-card img {
		width: 100%;
		display: block;
	}

	.jangkauan-card .jangkauan-footer {
		background: var(--off-white);
		padding: 12px 20px;
		font-size: 12px;
		color: var(--slate);
		text-align: center;
		font-weight: 600;
		border-top: 1px solid var(--border);
	}

	/* ── CTA KEMITRAAN ────────────────────────────── */
	.kemitraan-section {
		background: var(--navy);
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}

	.kemitraan-section::before {
		content: '';
		position: absolute;
		top: -60px;
		right: -60px;
		width: 280px;
		height: 280px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.04);
	}

	.kemitraan-section::after {
		content: '';
		position: absolute;
		bottom: -80px;
		left: -40px;
		width: 200px;
		height: 200px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.03);
	}

	.kemitraan-section .eyebrow {
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 2.5px;
		text-transform: uppercase;
		color: rgba(255, 255, 255, 0.5);
		margin-bottom: 10px;
		display: block;
	}

	.kemitraan-section h2 {
		font-family: 'Roboto', sans-serif;
		font-size: 36px;
		font-weight: 700;
		color: #fff;
		margin-bottom: 16px;
		line-height: 1.2;
	}

	.kemitraan-section p {
		color: rgba(255, 255, 255, 0.65);
		font-size: 15px;
		line-height: 1.7;
		margin-bottom: 32px;
		max-width: 480px;
	}

	.kemitraan-benefits {
		display: flex;
		gap: 24px;
		flex-wrap: wrap;
		margin-bottom: 32px;
	}

	.kemitraan-benefit {
		display: flex;
		align-items: center;
		gap: 8px;
		color: rgba(255, 255, 255, 0.8);
		font-size: 13px;
		font-weight: 600;
	}

	.kemitraan-benefit::before {
		content: '';
		width: 6px;
		height: 6px;
		border-radius: 50%;
		background: #7ab8e8;
		flex-shrink: 0;
	}

	.btn-kemitraan {
		background: #fff;
		color: var(--navy);
		font-weight: 700;
		font-size: 14px;
		padding: 14px 32px;
		border-radius: 4px;
		text-decoration: none;
		transition: all 0.2s;
		display: inline-block;
	}

	.btn-kemitraan:hover {
		background: var(--accent);
		color: var(--navy);
		text-decoration: none;
		transform: translateY(-1px);
	}

	/* ── SECTION HEADER SHARED ────────────────────── */
	.section-header {
		margin-bottom: 48px;
	}

	/* ── UI jQuery Autocomplete override ─────────── */
	.ui-autocomplete {
		border: 1px solid var(--border) !important;
		border-radius: 4px !important;
		box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
		font-size: 13px !important;
	}

	.ui-menu-item-wrapper {
		padding: 8px 14px !important;
		color: var(--slate) !important;
	}

	.ui-menu-item-wrapper.ui-state-active {
		background: var(--accent) !important;
		color: var(--navy) !important;
		border: none !important;
	}

	/* ── Responsive ───────────────────────────────── */
	@media (max-width: 768px) {
		.smesco-hero .owl-carousel-item {
			height: 420px;
		}

		.smesco-hero h1 {
			font-size: 30px;
		}

		.section-title {
			font-size: 26px;
		}

		.kemitraan-section h2 {
			font-size: 26px;
		}

		.stats-bar .stat-number {
			font-size: 22px;
		}

		.tracking-band .tracking-inner {
			flex-wrap: wrap;
		}

		.cek-ongkir-card {
			padding: 24px 20px;
		}
	}
</style>

<!-- ══════════════════════════════════════════════ -->
<!-- HERO CAROUSEL                                  -->
<!-- ══════════════════════════════════════════════ -->
<section class="smesco-hero">
	<div class="owl-carousel header-carousel">
		<div class="owl-carousel-item" style="background-image: url('<?= base_url() ?>assets/front/img/1.png');">
			<div class="hero-content">
				<div class="container">
					<div class="row">
						<!-- <div class="col-lg-7 col-md-9">
							<span class="hero-badge">Smesco Express</span>
							<h1>Kirim Cepat,<br>Sampai <span>Tepat Waktu</span></h1>
							<p class="lead">Solusi pengiriman domestik & internasional terpercaya. Dari Jakarta ke seluruh Indonesia dan mancanegara.</p>
							<div class="d-flex gap-3 flex-wrap">
								<a href="<?= base_url('home/cek_ongkir') ?>" class="btn-hero-primary">Cek Ongkir</a>
								<a href="<?= base_url('home/track') ?>" class="btn-hero-outline">Lacak Kiriman</a>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
		<div class="owl-carousel-item" style="background-image: url('<?= base_url() ?>assets/front/img/2.png');">
			<div class="hero-content">
				<div class="container">
					<div class="row">
						<!-- <div class="col-lg-7 col-md-9">
							<span class="hero-badge">Pengiriman Udara</span>
							<h1>Jangkauan <span>Global</span>,<br>Layanan Lokal</h1>
							<p class="lead">Menjangkau 98% wilayah Indonesia dan 5 negara tujuan internasional dengan armada udara terpercaya.</p>
							<div class="d-flex gap-3 flex-wrap">
								<a href="<?= base_url('home/service') ?>" class="btn-hero-primary">Lihat Layanan</a>
								<a href="<?= base_url('home/about') ?>" class="btn-hero-outline">Tentang Kami</a>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
		<div class="owl-carousel-item" style="background-image: url('<?= base_url() ?>assets/front/img/3.png');">
			<div class="hero-content">
				<div class="container">
					<div class="row">
						<!-- <div class="col-lg-7 col-md-9">
							<span class="hero-badge">Kemitraan</span>
							<h1>Bergabung &<br><span>Berkembang</span> Bersama</h1>
							<p class="lead">Jadilah mitra agen Smesco Express. Raih penghasilan lebih dengan jaringan ekspedisi yang terus berkembang.</p>
							<div class="d-flex gap-3 flex-wrap">
								<a href="<?= base_url('home/agent') ?>" class="btn-hero-primary">Daftar Sekarang</a>
								<a href="<?= base_url('home/about') ?>" class="btn-hero-outline">Pelajari Lebih</a>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!-- STATS BAR                                      -->
<!-- ══════════════════════════════════════════════ -->
<div class="stats-bar">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-auto">
				<div class="d-flex align-items-center flex-wrap justify-content-center">
					<div class="stat-item">
						<div class="stat-number">98%</div>
						<div class="stat-label">Cakupan Domestik</div>
					</div>
					<div class="stat-item">
						<div class="stat-number">5+</div>
						<div class="stat-label">Negara Tujuan</div>
					</div>
					<div class="stat-item">
						<div class="stat-number">2023</div>
						<div class="stat-label">Berdiri Sejak</div>
					</div>
					<div class="stat-item">
						<div class="stat-number">24/7</div>
						<div class="stat-label">Layanan Tracking</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ══════════════════════════════════════════════ -->
<!-- CEK ONGKIR INLINE                              -->
<!-- ══════════════════════════════════════════════ -->
<section class="cek-ongkir-section">
	<div class="container">
		<div class="cek-ongkir-card">
			<div class="row align-items-start">
				<div class="col-lg-8">
					<span class="section-eyebrow">Kalkulator Ongkir</span>
					<h3>Cek Estimasi Biaya Pengiriman</h3>
					<div class="row g-3">
						<div class="col-md-4">
							<label for="lp_origin">Kota Asal</label>
							<input type="text" id="lp_origin" class="form-control"
								placeholder="Contoh: JAKARTA"
								oninput="this.value = this.value.toUpperCase()">
						</div>
						<div class="col-md-4">
							<label for="lp_destination">Kota Tujuan</label>
							<input type="text" id="lp_destination" class="form-control"
								placeholder="Contoh: SURABAYA"
								oninput="this.value = this.value.toUpperCase()">
						</div>
						<div class="col-md-4">
							<label for="lp_berat">Berat (kg)</label>
							<div class="d-flex gap-2">
								<input type="number" id="lp_berat" class="form-control"
									placeholder="Contoh: 5" min="1" step="0.5">
								<button class="btn-cek" id="lp_btn_cek" onclick="lpCekOngkir()">
									Cek
								</button>
							</div>
						</div>
					</div>

					<!-- Hasil -->
					<div id="hasil-ongkir">
						<div class="hasil-label">Estimasi Biaya</div>
						<div class="hasil-rute" id="hasil-rute"></div>
						<div class="hasil-nominal" id="hasil-nominal"></div>
						<div class="hasil-detail" id="hasil-detail"></div>
					</div>
				</div>

				<div class="col-lg-4 mt-4 mt-lg-0 d-flex flex-column justify-content-between align-items-lg-end">
					<div class="text-lg-end mb-3">
						<p class="mb-1" style="font-size:13px;color:var(--slate-light)">Butuh kalkulasi detail termasuk dimensi?</p>
						<a href="<?= base_url('home/cek_ongkir') ?>" class="btn-detail-link">
							Buka kalkulator lengkap &rarr;
						</a>
					</div>
					<div class="text-lg-end">
						<p class="mb-1" style="font-size:13px;color:var(--slate-light)">Sudah punya nomor resi?</p>
						<a href="<?= base_url('home/track') ?>" class="btn-detail-link">
							Lacak kiriman &rarr;
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!-- TENTANG KAMI                                   -->
<!-- ══════════════════════════════════════════════ -->
<section class="about-section" id="tentang">
	<div class="container">
		<div class="row g-5 align-items-center">
			<div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
				<div style="border-radius:8px;overflow:hidden;height:420px;">
					<img src="<?= base_url() ?>assets/front/img/smesco-01.png"
						alt="Smesco Express"
						style="width:100%;height:100%;object-fit:cover;">
				</div>
			</div>
			<div class="col-lg-6 wow fadeInRight" data-wow-delay="0.2s">
				<span class="section-eyebrow">Tentang Kami</span>
				<h2 class="section-title">Solusi Logistik Terpercaya sejak 2023</h2>
				<p class="section-body">
					Smesco Express hadir sejak November 2023 sebagai mitra pengiriman terpercaya untuk kebutuhan domestik maupun internasional. Kami melayani pengiriman paket, dokumen, dan kargo ke seluruh penjuru Indonesia dan mancanegara.
				</p>
				<div class="about-feat">
					<div class="about-feat-icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10" />
							<line x1="2" y1="12" x2="22" y2="12" />
							<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
						</svg>
					</div>
					<div class="about-feat-text">
						<h6>Cakupan Luas</h6>
						<p>Menjangkau 98% wilayah Indonesia dan 5 negara tujuan internasional.</p>
					</div>
				</div>
				<div class="about-feat">
					<div class="about-feat-icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
							<polyline points="17 6 23 6 23 12" />
						</svg>
					</div>
					<div class="about-feat-text">
						<h6>Pengiriman Tepat Waktu</h6>
						<p>Komitmen kami: setiap paket tiba sesuai estimasi, setiap saat.</p>
					</div>
				</div>
				<div class="about-feat">
					<div class="about-feat-icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
						</svg>
					</div>
					<div class="about-feat-text">
						<h6>Tracking Real-Time</h6>
						<p>Pantau status kiriman kapan saja dan di mana saja.</p>
					</div>
				</div>
				<a href="<?= base_url('home/about') ?>" class="btn-navy">Selengkapnya</a>
			</div>
		</div>
	</div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!-- LAYANAN — ICON OVERVIEW                        -->
<!-- ══════════════════════════════════════════════ -->
<section class="layanan-icon-section" id="layanan">
	<div class="container">
		<div class="section-header text-center">
			<span class="section-eyebrow">Layanan Kami</span>
			<h2 class="section-title">Solusi Pengiriman Lengkap</h2>
		</div>
		<div class="row g-4 justify-content-center">
			<div class="col-6 col-md-4 col-lg-2">
				<div class="layanan-icon-card">
					<div class="icon-wrap">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
						</svg>
					</div>
					<h5>Udara</h5>
					<p>Pengiriman cepat via jalur udara</p>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="layanan-icon-card">
					<div class="icon-wrap">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="1" y="3" width="15" height="13" />
							<path d="M16 8h4l3 3v5h-7V8z" />
							<circle cx="5.5" cy="18.5" r="2.5" />
							<circle cx="18.5" cy="18.5" r="2.5" />
						</svg>
					</div>
					<h5>Darat</h5>
					<p>Pengiriman andal via jalur darat</p>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="layanan-icon-card">
					<div class="icon-wrap">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
							<path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
							<line x1="12" y1="12" x2="12" y2="16" />
							<line x1="10" y1="14" x2="14" y2="14" />
						</svg>
					</div>
					<h5>Kereta</h5>
					<p>Efisien & tepat waktu via kereta</p>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="layanan-icon-card">
					<div class="icon-wrap">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
						</svg>
					</div>
					<h5>Bea Cukai</h5>
					<p>Pengurusan bea cukai internasional</p>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="layanan-icon-card">
					<div class="icon-wrap">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
							<polyline points="9 22 9 12 15 12 15 22" />
						</svg>
					</div>
					<h5>Pergudangan</h5>
					<p>Solusi pergudangan terintegrasi</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!-- LAYANAN — IMAGE CARDS                          -->
<!-- ══════════════════════════════════════════════ -->
<section class="layanan-img-section">
	<div class="container">
		<div class="row g-4">
			<div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
				<div class="layanan-img-card">
					<div class="img-wrap">
						<img src="<?= base_url() ?>assets/front/img/udara.png" alt="Pengiriman Udara">
					</div>
					<div class="card-body-custom">
						<h5>Pengiriman Udara</h5>
						<p>Layanan pengiriman udara cepat dan aman untuk kebutuhan domestik maupun internasional dengan estimasi waktu terpendek.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
				<div class="layanan-img-card">
					<div class="img-wrap">
						<img src="<?= base_url() ?>assets/front/img/darat.png" alt="Pengiriman Darat">
					</div>
					<div class="card-body-custom">
						<h5>Pengiriman Darat</h5>
						<p>Solusi pengiriman darat dengan jaringan transportasi luas yang menjangkau pelosok Indonesia secara efisien.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
				<div class="layanan-img-card">
					<div class="img-wrap">
						<img src="<?= base_url() ?>assets/front/img/kereta.png" alt="Pengiriman Kereta">
					</div>
					<div class="card-body-custom">
						<h5>Pengiriman Kereta</h5>
						<p>Alternatif pengiriman hemat dan tepat waktu via kereta api dengan jadwal yang teratur dan terprediksi.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.4s">
				<div class="layanan-img-card">
					<div class="img-wrap">
						<img src="<?= base_url() ?>assets/front/img/bc.png" alt="Bea Cukai">
					</div>
					<div class="card-body-custom">
						<h5>Layanan Bea Cukai</h5>
						<p>Penanganan proses bea cukai internasional cepat dan tepat untuk memastikan kelancaran pengiriman lintas batas.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
				<div class="layanan-img-card">
					<div class="img-wrap">
						<img src="<?= base_url() ?>assets/front/img/gudang.png" alt="Pergudangan">
					</div>
					<div class="card-body-custom">
						<h5>Solusi Pergudangan</h5>
						<p>Layanan pergudangan aman dan terintegrasi dengan sistem manajemen inventaris untuk mendukung rantai pasok bisnis Anda.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 d-flex align-items-center justify-content-center wow fadeInUp" data-wow-delay="0.6s">
				<div class="text-center p-4">
					<p style="font-size:15px;color:var(--slate);margin-bottom:16px;line-height:1.6">Tidak menemukan layanan yang Anda butuhkan?</p>
					<a href="<?= base_url('home/service') ?>" class="btn-navy">Lihat Semua Layanan</a>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!-- JANGKAUAN                                      -->
<!-- ══════════════════════════════════════════════ -->
<section class="jangkauan-section" id="jangkauan">
	<div class="container">
		<div class="section-header text-center">
			<span class="section-eyebrow">Jangkauan Kami</span>
			<h2 class="section-title">Hadir di Seluruh Indonesia<br>& Mancanegara</h2>
		</div>
		<div class="row g-4">
			<div class="col-md-6 wow fadeInLeft" data-wow-delay="0.1s">
				<div class="jangkauan-card">
					<div class="jangkauan-header">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
							<circle cx="12" cy="10" r="3" />
						</svg>
						<h6>Domestik — Indonesia</h6>
					</div>
					<img src="<?= base_url() ?>assets/front/img/map-smesco-1.png" alt="Peta Domestik">
					<div class="jangkauan-footer">Menjangkau 98% area di Indonesia</div>
				</div>
			</div>
			<div class="col-md-6 wow fadeInRight" data-wow-delay="0.2s">
				<div class="jangkauan-card">
					<div class="jangkauan-header">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10" />
							<line x1="2" y1="12" x2="22" y2="12" />
							<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
						</svg>
						<h6>Internasional</h6>
					</div>
					<img src="<?= base_url() ?>assets/front/img/map-smesco-2.png" alt="Peta Internasional">
					<div class="jangkauan-footer">Singapura &bull; Thailand &bull; Malaysia &bull; Guangzhou &bull; Taipei</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!-- CTA KEMITRAAN                                  -->
<!-- ══════════════════════════════════════════════ -->
<section class="kemitraan-section">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-7 wow fadeInLeft" data-wow-delay="0.1s">
				<span class="eyebrow">Kemitraan</span>
				<h2>Jadilah Bagian dari<br>Smesco Express</h2>
				<p>Bergabunglah sebagai mitra agen kami dan raih peluang bisnis yang menjanjikan. Kami siap mendukung pertumbuhan Anda dengan sistem, tools, dan tim yang berpengalaman.</p>
				<div class="kemitraan-benefits">
					<div class="kemitraan-benefit">Komisi kompetitif</div>
					<div class="kemitraan-benefit">Dukungan operasional</div>
					<div class="kemitraan-benefit">Sistem manajemen online</div>
					<div class="kemitraan-benefit">Jaringan luas</div>
				</div>
				<a href="<?= base_url('home/agent') ?>" class="btn-kemitraan">Daftar Sebagai Mitra</a>
			</div>
		</div>
	</div>
</section>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
	(function() {
		// ── Autocomplete origin ──────────────────────
		$("#lp_origin").autocomplete({
			source: function(req, res) {
				$.ajax({
					url: base_url + 'home/autocompleteOrigin',
					dataType: "json",
					data: {
						term: req.term
					},
					success: res
				});
			},
			minLength: 2
		});

		// ── Autocomplete destination ─────────────────
		$("#lp_destination").autocomplete({
			source: function(req, res) {
				$.ajax({
					url: base_url + 'home/autocompleteDestination',
					dataType: "json",
					data: {
						term: req.term
					},
					success: res
				});
			},
			minLength: 2
		});

		// ── Cek Ongkir ───────────────────────────────
		window.lpCekOngkir = function() {
			var origin = $("#lp_origin").val().trim();
			var destination = $("#lp_destination").val().trim();
			var berat = parseFloat($("#lp_berat").val()) || 0;
			var hasil = $("#hasil-ongkir");

			if (!origin || !destination || berat <= 0) {
				hasil.removeClass('show error-state').addClass('show error-state');
				$("#hasil-rute").text('');
				$("#hasil-nominal").text('Lengkapi semua field terlebih dahulu.');
				$("#hasil-detail").text('');
				return;
			}

			var chargeable = (berat < 10) ? 10 : berat;

			$("#lp_btn_cek").prop('disabled', true).text('Menghitung...');

			$.ajax({
				type: 'POST',
				url: base_url + 'home/getPrice',
				data: {
					origin: origin,
					destination: destination,
					jenis_pengiriman: 'D',
					chargeable: chargeable
				},
				success: function(response) {
					var data = JSON.parse(response);
					hasil.removeClass('error-state');

					if (data.harga_up && data.harga_up > 0) {
						var formatted = new Intl.NumberFormat('id-ID', {
							style: 'currency',
							currency: 'IDR',
							minimumFractionDigits: 0
						}).format(data.harga_up);

						hasil.addClass('show');
						$("#hasil-rute").text(origin + ' → ' + destination);
						$("#hasil-nominal").text(formatted);
						$("#hasil-detail").text(
							'Chargeable: ' + chargeable + ' kg' +
							' · Harga/kg: Rp ' + Number(data.per_kg).toLocaleString('id-ID')
						);
					} else {
						hasil.addClass('show error-state');
						$("#hasil-rute").text(origin + ' → ' + destination);
						$("#hasil-nominal").text('Rute tidak tersedia atau harga belum diatur.');
						$("#hasil-detail").text('Hubungi kami untuk informasi lebih lanjut.');
					}
				},
				error: function() {
					hasil.addClass('show error-state');
					$("#hasil-nominal").text('Terjadi kesalahan. Coba lagi.');
					$("#hasil-detail").text('');
				},
				complete: function() {
					$("#lp_btn_cek").prop('disabled', false).text('Cek');
				}
			});
		};

		// Enter key on berat input
		$("#lp_berat").on('keypress', function(e) {
			if (e.which === 13) lpCekOngkir();
		});
	})();
</script>
