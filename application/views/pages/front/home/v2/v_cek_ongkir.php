<!-- v_cek_ongkir.php -->
<style>
	:root {
		--navy: #193b5c;
		--navy-dark: #0f2740;
		--navy-light: #1e4a73;
		--slate: #4d545e;
		--slate-light: #6b7480;
		--off-white: #f7f8fa;
		--border: #e2e6ea;
		--accent: #e8f0f8;
	}

	/* ── Page Hero ───────────────────────────────── */
	.page-hero {
		background: var(--navy);
		padding: 100px;
		position: relative;
		overflow: hidden;
	}

	.page-hero::after {
		content: '';
		position: absolute;
		right: -80px;
		top: -80px;
		width: 320px;
		height: 320px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.04);
		pointer-events: none;
	}

	.page-hero h1 {
		font-family: 'Roboto', sans-serif;
		font-size: 4rem;
		font-weight: 700;
		color: #fff;
		margin-bottom: 8px;
	}

	.page-hero .breadcrumb {
		background: transparent;
		padding: 0;
		margin: 0;
	}

	.page-hero .breadcrumb-item a {
		color: rgba(255, 255, 255, 0.6);
		text-decoration: none;
		font-size: 13px;
		transition: color 0.2s;
	}

	.page-hero .breadcrumb-item a:hover {
		color: #fff;
	}

	.page-hero .breadcrumb-item.active {
		color: rgba(255, 255, 255, 0.9);
		font-size: 13px;
	}

	.page-hero .breadcrumb-item+.breadcrumb-item::before {
		color: rgba(255, 255, 255, 0.35);
	}

	/* ── Main content ────────────────────────────── */
	.cek-ongkir-page {
		background: var(--off-white);
		padding: 48px 0 64px;
	}

	/* ── Card wrapper ────────────────────────────── */
	.co-card {
		background: #fff;
		border: 1px solid var(--border);
		border-radius: 8px;
		padding: 32px 36px;
		margin-bottom: 24px;
	}

	.co-card-title {
		font-family: 'Roboto', sans-serif;
		font-size: 16px;
		font-weight: 700;
		color: var(--navy-dark);
		margin-bottom: 20px;
		padding-bottom: 12px;
		border-bottom: 1px solid var(--border);
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.co-card-title .title-icon {
		width: 28px;
		height: 28px;
		background: var(--accent);
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
	}

	.co-card-title .title-icon svg {
		stroke: var(--navy);
	}

	/* ── Form elements ───────────────────────────── */
	.co-label {
		font-size: 12px;
		font-weight: 700;
		color: var(--slate);
		margin-bottom: 5px;
		display: block;
		letter-spacing: 0.3px;
	}

	.co-label .req {
		color: #dc3545;
		margin-left: 2px;
	}

	.co-input {
		border: 1px solid var(--border);
		border-radius: 4px;
		height: 44px;
		font-size: 14px;
		color: var(--navy-dark);
		width: 100%;
		padding: 0 12px;
		transition: border-color 0.2s, box-shadow 0.2s;
		background: #fff;
		outline: none;
	}

	.co-input:focus {
		border-color: var(--navy);
		box-shadow: 0 0 0 3px rgba(25, 59, 92, 0.08);
	}

	.co-input[readonly] {
		background: var(--off-white);
		color: var(--slate);
		cursor: default;
	}

	.co-input.is-valid {
		border-color: #198754;
		background-image: none;
	}

	.co-input.is-invalid {
		border-color: #dc3545;
		background-image: none;
	}

	.co-select {
		border: 1px solid var(--border);
		border-radius: 4px;
		height: 44px;
		font-size: 14px;
		color: var(--navy-dark);
		width: 100%;
		padding: 0 12px;
		transition: border-color 0.2s;
		background: #fff;
		outline: none;
		appearance: none;
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%234d545e' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
		background-repeat: no-repeat;
		background-position: right 12px center;
	}

	.co-select:focus {
		border-color: var(--navy);
		box-shadow: 0 0 0 3px rgba(25, 59, 92, 0.08);
	}

	.field-hint {
		font-size: 11px;
		color: var(--slate-light);
		margin-top: 4px;
	}

	.field-valid {
		font-size: 11px;
		color: #198754;
		margin-top: 4px;
		display: none;
	}

	.field-invalid {
		font-size: 11px;
		color: #dc3545;
		margin-top: 4px;
		display: none;
	}

	/* ── Hasil section ───────────────────────────── */
	.hasil-section {
		background: var(--accent);
		border: 1px solid #c2d8ee;
		border-left: 4px solid var(--navy);
		border-radius: 0 6px 6px 0;
		padding: 20px 24px;
	}

	.hasil-section .hasil-label {
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 1.5px;
		text-transform: uppercase;
		color: var(--slate);
		margin-bottom: 14px;
	}

	.hasil-grid {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 16px;
	}

	.hasil-item {}

	.hasil-item .hi-label {
		font-size: 11px;
		color: var(--slate-light);
		margin-bottom: 3px;
		width: 50%;
	}

	.hasil-item .hi-value {
		font-family: 'Roboto', sans-serif;
		font-size: 20px;
		font-weight: 700;
		color: var(--navy);
		line-height: 1.1;
	}

	.hasil-item.hi-main .hi-value {
		font-size: 28px;
		color: var(--navy-dark);
	}

	.hasil-item .hi-sub {
		font-size: 11px;
		color: var(--slate-light);
		margin-top: 2px;
	}

	/* ── Tabel dimensi ───────────────────────────── */
	.dimensi-table {
		width: 100%;
		border-collapse: collapse;
		font-size: 13px;
	}

	.dimensi-table thead th {
		background: var(--off-white);
		color: var(--slate);
		font-size: 11px;
		font-weight: 700;
		letter-spacing: 0.5px;
		text-transform: uppercase;
		padding: 10px 12px;
		border-bottom: 1px solid var(--border);
		white-space: nowrap;
	}

	.dimensi-table tbody td {
		padding: 8px 12px;
		border-bottom: 1px solid var(--border);
		vertical-align: middle;
	}

	.dimensi-table tbody tr:last-child td {
		border-bottom: none;
	}

	.dimensi-table .td-num {
		color: var(--slate-light);
		font-size: 12px;
		width: 32px;
	}

	.dimensi-table input.form-control {
		height: 36px;
		font-size: 13px;
		border: 1px solid var(--border);
		border-radius: 4px;
		padding: 0 10px;
		min-width: 70px;
		transition: border-color 0.2s;
	}

	.dimensi-table input.form-control:focus {
		border-color: var(--navy);
		box-shadow: 0 0 0 2px rgba(25, 59, 92, 0.08);
		outline: none;
	}

	.dimensi-table input.form-control[readonly] {
		background: var(--off-white);
		color: var(--slate);
	}

	.btn-hapus-row {
		background: transparent;
		border: 1px solid #dee2e6;
		color: #dc3545;
		border-radius: 4px;
		width: 30px;
		height: 30px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: all 0.15s;
		padding: 0;
	}

	.btn-hapus-row:hover {
		background: #fff5f5;
		border-color: #dc3545;
	}

	.btn-add-row {
		background: transparent;
		border: 1px dashed var(--border);
		color: var(--navy);
		border-radius: 4px;
		font-size: 13px;
		font-weight: 600;
		padding: 8px 16px;
		cursor: pointer;
		transition: all 0.2s;
		display: inline-flex;
		align-items: center;
		gap: 6px;
		margin-top: 8px;
	}

	.btn-add-row:hover {
		border-color: var(--navy);
		background: var(--accent);
	}

	/* ── jQuery UI autocomplete override ─────────── */
	.ui-autocomplete {
		border: 1px solid var(--border) !important;
		border-radius: 4px !important;
		box-shadow: 0 4px 16px rgba(25, 59, 92, 0.1) !important;
		font-size: 13px !important;
		z-index: 9999 !important;
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

	@media (max-width: 768px) {
		.co-card {
			padding: 20px 16px;
		}

		.hasil-grid {
			grid-template-columns: 1fr 1fr;
		}

		.hasil-item.hi-main {
			grid-column: 1 / -1;
		}

		.dimensi-table thead th,
		.dimensi-table tbody td {
			padding: 6px 8px;
		}
	}
</style>

<!-- ══════════════════════════════════════════════ -->
<!-- PAGE HERO                                      -->
<!-- ══════════════════════════════════════════════ -->
<div class="page-hero" style="background: linear-gradient(rgba(6, 3, 21, .5), rgba(6, 3, 21, .5)), url(<?= base_url() ?>assets/front/img/about-us.jpg) center center no-repeat;
    background-size: cover;">
	<div class="container">
		<h1>Kalkulator Ongkir</h1>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="<?= base_url() ?>">Beranda</a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">Cek Ongkir</li>
			</ol>
		</nav>
	</div>
</div>

<!-- ══════════════════════════════════════════════ -->
<!-- MAIN CONTENT                                   -->
<!-- ══════════════════════════════════════════════ -->
<div class="cek-ongkir-page">
	<div class="container">
		<div class="row g-4">

			<!-- ── Kolom kiri: Form ─────────────────── -->
			<div class="col-lg-8">

				<!-- Card: Rute & Jenis -->
				<div class="co-card wow fadeIn" data-wow-delay="0.1s">
					<div class="co-card-title">
						<div class="title-icon">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="12" cy="12" r="10" />
								<line x1="2" y1="12" x2="22" y2="12" />
								<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
							</svg>
						</div>
						Informasi Pengiriman
					</div>
					<div class="row g-3">
						<div class="col-md-4">
							<label class="co-label">
								Jenis Pengiriman <span class="req">*</span>
							</label>
							<select name="jenis_pengiriman" id="jenis_pengiriman" class="co-select">
								<option value="D">Domestik</option>
								<option value="I">Internasional</option>
							</select>
						</div>
						<div class="col-md-4">
							<label class="co-label">
								Kota Asal <span class="req">*</span>
							</label>
							<input type="text" name="origin" id="origin"
								class="co-input"
								placeholder="Contoh: JAKARTA"
								oninput="this.value = this.value.toUpperCase()">
						</div>
						<div class="col-md-4">
							<label class="co-label">
								Kota Tujuan <span class="req">*</span>
							</label>
							<input type="text" name="destination" id="destination"
								class="co-input"
								placeholder="Contoh: SURABAYA"
								oninput="this.value = this.value.toUpperCase()">
						</div>
					</div>
				</div>

				<!-- Card: Berat -->
				<div class="co-card wow fadeIn" data-wow-delay="0.15s">
					<div class="co-card-title">
						<div class="title-icon">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
							</svg>
						</div>
						Berat & Volume
					</div>
					<div class="row g-3">
						<div class="col-md-4">
							<label class="co-label">
								Berat Timbang (kg) <span class="req">*</span>
							</label>
							<input type="text" name="berat_timbang" id="berat_timbang"
								class="co-input"
								placeholder="Contoh: 5">
							<p class="field-hint">Berat fisik barang dalam kg</p>
						</div>
						<div class="col-md-4">
							<label class="co-label">Total Koli</label>
							<input type="text" name="total_koli" id="total_koli"
								class="co-input" value="1" readonly>
							<p class="field-hint">Dihitung otomatis dari tabel dimensi</p>
						</div>
						<div class="col-md-4">
							<label class="co-label">Total Volume (kg)</label>
							<input type="text" name="total_volume" id="total_volume"
								class="co-input" value="0" readonly>
							<p class="field-hint">Volume berat dari tabel dimensi</p>
						</div>
					</div>
				</div>

				<!-- Card: Tabel Dimensi -->
				<div class="co-card wow fadeIn" data-wow-delay="0.2s">
					<div class="co-card-title">
						<div class="title-icon">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
								<line x1="3" y1="9" x2="21" y2="9" />
								<line x1="3" y1="15" x2="21" y2="15" />
								<line x1="9" y1="3" x2="9" y2="21" />
								<line x1="15" y1="3" x2="15" y2="21" />
							</svg>
						</div>
						Input Dimensi
						<span style="font-size:11px;font-weight:400;color:var(--slate-light);margin-left:4px;">(opsional — untuk hitung volume berat)</span>
					</div>

					<div style="overflow-x:auto;">
						<table class="dimensi-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Panjang (cm)</th>
									<th>Lebar (cm)</th>
									<th>Tinggi (cm)</th>
									<th>Koli</th>
									<th>Vol. Berat</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="table-body">
								<tr class="baris">
									<td class="td-num nomor-urut">1.</td>
									<td><input type="text" name="panjang[]" class="form-control" placeholder="0"></td>
									<td><input type="text" name="lebar[]" class="form-control" placeholder="0"></td>
									<td><input type="text" name="tinggi[]" class="form-control" placeholder="0"></td>
									<td><input type="text" name="jumlah[]" class="form-control" value="1"></td>
									<td><input type="text" name="volume[]" class="form-control" value="0" readonly></td>
									<td>
										<button class="btn-hapus-row hapusRow" title="Hapus baris">
											<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<polyline points="3 6 5 6 21 6" />
												<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
												<path d="M10 11v6" />
												<path d="M14 11v6" />
											</svg>
										</button>
									</td>
								</tr>
								<tr id="add-row-container">
									<td colspan="7" style="padding-top:8px;">
										<button type="button" class="btn-add-row" id="addRow">
											<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<line x1="12" y1="5" x2="12" y2="19" />
												<line x1="5" y1="12" x2="19" y2="12" />
											</svg>
											Tambah baris
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>

			<!-- ── Kolom kanan: Hasil & Ringkasan ──── -->
			<div class="col-lg-4">

				<!-- Card: Chargeable -->
				<div class="co-card wow fadeIn" data-wow-delay="0.1s" style="position:sticky;top:80px;">
					<div class="co-card-title">
						<div class="title-icon">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<line x1="12" y1="1" x2="12" y2="23" />
								<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
							</svg>
						</div>
						Hasil Kalkulasi
					</div>

					<div class="row g-3 mb-4">
						<div class="col-12">
							<label class="co-label">Total Chargeable (kg)</label>
							<input type="text" name="chargeable" id="chargeable"
								class="co-input" value="0" readonly>
							<p class="field-hint">max(berat timbang, volume berat)</p>
						</div>
						<div class="col-12">
							<label class="co-label">Harga per kg</label>
							<input type="text" name="harga" id="harga"
								class="co-input" value="0" readonly>
							<p class="field-valid" id="harga-valid">Harga tersedia untuk rute ini</p>
							<p class="field-invalid" id="harga-invalid">Harga tidak tersedia untuk rute ini</p>
						</div>
						<input type="hidden" name="harga_jual" id="harga_jual" value="0">
					</div>

					<!-- Hasil nominal -->
					<div class="hasil-section">
						<div class="hasil-label">Estimasi Biaya</div>
						<div class="hasil-grid">
							<div class="hasil-item hi-main" style="grid-column:1/-1">
								<div class="hi-label">Total Nominal</div>
								<div class="hi-value" id="nominal-display">Rp 0</div>
								<input type="hidden" name="nominal" id="nominal" value="0">
							</div>
							<div class="hasil-item">
								<div class="hi-label">Chargeable</div>
								<div class="hi-value" id="chargeable-display" style="font-size:16px">0 kg</div>
							</div>
							<div class="hasil-item">
								<div class="hi-label">Per kg</div>
								<div class="hi-value" id="perkg-display" style="font-size:16px">Rp 0</div>
							</div>
						</div>
					</div>

					<div style="margin-top:16px;padding:12px;background:var(--off-white);border-radius:6px;border:1px solid var(--border);">
						<p style="font-size:11px;color:var(--slate-light);margin:0;line-height:1.6;">
							* Estimasi biaya berdasarkan chargeable weight. Harga akhir dapat berbeda tergantung kondisi aktual pengiriman.
						</p>
					</div>

					<div style="margin-top:16px;">
						<a href="<?= base_url('booking') ?>"
							class="btn w-100"
							style="background:var(--navy);color:#fff;font-weight:700;font-size:14px;padding:12px;border-radius:4px;text-decoration:none;display:block;text-align:center;transition:background 0.2s;">
							Buat Booking Sekarang
						</a>
						<a href="<?= base_url('home/track') ?>"
							style="display:block;text-align:center;margin-top:10px;font-size:13px;color:var(--slate-light);text-decoration:none;">
							Atau lacak kiriman &rarr;
						</a>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		// ── Autocomplete ─────────────────────────────
		$("#origin").autocomplete({
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
			minLength: 2,
			select: function(e, ui) {
				$("#origin").val(ui.item.value);
				tryFetchPrice();
			}
		});

		$("#destination").autocomplete({
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
			minLength: 2,
			select: function(e, ui) {
				$("#destination").val(ui.item.value);
				tryFetchPrice();
			}
		});

		// ── Trigger fetch price ───────────────────────
		function tryFetchPrice() {
			var origin = $("#origin").val();
			var destination = $("#destination").val();
			if (origin && destination) fetchPrice(origin, destination);
		}

		$('#berat_timbang').on('input', function() {
			tentukanChargeable();
			hitungNominal();
			tryFetchPrice();
		});

		$('#jenis_pengiriman').on('change', function() {
			tentukanChargeable();
			tryFetchPrice();
		});

		// ── Fetch price ───────────────────────────────
		function fetchPrice(origin, destination) {
			var chargeable = parseFloat($("#chargeable").val().replace(/,/g, '')) || 0;
			$.ajax({
				type: 'POST',
				url: base_url + 'home/getPrice',
				data: {
					origin,
					destination,
					chargeable
				},
				cache: false,
				success: function(response) {
					var data = JSON.parse(response);
					var per_kg = parseFloat(data.per_kg) || 0;
					var harga_up = parseFloat(data.harga_up) || 0;
					var harga_jual = parseFloat(data.harga_jual) || 0;

					if (harga_up > 0) {
						$('#harga').removeClass('is-invalid').addClass('is-valid');
						$('#harga-valid').show();
						$('#harga-invalid').hide();
					} else {
						$('#harga').removeClass('is-valid').addClass('is-invalid');
						$('#harga-valid').hide();
						$('#harga-invalid').show();
					}

					$('#harga').val(per_kg);
					$('#harga_jual').val(harga_jual);
					$('#nominal').val(harga_up);

					updateDisplay(harga_up, chargeable, per_kg);
				},
				error: function() {
					$('#harga').removeClass('is-valid').addClass('is-invalid');
					$('#harga-valid').hide();
					$('#harga-invalid').show();
				}
			});
		}

		// ── Update display hasil ──────────────────────
		function updateDisplay(nominal, chargeable, per_kg) {
			var fmt = new Intl.NumberFormat('id-ID', {
				style: 'currency',
				currency: 'IDR',
				minimumFractionDigits: 0
			});
			$('#nominal-display').text(nominal > 0 ? fmt.format(nominal) : 'Rp 0');
			$('#chargeable-display').text(chargeable + ' kg');
			$('#perkg-display').text(per_kg > 0 ? 'Rp ' + Number(per_kg).toLocaleString('id-ID') : 'Rp 0');
		}

		// ── Hitung nominal ────────────────────────────
		function hitungNominal() {
			var chargeable = parseFloat($('#chargeable').val().replace(/,/g, '')) || 0;
			var harga = parseFloat($('#harga').val()) || 0;
			var nominal = Math.round(chargeable * harga);
			$('#nominal').val(nominal);
			updateDisplay(nominal, chargeable, harga);
		}

		// ── Dimensi input ─────────────────────────────
		$(document).on('change click keyup input paste',
			'input[name="panjang[]"], input[name="lebar[]"], input[name="tinggi[]"], input[name="jumlah[]"]',
			function() {
				$(this).val(function(i, v) {
					return v.replace(/(?!\.)\D/g, "")
						.replace(/(?<=\..*)\./g, "")
						.replace(/(?<=\.\d\d).*/g, "")
						.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				});
				hitungTotal($(this).closest('.baris'));
				updateTotalRow();
			}
		);

		function hitungTotal(row) {
			var p = parseFloat(row.find('input[name="panjang[]"]').val().replace(/,/g, '')) || 0;
			var l = parseFloat(row.find('input[name="lebar[]"]').val().replace(/,/g, '')) || 0;
			var t = parseFloat(row.find('input[name="tinggi[]"]').val().replace(/,/g, '')) || 0;
			var j = parseFloat(row.find('input[name="jumlah[]"]').val().replace(/,/g, '')) || 1;
			var vol = ((p * l * t) / 5000) * j;
			row.find('input[name="volume[]"]').val(vol.toFixed(2));
			updateTotalRow();
			tentukanChargeable();
		}

		function updateTotalRow() {
			var total_koli = 0,
				total_volume = 0;
			$(".baris").each(function() {
				total_koli += parseFloat($(this).find('input[name="jumlah[]"]').val().replace(/,/g, '')) || 0;
				total_volume += parseFloat($(this).find('input[name="volume[]"]').val().replace(/,/g, '')) || 0;
			});
			$('#total_koli').val(total_koli.toFixed(0));
			$('#total_volume').val(total_volume.toFixed(2));
			tentukanChargeable();
		}

		function tentukanChargeable() {
			var jenis = $("#jenis_pengiriman").val();
			var berat = parseFloat($('#berat_timbang').val().replace(/,/g, '')) || 0;
			var volume = parseFloat($('#total_volume').val().replace(/,/g, '')) || 0;
			var chargeable = Math.max(berat, volume);

			if (jenis === 'D') chargeable = Math.max(chargeable, 10);

			$('#chargeable').val(chargeable.toFixed(2));
			hitungNominal();
		}

		// ── Tambah / hapus baris ──────────────────────
		var rowCount = 1;

		function updateRowNumbers() {
			$('#table-body .baris').each(function(i) {
				$(this).find('.nomor-urut').text((i + 1) + '.');
			});
		}

		updateRowNumbers();

		$('#addRow').on('click', function() {
			var prev = $('.baris').last();
			var empty = false;

			prev.find('input[type="text"]').each(function() {
				if ($(this).val().trim() === '') {
					empty = true;
					return false;
				}
			});

			if (empty) {
				Swal.fire({
					icon: 'warning',
					title: 'Isi dulu',
					text: 'Lengkapi baris sebelumnya terlebih dahulu.'
				});
				return;
			}

			var newRow = prev.clone();
			newRow.find('input').val('');
			newRow.find('input[name="panjang[]"]').val('0');
			newRow.find('input[name="lebar[]"]').val('0');
			newRow.find('input[name="tinggi[]"]').val('0');
			newRow.find('input[name="jumlah[]"]').val('1');
			newRow.find('input[name="volume[]"]').val('0');
			rowCount++;

			$('#add-row-container').before(newRow);
			updateRowNumbers();
		});

		$(document).on('click', '.hapusRow', function() {
			if ($('.baris').length <= 1) return;
			$(this).closest('.baris').remove();
			updateTotalRow();
			updateRowNumbers();
		});

	});
</script>
