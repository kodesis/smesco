<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
	:root {
		--navy: #193b5c;
		--navy-deep: #0f2740;
		--yellow: #ffcf26;
		--off: #f4f6f9;
		--border: #e2e6eb;
		--grey: #4d545e;
	}

	body {
		/* font-family: 'Inter', sans-serif; */
		background: var(--off);
	}

	/* ── BREADCRUMB HEADER ── */
	.page-header {
		position: relative;
		background: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2070&auto=format&fit=crop');
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

	/* ── CARD OVERLAP ── */
	.card-calculator {
		border: none;
		border-radius: 20px;
		margin-top: -40px;
		box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
		z-index: 10;
		position: relative;
	}

	.field-label {
		font-size: 0.85rem;
		font-weight: 700;
		color: var(--navy);
		margin-bottom: 0.5rem;
		text-transform: uppercase;
	}

	/* Autocomplete fix */
	.ui-autocomplete {
		z-index: 9999 !important;
		background: #ffffff;
		border: 1px solid var(--border);
		border-radius: 8px;
		padding: 5px 0;
		box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
		max-height: 200px;
		overflow-y: auto;
		font-family: 'Inter', sans-serif;
		font-size: 0.9rem;
	}

	.ui-menu-item {
		padding: 8px 20px;
		cursor: pointer;
		list-style: none;
	}

	.ui-menu-item:hover {
		background-color: var(--off);
		color: var(--navy);
	}

	.ui-helper-hidden-accessible {
		display: none;
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
				<li class="breadcrumb-item active" aria-current="page">Kalkulator Ongkir</li>
			</ol>
		</nav>
		<h1 class="display-5 fw-900">Cek Ongkir & Pickup</h1>
		<p class="text-white-50">Hitung biaya pengiriman akurat berdasarkan berat aktual dan dimensi (volume).</p>
	</div>
</section>

<div class="container pb-5">
	<div class="row justify-content-center">
		<div class="col-lg-10">
			<div class="card card-calculator bg-white p-4 p-md-5">

				<div class="row g-5">
					<div class="col-md-7 border-end pe-md-4">
						<div class="mb-4">
							<h5 class="fw-800 text-navy mb-3">1. Rute Pengiriman</h5>
							<div class="row g-3">
								<div class="col-md-6">
									<label class="field-label">Kota Asal</label>
									<input type="text" id="calc_origin" class="form-control form-control-lg trigger-calc" placeholder="Ketik kota asal..." oninput="this.value = this.value.toUpperCase()">
								</div>
								<div class="col-md-6">
									<label class="field-label">Kota Tujuan</label>
									<input type="text" id="calc_destination" class="form-control form-control-lg trigger-calc" placeholder="Ketik kota tujuan..." oninput="this.value = this.value.toUpperCase()">
								</div>
							</div>
						</div>

						<div class="mb-4">
							<h5 class="fw-800 text-navy mb-3">2. Berat & Dimensi Barang</h5>
							<div class="row g-3 mb-2">
								<div class="col-md-12">
									<label class="field-label">Berat Aktual (Timbangan)</label>
									<div class="input-group">
										<input type="number" id="calc_actual_weight" class="form-control form-control-lg trigger-calc" value="1" min="1">
										<span class="input-group-text bg-light fw-bold">Kg</span>
									</div>
								</div>
							</div>
							<label class="field-label mt-2">Dimensi (Opsional)</label>
							<div class="row g-2 mb-2">
								<div class="col-4">
									<input type="number" id="calc_p" class="form-control trigger-calc" placeholder="P (cm)">
								</div>
								<div class="col-4">
									<input type="number" id="calc_l" class="form-control trigger-calc" placeholder="L (cm)">
								</div>
								<div class="col-4">
									<input type="number" id="calc_t" class="form-control trigger-calc" placeholder="T (cm)">
								</div>
							</div>
							<small class="text-muted">*Isi dimensi jika paket memakan tempat untuk akurasi berat volume.</small>
						</div>

						<div class="mb-2">
							<h5 class="fw-800 text-navy mb-3">3. Layanan Penjemputan</h5>
							<div class="form-check form-switch mb-3">
								<input class="form-check-input trigger-calc" type="checkbox" id="calc_use_pickup" style="transform: scale(1.3); margin-left: -2.5em;">
								<label class="form-check-label fw-bold text-navy ms-2" for="calc_use_pickup">Request Pickup</label>
							</div>
							<div id="pickup_box" class="d-none bg-light p-3 rounded-3 border border-2">
								<label class="field-label">Area Penjemputan</label>
								<select id="calc_pickup_area" class="form-select trigger-calc border-secondary">
									<option value="">- Pilih Area -</option>
									<?php foreach ($pickup_rates as $rate): ?>
										<option value="<?= $rate->price_smesco ?>"><?= $rate->area_name ?> (+ Rp <?= number_format($rate->price_smesco) ?>)</option>
									<?php endforeach; ?>
								</select>
								<small class="text-danger mt-2 d-block fw-600">*Minimal Chargeable Weight 50 Kg.</small>
							</div>
						</div>
					</div>

					<div class="col-md-5">
						<div class="position-sticky" style="top: 20px;">
							<div class="bg-light p-4 rounded-4 border-start border-4 border-navy h-100">
								<h5 class="fw-800 text-navy mb-4 border-bottom pb-2">Estimasi Biaya</h5>

								<div class="d-flex justify-content-between mb-2">
									<span class="text-muted">Berat Aktual</span>
									<span class="fw-bold text-navy" id="sum_actual">1 Kg</span>
								</div>
								<div class="d-flex justify-content-between mb-2">
									<span class="text-muted">Berat Volume</span>
									<span class="fw-bold text-navy" id="sum_volume">0 Kg</span>
								</div>
								<div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
									<span class="fw-bold text-navy">Chargeable Weight</span>
									<span class="fw-900 text-danger h5 mb-0" id="sum_chargeable">1 Kg</span>
								</div>

								<div class="d-flex justify-content-between mb-2">
									<span class="text-muted small">Tarif Dasar / Kg</span>
									<span class="fw-bold small text-navy" id="sum_price_kg">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between mb-2">
									<span class="text-muted small">Biaya Kirim</span>
									<span class="fw-bold small text-navy" id="sum_shipping">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
									<span class="text-muted small">Biaya Pickup</span>
									<span class="fw-bold small text-danger" id="sum_pickup">Rp 0</span>
								</div>

								<div class="text-center mb-4">
									<div class="text-muted small mb-1 fw-bold">TOTAL ESTIMASI</div>
									<div class="h2 fw-900 text-navy mb-0" id="sum_grand_total">Rp 0</div>
								</div>

								<a href="https://wa.me/6281234567890?text=Halo%20Smesco,%20saya%20mau%20booking%20pengiriman" target="_blank" class="btn btn-dark w-100 py-3 fw-bold rounded-3">
									<i class="bi bi-whatsapp me-2"></i> Pesan via CS
								</a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		let currentRate = 0;
		let minWeight = 1;

		const formatRp = (angka) => new Intl.NumberFormat('id-ID', {
			style: 'currency',
			currency: 'IDR',
			minimumFractionDigits: 0
		}).format(angka);

		if (jQuery().autocomplete) {
			$("#calc_origin, #calc_destination").autocomplete({
				source: function(request, response) {
					$.ajax({
						url: "<?= site_url('home/ajax_autocomplete_route') ?>",
						dataType: "json",
						data: {
							term: request.term
						},
						success: function(data) {
							response(data);
						}
					});
				},
				minLength: 2,
				open: function() {
					$(this).autocomplete("widget").css({
						"width": ($(this).outerWidth() + "px")
					});
				},
				select: function(event, ui) {
					$(this).val(ui.item.value);
					fetchRate();
					return false;
				}
			});
		}

		function fetchRate() {
			const origin = document.getElementById('calc_origin').value;
			const dest = document.getElementById('calc_destination').value;
			const weight = parseFloat(document.getElementById('sum_chargeable').innerText) || 1;

			if (origin && dest) {
				let formData = new FormData();
				formData.append('origin', origin);
				formData.append('destination', dest);
				formData.append('weight', weight);

				fetch("<?= site_url('home/ajax_get_rate_public') ?>", {
						method: 'POST',
						body: formData
					})
					.then(res => res.json())
					.then(res => {
						if (res.status) {
							currentRate = parseFloat(res.data.price_per_kg);
							minWeight = parseFloat(res.data.min_weight_kg);
						} else {
							currentRate = 0;
						}
						calculateAll();
					}).catch(e => console.log(e));
			}
		}

		function calculateAll() {
			let actual = parseFloat(document.getElementById('calc_actual_weight').value) || 0;
			let p = parseFloat(document.getElementById('calc_p').value) || 0;
			let l = parseFloat(document.getElementById('calc_l').value) || 0;
			let t = parseFloat(document.getElementById('calc_t').value) || 0;

			let volume = (p * l * t) / 5000;

			let chargeable = Math.max(actual, volume);
			if (chargeable < minWeight) chargeable = minWeight;
			chargeable = Math.ceil(chargeable);

			document.getElementById('sum_actual').innerText = actual + " Kg";
			document.getElementById('sum_volume').innerText = volume.toFixed(2) + " Kg";
			document.getElementById('sum_chargeable').innerText = chargeable + " Kg";

			const usePickup = document.getElementById('calc_use_pickup');
			const pickupBox = document.getElementById('pickup_box');
			const pickupSelect = document.getElementById('calc_pickup_area');
			let pickupFee = 0;

			if (usePickup.checked) {
				if (chargeable < 50) {
					alert("Layanan pickup khusus untuk barang di atas 50 Kg.");
					usePickup.checked = false;
					pickupBox.classList.add('d-none');
					pickupSelect.value = "";
				} else {
					pickupBox.classList.remove('d-none');
					pickupFee = parseFloat(pickupSelect.value) || 0;
				}
			} else {
				pickupBox.classList.add('d-none');
				pickupSelect.value = "";
			}

			let shippingFee = chargeable * currentRate;
			let grandTotal = shippingFee + pickupFee;

			document.getElementById('sum_price_kg').innerText = formatRp(currentRate);
			document.getElementById('sum_shipping').innerText = formatRp(shippingFee);
			document.getElementById('sum_pickup').innerText = formatRp(pickupFee);
			document.getElementById('sum_grand_total').innerText = formatRp(grandTotal);
		}

		document.querySelectorAll('.trigger-calc').forEach(el => {
			el.addEventListener('input', () => {
				calculateAll();
				if (['calc_actual_weight', 'calc_p', 'calc_l', 'calc_t'].includes(el.id)) {
					fetchRate();
				}
			});
			el.addEventListener('change', calculateAll);
		});
	});
</script>
