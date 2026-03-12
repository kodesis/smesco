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
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<title>Page 404 - Smesco Express.</title>
	<link href="<?= base_url() ?>assets/logo/icon-smesco.png" rel="icon">
	<!-- CSS files -->
	<link href="<?= base_url() ?>assets/dashboard/css/tabler.min.css?1684106062" rel="stylesheet" />
	<link href="<?= base_url() ?>assets/dashboard/css/tabler-flags.min.css?1684106062" rel="stylesheet" />
	<link href="<?= base_url() ?>assets/dashboard/css/tabler-payments.min.css?1684106062" rel="stylesheet" />
	<link href="<?= base_url() ?>assets/dashboard/css/tabler-vendors.min.css?1684106062" rel="stylesheet" />
	<link href="<?= base_url() ?>assets/dashboard/css/demo.min.css?1684106062" rel="stylesheet" />
	<style>
		@import url('https://rsms.me/inter/inter.css');

		:root {
			--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
		}

		body {
			font-feature-settings: "cv03", "cv04", "cv11";
		}
	</style>
</head>

<body class=" border-top-wide border-primary d-flex flex-column">
	<script src="<?= base_url() ?>assets/dashboard/js/demo-theme.min.js?1684106062"></script>
	<div class="page page-center">
		<div class="container-tight py-4">
			<div class="empty">
				<div class="text-center mb-4">
					<a href="." class="navbar-brand navbar-brand-autodark"><img src="<?= base_url() ?>assets/logo/logo-smesco.png" width="200" alt=""></a>
				</div>
				<div class="empty-header">404</div>
				<p class="empty-title">Oops… You just found an error page</p>
				<p class="empty-subtitle text-muted">
					We are sorry but the page you are looking for was not found
				</p>
				<div class="empty-action">
					<a href="<?= isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url('dashboard') ?>" class="btn btn-primary">
						<!-- Download SVG icon from http://tabler-icons.io/i/arrow-left -->
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none" />
							<path d="M5 12l14 0" />
							<path d="M5 12l6 6" />
							<path d="M5 12l6 -6" />
						</svg>
						Take me home
					</a>
				</div>
			</div>
		</div>
	</div>
	<!-- Libs JS -->
	<!-- Tabler Core -->
	<script src="<?= base_url() ?>assets/dashboard/js/tabler.min.js?1684106062" defer></script>
	<script src="<?= base_url() ?>assets/dashboard/js/demo.min.js?1684106062" defer></script>
</body>

</html>