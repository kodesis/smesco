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
	<title><?= $title ?> - Smesco Express</title>
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

<body class=" d-flex flex-column">
	<script src="<?= base_url() ?>assets/dashboard/js/demo-theme.min.js?1684106062"></script>
	<div class="page page-center">
		<div class="container container-tight py-4">
			<div class="text-center mb-4">
				<a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark"><img src="https://smesco.go.id/assets/logo.svg" height="36" alt=""></a>
			</div>
			<div class="card card-md">
				<?php if (isset($pages)) $this->load->view($pages); ?>
			</div>
		</div>
	</div>
	<!-- Libs JS -->
	<!-- Tabler Core -->
	<script src="<?= base_url() ?>assets/dashboard/js/tabler.min.js?1684106062" defer></script>
	<script src="<?= base_url() ?>assets/dashboard/js/demo.min.js?1684106062" defer></script>
	<script>
		document.getElementById('toggle-password').addEventListener('click', function(e) {
			e.preventDefault();
			const passwordField = document.getElementById('password');
			const passwordFieldType = passwordField.getAttribute('type');

			if (passwordFieldType === 'password') {
				passwordField.setAttribute('type', 'text');
			} else {
				passwordField.setAttribute('type', 'password');
			}
		});
	</script>
</body>

</html>
