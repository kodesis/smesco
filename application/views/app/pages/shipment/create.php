<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<h2 class="page-title">Buat Shipment (Booking Baru)</h2>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<?= form_open_multipart('shipment/create', ['id' => 'form-booking']) ?>

		<div class="row row-cards">
			<div class="col-lg-8">
				<div class="row row-cards">

					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Rute & Layanan</h3>
							</div>
							<div class="card-body row">
								<div class="col-md-4 mb-3">
									<label class="form-label required">Kota Asal</label>
									<select name="origin" id="origin" class="form-select trigger-price select2" required>
										<option value="">- Pilih Asal -</option>
										<?php foreach ($cities as $c):
											if ($c->name == 'JAKARTA') : ?>
												<option value="<?= $c->name ?>"><?= $c->code ?> - <?= $c->name ?></option>
											<?php
											endif ?>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-4 mb-3">
									<label class="form-label required">Kota Tujuan</label>
									<select name="destination" id="destination" class="form-select trigger-price select2" required>
										<option value="">- Pilih Tujuan -</option>
										<?php foreach ($cities as $c): ?>
											<option value="<?= $c->name ?>"><?= $c->code ?> - <?= $c->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-4 mb-3">
									<label class="form-label required">Layanan</label>
									<select name="service_type_id" id="service_type_id" class="form-select trigger-price select2" required>
										<option value="">- Pilih Layanan -</option>
										<?php foreach ($services as $s): ?>
											<option value="<?= $s->id ?>"><?= $s->code ?> - <?= $s->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Data Pengirim</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">Nama Pengirim</label>
									<input type="text" name="sender_name" id="nama_pengirim" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Budi Santoso / PT. Maju Jaya" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">No. WhatsApp</label>
									<input type="text" name="sender_phone" id="telepon_pengirim" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: 081234567890" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">Provinsi</label>
									<select name="sender_provinsi" id="sender_provinsi" class="form-select select2-wilayah" required>
										<option value="">- Pilih Provinsi -</option>
									</select>
								</div>

								<div class="row g-2 mb-3">
									<div class="col-6">
										<label class="form-label required">Kota/Kabupaten</label>
										<select name="sender_kota" id="sender_kota" class="form-select select2-wilayah" required disabled>
											<option value="">- Pilih Kota -</option>
										</select>
									</div>
									<div class="col-6">
										<label class="form-label required">Kecamatan</label>
										<select name="sender_kecamatan" id="sender_kecamatan" class="form-select select2-wilayah" required disabled>
											<option value="">- Pilih Kecamatan -</option>
										</select>
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label required">Kelurahan / Desa</label>
									<select name="sender_kelurahan" id="sender_kelurahan" class="form-select select2-wilayah" required disabled>
										<option value="">- Pilih Kelurahan -</option>
									</select>
								</div>

								<div class="mb-3">
									<label class="form-label required">Detail Jalan / Gedung / RT RW</label>
									<textarea name="sender_address_detail" id="alamat_pengirim" class="form-control" rows="2" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Jl. Sudirman No. 12, Gedung X Lantai 3, RT 01/RW 02" required></textarea>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Data Penerima</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">Nama Penerima</label>
									<input type="text" name="receiver_name" id="nama_penerima" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Ahmad Dahlan / CV. Lintas Nusantara" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">No. WhatsApp</label>
									<input type="text" name="receiver_phone" id="telepon_penerima" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: 081234567890" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">Provinsi</label>
									<select name="receiver_provinsi" id="receiver_provinsi" class="form-select" required>
										<option value="">- Pilih Provinsi -</option>
									</select>
								</div>
								<div class="row g-2 mb-3">
									<div class="col-6">
										<label class="form-label required">Kota/Kabupaten</label>
										<select name="receiver_kota" id="receiver_kota" class="form-select" required disabled>
											<option value="">- Pilih Kota -</option>
										</select>
									</div>
									<div class="col-6">
										<label class="form-label required">Kecamatan</label>
										<select name="receiver_kecamatan" id="receiver_kecamatan" class="form-select" required disabled>
											<option value="">- Pilih Kecamatan -</option>
										</select>
									</div>
								</div>
								<div class="mb-3">
									<label class="form-label required">Kelurahan / Desa</label>
									<select name="receiver_kelurahan" id="receiver_kelurahan" class="form-select" required disabled>
										<option value="">- Pilih Kelurahan -</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="form-label required">Detail Jalan / Gedung / RT RW</label>
									<textarea name="receiver_address_detail" id="alamat_penerima" class="form-control" rows="2" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Jl. Merdeka No. 45, Blok B, RT 03/RW 05" required></textarea>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Detail Barang & Pembayaran</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label required">Kategori Komoditi (IATA)</label>
										<select name="commodity_id" class="form-select select2" required>
											<option value="">- Pilih Kategori -</option>
											<?php foreach ($commodities as $cmd): ?>
												<option value="<?= $cmd->id ?>">[<?= $cmd->code ?>] <?= $cmd->name ?></option>
											<?php endforeach; ?>
										</select>
										<small class="text-muted">Pilih kategori besar untuk menentukan penanganan kargo.</small>
									</div>

									<div class="col-md-6 mb-3">
										<label class="form-label required">Isi Barang (Keterangan Spesifik)</label>
										<input type="text" name="commodity_detail" class="form-control" placeholder="Contoh: IKAN TUNA SEGAR, SPAREPART MOTOR, dll" oninput="this.value = this.value.toUpperCase()" required>
										<small class="text-muted">Ketik detail barang yang akan tertera di resi.</small>
									</div>

									<div class="col-md-6">
										<div class="p-3 border rounded">
											<div class="form-check mb-2">
												<input class="form-check-input" type="checkbox" name="is_valuable" id="is_valuable" value="1" disabled>
												<label class="form-check-label text-danger fw-bold" for="is_valuable">
													<?= tabler_icon('shield-check') ?> Proteksi Barang Berharga (Valuable Goods) 
													<small class="text-muted">Hanya untuk barang dengan nilai tinggi atau mudah rusak. Aktifkan jika diperlukan untuk perlindungan ekstra. (Coming soon)</small>
												</label>
											</div>
											<div class="d-none" id="wrap_goods_value">
												<label class="form-label text-danger">Estimasi Nilai Barang (Rp)</label>
												<div class="input-group">
													<span class="input-group-text">Rp</span>
													<input type="text" name="goods_value" id="goods_value" class="form-control border-danger indo-format" placeholder="0">
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-6 mb-3">
										<label class="form-label required">Metode Pembayaran</label>
										<select name="payment_type" class="form-select" required>
											<option value="TRANSFER">TRANSFER (PREPAID)</option>
											<option value="CASH">TUNAI (CASH)</option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-3">
							<div class="card-header">
								<h3 class="card-title">Bukti Fisik Barang</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">Upload Foto Barang</label>
									<input type="file" name="shipment_photo" id="shipment_photo" class="form-control" accept="image/jpeg, image/png, image/jpg" required>
									<small class="form-hint">Format yang diizinkan: JPG, JPEG, PNG. Maksimal ukuran 2MB.</small>
								</div>

								<div class="mt-2 d-none" id="preview-container">
									<img id="photo-preview" src="" alt="Preview Foto Barang" class="img-fluid rounded border" style="max-height: 200px;">
								</div>
							</div>
						</div>

						<div class="card mt-3 border-primary">
							<div class="card-body">
								<label class="form-check form-switch">
									<input class="form-check-input" type="checkbox" name="use_pickup" id="use_pickup" value="1">
									<span class="form-check-label fw-bold">Request Pickup Barang Customer (Penjemputan)</span>
								</label>

								<div id="pickup_detail" class="mt-3 d-none">
									<label class="form-label required">Area Penjemputan</label>
									<select name="pickup_rate_id" id="pickup_rate_id" class="form-select trigger-price">
										<option value="">- Pilih Area -</option>
										<?php
										$rates = $this->db->get_where('master_pickup_rates', ['is_active' => 1])->result();
										foreach ($rates as $r):
										?>
											<option value="<?= $r->id ?>" data-price="<?= $r->price_smesco ?>">
												<?= $r->area_name ?> (Rp <?= number_format($r->price_smesco) ?>)
											</option>
										<?php endforeach; ?>
									</select>
									<small class="text-danger">* Minimal penjemputan adalah 50 Kg.</small>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-4">
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">Kalkulasi Berat (Kg)</h3>
					</div>
					<div class="card-body">

						<div class="mb-3">
							<label class="form-label">Total Koli</label>
							<div class="input-group">
								<input type="text" id="total_koli_display" class="form-control bg-light fw-bold" value="1" readonly tabindex="-1">
								<span class="input-group-text">Pcs</span>
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label required">Total Berat Aktual (Timbangan)</label>
							<div class="input-group">
								<input type="text" name="actual_weight" id="actual_weight" class="form-control calc-weight indo-format" placeholder="0,00" required>
								<span class="input-group-text">Kg</span>
							</div>
						</div>

						<hr>

						<div class="d-flex justify-content-between align-items-center mb-2">
							<label class="form-label mb-0">Dimensi Koli (P x L x T)</label>
							<button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-dim">
								<?= tabler_icon('plus') ?> Tambah
							</button>
						</div>

						<div id="dimension-container">
							<div class="row g-1 dim-row mb-2">
								<div class="col-3">
									<input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format" placeholder="P">
								</div>
								<div class="col-3">
									<input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format" placeholder="L">
								</div>
								<div class="col-3">
									<div class="input-group">
										<input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format" placeholder="T">
									</div>
								</div>
								<div class="col-3">
									<input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" placeholder="Qty" value="1" required>
								</div>
							</div>
						</div>

						<hr>

						<div class="row text-center mb-3 mt-3">
							<div class="col-6">
								<div class="text-muted small">Total Volume</div>
								<div class="h3 mb-0"><span id="lbl_volume">0.00</span> Kg</div>
							</div>
							<div class="col-6 text-primary">
								<div class="small fw-bold">Chargeable Weight</div>
								<div class="h2 mb-0"><span id="lbl_chargeable">0.00</span> Kg</div>
							</div>
						</div>

					</div>
				</div>

				<div class="card bg-primary-lt">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<span class="text-muted">Harga / Kg</span>
							<div class="text-end">
								<span id="badge-tiered" class="badge bg-purple-lt d-none mb-1">
									<?= tabler_icon('layers-difference', 'icon-sm me-1') ?> Tiered Pricing Active
								</span>
								<br>
								<span class="font-weight-bold h4 mb-0" id="lbl_price">Rp 0</span>
							</div>
						</div>
						<hr class="my-2">
						<div class="d-flex justify-content-between align-items-center">
							<span class="h3 mb-0">Total Biaya</span>
							<span class="h1 mb-0 text-primary" id="lbl_total">Rp 0</span>
						</div>

						<div id="alert-price" class="alert alert-danger mt-3 d-none" style="padding: 0.5rem; font-size: 0.8rem;">
							Harga tidak ditemukan untuk rute ini!
						</div>

						<button type="submit" id="btn-submit" class="btn btn-primary w-100 mt-4" disabled>
							<?= tabler_icon('truck-delivery') ?> Buat Shipment
						</button>
					</div>
				</div>
			</div>

		</div>
		<?= form_close() ?>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const pembagi_volume = 5000;

		// --- Inisiasi Select2 ---
		if (jQuery().select2) {
			$('.select2').select2({
				width: '100%'
			});
			$('.select2').on('change', function() {
				checkPrice();
			});
		}

		// --- DOM Elements ---
		const inputActual = document.getElementById('actual_weight');
		const lblVolume = document.getElementById('lbl_volume');
		const lblChargeable = document.getElementById('lbl_chargeable');
		const lblPrice = document.getElementById('lbl_price');
		const lblTotal = document.getElementById('lbl_total');
		const btnSubmit = document.getElementById('btn-submit');
		const alertPrice = document.getElementById('alert-price');
		const totalKoliDisplay = document.getElementById('total_koli_display');
		const usePickupCheck = document.getElementById('use_pickup');
		const pickupSelect = document.getElementById('pickup_rate_id');

		let currentPricePerKg = 0;
		let minWeightKg = 1;

		// ==========================================
		// FUNGSI HELPER (Format & Parse) - FIXED
		// ==========================================
		function parseIndoNumber(str) {
			if (!str) return 0;
			// Hapus titik (ribuan), ganti koma jadi titik (desimal)
			let cleaned = str.toString().replace(/\./g, '').replace(/,/g, '.');
			return parseFloat(cleaned) || 0;
		}

		// BARU - GANTI DENGAN INI
		function formatIndoNumber(num) {
			let str = num.toString().replace('.', ',');
			let parts = str.split(',');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			return parts.join(',');
		}

		function formatRp(angka) {
			return new Intl.NumberFormat('id-ID', {
				style: 'currency',
				currency: 'IDR',
				minimumFractionDigits: 0
			}).format(angka);
		}

		function applyIndoMask() {
			document.querySelectorAll('.indo-format').forEach(el => {
				el.removeEventListener('input', maskHandler);
				el.addEventListener('input', maskHandler);
			});
		}

		// BARU - GANTI DENGAN INI
		function maskHandler(e) {
			let val = this.value.replace(/[^0-9,]/g, '');
			let parts = val.split(',');

			if (parts.length > 2) val = parts[0] + ',' + parts.slice(1).join('');

			let splitVal = val.split(',');
			splitVal[0] = splitVal[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			this.value = splitVal.join(',');
		}

		applyIndoMask();

		// ==========================================
		// CORE LOGIC: KALKULASI BERAT & TOTAL
		// ==========================================
		function calculateAll() {
			let actual = parseIndoNumber(inputActual.value);
			let totalVolume = 0;
			let totalKoli = 0;

			document.querySelectorAll('.dim-row').forEach(row => {
				let p = parseIndoNumber(row.querySelector('.dim-p').value);
				let l = parseIndoNumber(row.querySelector('.dim-l').value);
				let t = parseIndoNumber(row.querySelector('.dim-t').value);
				let q = parseIndoNumber(row.querySelector('.dim-qty').value);

				if (q > 0) {
					totalKoli += q;
					totalVolume += ((p * l * t) / pembagi_volume) * q;
				}
			});

			totalKoliDisplay.value = totalKoli > 0 ? totalKoli : 1;
			lblVolume.innerText = formatIndoNumber(totalVolume.toFixed(2));

			let chargeable = Math.max(actual, totalVolume);
			if (chargeable < minWeightKg) chargeable = minWeightKg;
			chargeable = Math.ceil(chargeable);
			lblChargeable.innerText = formatIndoNumber(chargeable);

			let pickupFee = 0;
			if (usePickupCheck.checked && pickupSelect.value !== "") {
				const selectedOption = pickupSelect.options[pickupSelect.selectedIndex];
				pickupFee = parseFloat(selectedOption.dataset.price || 0);
			}

			let shippingCost = chargeable * currentPricePerKg;
			let grandTotal = shippingCost + pickupFee;

			lblTotal.innerText = formatRp(grandTotal);
			btnSubmit.disabled = (shippingCost <= 0);
		}

		// ==========================================
		// AJAX: CEK HARGA
		// ==========================================
		function checkPrice() {
			let o = document.getElementById('origin').value;
			let d = document.getElementById('destination').value;
			let s = document.getElementById('service_type_id').value;
			let w = parseIndoNumber(lblChargeable.innerText);

			if (o && d && s) {
				let formData = new FormData();
				formData.append('origin', o);
				formData.append('destination', d);
				formData.append('service_type_id', s);
				formData.append('weight', w);

				fetch("<?= site_url('master/ajax_cek_harga') ?>", {
						method: 'POST',
						body: formData,
						headers: {
							"X-Requested-With": "XMLHttpRequest"
						}
					})
					.then(response => response.json())
					.then(res => {
						if (res.status) {
							alertPrice.classList.add('d-none');
							currentPricePerKg = parseFloat(res.data.price_per_kg);
							minWeightKg = parseFloat(res.data.min_weight_kg);

							const bTiered = document.getElementById('badge-tiered');
							res.data.is_tiered == 1 ? bTiered.classList.remove('d-none') : bTiered.classList.add('d-none');

							const icon = res.data.category === 'INTERNATIONAL' ? ' ✈️' : ' 🚛';
							lblPrice.innerText = formatRp(currentPricePerKg) + icon;

							calculateAll();
						} else {
							alertPrice.classList.remove('d-none');
							currentPricePerKg = 0;
							calculateAll();
						}
					});
			}
		}



		// --- Autocomplete Logic (Tetap Sama) ---
		// if (jQuery().autocomplete) {
		// 	$("#nama_pengirim, #nama_penerima").autocomplete({
		// 		source: function(request, response) {
		// 			$.ajax({
		// 				url: "<?= site_url('shipment/autocompleteCustomer') ?>",
		// 				dataType: "json",
		// 				data: {
		// 					term: request.term
		// 				},
		// 				success: function(data) {
		// 					response(data);
		// 				}
		// 			});
		// 		},
		// 		minLength: 2,
		// 		select: function(event, ui) {
		// 			const prefix = event.target.id === 'nama_pengirim' ? 'pengirim' : 'penerima';
		// 			$(`#nama_${prefix}`).val(ui.item.nama_customer);
		// 			$(`#telepon_${prefix}`).val(ui.item.telepon_customer);
		// 			$(`#alamat_${prefix}`).val(ui.item.alamat_customer);
		// 		}
		// 	});
		// }

		// ==========================================
		// EVENT LISTENERS
		// ==========================================
		document.body.addEventListener('input', function(e) {
			if (e.target.classList.contains('calc-weight')) {
				calculateAll();
				if (e.target.id === 'actual_weight' || e.target.closest('.dim-row')) {
					checkPrice();
				}
			}
		});

		usePickupCheck.addEventListener('change', function() {
			const detail = document.getElementById('pickup_detail');
			const weight = parseIndoNumber(lblChargeable.innerText);

			if (this.checked) {
				if (weight < 50) {
					Swal.fire('Opps!', 'Minimal berat untuk pickup adalah 50 Kg, bro.', 'warning');
					this.checked = false;
					return;
				}
				detail.classList.remove('d-none');
			} else {
				detail.classList.add('d-none');
				pickupSelect.value = '';
			}
			calculateAll();
		});

		pickupSelect.addEventListener('change', function() {
			calculateAll();
		});

		document.getElementById('is_valuable').addEventListener('change', function() {
			let box = document.getElementById('wrap_goods_value');
			let inputVal = document.getElementById('goods_value');
			if (this.checked) {
				box.classList.remove('d-none');
				inputVal.setAttribute('required', 'required');
			} else {
				box.classList.add('d-none');
				inputVal.removeAttribute('required');
				inputVal.value = '';
			}
		});

		document.getElementById('btn-add-dim').addEventListener('click', function() {
			const container = document.getElementById('dimension-container');
			const newRow = document.createElement('div');
			newRow.className = 'row g-1 dim-row mb-2';
			newRow.innerHTML = `
             <div class="col-3"><input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format" placeholder="P"></div>
             <div class="col-3"><input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format" placeholder="L"></div>
             <div class="col-3">
                <div class="input-group">
                   <input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format" placeholder="T">
                   <button type="button" class="btn btn-danger btn-remove-dim">X</button>
                </div>
             </div>
             <div class="col-3"><input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="1" required></div>
          `;
			container.appendChild(newRow);
			applyIndoMask();
			calculateAll();
		});

		document.body.addEventListener('click', function(e) {
			if (e.target.classList.contains('btn-remove-dim')) {
				e.target.closest('.dim-row').remove();
				calculateAll();
			}
		});

		const form = document.getElementById('form-booking');
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			if (!form.checkValidity()) {
				form.reportValidity();
				return;
			}

			Swal.fire({
				title: 'Konfirmasi Booking?',
				text: "Pastikan semua data sudah benar sebelum diproses.",
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Ya, Buat Shipment!',
				cancelButtonText: 'Cek Lagi',
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					Swal.fire({
						title: 'Memproses...',
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
					form.submit();
				}
			});
		});
	});

	// Function global buat init Select2 wilayah
	function initWilayah(type) {
		const selProv = $(`#${type}_provinsi`);
		const selKota = $(`#${type}_kota`);
		const selKec = $(`#${type}_kecamatan`);
		const selKel = $(`#${type}_kelurahan`);

		// Helper: init atau reinit select2 pada elemen
		function initS2(el) {
			if (el.hasClass('select2-hidden-accessible')) {
				el.select2('destroy');
			}
			el.select2({
				width: '100%'
			});
		}

		// 1. Load Provinsi → BARU init Select2-nya
		$.ajax({
			url: "<?= site_url('master/ajax_get_provinsi') ?>",
			type: "GET",
			dataType: "json",
			success: function(res) {
				let html = '<option value="">- Pilih Provinsi -</option>';
				res.forEach(item => {
					html += `<option value="${item.id}">${item.nama_provinsi}</option>`;
				});
				selProv.html(html);
				initS2(selProv); // ← Select2 init SETELAH data ada
			}
		});

		// 2. Provinsi → Kota
		selProv.on('change', function() {
			let id = $(this).val();

			selKota.html('<option value="">- Pilih Kota -</option>').prop('disabled', true);
			selKec.html('<option value="">- Pilih Kecamatan -</option>').prop('disabled', true);
			selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);

			if (id) {
				$.ajax({
					url: "<?= site_url('master/ajax_get_kota/') ?>" + id,
					type: "GET",
					dataType: "json",
					success: function(res) {
						let html = '<option value="">- Pilih Kota -</option>';
						res.forEach(item => {
							html += `<option value="${item.id}">${item.nama_kota}</option>`;
						});
						selKota.html(html).prop('disabled', false);
						initS2(selKota); // ← reinit setelah data masuk
					}
				});
			}
		});

		// 3. Kota → Kecamatan
		selKota.on('change', function() {
			let id = $(this).val();

			selKec.html('<option value="">- Pilih Kecamatan -</option>').prop('disabled', true);
			selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);

			if (id) {
				$.ajax({
					url: "<?= site_url('master/ajax_get_kecamatan/') ?>" + id,
					type: "GET",
					dataType: "json",
					success: function(res) {
						let html = '<option value="">- Pilih Kecamatan -</option>';
						res.forEach(item => {
							html += `<option value="${item.id}">${item.nama_kecamatan}</option>`;
						});
						selKec.html(html).prop('disabled', false);
						initS2(selKec);
					}
				});
			}
		});

		// 4. Kecamatan → Kelurahan
		selKec.on('change', function() {
			let id = $(this).val();
			selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);

			if (id) {
				$.ajax({
					url: "<?= site_url('master/ajax_get_kelurahan/') ?>" + id,
					type: "GET",
					dataType: "json",
					success: function(res) {
						let html = '<option value="">- Pilih Kelurahan -</option>';
						res.forEach(item => {
							html += `<option value="${item.id}">${item.nama_kelurahan}</option>`;
						});
						selKel.html(html).prop('disabled', false);
						initS2(selKel);
					}
				});
			}
		});
	}

	// Di ready: JANGAN init select2-wilayah di sini
	$(document).ready(function() {
		initWilayah('sender');
		initWilayah('receiver');
	});

	// Tambahkan di dalam blok <script> lu
	document.getElementById('shipment_photo').addEventListener('change', function() {
		const previewContainer = document.getElementById('preview-container');
		const photoPreview = document.getElementById('photo-preview');

		// Guard: tidak ada file (user cancel dialog)
		if (!this.files || this.files.length === 0) {
			previewContainer.classList.add('d-none');
			return;
		}

		const file = this.files[0]; // ← ambil File object dari index 0

		// Validasi tipe
		const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
		if (!allowedTypes.includes(file.type)) {
			Swal.fire('Format Tidak Valid', 'Hanya JPG, JPEG, dan PNG yang diizinkan.', 'error');
			this.value = '';
			previewContainer.classList.add('d-none');
			return;
		}

		// Validasi ukuran 2MB
		if (file.size > 2 * 1024 * 1024) {
			Swal.fire('File Terlalu Besar', 'Maksimal ukuran foto adalah 2MB.', 'error');
			this.value = '';
			previewContainer.classList.add('d-none');
			return;
		}

		// Preview
		const reader = new FileReader();
		reader.onload = function(e) {
			photoPreview.src = e.target.result;
			previewContainer.classList.remove('d-none');
		};
		reader.readAsDataURL(file); // ← sekarang File object, bukan FileList
	});
</script>
