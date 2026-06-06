<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="d-flex align-items-center gap-3">
			<a href="<?= site_url('shipment/detail/' . $shipment->id) ?>" class="btn btn-outline-secondary btn-sm">
				<?= tabler_icon('arrow-left') ?> Kembali
			</a>
			<h2 class="page-title mb-0">Edit Shipment — <span class="text-muted"><?= $shipment->no_resi ?></span></h2>
			<span class="badge bg-warning-lt text-warning ms-2">BOOKED</span>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<?= form_open_multipart('shipment/save_edit/' . $shipment->id, ['id' => 'form-edit']) ?>

		<div class="row row-cards">
			<div class="col-lg-8">
				<div class="row row-cards">

					<!-- RUTE & LAYANAN -->
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
										<?php foreach ($origins as $c): ?>
											<option value="<?= $c->origin ?>" <?= $shipment->origin === $c->origin ? 'selected' : '' ?>><?= $c->origin ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-4 mb-3">
									<label class="form-label required">Kota Tujuan</label>
									<select name="destination" id="destination" class="form-select trigger-price select2" required>
										<option value="<?= $shipment->destination ?>"><?= $shipment->destination ?></option>
									</select>
								</div>
								<div class="col-md-4 mb-3">
									<label class="form-label required">Layanan</label>
									<select name="service_type_id" id="service_type_id" class="form-select trigger-price select2" required>
										<?php foreach ($services as $s): ?>
											<option value="<?= $s->id ?>" <?= $shipment->service_type_id == $s->id ? 'selected' : '' ?>><?= $s->code ?> - <?= $s->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
					</div>

					<!-- DETAIL BARANG -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Detail Barang & Pembayaran</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label required">Kategori Komoditi</label>
										<select name="commodity_id" class="form-select select2" required>
											<?php foreach ($commodities as $cmd): ?>
												<option value="<?= $cmd->id ?>" <?= $shipment->commodity_id == $cmd->id ? 'selected' : '' ?>>[<?= $cmd->code ?>] <?= $cmd->name ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label required">Isi Barang</label>
										<input type="text" name="commodity_detail" class="form-control" value="<?= $shipment->commodity_detail ?>" oninput="this.value = this.value.toUpperCase()" required>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label required">Metode Pembayaran</label>
										<select name="payment_type" class="form-select" required>
											<option value="TRANSFER" <?= $shipment->payment_type === 'TRANSFER' ? 'selected' : '' ?>>TRANSFER (PREPAID)</option>
											<option value="CASH" <?= $shipment->payment_type === 'CASH' ? 'selected' : '' ?>>TUNAI (CASH)</option>
										</select>
									</div>
									<div class="col-12 mt-1">
										<hr class="my-2">
										<label class="form-label">Foto Barang <span class="text-muted small">(kosongkan jika tidak ingin mengganti)</span></label>
										<?php if ($shipment->shipment_photo): ?>
											<div class="mb-2">
												<img src="<?= base_url($shipment->shipment_photo) ?>" class="img-fluid rounded border" style="max-height: 120px;" alt="Foto saat ini">
												<small class="d-block text-muted mt-1">Foto saat ini</small>
											</div>
										<?php endif; ?>
										<input type="file" name="shipment_photo" id="shipment_photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
										<small class="form-hint">Format: JPG, PNG. Maks. 2MB.</small>
										<div class="mt-2 d-none" id="preview-container">
											<img id="photo-preview" src="" class="img-fluid rounded border" style="max-height: 200px;">
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- PICKUP -->
						<div class="card mt-3 border-primary border-2">
							<div class="card-body">
								<label class="form-check form-switch">
									<input class="form-check-input" type="checkbox" name="use_pickup" id="use_pickup" value="1" <?= $shipment->pickup_rate_id ? 'checked' : '' ?>>
									<span class="form-check-label fw-bold">Request Pickup Barang Customer</span>
								</label>
								<div id="pickup_detail" class="mt-3 <?= $shipment->pickup_rate_id ? '' : 'd-none' ?>">
									<label class="form-label required">Area Penjemputan</label>
									<select name="pickup_rate_id" id="pickup_rate_id" class="form-select trigger-price">
										<option value="">- Pilih Area -</option>
										<?php
										$rates = $this->db->get_where('master_pickup_rates', ['is_active' => 1])->result();
										foreach ($rates as $r):
										?>
											<option value="<?= $r->id ?>" data-price="<?= $r->price_smesco ?>" <?= $shipment->pickup_rate_id == $r->id ? 'selected' : '' ?>>
												<?= $r->area_name ?> (Rp <?= number_format($r->price_smesco) ?>)
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
					</div>

					<!-- PENGIRIM -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Data Pengirim</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">No. WhatsApp</label>
									<input type="text" name="sender_phone" class="form-control" value="<?= $shipment->sender_phone ?>" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">Nama Pengirim</label>
									<input type="text" name="sender_name" class="form-control" value="<?= $shipment->sender_name ?>" oninput="this.value = this.value.toUpperCase()" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">Provinsi</label>
									<select name="sender_provinsi" id="sender_provinsi" class="form-select" required>
										<option value="">- Pilih Provinsi -</option>
									</select>
								</div>
								<div class="row g-2 mb-3">
									<div class="col-6">
										<label class="form-label required">Kota/Kabupaten</label>
										<select name="sender_kota" id="sender_kota" class="form-select" required disabled>
											<option value="">- Pilih Kota -</option>
										</select>
									</div>
									<div class="col-6">
										<label class="form-label required">Kecamatan</label>
										<select name="sender_kecamatan" id="sender_kecamatan" class="form-select" required disabled>
											<option value="">- Pilih Kecamatan -</option>
										</select>
									</div>
								</div>
								<div class="mb-3">
									<label class="form-label required">Kelurahan</label>
									<select name="sender_kelurahan" id="sender_kelurahan" class="form-select" required disabled>
										<option value="">- Pilih Kelurahan -</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="form-label required">Detail Alamat</label>
									<textarea name="sender_address_detail" class="form-control" rows="2" oninput="this.value = this.value.toUpperCase()" required><?= $shipment->sender_address_detail ?></textarea>
								</div>
							</div>
						</div>
					</div>

					<!-- PENERIMA -->
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Data Penerima</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">Nama Penerima</label>
									<input type="text" name="receiver_name" class="form-control" value="<?= $shipment->receiver_name ?>" oninput="this.value = this.value.toUpperCase()" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">No. WhatsApp</label>
									<input type="text" name="receiver_phone" class="form-control" value="<?= $shipment->receiver_phone ?>" required>
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
									<label class="form-label required">Kelurahan</label>
									<select name="receiver_kelurahan" id="receiver_kelurahan" class="form-select" required disabled>
										<option value="">- Pilih Kelurahan -</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="form-label required">Detail Alamat</label>
									<textarea name="receiver_address_detail" class="form-control" rows="2" oninput="this.value = this.value.toUpperCase()" required><?= $shipment->receiver_address_detail ?></textarea>
								</div>
							</div>
						</div>
					</div>

					<!-- ADDONS -->
					<div class="col-12 mt-3">
						<div class="card border-info">
							<div class="card-header bg-info-lt">
								<h3 class="card-title text-info"><?= tabler_icon('box') ?> Layanan Tambahan</h3>
							</div>
							<div class="card-body">
								<div class="row g-3">
									<?php foreach ($addons as $addon):
										$icon = 'plus';
										$color = 'blue';
										$desc = '';
										if ($addon->code == 'REPACK') {
											$icon = 'package';
											$desc = 'P x L x T x ' . $addon->base_factor . ' per koli';
										} elseif ($addon->code == 'KAYU') {
											$icon = 'box';
											$color = 'brown';
											$desc = 'Ekstra aman (P+10 x L+10 x T+10)';
										} elseif ($addon->code == 'BUBBLE') {
											$icon = 'circles';
											$color = 'teal';
											$desc = 'Flat Rp ' . number_format($addon->base_factor, 0, ',', '.') . ' / Koli';
										}

										// Cek apakah addon ini aktif di shipment ini
										$is_checked = in_array($addon->id, $addon_ids);
									?>
										<div class="col-md-4">
											<label class="form-selectgroup-item flex-fill h-100">
												<input type="checkbox" name="addons[]" value="<?= $addon->code ?>"
													class="form-selectgroup-input chk-addon"
													data-method="<?= $addon->calc_method ?>"
													data-factor="<?= $addon->base_factor ?>"
													data-min="<?= $addon->min_charge ?>"
													data-name="<?= html_escape($addon->name) ?>"
													<?= $is_checked ? 'checked' : '' ?>>
												<div class="form-selectgroup-label d-flex align-items-center p-3 h-100">
													<div class="me-3"><span class="form-selectgroup-check"></span></div>
													<div class="form-selectgroup-label-content d-flex align-items-center">
														<span class="bg-<?= $color ?>-lt avatar me-3"><?= tabler_icon($icon) ?></span>
														<div class="text-start">
															<div class="fw-bold mb-1"><?= html_escape($addon->name) ?></div>
															<div class="text-muted small"><?= $desc ?></div>
														</div>
													</div>
												</div>
											</label>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!-- SIDEBAR KALKULASI -->
			<div class="col-lg-4">
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">Kalkulasi Berat (Kg)</h3>
					</div>
					<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Total Koli</label>
							<div class="input-group">
								<input type="text" id="total_koli_display" class="form-control bg-light fw-bold" value="<?= $shipment->koli ?>" readonly>
								<span class="input-group-text">Pcs</span>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label required">Total Berat Aktual</label>
							<div class="input-group">
								<input type="text" name="actual_weight" id="actual_weight" class="form-control calc-weight indo-format"
									value="<?= number_format($shipment->actual_weight, 2, ',', '.') ?>" required>
								<span class="input-group-text">Kg</span>
							</div>
						</div>
						<hr>
						<div class="d-flex justify-content-between align-items-center mb-2">
							<label class="form-label mb-0">Dimensi Koli (P x L x T)</label>
							<button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-dim"><?= tabler_icon('plus') ?> Tambah</button>
						</div>
						<div id="dimension-container">
							<?php
							// Group dimensi berdasarkan P x L x T yang sama untuk ditampilkan per baris
							$grouped = [];
							foreach ($dimensions as $dim) {
								$key = $dim->length . '_' . $dim->width . '_' . $dim->height;
								if (!isset($grouped[$key])) {
									$grouped[$key] = ['length' => $dim->length, 'width' => $dim->width, 'height' => $dim->height, 'qty' => 0];
								}
								$grouped[$key]['qty']++;
							}
							foreach ($grouped as $g):
							?>
								<div class="row g-1 dim-row mb-2">
									<div class="col-3"><input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format" value="<?= number_format($g['length'], 0, ',', '.') ?>" placeholder="P"></div>
									<div class="col-3"><input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format" value="<?= number_format($g['width'], 0, ',', '.') ?>" placeholder="L"></div>
									<div class="col-3">
										<div class="input-group">
											<input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format" value="<?= number_format($g['height'], 0, ',', '.') ?>" placeholder="T">
											<button type="button" class="btn btn-danger btn-remove-dim">X</button>
										</div>
									</div>
									<div class="col-3"><input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="<?= $g['qty'] ?>" required></div>
								</div>
							<?php endforeach; ?>
							<?php if (empty($grouped)): ?>
								<div class="row g-1 dim-row mb-2">
									<div class="col-3"><input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format" placeholder="P"></div>
									<div class="col-3"><input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format" placeholder="L"></div>
									<div class="col-3">
										<div class="input-group"><input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format" placeholder="T"></div>
									</div>
									<div class="col-3"><input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="1" required></div>
								</div>
							<?php endif; ?>
						</div>
						<hr>
						<div class="row text-center mb-3 mt-3">
							<div class="col-6">
								<div class="text-muted small">Total Volume</div>
								<div class="h3 mb-0"><span id="lbl_volume">0,00</span> Kg</div>
							</div>
							<div class="col-6 text-primary">
								<div class="small fw-bold">Chargeable Weight</div>
								<div class="h2 mb-0"><span id="lbl_chargeable">0,00</span> Kg</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card bg-primary-lt">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<span class="text-muted">Harga / Kg</span>
							<div class="text-end">
								<span id="badge-tiered" class="badge bg-purple-lt d-none mb-1"><?= tabler_icon('layers-difference', 'icon-sm me-1') ?> Tiered Pricing Active</span><br>
								<span class="font-weight-bold h3 mb-0" id="lbl_price">Rp 0</span>
							</div>
						</div>
						<hr class="my-3 border-secondary opacity-25">
						<div class="d-flex justify-content-between mb-2">
							<span class="text-dark small">Biaya Pengiriman</span>
							<span class="fw-bold text-dark small" id="lbl_shipping_cost">Rp 0</span>
						</div>
						<div class="d-flex justify-content-between mb-2 d-none" id="row_pickup_cost">
							<span class="text-dark small">Biaya Penjemputan</span>
							<span class="fw-bold text-dark small" id="lbl_pickup_cost">Rp 0</span>
						</div>
						<div id="addon_summary_container"></div>
						<hr class="my-3 border-secondary opacity-25">
						<div class="d-flex justify-content-between align-items-center">
							<span class="h3 mb-0">Total Biaya</span>
							<span class="h1 mb-0 text-primary" id="lbl_total">Rp 0</span>
						</div>
						<div id="alert-price" class="alert alert-danger mt-3 d-none" style="padding:0.5rem;font-size:0.8rem;">Harga tidak ditemukan untuk rute ini!</div>
						<button type="submit" id="btn-submit" class="btn btn-warning w-100 mt-4">
							<?= tabler_icon('device-floppy') ?> Simpan Perubahan
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

		const preloadOrigin = "<?= $shipment->origin ?>";
		const preloadDestination = "<?= $shipment->destination ?>";

		// ── DOM Elements (deklarasi duluan sebelum apapun) ──
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

		// ── Helpers ──
		function parseIndoNumber(str) {
			if (!str) return 0;
			return parseFloat(str.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
		}

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

		function maskHandler() {
			let val = this.value.replace(/[^0-9,]/g, '');
			let parts = val.split(',');
			if (parts.length > 2) val = parts[0] + ',' + parts.slice(1).join('');
			let splitVal = val.split(',');
			splitVal[0] = splitVal[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			this.value = splitVal.join(',');
		}

		applyIndoMask();

		// ── calculateAll ──
		function calculateAll() {
			let actual = parseIndoNumber(inputActual.value);
			let totalVolume = 0,
				totalKoli = 0,
				totalAddonFee = 0;
			let activeAddons = [];

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

			document.querySelectorAll('.chk-addon:checked').forEach(chk => {
				let method = chk.dataset.method;
				let factor = parseFloat(chk.dataset.factor);
				let name = chk.dataset.name;
				let feePerItem = 0;
				const min = parseFloat(chk.dataset.min || 0);

				document.querySelectorAll('.dim-row').forEach(row => {
					let p = parseIndoNumber(row.querySelector('.dim-p').value);
					let l = parseIndoNumber(row.querySelector('.dim-l').value);
					let t = parseIndoNumber(row.querySelector('.dim-t').value);
					let q = parseIndoNumber(row.querySelector('.dim-qty').value);
					if (q > 0) {
						let feeKoli = 0;
						if (method === 'VOLUME') feeKoli = p * l * t * factor;
						else if (method === 'VOLUME_PLUS') feeKoli = (p + 10) * (l + 10) * (t + 10) * factor;
						else if (method === 'PER_KOLI') feeKoli = factor;
						feePerItem += Math.max(feeKoli, min) * q;
					}
				});

				if (feePerItem > 0) {
					totalAddonFee += feePerItem;
					activeAddons.push({
						name,
						fee: feePerItem
					});
				}
			});

			totalKoliDisplay.value = totalKoli > 0 ? totalKoli : 1;
			lblVolume.innerText = formatIndoNumber(totalVolume.toFixed(2));

			let chargeable = Math.max(actual, totalVolume);
			if (chargeable < minWeightKg) chargeable = minWeightKg;
			chargeable = Math.ceil(chargeable);
			lblChargeable.innerText = formatIndoNumber(chargeable);

			let shippingCost = chargeable * currentPricePerKg;
			document.getElementById('lbl_shipping_cost').innerText = formatRp(shippingCost);

			let pickupFee = 0;
			const rowPickup = document.getElementById('row_pickup_cost');
			if (usePickupCheck.checked && pickupSelect.value !== '') {
				pickupFee = parseFloat(pickupSelect.options[pickupSelect.selectedIndex].dataset.price || 0);
				document.getElementById('lbl_pickup_cost').innerText = formatRp(pickupFee);
				rowPickup.classList.remove('d-none');
			} else {
				rowPickup.classList.add('d-none');
			}

			const addonContainer = document.getElementById('addon_summary_container');
			addonContainer.innerHTML = '';
			if (activeAddons.length > 0) {
				let html = `<div class="mt-2 mb-1 text-muted fw-bold small">Layanan Tambahan:</div>`;
				activeAddons.forEach(item => {
					html += `<div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small ps-2">&bull; ${item.name}</span>
                    <span class="fw-bold text-warning small">+ ${formatRp(item.fee)}</span>
                </div>`;
				});
				addonContainer.innerHTML = html;
			}

			lblTotal.innerText = formatRp(shippingCost + pickupFee + totalAddonFee);
			btnSubmit.disabled = (shippingCost <= 0);
		}

		// ── checkPrice ──
		function checkPrice() {
			let o = document.getElementById('origin').value;
			let d = document.getElementById('destination').value;
			let s = document.getElementById('service_type_id').value;
			let w = parseIndoNumber(lblChargeable.innerText);
			if (!o || !d || !s) return;

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
				.then(r => r.json())
				.then(res => {
					if (res.status) {
						alertPrice.classList.add('d-none');
						currentPricePerKg = parseFloat(res.data.price_per_kg);
						minWeightKg = parseFloat(res.data.min_weight_kg);
						const bTiered = document.getElementById('badge-tiered');
						res.data.is_tiered == 1 ? bTiered.classList.remove('d-none') : bTiered.classList.add('d-none');
						lblPrice.innerText = formatRp(currentPricePerKg) + ' 🚛';
						calculateAll();
					} else {
						alertPrice.classList.remove('d-none');
						currentPricePerKg = 0;
						calculateAll();
					}
				});
		}

		// ── Wilayah ──
		function initWilayah(type, preloadData) {
			const selProv = $(`#${type}_provinsi`);
			const selKota = $(`#${type}_kota`);
			const selKec = $(`#${type}_kecamatan`);
			const selKel = $(`#${type}_kelurahan`);

			function initS2(el) {
				if (el.hasClass('select2-hidden-accessible')) el.select2('destroy');
				el.select2({
					width: '100%'
				});
			}

			$.ajax({
				url: "<?= site_url('master/ajax_get_provinsi') ?>",
				type: "GET",
				dataType: "json",
				success: function(res) {
					let html = '<option value="">- Pilih Provinsi -</option>';
					res.forEach(item => {
						const sel = item.nama_provinsi === preloadData.provinsi ? 'selected' : '';
						html += `<option value="${item.id}" ${sel}>${item.nama_provinsi}</option>`;
					});
					selProv.html(html);
					initS2(selProv);
					if (preloadData.provinsi) selProv.trigger('change');
				}
			});

			selProv.on('change', function() {
				const id = $(this).val();
				selKota.html('<option value="">- Pilih Kota -</option>').prop('disabled', true);
				selKec.html('<option value="">- Pilih Kecamatan -</option>').prop('disabled', true);
				selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);
				if (!id) return;
				$.ajax({
					url: "<?= site_url('master/ajax_get_kota/') ?>" + id,
					type: "GET",
					dataType: "json",
					success: function(res) {
						let html = '<option value="">- Pilih Kota -</option>';
						res.forEach(item => {
							const sel = item.nama_kota === preloadData.kota ? 'selected' : '';
							html += `<option value="${item.id}" ${sel}>${item.nama_kota}</option>`;
						});
						selKota.html(html).prop('disabled', false);
						initS2(selKota);
						if (preloadData.kota) selKota.trigger('change');
					}
				});
			});

			selKota.on('change', function() {
				const id = $(this).val();
				selKec.html('<option value="">- Pilih Kecamatan -</option>').prop('disabled', true);
				selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);
				if (!id) return;
				$.ajax({
					url: "<?= site_url('master/ajax_get_kecamatan/') ?>" + id,
					type: "GET",
					dataType: "json",
					success: function(res) {
						let html = '<option value="">- Pilih Kecamatan -</option>';
						res.forEach(item => {
							const sel = item.nama_kecamatan === preloadData.kecamatan ? 'selected' : '';
							html += `<option value="${item.id}" ${sel}>${item.nama_kecamatan}</option>`;
						});
						selKec.html(html).prop('disabled', false);
						initS2(selKec);
						if (preloadData.kecamatan) selKec.trigger('change');
					}
				});
			});

			selKec.on('change', function() {
				const id = $(this).val();
				selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);
				if (!id) return;
				$.ajax({
					url: "<?= site_url('master/ajax_get_kelurahan/') ?>" + id,
					type: "GET",
					dataType: "json",
					success: function(res) {
						let html = '<option value="">- Pilih Kelurahan -</option>';
						res.forEach(item => {
							const sel = item.nama_kelurahan === preloadData.kelurahan ? 'selected' : '';
							html += `<option value="${item.id}" ${sel}>${item.nama_kelurahan}</option>`;
						});
						selKel.html(html).prop('disabled', false);
						initS2(selKel);
					}
				});
			});
		}

		// ── Event Listeners ──
		document.querySelectorAll('.chk-addon').forEach(chk => chk.addEventListener('change', calculateAll));

		document.body.addEventListener('input', function(e) {
			if (e.target.classList.contains('calc-weight')) {
				calculateAll();
				if (e.target.id === 'actual_weight' || e.target.closest('.dim-row')) checkPrice();
			}
		});

		usePickupCheck.addEventListener('change', function() {
			const detail = document.getElementById('pickup_detail');
			if (this.checked) {
				detail.classList.remove('d-none');
			} else {
				detail.classList.add('d-none');
				pickupSelect.value = '';
			}
			calculateAll();
		});

		pickupSelect.addEventListener('change', calculateAll);

		document.getElementById('btn-add-dim').addEventListener('click', function() {
			const container = document.getElementById('dimension-container');
			const newRow = document.createElement('div');
			newRow.className = 'row g-1 dim-row mb-2';
			newRow.innerHTML = `
            <div class="col-3"><input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format" placeholder="P"></div>
            <div class="col-3"><input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format" placeholder="L"></div>
            <div class="col-3"><div class="input-group"><input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format" placeholder="T"><button type="button" class="btn btn-danger btn-remove-dim">X</button></div></div>
            <div class="col-3"><input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="1" required></div>`;
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

		document.getElementById('shipment_photo').addEventListener('change', function() {
			const previewContainer = document.getElementById('preview-container');
			const photoPreview = document.getElementById('photo-preview');
			if (!this.files || this.files.length === 0) {
				previewContainer.classList.add('d-none');
				return;
			}
			const file = this.files[0];
			if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
				Swal.fire('Format Tidak Valid', 'Hanya JPG dan PNG.', 'error');
				this.value = '';
				previewContainer.classList.add('d-none');
				return;
			}
			if (file.size > 2 * 1024 * 1024) {
				Swal.fire('File Terlalu Besar', 'Maksimal 2MB.', 'error');
				this.value = '';
				previewContainer.classList.add('d-none');
				return;
			}
			const reader = new FileReader();
			reader.onload = e => {
				photoPreview.src = e.target.result;
				previewContainer.classList.remove('d-none');
			};
			reader.readAsDataURL(file);
		});

		document.getElementById('form-edit').addEventListener('submit', function(e) {
			e.preventDefault();
			if (!this.checkValidity()) {
				this.reportValidity();
				return;
			}
			Swal.fire({
				title: 'Simpan Perubahan?',
				text: 'Data shipment akan diperbarui dan harga akan direcalculate.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, Simpan!',
				cancelButtonText: 'Batal',
				reverseButtons: true
			}).then(result => {
				if (result.isConfirmed) {
					Swal.fire({
						title: 'Menyimpan...',
						allowOutsideClick: false,
						didOpen: () => Swal.showLoading()
					});
					this.submit();
				}
			});
		});

		// ── Init Select2 (SETELAH semua fungsi siap) ──
		if (jQuery().select2) {
			$('.select2').select2({
				width: '100%'
			});
			// Listener untuk service_type_id
			$('#service_type_id').on('change', function() {
				checkPrice();
			});
		}

		// ── Init Wilayah ──
		initWilayah('sender', {
			provinsi: "<?= $shipment->sender_provinsi ?>",
			kota: "<?= $shipment->sender_kota ?>",
			kecamatan: "<?= $shipment->sender_kecamatan ?>",
			kelurahan: "<?= $shipment->sender_kelurahan ?>"
		});
		initWilayah('receiver', {
			provinsi: "<?= $shipment->receiver_provinsi ?>",
			kota: "<?= $shipment->receiver_kota ?>",
			kecamatan: "<?= $shipment->receiver_kecamatan ?>",
			kelurahan: "<?= $shipment->receiver_kelurahan ?>"
		});

		// ── Destination cascade ──
		$('#origin').on('change', function() {
			const origin = $(this).val();
			const destSelect = $('#destination');
			destSelect.html('<option value="">- Pilih Tujuan -</option>');
			if (!origin) return;
			$.ajax({
				url: "<?= site_url('master/ajax_get_domestic_destination_by_origin') ?>",
				type: "GET",
				dataType: "json",
				data: {
					origin: origin
				},
				success: function(res) {
					let html = '<option value="">- Pilih Tujuan -</option>';
					res.forEach(item => {
						const selected = item.destination === preloadDestination ? 'selected' : '';
						html += `<option value="${item.destination}" ${selected}>${item.destination}</option>`;
					});
					destSelect.html(html);
					if (destSelect.hasClass('select2-hidden-accessible')) destSelect.select2('destroy');
					destSelect.select2({
						width: '100%'
					});
					// Listener destination juga perlu dipasang ulang setelah reinit
					destSelect.on('change', function() {
						checkPrice();
					});
					checkPrice();
				}
			});
		});

		// ── TRIGGER PALING TERAKHIR ──
		if (preloadOrigin) $('#origin').trigger('change');
	});
</script>
