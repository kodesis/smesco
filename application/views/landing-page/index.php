<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Smesco Express - <?= $title ?></title>
	<meta name="description" content="SMESCO Express — jasa pengiriman terpercaya untuk UMKM Indonesia. Layanan pengiriman udara, darat, ekspor impor, dan fulfillment ke seluruh Indonesia dan mancanegara." />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://smesco.kodesis.id/" />
	<meta property="og:title" content="SMESCO Express — Teman Kirim Terpercaya UMKM Indonesia" />
	<meta property="og:description" content="Layanan pengiriman udara, darat, ekspor impor, dan fulfillment untuk UMKM Indonesia. Cepat, aman, dan terpercaya." />
	<meta property="og:image" content="<?= base_url() ?>assets/logo/logo-smesco-hera-2-small.png" />
	<meta property="og:locale" content="id_ID" />
	<meta property="og:site_name" content="SMESCO Express" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="SMESCO Express — Teman Kirim Terpercaya UMKM Indonesia" />
	<meta name="twitter:description" content="Layanan pengiriman udara, darat, ekspor impor, dan fulfillment untuk UMKM Indonesia." />
	<meta name="twitter:image" content="<?= base_url() ?>assets/logo/logo-smesco-hera-2-small.png" />
	<link href="<?= base_url() ?>assets/logo/icon-smesco.png" rel="icon">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

	<?php $this->load->view('landing-page/layouts/_style') ?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />

	<!-- SESUDAH -->
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js"></script>
</head>

