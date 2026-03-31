<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<title><?= $title ?> | Smesco Express</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="ekspedisi, pengiriman, logistik, smesco express, cek ongkir, tracking" name="keywords">
	<meta content="Smesco Express — Solusi pengiriman domestik dan internasional terpercaya." name="description">

	<link href="<?= base_url() ?>assets/logo/icon-smesco.png" rel="icon">

	<!-- Google Web Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

	<!-- Icon Font Stylesheet -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

	<!-- Libraries Stylesheet -->
	<link href="<?= base_url() ?>assets/front/lib/animate/animate.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/front/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

	<!-- Customized Bootstrap Stylesheet -->
	<link href="<?= base_url() ?>assets/front/css/bootstrap.min.css" rel="stylesheet">

	<!-- Template Stylesheet -->
	<link href="<?= base_url() ?>assets/front/css/style.css" rel="stylesheet">

	<!-- jQuery UI (autocomplete) -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<style>
		/* ── Variabel branding ──────────────────────── */
		:root {
			--smesco-navy: #193b5c;
			--smesco-navy-dark: #0f2740;
			--smesco-slate: #4d545e;
		}

		/* ── Navbar ─────────────────────────────────── */
		.navbar {
			border-top: 4px solid var(--smesco-navy) !important;
			box-shadow: 0 2px 12px rgba(25, 59, 92, 0.08) !important;
		}

		.navbar-brand img {
			height: 36px;
			width: auto;
		}

		.navbar .nav-link {
			font-size: 14px;
			font-weight: 600;
			color: var(--smesco-slate) !important;
			padding: 28px 16px !important;
			transition: color 0.2s;
		}

		.navbar .nav-link:hover,
		.navbar .nav-link.active {
			color: var(--smesco-navy) !important;
		}

		.navbar .nav-link.active {
			border-bottom: 2px solid var(--smesco-navy);
		}

		.navbar .dropdown-menu {
			border: 1px solid #e2e6ea;
			border-radius: 4px;
			box-shadow: 0 4px 16px rgba(25, 59, 92, 0.1);
			margin-top: 0;
		}

		.navbar .dropdown-item {
			font-size: 13px;
			color: var(--smesco-slate);
			padding: 8px 16px;
			transition: background 0.15s;
		}

		.navbar .dropdown-item:hover,
		.navbar .dropdown-item.active {
			background: #e8f0f8;
			color: var(--smesco-navy);
		}

		/* Tombol Login di navbar */
		.navbar .nav-link-login {
			background: var(--smesco-navy);
			color: #fff !important;
			border-radius: 4px;
			padding: 8px 18px !important;
			margin: auto 8px auto 16px;
			font-size: 13px;
			transition: background 0.2s;
		}

		.navbar .nav-link-login:hover {
			background: #1e4a73 !important;
			color: #fff !important;
			border-bottom: none;
		}

		/* ── Footer ─────────────────────────────────── */
		.smesco-footer {
			background: #0f2740;
			color: rgba(255, 255, 255, 0.75);
			padding: 56px 0 0;
			margin-top: 0;
		}

		.smesco-footer h5 {
			color: #fff;
			font-size: 13px;
			font-weight: 700;
			letter-spacing: 1.5px;
			text-transform: uppercase;
			margin-bottom: 20px;
		}

		.smesco-footer p {
			font-size: 13px;
			line-height: 1.8;
			color: rgba(255, 255, 255, 0.6);
		}

		.smesco-footer .footer-link {
			display: block;
			font-size: 13px;
			color: rgba(255, 255, 255, 0.6);
			text-decoration: none;
			margin-bottom: 8px;
			transition: color 0.2s;
		}

		.smesco-footer .footer-link:hover {
			color: #fff;
		}

		.smesco-footer .footer-contact {
			display: flex;
			align-items: flex-start;
			gap: 10px;
			margin-bottom: 10px;
		}

		.smesco-footer .footer-contact i {
			color: rgba(255, 255, 255, 0.4);
			font-size: 13px;
			margin-top: 2px;
			flex-shrink: 0;
		}

		.smesco-footer .footer-contact span {
			font-size: 13px;
			color: rgba(255, 255, 255, 0.6);
			line-height: 1.6;
		}

		.smesco-footer .footer-bottom {
			border-top: 1px solid rgba(255, 255, 255, 0.08);
			padding: 16px 0;
			margin-top: 40px;
		}

		.smesco-footer .footer-bottom p {
			font-size: 12px;
			color: rgba(255, 255, 255, 0.35);
			margin: 0;
		}

		.smesco-footer .social-btn {
			width: 34px;
			height: 34px;
			border-radius: 50%;
			border: 1px solid rgba(255, 255, 255, 0.2);
			display: inline-flex;
			align-items: center;
			justify-content: center;
			color: rgba(255, 255, 255, 0.5);
			text-decoration: none;
			font-size: 13px;
			transition: all 0.2s;
		}

		.smesco-footer .social-btn:hover {
			border-color: rgba(255, 255, 255, 0.6);
			color: #fff;
		}

		/* ── Misc helper ─────────────────────────────── */
		.bs4-order-tracking-2 {
			margin-bottom: 30px;
			overflow: hidden;
			color: #878788;
			padding-left: 0px;
			margin-top: 30px
		}

		.bs4-order-tracking-2 li {
			list-style-type: none;
			font-size: 13px;
			width: 25%;
			float: left;
			position: relative;
			font-weight: 400;
			color: #878788;
			text-align: center;
		}

		.bs4-order-tracking-2 li>div {
			color: #fff;
			width: 80px;
			text-align: center;
			line-height: 80px;
			display: block;
			font-size: 12px;
			background: #878788;
			border-radius: 50%;
			margin: auto
		}

		.bs4-order-tracking-2 li:after {
			content: '';
			width: 150%;
			height: 2px;
			background: #878788;
			position: absolute;
			left: 0%;
			right: 0%;
			z-index: -1;
			top: 30%;
		}

		.bs4-order-tracking-2 li:first-child:after {
			left: 50%
		}

		.bs4-order-tracking-2 li:last-child:after {
			left: 0% !important;
			width: 0% !important
		}

		.bs4-order-tracking-2 li:first-child:before {
			margin-left: 15px !important;
			padding-left: 11px !important;
			text-align: left !important
		}

		.bs4-order-tracking-2 li:last-child:before {
			margin-right: 5px !important;
			padding-right: 11px !important;
			text-align: right !important
		}

		.bs4-order-tracking-2 li.active {
			font-weight: bold;
			color: #fe0a26
		}

		.bs4-order-tracking-2 li.active>div {
			background: #fe0a26
		}

		.bs4-order-tracking-2 li.active:after {
			background: #fe0a26
		}

		.radio-container {
			display: grid;
			gap: 20px;
		}

		.radio-card {
			display: inline-block;
			width: 100%;
			padding: 25px;
			border: 1px solid #e0e0e0;
			border-radius: 10px;
			cursor: pointer;
			transition: border-color 0.3s;
			background-color: #fff;
		}

		.radio-card input[type="radio"] {
			display: none;
		}

		.radio-card .card-content {
			display: flex;
			align-items: center;
		}

		.radio-card .card-icon {
			width: 50px;
			margin-right: 15px;
		}

		.radio-card .card-text h4 {
			margin: 0;
			font-size: 18px;
			font-weight: bold;
		}

		.radio-card .card-text p {
			margin: 0;
			font-size: 14px;
			color: #888;
		}

		.radio-card:hover {
			border-color: #ccc;
		}

		.radio-card input[type="radio"]:checked+.card-content {
			border: 2px solid #ff0000;
			border-radius: 10px;
		}

		.radio-card input[type="radio"]:checked+.card-content h4 {
			color: #000;
		}
	</style>
