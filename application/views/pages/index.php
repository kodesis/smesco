<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
	<!-- Head -->
	<?php $this->load->view('pages/layouts/_style') ?>
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>

<body onload="startTimeEnglish()" class="">

	<?php
	$user = $this->M_Auth->cek_user($this->session->userdata('username'));
	$role = $this->M_Auth->cek_role($user['role_id']);
	?>

	<div class="flash-data" data-flashdata="<?= $this->session->flashdata('message_name') ?>"></div>
	<div class="flash-data-error" data-flashdata="<?= $this->session->flashdata('message_error') ?>"></div>

	<script src="<?= base_url() ?>assets/dashboard/js/demo-theme.min.js?1684106062"></script>

	<div class="page">
		<!-- Navbar -->
		<header class="navbar navbar-expand-md d-print-none">
			<div class="container-xl">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
					<a href="<?= base_url() ?>" target="_blank">
						<img src="https://smesco.go.id/assets/logo.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image">
					</a>
				</h1>
				<div class="navbar-nav flex-row order-md-last">
					<div class="nav-item d-none d-md-flex me-3">
						<span id="timer" class="ms-3 d-none d-md-block"></span>
					</div>
					<div class="d-none d-md-flex">
						<a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
							<!-- Download SVG icon from http://tabler-icons.io/i/moon -->
							<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none" />
								<path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
							</svg>
						</a>
						<a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
							<!-- Download SVG icon from http://tabler-icons.io/i/sun -->
							<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none" />
								<path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
								<path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
							</svg>
						</a>
						<div class="nav-item dropdown d-none d-md-flex me-3">
						</div>
					</div>
					<div class="nav-item dropdown">
						<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
							<span class="avatar avatar-sm" style="background-image: url(<?= base_url() ?>assets/img/profile/<?= $user['image'] ?>)"></span>
							<div class="d-none d-xl-block ps-2">
								<div><?= $user['name'] ?></div>
								<div class="mt-1 small text-muted"><?= $role['role'] ?></div>
							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

							<a href="<?= base_url('auth/logout') ?>" class="dropdown-item btn-logout">Logout</a>
						</div>
					</div>
				</div>
			</div>
		</header>
		<header class="navbar-expand-md">
			<div class="collapse navbar-collapse" id="navbar-menu">
				<!-- Navbar -->
				<?php $this->load->view('pages/layouts/_navbar') ?>
			</div>
		</header>
		<div class="page-wrapper">

			<!-- Pages -->
			<?php $this->load->view($pages) ?>

			<footer class="footer footer-transparent d-print-none">
				<div class="container-xl">
					<div class="row text-center align-items-center flex-row-reverse">
						<div class="col-12 col-lg-auto mt-3 mt-lg-0">
							<ul class="list-inline list-inline-dots mb-0">
								<li class="list-inline-item">
									Copyright &copy; <span id="tahun"></span> Smesco Express - Template by
									<a href="." class="link-secondary">Tabler</a>.
									All rights reserved.
								</li>
							</ul>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<!-- Script -->
	<?php $this->load->view('pages/layouts/_script') ?>
</body>

</html>