<body>

	<!-- ════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════ -->
	<?php
	// Cek apakah user sedang di fungsi index() pada controller Home
	$is_home = (empty($this->uri->segment(2)) || $this->uri->segment(2) === 'index');

	// Tentukan URL berdasarkan posisi user
	$link_layanan   = $is_home ? '#layanan' : base_url('home#layanan');
	$link_jangkauan = $is_home ? '#jangkauan' : base_url('home#jangkauan');
	?>
	<nav class="site-nav">
		<div class="container">
			<div class="nav-inner">
				<a href="<?= base_url('home') ?>" class="nav-brand">
					<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2-small.png" width="150" height="" alt="SMESCO Express" class="navbar-brand-image">
				</a>

				<ul class="nav-menu">
					<li><a href="<?= base_url('home') ?>">Home</a></li>
					<li><a href="<?= $link_layanan ?>">Layanan</a></li>
					<li><a href="<?= $link_jangkauan ?>">Jangkauan</a></li>
					<li><a href="<?= base_url('home/cek_ongkir') ?>">Cek Ongkir</a></li>
					<li><a href="<?= base_url('home/tracking') ?>">Lacak Resi</a></li>
				</ul>

				<div class="nav-end">
					<?php if ($this->session->userdata('is_logged_in')) : ?>
						<a href="<?= base_url('dashboard') ?>" class="btn-dark">Dashboard</a>
					<?php else : ?>
						<a href="<?= base_url('auth') ?>" class="btn-dark">Login</a>
					<?php endif; ?>
				</div>

				<!-- Hamburger Button (mobile only) -->
				<button class="nav-hamburger" id="navToggle" aria-label="Toggle menu">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</div>
	</nav>

	<!-- Mobile Drawer -->
	<div class="mobile-overlay" id="mobileOverlay"></div>
	<div class="mobile-drawer" id="mobileDrawer">
		<div class="drawer-header">
			<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2-small.png" width="130" alt="SMESCO Express">
			<button class="drawer-close" id="drawerClose" aria-label="Close menu">
				<i class="bi bi-x-lg"></i>
			</button>
		</div>
		<ul class="drawer-menu">
			<li><a href="<?= base_url('home') ?>"><i class="bi bi-house"></i> Home</a></li>
			<li><a href="<?= $link_layanan ?>"><i class="bi bi-box-seam"></i> Layanan</a></li>
			<li><a href="<?= $link_jangkauan ?>"><i class="bi bi-geo-alt"></i> Jangkauan</a></li>
			<li><a href="<?= base_url('home/tracking') ?>"><i class="bi bi-search"></i> Lacak Resi</a></li>
			<li><a href="<?= base_url('home/cek_ongkir') ?>"><i class="bi bi-calculator"></i> Cek Ongkir</a></li>
		</ul>
		<div class="drawer-footer">
			<?php if ($this->session->userdata('is_logged_in')) : ?>
				<a href="<?= base_url('dashboard') ?>" class="btn-primary-cta" style="width:100%;justify-content:center;">
					<i class="bi bi-speedometer2"></i> Dashboard
				</a>
			<?php else : ?>
				<a href="<?= base_url('auth') ?>" class="btn-primary-cta" style="width:100%;justify-content:center;">
					<i class="bi bi-box-arrow-in-right"></i> Login
				</a>
			<?php endif; ?>
		</div>
	</div>

	<?php $this->load->view($pages) ?>

	<!-- ════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════ -->
	<footer class="site-footer">
		<div class="container">
			<div class="row g-5">
				<div class="col-lg-4">
					<div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
						<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2-small.png" width="300" alt="Smesco Express">
					</div>
					<!-- UPDATED: friendlier tagline -->
					<p class="footer-tagline">
						Teman kirim terpercaya untuk UMKM Indonesia — dari dalam negeri sampai mancanegara.
					</p>
				</div>
				<div class="col-6 col-lg-2">
					<div class="footer-col-head">Layanan</div>
					<ul class="footer-links">
						<li><a href="#">Pengiriman Udara</a></li>
						<li><a href="#">Pengiriman Darat</a></li>
						<li><a href="#">Ekspor Impor</a></li>
						<li><a href="#">Asuransi Kargo</a></li>
						<li><a href="#">Fulfillment</a></li>
					</ul>
				</div>
				<div class="col-6 col-lg-2">
					<div class="footer-col-head">Perusahaan</div>
					<ul class="footer-links">
						<li><a href="#">Tentang Kami</a></li>
						<li><a href="#">Karir</a></li>
						<li><a href="#">Blog</a></li>
						<li><a href="#">Kontak</a></li>
					</ul>
				</div>
				<div class="col-lg-3">
					<div class="footer-col-head">Hubungi Kami</div>
					<ul class="footer-links">
						<li><a href="#"><i class="bi bi-whatsapp me-2" style="color:#25d366;"></i>+62 812-3456-7890</a></li>
						<li><a href="#"><i class="bi bi-envelope me-2" style="color:var(--navy);"></i>info@smesco.go.id</a></li>
						<li><a href="#"><i class="bi bi-geo-alt me-2" style="color:var(--navy);"></i>DKI Jakarta, Indonesia</a></li>
					</ul>
				</div>
			</div>
			<div class="footer-bottom">
				<span class="footer-copy">© 2026 Smesco Express. All rights reserved.</span>
				<span class="footer-copy">Teman Kirim Terpercaya untuk UMKM Indonesia</span>
			</div>
		</div>
	</footer>

	<!-- WA Float -->
	<a href="https://wa.me/6281234567890" class="wa-float" target="_blank" aria-label="WhatsApp">
		<i class="bi bi-whatsapp"></i>
	</a>

	<script>
		const navToggle = document.getElementById('navToggle');
		const mobileDrawer = document.getElementById('mobileDrawer');
		const mobileOverlay = document.getElementById('mobileOverlay');
		const drawerClose = document.getElementById('drawerClose');

		function openDrawer() {
			mobileDrawer.classList.add('open');
			mobileOverlay.classList.add('show');
			navToggle.classList.add('open');
			document.body.style.overflow = 'hidden';
		}

		function closeDrawer() {
			mobileDrawer.classList.remove('open');
			mobileOverlay.classList.remove('show');
			navToggle.classList.remove('open');
			document.body.style.overflow = '';
		}

		navToggle.addEventListener('click', openDrawer);
		drawerClose.addEventListener('click', closeDrawer);
		mobileOverlay.addEventListener('click', closeDrawer);

		// Tutup drawer saat klik link anchor
		document.querySelectorAll('.drawer-menu a').forEach(link => {
			link.addEventListener('click', closeDrawer);
		});
	</script>
</body>

</html>