</head>

<body>
	<!-- Spinner Start -->
	<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
		<div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
			<span class="sr-only">Loading...</span>
		</div>
	</div>
	<!-- Spinner End -->

	<div class="flash-data" data-flashdata="<?= $this->session->flashdata('message_name') ?>"></div>
	<div class="flash-data-error" data-flashdata="<?= $this->session->flashdata('message_error') ?>"></div>

	<!-- ══════════════════════════════════════════ -->
	<!-- NAVBAR                                     -->
	<!-- ══════════════════════════════════════════ -->
	<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
		<a href="<?= base_url() ?>" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
			<img src="<?= base_url() ?>assets/logo/logo-smesco-01.png"
				alt="Smesco Express"
				onerror="this.onerror=null;this.src='https://smesco.go.id/assets/logo.svg'">
		</a>
		<button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarCollapse">
			<div class="navbar-nav ms-auto p-4 p-lg-0 align-items-lg-center">
				<a href="<?= base_url() ?>"
					class="nav-item nav-link <?= ($segment == 'home') ? 'active' : '' ?>">Beranda</a>

				<a href="<?= base_url('home/about') ?>"
					class="nav-item nav-link <?= ($segment == 'about') ? 'active' : '' ?>">Tentang</a>

				<!-- Dropdown: Layanan -->
				<div class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle <?= in_array($segment, ['service', 'pricelist']) ? 'active' : '' ?>"
						data-bs-toggle="dropdown">Layanan</a>
					<div class="dropdown-menu rounded-0 m-0">
						<a href="<?= base_url('home/service') ?>"
							class="dropdown-item <?= ($segment == 'service') ? 'active' : '' ?>">Layanan Kami</a>
						<a href="<?= base_url('home/pricelist') ?>"
							class="dropdown-item <?= ($segment == 'pricelist') ? 'active' : '' ?>">Daftar Harga</a>
					</div>
				</div>

				<!-- Dropdown: Info Pengiriman -->
				<div class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle <?= in_array($segment, ['track', 'cek_ongkir', 'outlet']) ? 'active' : '' ?>"
						data-bs-toggle="dropdown">Info Pengiriman</a>
					<div class="dropdown-menu rounded-0 m-0">
						<a href="<?= base_url('home/track') ?>"
							class="dropdown-item <?= ($segment == 'track') ? 'active' : '' ?>">Tracking</a>
						<a href="<?= base_url('home/cek_ongkir') ?>"
							class="dropdown-item <?= ($segment == 'cek_ongkir') ? 'active' : '' ?>">Cek Ongkir</a>
						<a href="<?= base_url('home/outlet') ?>"
							class="dropdown-item <?= ($segment == 'outlet') ? 'active' : '' ?>">Outlet Kami</a>
					</div>
				</div>

				<a href="<?= base_url('home/agent') ?>"
					class="nav-item nav-link <?= ($segment == 'agent') ? 'active' : '' ?>">Kemitraan</a>

				<a href="<?= base_url('auth') ?>" class="nav-item nav-link nav-link-login">
					<?= ($this->session->userdata('is_logged_in')) ? 'Dashboard' : 'Login' ?>
				</a>
			</div>
		</div>
	</nav>

	<!-- ══════════════════════════════════════════ -->
	<!-- PAGE CONTENT                               -->
	<!-- ══════════════════════════════════════════ -->
	<?php $this->load->view($pages) ?>

	<!-- ══════════════════════════════════════════ -->
	<!-- FOOTER                                     -->
	<!-- ══════════════════════════════════════════ -->
	<footer class="smesco-footer wow fadeIn" data-wow-delay="0.1s">
		<div class="container">
			<div class="row g-5">

				<!-- Kolom 1: Brand + Kontak -->
				<div class="col-lg-4 col-md-6">
					<img src="<?= base_url() ?>assets/logo/logo-smesco-01.png"
						alt="Smesco Express"
						style="height:32px;margin-bottom:16px;"
						onerror="this.style.display='none'">
					<p style="margin-bottom:20px;">
						Solusi pengiriman domestik & internasional terpercaya sejak November 2023.
					</p>
					<div class="footer-contact">
						<i class="fa fa-map-marker-alt"></i>
						<span>Jl. Andara Raya No.1 A, Pondok Labu, Cilandak, Jakarta Selatan 12450</span>
					</div>
					<div class="footer-contact">
						<i class="fa fa-phone-alt"></i>
						<span>+62 812 3456 7890</span>
					</div>
					<div class="footer-contact">
						<i class="fa fa-envelope"></i>
						<span>info@smesco-express.id</span>
					</div>
					<div class="d-flex gap-2 mt-3">
						<a href="#" class="social-btn" target="_blank"><i class="fab fa-instagram"></i></a>
						<a href="#" class="social-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
					</div>
				</div>

				<!-- Kolom 2: Layanan -->
				<div class="col-lg-2 col-md-6">
					<h5>Layanan</h5>
					<a href="<?= base_url('home/service') ?>" class="footer-link">Pengiriman Udara</a>
					<a href="<?= base_url('home/service') ?>" class="footer-link">Pengiriman Darat</a>
					<a href="<?= base_url('home/service') ?>" class="footer-link">Pengiriman Kereta</a>
					<a href="<?= base_url('home/service') ?>" class="footer-link">Bea Cukai</a>
					<a href="<?= base_url('home/service') ?>" class="footer-link">Pergudangan</a>
				</div>

				<!-- Kolom 3: Tautan Cepat -->
				<div class="col-lg-2 col-md-6">
					<h5>Perusahaan</h5>
					<a href="<?= base_url('home/about') ?>" class="footer-link">Tentang Kami</a>
					<a href="<?= base_url('home/agent') ?>" class="footer-link">Kemitraan</a>
					<a href="<?= base_url('home/outlet') ?>" class="footer-link">Outlet Kami</a>
					<a href="<?= base_url('home/pricelist') ?>" class="footer-link">Daftar Harga</a>
				</div>

				<!-- Kolom 4: Info Pengiriman -->
				<div class="col-lg-4 col-md-6">
					<h5>Info Pengiriman</h5>
					<a href="<?= base_url('home/track') ?>" class="footer-link">Lacak Kiriman</a>
					<a href="<?= base_url('home/cek_ongkir') ?>" class="footer-link">Cek Ongkir</a>
					<div style="margin-top:20px;padding:16px;background:rgba(255,255,255,0.05);border-radius:6px;border:1px solid rgba(255,255,255,0.08);">
						<p style="font-size:12px;margin-bottom:8px;color:rgba(255,255,255,0.5);">Lacak kiriman Anda</p>
						<div class="d-flex gap-2">
							<input type="text"
								id="footer-resi"
								class="form-control form-control-sm"
								placeholder="Masukkan no. resi"
								style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:#fff;font-size:13px;">
							<a id="footer-track-btn"
								href="<?= base_url('home/track') ?>"
								class="btn btn-sm"
								style="background:#fff;color:#193b5c;font-weight:700;white-space:nowrap;font-size:13px;padding:0 14px;">
								Track
							</a>
						</div>
					</div>
				</div>

			</div>
		</div>

		<!-- Footer Bottom -->
		<div class="footer-bottom">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-6">
						<p>Copyright &copy; <span id="tahun"></span> Smesco Express. All rights reserved.</p>
					</div>
					<div class="col-md-6 text-md-end">
						<p>Designed by <a href="https://htmlcodex.com" style="color:rgba(255,255,255,0.35);text-decoration:none;">HTML Codex</a></p>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- Back to Top -->
	<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top">
		<i class="bi bi-arrow-up"></i>
	</a>

	<script>
		var base_url = "<?= base_url() ?>";
	</script>

	<!-- JavaScript Libraries -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="<?= base_url() ?>assets/front/lib/wow/wow.min.js"></script>
	<script src="<?= base_url() ?>assets/front/lib/easing/easing.min.js"></script>
	<script src="<?= base_url() ?>assets/front/lib/waypoints/waypoints.min.js"></script>
	<script src="<?= base_url() ?>assets/front/lib/counterup/counterup.min.js"></script>
	<script src="<?= base_url() ?>assets/front/lib/owlcarousel/owl.carousel.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="<?= base_url() ?>assets/front/js/main.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="<?= base_url() ?>assets/dashboard/js/jquery.mask.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/vendor/select2/js/select2.min.js"></script>

	<script>
		$(function() {
			// ── Tahun copyright otomatis ─────────────
			$('#tahun').text(new Date().getFullYear());

			// ── Hero carousel ────────────────────────
			// Override init dari main.js jika ada,
			// atau init manual kalau main.js belum handle .header-carousel
			if ($('.header-carousel').length && !$('.header-carousel').hasClass('owl-loaded')) {
				$('.header-carousel').owlCarousel({
					autoplay: true,
					autoplayTimeout: 5000,
					autoplayHoverPause: true,
					smartSpeed: 800,
					loop: true,
					dots: true,
					nav: false,
					items: 1,
					animateOut: 'fadeOut'
				});
			}

			// ── Footer tracking shortcut ─────────────
			$('#footer-track-btn').on('click', function(e) {
				var resi = $('#footer-resi').val().trim();
				if (resi) {
					e.preventDefault();
					window.location.href = base_url + 'home/track/' + encodeURIComponent(resi);
				}
			});

			$('#footer-resi').on('keypress', function(e) {
				if (e.which === 13) {
					var resi = $(this).val().trim();
					if (resi) {
						window.location.href = base_url + 'home/track/' + encodeURIComponent(resi);
					}
				}
			});
		});
	</script>

</body>

</html>
