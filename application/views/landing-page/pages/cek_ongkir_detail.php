<!-- cek_ongkir.php -->
<!-- ── PAGE HEADER ── -->
<section class="page-header">
	<div class="container">
		<nav aria-label="breadcrumb" class="mb-3">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
				<li class="breadcrumb-item active">Cek Ongkir</li>
			</ol>
		</nav>
		<h1>Cek Ongkir & Pickup</h1>
		<p>Hitung estimasi biaya pengiriman berdasarkan berat aktual, volume, dan jumlah koli.</p>
	</div>
</section>

<!-- ── MAIN CONTENT ── -->
<div class="container pb-5">
	<div class="row justify-content-center">
		<div class="col-lg-11">
			<div class="card card-calculator bg-white">
				<div class="row g-5">

					<!-- ── KOLOM KIRI: FORM ── -->
					<div class="col-md-7 border-end pe-md-5">

						<!-- Rute -->
						<div class="mb-4">
							<div class="section-title">
								<i class="bi bi-map"></i> Rute Pengiriman
							</div>
							<div class="row g-3">
								<div class="col-md-6 col-12">
									<label class="field-label">Kota Asal</label>
									<input type="text" id="calc_origin"
										class="form-control trigger-calc"
										placeholder="Contoh: JAKARTA"
										oninput="this.value = this.value.toUpperCase()">
								</div>
								<div class="col-md-6 col-12">
									<label class="field-label">Kota Tujuan</label>
									<input type="text" id="calc_destination"
										class="form-control trigger-calc"
										placeholder="Contoh: SURABAYA"
										oninput="this.value = this.value.toUpperCase()">
								</div>
							</div>
						</div>

						<!-- Berat & Dimensi -->
						<div class="mb-4">
							<div class="section-title">
								<i class="bi bi-box-seam"></i> Detail Barang
							</div>

							<div class="mb-3">
								<label class="field-label">Total Berat Aktual (Timbangan)</label>
								<div class="input-group">
									<input type="number" id="calc_actual_weight"
										class="form-control trigger-calc"
										value="1" min="1">
									<span class="input-group-text">Kg</span>
								</div>
							</div>

							<div class="d-flex align-items-center justify-content-between mb-2">
								<label class="field-label mb-0">Dimensi per Jenis Koli (opsional)</label>
								<button type="button"
									class="btn btn-sm btn-outline-primary fw-600 rounded-pill px-3"
									style="font-size:0.75rem;"
									onclick="addDimRow()">
									<i class="bi bi-plus-lg me-1"></i> Tambah
								</button>
							</div>

							<div id="dim_container">
								<div class="dim-row" id="row_0">
									<div class="dim-row-inner">
										<div class="dim-field dim-field-qty">
											<div class="dim-sub-label">Jml Koli</div>
											<input type="number" class="form-control trigger-calc row-qty" value="1" min="1">
										</div>
										<div class="dim-field dim-field-p">
											<div class="dim-sub-label">P (cm)</div>
											<input type="number" class="form-control trigger-calc row-p" placeholder="0">
										</div>
										<div class="dim-field dim-field-sep">×</div>
										<div class="dim-field dim-field-l">
											<div class="dim-sub-label">L (cm)</div>
											<input type="number" class="form-control trigger-calc row-l" placeholder="0">
										</div>
										<div class="dim-field dim-field-sep">×</div>
										<div class="dim-field dim-field-t">
											<div class="dim-sub-label">T (cm)</div>
											<input type="number" class="form-control trigger-calc row-t" placeholder="0">
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Pickup -->
						<div>
							<div class="section-title">
								<i class="bi bi-truck"></i> Penjemputan
							</div>

							<div class="form-check form-switch mb-3 ms-1" style="padding-left: 2.8em;">
								<input class="form-check-input trigger-calc" type="checkbox"
									id="calc_use_pickup"
									style="width: 2.2em; height: 1.1em; margin-left: -2.8em;">
								<label class="form-check-label fw-600 ms-2" for="calc_use_pickup"
									style="font-size:0.875rem; color: var(--navy);">
									Request Pickup (min. 50 Kg)
								</label>
							</div>

							<div id="pickup_box" class="pickup-box d-none">
								<label class="field-label">Area Penjemputan</label>
								<select id="calc_pickup_area" class="form-select trigger-calc">
									<option value="">- Pilih Area -</option>
									<?php foreach ($pickup_rates as $rate): ?>
										<option value="<?= $rate->price_smesco ?>">
											<?= $rate->area_name ?> (+Rp <?= number_format($rate->price_smesco) ?>)
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

					</div>

					<!-- ── KOLOM KANAN: SUMMARY ── -->
					<div class="col-md-5">
						<div class="summary-panel">

							<div class="summary-title">Ringkasan Estimasi</div>

							<!-- Berat -->
							<div class="summary-row">
								<span class="label">Total Koli</span>
								<span class="value" id="sum_koli">1 Koli</span>
							</div>
							<div class="summary-row">
								<span class="label">Berat Aktual</span>
								<span class="value" id="sum_actual">1 Kg</span>
							</div>
							<div class="summary-row">
								<span class="label">Berat Volume</span>
								<span class="value" id="sum_volume">0 Kg</span>
							</div>
							<div class="summary-row">
								<span class="label d-flex align-items-center">
									Chargeable Weight
									<i class="bi bi-info-circle ms-2 text-white"
										style="cursor: pointer; font-size: 0.95rem; transition: color 0.2s;"
										onmouseover="this.classList.replace('text-white', 'text-secondary')"
										onmouseout="this.classList.replace('text-secondary', 'text-white')"
										data-bs-toggle="tooltip"
										data-bs-placement="top"
										data-bs-html="true"
										data-bs-custom-class="custom-smesco-tooltip"
										title="Berat yang dijadikan dasar hitungan tarif.<br><br>Kami mengambil nilai tertinggi antara <strong>Berat Aktual</strong> (timbangan) atau <strong>Berat Volume</strong> (dimensi P x L x T).">
									</i>
								</span>
								<span class="chargeable-badge" id="sum_chargeable">1 Kg</span>
							</div>

							<hr class="summary-divider">

							<!-- Biaya -->
							<div class="summary-row">
								<span class="label">Tarif / Kg</span>
								<span class="value" id="sum_price_kg">Rp 0</span>
							</div>
							<div class="summary-row">
								<span class="label">Biaya Kirim</span>
								<span class="value" id="sum_shipping">Rp 0</span>
							</div>
							<div class="summary-row">
								<span class="label">Biaya Pickup</span>
								<span class="value" id="sum_pickup">Rp 0</span>
							</div>

							<hr class="summary-divider">

							<!-- Grand Total -->
							<div class="grand-total-label">Total Estimasi</div>
							<div class="grand-total-value" id="sum_grand_total">Rp 0</div>
							<div class="est-badge">HARGA ESTIMASI</div>

							<!-- Catatan -->
							<div class="info-note">
								<div><i class="bi bi-info-circle-fill"></i> Minimal kargo <strong>10 Kg</strong>.</div>
								<div><i class="bi bi-truck-flatbed"></i> Pickup tersedia untuk berat <strong>> 50 Kg</strong>.</div>
								<div><i class="bi bi-exclamation-triangle-fill"></i> Verifikasi fisik oleh Acceptance bersifat final.</div>
							</div>

							<!-- CTA -->
							<a href="https://wa.me/6282220282863?text=Halo%20Smesco,%20saya%20mau%20booking%20kargo"
								target="_blank" class="btn-wa">
								<i class="bi bi-whatsapp fs-6"></i> Pesan via WhatsApp
							</a>

						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js"></script>

