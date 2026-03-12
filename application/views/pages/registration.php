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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/select2/css/select2.min.css">

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
		<div class="container container-normal py-4">
			<div class="text-center mb-4">
				<a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark"><img src="<?= base_url() ?>assets/logo/logo-smesco.png" width="200" alt=""></a>
			</div>
			<div class="card card-sm">
				<form method="POST" action="<?= base_url('registration') ?>" autocomplete="off">
					<div class="card-body">
						<h2 class="h2 text-center mb-4">Formulir Pendaftaran Agen</h2>
						<div class="row">
							<div class="col-md-4 col-12 mb-3">
								<label for="nama_agent" class="form-label">Nama Lengkap</label>
								<input type="text" name="nama_agent" id="nama_agent" class="form-control" placeholder="Nama lengkap..." autocomplete="off" value="<?= set_value('nama_agent') ?>" required>
								<?= form_error('nama_agent', '<small class="text-danger">', '</small>'); ?>
							</div>
							<div class="col-md-4 col-12 mb-3">
								<label for="no_telepon" class="form-label">No. Telepon</label>
								<input type="text" name="no_telepon" id="no_telepon" class="form-control" placeholder="Nomor telepon..." autocomplete="off" value="<?= set_value('no_telepon') ?>" required>
								<?= form_error('no_telepon', '<small class="text-danger">', '</small>'); ?>
							</div>
							<div class="col-md-4 col-12 mb-3">
								<label for="email" class="form-label">Email</label>
								<input type="email" name="email" id="email" class="form-control" placeholder="Email..." autocomplete="off" value="<?= set_value('email') ?>" required>
								<?= form_error('email', '<small class="text-danger">', '</small>'); ?>
								<div class="invalid-feedback">Email sudah terdaftar</div>
								<div class="valid-feedback">Email bisa digunakan</div>
							</div>

							<div class="col-md-6 col-12 mb-3">
								<label for="provinsi" class="form-label">Provinsi</label>
								<select name="provinsi" id="provinsi" class="form-select select2" required>
									<option value="">:: Pilih provinsi</option>
								</select>
								<?= form_error('provinsi', '<small class="text-danger">', '</small>'); ?>
								<!-- <input type="text" name="provinsi" id="provinsi" class="form-control" placeholder="Nama lengkap..." autocomplete="off" value="<?= set_value('provinsi') ?>" required> -->
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="kota" class="form-label">Kota/Kabupaten</label>
								<select name="kota" id="kota" class="form-select select2" required>
									<option value="">:: Pilih kota/kabupaten</option>
								</select>
								<?= form_error('kota', '<small class="text-danger">', '</small>'); ?>
								<!-- <input type="text" name="kota" id="kota" class="form-control" placeholder="Nomor telepon..." autocomplete="off" value="<?= set_value('kota') ?>" required> -->
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="kecamatan" class="form-label">Kecamatan</label>
								<select name="kecamatan" id="kecamatan" class="form-select select2" required>
									<option value="">:: Pilih kecamatan</option>
								</select>
								<?= form_error('kecamatan', '<small class="text-danger">', '</small>'); ?>
								<!-- <input type="text" name="kecamatan" id="kecamatan" class="form-control" placeholder="Nama lengkap..." autocomplete="off" value="<?= set_value('kecamatan') ?>" required> -->
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="kelurahan" class="form-label">Kelurahan</label>
								<select name="kelurahan" id="kelurahan" class="form-select select2" required>
									<option value="">:: Pilih kelurahan</option>
								</select>
								<?= form_error('kelurahan', '<small class="text-danger">', '</small>'); ?>
								<!-- <input type="text" name="kelurahan" id="kelurahan" class="form-control" placeholder="Nomor telepon..." autocomplete="off" value="<?= set_value('kelurahan') ?>" required> -->
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="rt" class="form-label">RT</label>
								<input type="text" name="rt" id="rt" class="form-control" placeholder="Nama lengkap..." autocomplete="off" value="<?= set_value('rt') ?>" required>
								<?= form_error('rt', '<small class="text-danger">', '</small>'); ?>
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="rw" class="form-label">RW</label>
								<input type="text" name="rw" id="rw" class="form-control" placeholder="Nomor telepon..." autocomplete="off" value="<?= set_value('rw') ?>" required>
								<?= form_error('rw', '<small class="text-danger">', '</small>'); ?>
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="alamat" class="form-label">Alamat</label>
								<textarea type="text" name="alamat" id="alamat" class="form-control" placeholder="Nama lengkap..." autocomplete="off" value="<?= set_value('alamat') ?>" rows="3" required></textarea>
								<?= form_error('alamat', '<small class="text-danger">', '</small>'); ?>
							</div>
							<div class="col-md-6 col-12 mb-3">
								<label for="google_maps" class="form-label">Google Maps URL</label>
								<textarea type="text" name="google_maps" id="google_maps" class="form-control" placeholder="Nama lengkap..." autocomplete="off" value="<?= set_value('google_maps') ?>" rows="3" required></textarea>
								<?= form_error('google_maps', '<small class="text-danger">', '</small>'); ?>
							</div>
						</div>
					</div>
					<div class="card-footer text-end">
						<div class="d-flex">
							<a href="<?= base_url('home') ?>" type="button" class="btn btn-warning ms-auto">Kembali</a>
							<button type="submit" class="btn btn-primary btn-submit ms-1">
								Simpan
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- Libs JS -->
		<!-- Tabler Core -->
		<script src="<?= base_url() ?>assets/dashboard/js/tabler.min.js?1684106062" defer></script>
		<script src="<?= base_url() ?>assets/dashboard/js/demo.min.js?1684106062" defer></script>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<!-- <script src="<?= base_url() ?>assets/js/sweetalert2/sweetalert2.all.min.js"></script> -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<script type="text/javascript" src="<?= base_url(); ?>assets/vendor/select2/js/select2.min.js"></script>
		<script>
			$(document).ready(function() {
				$('.select2').select2();

				$.ajax({
					type: 'POST',
					url: '<?= base_url('registration/getProvinsi') ?>',
					cache: false,
					success: function(msg) {
						$('#provinsi').html(msg);
					}
				});

				$('#email').on('input', function() {
					var email = $(this).val();

					$.ajax({
						type: 'POST',
						url: '<?= base_url('registration/checkEmail') ?>',
						data: {
							email: email
						},
						cache: false,
						success: function(response) {
							var isEmailAvailable = response == 0;

							$('#email')
								.toggleClass('is-invalid', !isEmailAvailable)
								.toggleClass('is-valid', isEmailAvailable);
						},
						error: function() {
							console.log('Error while checking email');
						}
					});
				});


				$('#provinsi').change(function() {
					var provinsi = $('#provinsi').val();

					$.ajax({
						type: 'POST',
						url: '<?= base_url('registration/getKota') ?>',
						data: {
							provinsi: provinsi,
						},
						cache: false,
						success: function(msg) {
							$('#kota').html(msg);
						}
					})
				});

				$('#kota').change(function() {
					var kota = $('#kota').val();

					$.ajax({
						type: 'POST',
						url: '<?= base_url('registration/getKecamatan') ?>',
						data: {
							kota: kota,
						},
						cache: false,
						success: function(msg) {
							$('#kecamatan').html(msg);
						}
					})
				});

				$('#kecamatan').change(function() {
					var kecamatan = $('#kecamatan').val();

					$.ajax({
						type: 'POST',
						url: '<?= base_url('registration/getKelurahan') ?>',
						data: {
							kecamatan: kecamatan,
						},
						cache: false,
						success: function(msg) {
							$('#kelurahan').html(msg);
						}
					})
				});
			})

			$(document).on("click", ".btn-submit", function(e) {
				e.preventDefault();
				const form = $(this).parents("form");


				// Validasi semua input form
				let inputs = form.find('input, select, textarea');
				let valid = true;

				inputs.each(function() {
					if (!$(this).val()) {
						$(this).addClass('is-invalid');
						valid = false;
					} else {
						$(this).removeClass('is-invalid').addClass('is-valid');
					}
				});

				// Jika ada input yang tidak valid, cegah submit dan tampilkan peringatan
				if (!valid) {
					Swal.fire({
						icon: 'error',
						text: 'Please fill out all required fields!'
					});
					return;
				}

				Swal.fire({
					title: "Are you sure?",
					text: "You won't be able to revert this!",
					icon: "warning",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Yes, confirm!",
				}).then((result) => {
					if (result.isConfirmed) {

						form.on("submit", function() {
							$(".btn-submit").prop('disabled', true);
							Swal.fire({
								title: "Loading...",
								timerProgressBar: true,
								allowOutsideClick: false,
								didOpen: () => {
									Swal.showLoading();
								},
							});
						});

						form.submit();
					}
				});
			});
		</script>

</body>

</html>