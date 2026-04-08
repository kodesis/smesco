<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<h2 class="page-title">Buat Shipment (Booking Baru)</h2>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<?= form_open('shipment/create', ['id' => 'form-booking']) ?>

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
									<label class="form-label required">Alamat Lengkap</label>
									<textarea name="sender_address" id="alamat_pengirim" class="form-control" rows="3" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Jl. Sudirman No. 12, RT 01/RW 02, Gedung X Lantai 3..." required></textarea>
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
									<label class="form-label required">Alamat Lengkap</label>
									<textarea name="receiver_address" id="alamat_penerima" class="form-control" rows="3" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Jl. Merdeka No. 45, Komplek Y, Blok B..." required></textarea>
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
										<div class="p-3 border rounded bg-light-lt">
											<div class="form-check mb-2">
												<input class="form-check-input" type="checkbox" name="is_valuable" id="is_valuable" value="1">
												<label class="form-check-label text-danger fw-bold" for="is_valuable">
													<?= tabler_icon('shield-check') ?> Proteksi Barang Berharga (Valuable Goods)
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

									<div class="col-md-3 mb-3">
										<label class="form-label required">Metode Pembayaran</label>
										<select name="payment_type" class="form-select" required>
											<option value="TRANSFER">TRANSFER (PREPAID)</option>
											<option value="CASH">TUNAI (CASH)</option>
										</select>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label">Total Koli</label>
										<div class="input-group">
											<input type="text" id="total_koli_display" class="form-control bg-light fw-bold" value="1" readonly tabindex="-1">
											<span class="input-group-text">Pcs</span>
										</div>
									</div>
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
							<span class="font-weight-bold" id="lbl_price">Rp 0</span>
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

		// Inisiasi Select2
		if (jQuery().select2) {
			$('.select2').select2({
				width: '100%'
			});
			// Karena select2 menimpa input asli, tangkap event change via jQuery
			$('.select2').on('change', function() {
				checkPrice();
			});
		}

		// DOM Elements
		const inputActual = document.getElementById('actual_weight');
		const lblVolume = document.getElementById('lbl_volume');
		const lblChargeable = document.getElementById('lbl_chargeable');
		const lblPrice = document.getElementById('lbl_price');
		const lblTotal = document.getElementById('lbl_total');
		const btnSubmit = document.getElementById('btn-submit');
		const alertPrice = document.getElementById('alert-price');
		const totalKoliDisplay = document.getElementById('total_koli_display');

		let currentPricePerKg = 0;
		let minWeightKg = 1;

		// ==========================================
		// FUNGSI FORMAT ANGKA (Titik & Koma)
		// ==========================================
		function parseIndoNumber(str) {
			if (!str) return 0;
			str = str.toString().replace(/\./g, '').replace(/,/g, '.');
			return parseFloat(str) || 0;
		}

		function formatIndoNumber(num) {
			let str = num.toString().replace('.', ',');
			let parts = str.split(',');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, "."); // ← fix
			return parts.join(',');
		}

		function applyIndoMask() {
			document.querySelectorAll('.indo-format').forEach(el => {
				el.removeEventListener('input', maskHandler); // Mencegah duplicate event
				el.addEventListener('input', maskHandler);
			});
		}

		function maskHandler(e) {
			let val = this.value.replace(/[^0-9,]/g, '');
			let parts = val.split(',');
			if (parts.length > 2) {
				val = parts[0] + ',' + parts.slice(1).join('');
			}
			if (val) {
				let splitVal = val.split(',');
				splitVal[0] = splitVal[0].replace(/\B(?=(\d{3})+(?!\d))/g, "."); // ← fix
				this.value = splitVal.join(',');
			} else {
				this.value = '';
			}
		}

		// Terapkan mask pertama kali
		applyIndoMask();

		function formatRp(angka) {
			return new Intl.NumberFormat('id-ID', {
				style: 'currency',
				currency: 'IDR',
				minimumFractionDigits: 0
			}).format(angka);
		}

		// ==========================================
		// FUNGSI KALKULASI BERAT & TOTAL
		// ==========================================
		function calculateAll() {
			let actual = parseIndoNumber(inputActual.value);
			let totalVolume = 0;
			let totalKoli = 0;

			// Hitung dari Multi-Row Dimensi
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

			// Update Tampilan Koli & Volume
			totalKoliDisplay.value = totalKoli > 0 ? totalKoli : 1;
			lblVolume.innerText = formatIndoNumber(totalVolume.toFixed(2));

			// Hitung Chargeable Weight
			let chargeable = Math.max(actual, totalVolume);
			if (chargeable < minWeightKg) chargeable = minWeightKg;
			chargeable = Math.ceil(chargeable); // Pembulatan ke atas

			lblChargeable.innerText = formatIndoNumber(chargeable);

			// Hitung Total Uang
			let total = chargeable * currentPricePerKg;
			lblTotal.innerText = formatRp(total);

			btnSubmit.disabled = (total <= 0);
		}
		$("#origin, #destination, #service_type_id").on("change", function() {
			checkPrice();
		});

		// ==========================================
		// FUNGSI AJAX CEK HARGA
		// ==========================================
		function checkPrice() {
			let o = document.getElementById('origin').value;
			let d = document.getElementById('destination').value;
			let s = document.getElementById('service_type_id').value;

			if (o && d && s) {
				let url = "<?= site_url('master/ajax_cek_harga') ?>";
				let formData = new FormData();
				formData.append('origin', o);
				formData.append('destination', d);
				formData.append('service_type_id', s);

				fetch(url, {
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
							lblPrice.innerText = formatRp(currentPricePerKg);
							calculateAll();
						} else {
							alertPrice.classList.remove('d-none');
							currentPricePerKg = 0;
							lblPrice.innerText = "Rp 0";
							calculateAll();
						}
					})
					.catch(err => console.log(err));
			}
		}

		// ==========================================
		// EVENT LISTENERS
		// ==========================================
		// Listener untuk input berat dan dimensi (Event delegate karena baris bisa ditambah)
		document.body.addEventListener('input', function(e) {
			if (e.target.classList.contains('calc-weight')) {
				calculateAll();
			}
		});

		// Barang Berharga Toggle
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

		// Multi Row Dimensi: Tambah
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
                  <button type="button" class="btn btn-danger btn-remove-dim" title="Hapus">X</button>
               </div>
            </div>
            <div class="col-3"><input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" placeholder="Qty" value="1" required></div>
         `;
			container.appendChild(newRow);

			// Apply mask ke element baru
			applyIndoMask();
			calculateAll();
		});

		// Multi Row Dimensi: Hapus
		document.body.addEventListener('click', function(e) {
			if (e.target.classList.contains('btn-remove-dim')) {
				e.target.closest('.dim-row').remove();
				calculateAll();
			}
		});

		// ==========================================
		// AUTOCOMPLETE (Sudah Pakai jQuery Global)
		// ==========================================
		if (jQuery().autocomplete) {
			$("#nama_pengirim").autocomplete({
				source: function(request, response) {
					$.ajax({
						url: "<?= site_url('shipment/autocompleteCustomer') ?>",
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
				select: function(event, ui) {
					$("#nama_pengirim").val(ui.item.nama_customer);
					$("#telepon_pengirim").val(ui.item.telepon_customer);
					$("#alamat_pengirim").val(ui.item.alamat_customer);
				}
			});

			$("#nama_penerima").autocomplete({
				source: function(request, response) {
					$.ajax({
						url: "<?= site_url('shipment/autocompleteCustomer') ?>",
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
				select: function(event, ui) {
					$("#nama_penerima").val(ui.item.nama_customer);
					$("#telepon_penerima").val(ui.item.telepon_customer);
					$("#alamat_penerima").val(ui.item.alamat_customer);
				}
			});
		}
	});

	// ==========================================
	// LOGIC SUBMIT DENGAN SWEETALERT2
	// ==========================================
	const form = document.getElementById('form-booking');

	form.addEventListener('submit', function(e) {
		e.preventDefault(); // Stop submit manual

		// 1. Cek validasi HTML5 (required, dll)
		if (!form.checkValidity()) {
			form.reportValidity(); // Munculin bubble error bawaan browser
			return;
		}

		// 2. Jika valid, munculkan SweetAlert
		Swal.fire({
			title: 'Konfirmasi Booking?',
			text: "Pastikan data pengirim, penerima, dan berat barang sudah benar.",
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#206bc4', // Warna biru Tabler
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ya, Buat Shipment!',
			cancelButtonText: 'Cek Lagi',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				// Tampilkan loading saat submit
				Swal.fire({
					title: 'Memproses...',
					text: 'Harap tunggu sebentar',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				form.submit(); // Kirim data ke PHP
			}
		});
	});

	// Update toggle barang berharga (sesuai permintaan validasi)
	document.getElementById('is_valuable').addEventListener('change', function() {
		let box = document.getElementById('wrap_goods_value');
		let inputVal = document.getElementById('goods_value');
		if (this.checked) {
			box.classList.remove('d-none');
			inputVal.setAttribute('required', 'required'); // Wajib isi kalau dicentang
		} else {
			box.classList.add('d-none');
			inputVal.removeAttribute('required'); // Tidak wajib kalau tidak dicentang
			inputVal.value = '';
		}
	});
</script>