<script>
	let rowCount = 1;

	function addDimRow() {
		const id = rowCount++;
		const html = `
        <div class="dim-row" id="row_${id}">
            <button type="button" class="btn btn-danger btn-remove-row" onclick="removeDimRow(${id})">
                <i class="bi bi-x"></i>
            </button>
            <div class="dim-row-inner">
                <div class="dim-field dim-field-qty">
                    <div class="dim-sub-label">Jml Koli</div>
                    <input type="number" class="form-control trigger-calc row-qty" value="1" min="1">
                </div>
                <div class="dim-field dim-field-p">
                    <div class="dim-sub-label">P (cm)</div>
                    <input type="number" class="form-control trigger-calc row-p" placeholder="0">
                </div>
                <div class="dim-field dim-field-sep">×</div>
                <div class="dim-field dim-field-l">
                    <div class="dim-sub-label">L (cm)</div>
                    <input type="number" class="form-control trigger-calc row-l" placeholder="0">
                </div>
                <div class="dim-field dim-field-sep">×</div>
                <div class="dim-field dim-field-t">
                    <div class="dim-sub-label">T (cm)</div>
                    <input type="number" class="form-control trigger-calc row-t" placeholder="0">
                </div>
            </div>
        </div>`;
		$('#dim_container').append(html);
		attachEvents();
		calculateAll();
	}

	function removeDimRow(id) {
		$(`#row_${id}`).remove();
		calculateAll();
	}

	function attachEvents() {
		$('.trigger-calc').off('input change').on('input change', calculateAll);
	}

	document.addEventListener("DOMContentLoaded", function() {

		// ← Semua state di sini, SATU scope, tidak ada redeclare
		let currentRate = 0;
		let minWeightGlobal = 1;
		let isTiered = false;

		const formatRp = (num) => new Intl.NumberFormat('id-ID', {
			style: 'currency',
			currency: 'IDR',
			minimumFractionDigits: 0
		}).format(num);

		// Autocomplete
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
						"width": $(this).outerWidth() + "px"
					});
				},
				select: function(event, ui) {
					$(this).val(ui.item.value);
					fetchRate();
					return false;
				}
			});
		}

		// Juga trigger fetchRate saat user manual ketik (blur)
		$('#calc_origin, #calc_destination').on('change blur', function() {
			fetchRate();
		});

		function getChargeableWeight() {
			let actual = parseFloat($('#calc_actual_weight').val()) || 0;
			let totalVol = 0;

			$('.dim-row').each(function() {
				let qty = parseInt($(this).find('.row-qty').val()) || 0;
				let p = parseFloat($(this).find('.row-p').val()) || 0;
				let l = parseFloat($(this).find('.row-l').val()) || 0;
				let t = parseFloat($(this).find('.row-t').val()) || 0;
				if (p > 0 && l > 0 && t > 0) totalVol += ((p * l * t) / 5000) * qty;
			});

			let chargeable = Math.max(actual, totalVol);
			if (chargeable > 0 && chargeable < minWeightGlobal) chargeable = minWeightGlobal;
			return Math.ceil(chargeable);
		}

		function fetchRate() {
			const origin = $('#calc_origin').val();
			const dest = $('#calc_destination').val();
			if (!origin || !dest) return;

			const chargeable = getChargeableWeight();

			let fd = new FormData();
			fd.append('origin', origin);
			fd.append('destination', dest);
			fd.append('weight', chargeable);

			fetch("<?= site_url('home/ajax_get_rate_public') ?>", {
					method: 'POST',
					body: fd
				})
				.then(r => r.json())
				.then(res => {
					if (res.status) {
						currentRate = parseFloat(res.data.price_per_kg);
						minWeightGlobal = parseFloat(res.data.min_weight_kg);
						isTiered = res.data.is_tiered == 1;
					} else {
						currentRate = 0;
						minWeightGlobal = 1;
						isTiered = false;
					}
					renderSummary();
				})
				.catch(e => console.error(e));
		}

		function renderSummary() {
			let actual = parseFloat($('#calc_actual_weight').val()) || 0;
			let totalVol = 0;
			let totalKoli = 0;

			$('.dim-row').each(function() {
				let qty = parseInt($(this).find('.row-qty').val()) || 0;
				let p = parseFloat($(this).find('.row-p').val()) || 0;
				let l = parseFloat($(this).find('.row-l').val()) || 0;
				let t = parseFloat($(this).find('.row-t').val()) || 0;
				if (p > 0 && l > 0 && t > 0) totalVol += ((p * l * t) / 5000) * qty;
				totalKoli += qty;
			});

			let chargeable = Math.max(actual, totalVol);
			if (chargeable > 0 && chargeable < minWeightGlobal) chargeable = minWeightGlobal;
			chargeable = Math.ceil(chargeable);

			const usePickup = $('#calc_use_pickup').is(':checked');
			let pickupFee = 0;

			if (usePickup) {
				if (chargeable < 50 && chargeable > 0) {
					$('#calc_use_pickup').prop('checked', false);
					$('#pickup_box').addClass('d-none');
				} else {
					$('#pickup_box').removeClass('d-none');
					pickupFee = parseFloat($('#calc_pickup_area').val()) || 0;
				}
			} else {
				$('#pickup_box').addClass('d-none');
			}

			const shippingFee = chargeable * currentRate;
			const grandTotal = shippingFee + pickupFee;

			$('#sum_koli').text(totalKoli + ' Koli');
			$('#sum_actual').text(actual + ' Kg');
			$('#sum_volume').text(totalVol.toFixed(2) + ' Kg');
			$('#sum_chargeable').text(chargeable + ' Kg');
			$('#sum_price_kg').text(formatRp(currentRate) + ' /Kg');
			$('#sum_shipping').text(formatRp(shippingFee));
			$('#sum_pickup').text(formatRp(pickupFee));
			$('#sum_grand_total').text(formatRp(grandTotal));
		}

		// ← expose ke global agar attachEvents() bisa manggilnya
		window.calculateAll = function() {
			const origin = $('#calc_origin').val();
			const dest = $('#calc_destination').val();

			if (origin && dest && isTiered) {
				fetchRate(); // re-fetch bawa berat terbaru → update harga tier
			} else {
				renderSummary(); // non-tiered, langsung render
			}
		};

		attachEvents();
		renderSummary();

		// Tooltip Bootstrap
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>
