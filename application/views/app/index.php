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
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<?php $this->load->view('app/layouts/_style') ?>
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

</head>

<body onload="startTimeEnglish()" class="">

	<?php
	$current_user = $this->session->userdata('user'); ?>

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
						<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2-small.png" width="" alt="SMESCO Express" class="navbar-brand-image">
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
							<span class="avatar avatar-sm"><?= tabler_icon('user') ?></span>
							<div class="d-none d-xl-block ps-2">
								<div><?= $current_user['name'] ?></div>
								<div class="mt-1 small text-muted"><?= $current_user['role_name'] ?></div>
							</div>
						</a>

						<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
							<a href="<?= base_url('profile') ?>" class="dropdown-item">Profile</a>
							<a href="<?= base_url('auth/logout') ?>" class="dropdown-item">Logout</a>
						</div>
						<!-- <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
							<div class="dropdown-item text-muted small">
								<?= $current_user['email'] ?>
							</div>
							<div class="dropdown-divider"></div>
							<a href="<?= base_url('profile') ?>" class="dropdown-item">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
									viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
									<path stroke="none" d="M0 0h24v24H0z" fill="none" />
									<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
									<path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
									<path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
								</svg>
								Profil Saya
							</a>
							<div class="dropdown-divider"></div>
							<a href="<?= base_url('auth/logout') ?>" class="dropdown-item text-danger">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
									viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
									<path stroke="none" d="M0 0h24v24H0z" fill="none" />
									<path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
									<path d="M9 12h12l-3 -3m0 6l3 -3" />
								</svg>
								Logout
							</a>
						</div> -->
					</div>
				</div>
			</div>
		</header>
		<header class="navbar-expand-md">
			<div class="collapse navbar-collapse" id="navbar-menu">
				<!-- Navbar -->
				<?php $this->load->view('app/layouts/_navbar') ?>
			</div>
		</header>
		<div class="page-wrapper">

			<?php if ($this->session->flashdata('message_name')): ?>
				<div class="container-xl pt-3">
					<?= $this->session->flashdata('message_name') ?>
				</div>
			<?php endif; ?>
			<?php if ($this->session->flashdata('message_warning')): ?>
				<div class="container-xl pt-3">
					<?= $this->session->flashdata('message_warning') ?>
				</div>
			<?php endif; ?>
			<?php if ($this->session->flashdata('message_error')): ?>
				<div class="container-xl pt-3">
					<?= $this->session->flashdata('message_error') ?>
				</div>
			<?php endif; ?>

			<!-- Pages -->
			<?php $this->load->view($pages) ?>

			<footer class="footer footer-transparent d-print-none">
				<div class="container-xl">
					<div class="row text-center align-items-center flex-row-reverse">
						<div class="col-12 col-lg-auto mt-3 mt-lg-0">
							<ul class="list-inline list-inline-dots mb-0">
								<li class="list-inline-item">
									Copyright &copy; <span><?= date('Y') ?></span> Smesco Express - Template by
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
	<?php $this->load->view('app/layouts/_script') ?>
</body>

</html>
