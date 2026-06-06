<!-- create_intl.php -->
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<h2 class="page-title">Buat Shipment (Booking Baru)</h2>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<div class="mb-4">
			<ul class="nav nav-tabs" data-bs-toggle="tabs">
				<li class="nav-item">
					<a href="<?= site_url('shipment/create') ?>" class="nav-link text-muted">
						<?= tabler_icon('truck', 'me-1') ?> Pengiriman Domestik
					</a>
				</li>
				<li class="nav-item">
					<a href="<?= site_url('shipment/create_intl') ?>" class="nav-link active fw-bold text-primary">
						<?= tabler_icon('plane-departure', 'me-1') ?> Pengiriman Internasional
					</a>
				</li>
			</ul>
		</div>
		<?= form_open_multipart('shipment/save_intl', ['id' => 'form-booking-intl']) ?>

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
										<?php
										foreach ($origins as $c): ?>
											<option value="<?= $c->origin ?>"><?= $c->origin ?></option>
										<?php
										endforeach; ?>
									</select>
								</div>
								<div class="col-md-4 mb-3">
									<label class="form-label required">Negara Tujuan</label>
									<select name="destination_country" id="destination" class="form-select trigger-price select2" required>
										<option value="">- Pilih Negara -</option>
									</select>
								</div>
								<div class="col-md-4 mb-3">
									<label class="form-label required">Layanan (Ekspor)</label>
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



									<div class="col-md-6 mb-3">
										<label class="form-label required">Metode Pembayaran</label>
										<select name="payment_type" class="form-select" required>
											<option value="TRANSFER">TRANSFER (PREPAID)</option>
											<option value="CASH">TUNAI (CASH)</option>
										</select>
									</div>

									<div class="col-md-6 mb-3">
										<div class="p-3 border rounded h-100">
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
										<label class="form-label required">Isi Barang (English Description)</label>
										<input type="text" name="commodity_detail_en" class="form-control" placeholder="e.g. CASSAVA CHIPS, COTTON T-SHIRT" oninput="this.value = this.value.toUpperCase()" required>
										<small class="text-muted">Wajib bahasa Inggris untuk keperluan Bea Cukai tujuan.</small>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label required">Nilai Barang (Customs Value - USD)</label>
										<div class="input-group">
											<span class="input-group-text">US$</span>
											<input type="text" name="customs_value_usd" class="form-control indo-format" placeholder="0,00" required>
										</div>
										<small class="text-muted">Estimasi harga barang untuk pajak masuk.</small>
									</div>

									<div class="col-12 mt-1">
										<hr class="my-2">
										<label class="form-label required">Foto Barang</label>
										<input type="file" name="shipment_photo" id="shipment_photo" class="form-control" accept="image/jpeg, image/png, image/jpg" required>
										<small class="form-hint">Format: JPG, JPEG, PNG. Maks. 2MB.</small>
										<div class="mt-2 d-none" id="preview-container">
											<img id="photo-preview" src="" alt="Preview Foto Barang" class="img-fluid rounded border" style="max-height: 200px;">
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-3 border-primary border-2">
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

					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Data Pengirim</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">No. WhatsApp</label>
									<input type="text" name="sender_phone" id="telepon_pengirim" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: 081234567890" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">Nama Pengirim</label>
									<input type="text" name="sender_name" id="nama_pengirim" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: Budi Santoso / PT. Maju Jaya" required>
								</div>
								<div class="mb-3">
									<label class="form-label">NIK (Nomor KTP)</label>
									<input type="text" name="sender_nik" id="nik_pengirim" class="form-control"
										maxlength="16" placeholder="16 digit nomor KTP"
										oninput="this.value = this.value.replace(/\D/g, '')">
									<small class="text-muted">Opsional. Diperlukan untuk barang berharga.</small>
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
							<div class="card-header bg-success-lt">
								<h3 class="card-title text-success"><?= tabler_icon('user-check') ?> Data Penerima (Consignee)</h3>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<label class="form-label required">Nama Penerima / Perusahaan</label>
									<input type="text" name="receiver_name" id="nama_penerima" class="form-control" oninput="this.value = this.value.toUpperCase()" required>
								</div>
								<div class="mb-3">
									<label class="form-label required">No. Telepon / WhatsApp (Gunakan Kode Negara)</label>
									<input type="text" name="receiver_phone" id="telepon_penerima" class="form-control" placeholder="Contoh: +60123456789" required>
								</div>

								<div class="row g-2 mb-3">
									<div class="col-8">
										<label class="form-label required">Kota / State / Province</label>
										<input type="text" name="receiver_city" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="e.g. KUALA LUMPUR / CALIFORNIA" required>
									</div>
									<div class="col-4">
										<label class="form-label required">Zip/Postal Code</label>
										<input type="text" name="receiver_zipcode" class="form-control" placeholder="e.g. 50450" required>
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label required">Full Address (Jalan, Blok, Unit)</label>
									<textarea name="receiver_address_detail" id="alamat_penerima" class="form-control" rows="3" oninput="this.value = this.value.toUpperCase()" required></textarea>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12 mt-3">
						<div class="card border-info">
							<div class="card-header bg-info-lt">
								<h3 class="card-title text-info"><?= tabler_icon('box') ?> Layanan Tambahan (Opsional)</h3>
							</div>
							<div class="card-body">
								<div class="row g-3">
									<?php foreach ($addons as $addon): ?>
										<?php
										// Sesuaikan icon dan deskripsi visual berdasarkan tipe Add-on
										$icon = 'plus';
										$desc = '';
										$color = 'blue';

										if ($addon->code == 'REPACK') {
											$icon = 'package';
											$color = 'blue';
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
										?>
										<div class="col-md-4">
											<label class="form-selectgroup-item flex-fill h-100">
												<input type="checkbox" name="addons[]" value="<?= $addon->code ?>"
													class="form-selectgroup-input chk-addon"
													data-method="<?= $addon->calc_method ?>"
													data-factor="<?= $addon->base_factor ?>"
													data-name="<?= html_escape($addon->name) ?>">

												<div class="form-selectgroup-label d-flex align-items-center p-3 h-100">
													<div class="me-3">
														<span class="form-selectgroup-check"></span>
													</div>
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
						<div class="d-flex justify-content-between align-items-center mb-3">
							<span class="text-muted">Harga / Kg</span>
							<div class="text-end">
								<span id="badge-tiered" class="badge bg-purple-lt d-none mb-1">
									<?= tabler_icon('layers-difference', 'icon-sm me-1') ?> Tiered Pricing Active
								</span>
								<br>
								<span class="font-weight-bold h3 mb-0" id="lbl_price">Rp 0</span>
							</div>
						</div>

						<hr class="my-3 border-secondary opacity-25">

						<div class="text-muted fw-bold mb-2 small text-uppercase tracking-wide">Rincian Tagihan</div>

						<div class="d-flex justify-content-between align-items-center mb-2">
							<span class="text-dark small">Biaya Pengiriman</span>
							<span class="fw-bold text-dark small" id="lbl_shipping_cost">Rp 0</span>
						</div>

						<div class="d-flex justify-content-between align-items-center mb-2 d-none" id="row_pickup_cost">
							<span class="text-dark small">Biaya Penjemputan</span>
							<span class="fw-bold text-dark small" id="lbl_pickup_cost">Rp 0</span>
						</div>

						<div id="addon_summary_container"></div>

						<hr class="my-3 border-secondary opacity-25">

						<div class="d-flex justify-content-between align-items-center">
							<span class="h3 mb-0">Total Biaya</span>
							<span class="h1 mb-0 text-primary" id="lbl_total">Rp 0</span>
						</div>

						<div id="alert-price" class="alert alert-danger mt-3 d-none" style="padding: 0.5rem; font-size: 0.8rem;">
							Harga tidak ditemukan untuk rute ini!
						</div>

						<div class="mb-3 p-3 border border-warning rounded bg-warning-lt text-center mt-3">
							<input type="hidden" name="is_lartas_agreed" value="1">

							<span class="text-dark small" style="line-height: 1.4;">
								<?= tabler_icon('shield-check', 'text-warning') ?> Dengan mengklik tombol simpan, Petugas menyatakan telah memverifikasi bahwa kiriman ini bebas dari <a href="#" data-bs-toggle="modal" data-bs-target="#modal-lartas" class="text-warning fw-bold text-decoration-underline">Barang Terlarang (LARTAS)</a>.
							</span>
						</div>

						<div class="modal modal-blur fade" id="modal-lartas" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
								<div class="modal-content">
									<div class="modal-header bg-red-lt">
										<h5 class="modal-title text-danger fw-bold"><?= tabler_icon('alert-triangle', 'me-1') ?> Daftar Barang Larangan & Terbatas (LARTAS)</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body text-dark">
										<p class="mb-3">Sesuai dengan regulasi Keselamatan Penerbangan, Kepabeanan, dan <strong>Permendag No. 20 Tahun 2024</strong>, barang-barang berikut dilarang keras untuk diekspor / dikirimkan melalui Smesco Express:</p>

										<h6 class="fw-bold text-danger mb-2">A. Kategori Komoditas Ekspor Terlarang (Permendag)</h6>
										<div class="list-group list-group-flush list-group-hoverable mb-4">

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('tree') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Barang Bidang Kehutanan</div>
														<div class="text-muted small" style="line-height: 1.4;">Meliputi kayu bulat, bahan baku kayu serpih, dan produk kehutanan mentah lainnya untuk menjaga kelestarian alam.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('seeding') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Barang Bidang Pertanian</div>
														<div class="text-muted small" style="line-height: 1.4;">Meliputi rotan mentah, karet alam kualitas tertentu, dan bibit unggul untuk mengamankan bahan baku industri lokal.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('plant-2') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Pupuk Bersubsidi</div>
														<div class="text-muted small" style="line-height: 1.4;">Segala jenis pupuk yang mendapatkan subsidi harga dari pemerintah, dikhususkan murni untuk ketahanan pangan dalam negeri.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('diamond') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Barang Bidang Pertambangan</div>
														<div class="text-muted small" style="line-height: 1.4;">Bijih mineral dan hasil tambang yang belum melalui proses pemurnian (smelter). Dilarang diekspor mentah.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('building-monument') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Barang Cagar Budaya</div>
														<div class="text-muted small" style="line-height: 1.4;">Benda bersejarah, artefak, dan warisan budaya nasional dilarang keras dikeluarkan dari wilayah Indonesia.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('settings') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Sisa dan Skrap Logam</div>
														<div class="text-muted small" style="line-height: 1.4;">Limbah atau potongan logam rongsokan dilarang dikirim ke luar negeri untuk mencegah kelangkaan bahan baku daur ulang lokal.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('ripple') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Hasil Sedimentasi di Laut</div>
														<div class="text-muted small" style="line-height: 1.4;">Meliputi pasir laut dan hasil pengerukan laut. Dilarang untuk mencegah kerusakan ekosistem biota laut.</div>
													</div>
												</div>
											</div>

										</div>

										<h6 class="fw-bold text-danger mb-2 mt-4">B. Keamanan Penerbangan & Kargo (Dangerous Goods)</h6>
										<div class="list-group list-group-flush list-group-hoverable mb-4">

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('pill') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Narkotika & Barang Ilegal</div>
														<div class="text-muted small" style="line-height: 1.4;">Segala jenis narkoba, psikotropika, obat-obatan tanpa izin edar BPOM, serta barang ilegal seperti uang palsu.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('flame') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Barang Berbahaya (Dangerous Goods)</div>
														<div class="text-muted small" style="line-height: 1.4;">Bahan mudah meledak (amunisi), gas bertekanan (aerosol), cairan mudah terbakar (alkohol murni, tiner), zat beracun, korosif, dan radioaktif.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('swords') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Senjata & Benda Tajam</div>
														<div class="text-muted small" style="line-height: 1.4;">Senjata api, senjata tajam tempur, airsoft gun, dan segala jenis replika senjata tanpa kelengkapan surat izin kepolisian.</div>
													</div>
												</div>
											</div>

											<div class="list-group-item px-0 py-2">
												<div class="d-flex align-items-start">
													<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
														<?= tabler_icon('paw') ?>
													</span>
													<div>
														<div class="fw-bold text-dark">Biologis & Makhluk Hidup</div>
														<div class="text-muted small" style="line-height: 1.4;">Segala jenis hewan hidup, spesimen medis/biologi, dan tanaman langka yang dilarang oleh otoritas karantina bandara.</div>
													</div>
												</div>
											</div>

										</div>

										<div class="alert alert-danger mt-4 mb-0 small">
											<strong>Peringatan Hukum:</strong> Pengirim yang dengan sengaja mengirimkan barang terlarang akan dilaporkan ke pihak berwajib dan menanggung seluruh denda atau sanksi pidana yang timbul.
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
									</div>
								</div>
							</div>
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

		// Destination cascade by origin
		$('#origin').on('change', function() {
			const origin = $(this).val();
			const destSelect = $('#destination');

			// Reset destination
			destSelect.html('<option value="">- Pilih Tujuan -').trigger('change');

			if (!origin) return;

			$.ajax({
				url: "<?= site_url('master/ajax_get_international_destination_by_origin') ?>",
				type: "GET",
				dataType: "json",
				data: {
					origin: origin
				},
				success: function(res) {
					let html = '<option value="">- Pilih Tujuan -</option>';
					res.forEach(function(item) {
						html += `<option value="${item.destination}">${item.destination}</option>`;
					});
					destSelect.html(html);

					// Reinit Select2 setelah data masuk
					if (destSelect.hasClass('select2-hidden-accessible')) {
						destSelect.select2('destroy');
					}
					destSelect.select2({
						width: '100%'
					});
				}
			});
		});

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
			let totalAddonFee = 0;
			let activeAddons = []; // Buat nampung rincian addon yang terpilih

			// 1. Hitung Volume & Koli Dasar Dulu
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

			// 2. Hitung Rincian Add-on (FULL DYNAMIC)
			document.querySelectorAll('.chk-addon:checked').forEach(chk => {
				let method = chk.dataset.method; // Ambil metode hitungnya dari DB
				let factor = parseFloat(chk.dataset.factor);
				let name = chk.dataset.name;
				let feePerItem = 0;

				// Looping dimensi khusus buat ngitung addon ini
				document.querySelectorAll('.dim-row').forEach(row => {
					let p = parseIndoNumber(row.querySelector('.dim-p').value);
					let l = parseIndoNumber(row.querySelector('.dim-l').value);
					let t = parseIndoNumber(row.querySelector('.dim-t').value);
					let q = parseIndoNumber(row.querySelector('.dim-qty').value);

					if (q > 0) {
						// Logic perhitungannya dikendalikan oleh calc_method dari Database
						if (method === 'VOLUME') {
							feePerItem += (p * l * t * factor) * q;
						} else if (method === 'VOLUME_PLUS') {
							// Asumsi Packing Kayu nambah 10cm tiap sisi
							feePerItem += ((p + 10) * (l + 10) * (t + 10) * factor) * q;
						} else if (method === 'PER_KOLI') {
							feePerItem += (factor * q);
						}
					}
				});

				if (feePerItem > 0) {
					totalAddonFee += feePerItem;
					activeAddons.push({
						name: name,
						fee: feePerItem
					});
				}
			});

			// 3. Render Ringkasan (UI)
			totalKoliDisplay.value = totalKoli > 0 ? totalKoli : 1;
			lblVolume.innerText = formatIndoNumber(totalVolume.toFixed(2));

			let chargeable = Math.max(actual, totalVolume);
			if (chargeable < minWeightKg) chargeable = minWeightKg;
			chargeable = Math.ceil(chargeable);
			lblChargeable.innerText = formatIndoNumber(chargeable);

			// --- 1. HITUNG & TAMPILKAN BIAYA KIRIM ---
			let shippingCost = chargeable * currentPricePerKg;
			document.getElementById('lbl_shipping_cost').innerText = formatRp(shippingCost);

			// --- 2. HITUNG & TAMPILKAN BIAYA PICKUP ---
			let pickupFee = 0;
			const rowPickup = document.getElementById('row_pickup_cost');

			if (usePickupCheck.checked && pickupSelect.value !== "") {
				const selectedOption = pickupSelect.options[pickupSelect.selectedIndex];
				pickupFee = parseFloat(selectedOption.dataset.price || 0);

				// Tampilkan baris pickup dan set angkanya
				document.getElementById('lbl_pickup_cost').innerText = formatRp(pickupFee);
				rowPickup.classList.remove('d-none');
			} else {
				// Sembunyikan kalau tidak pakai pickup
				rowPickup.classList.add('d-none');
			}

			// --- 3. RENDER LAYANAN TAMBAHAN (ADD-ONS) ---
			const addonContainer = document.getElementById('addon_summary_container');
			addonContainer.innerHTML = ''; // Bersihkan kontainer

			if (activeAddons.length > 0) {
				let htmlList = `<div class="mt-2 mb-1 text-muted fw-bold small">Layanan Tambahan:</div>`;

				activeAddons.forEach(item => {
					htmlList += `
                  <div class="d-flex justify-content-between align-items-center mb-1">
                     <span class="text-muted small ps-2">&bull; ${item.name}</span>
                     <span class="fw-bold text-warning small">+ ${formatRp(item.fee)}</span>
                  </div>
               `;
				});
				addonContainer.innerHTML = htmlList;
			}

			// --- 4. GRAND TOTAL ---
			let grandTotal = shippingCost + pickupFee + totalAddonFee;

			lblTotal.innerText = formatRp(grandTotal);
			btnSubmit.disabled = (shippingCost <= 0);
		}

		// Jangan lupa pasang event listener buat checkbox Add-on biar kalau diklik, harganya langsung update
		document.querySelectorAll('.chk-addon').forEach(chk => {
			chk.addEventListener('change', calculateAll);
		});

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

		// ── Autocomplete Customer (by Phone) ──
		function initCustomerAutocomplete() {
			$('#telepon_pengirim').autocomplete({
				source: function(request, response) {
					$.ajax({
						url: "<?= site_url('shipment/autocomplete_customer') ?>",
						type: "GET",
						dataType: "json",
						data: {
							term: request.term
						},
						success: function(data) {
							response($.map(data, function(item) {
								return {
									label: item.phone + ' — ' + item.name,
									value: item.phone,
									data: item
								};
							}));
						}
					});
				},
				minLength: 3,
				select: function(event, ui) {
					event.preventDefault();
					const c = ui.item.data;

					// Fill field dasar
					$('#telepon_pengirim').val(c.phone);
					$('#nama_pengirim').val(c.name);
					$('#nik_pengirim').val(c.nik || '');
					$('#alamat_pengirim').val(c.address_detail || '');

					// Auto-fill cascade wilayah
					if (c.provinsi_id) {
						fillWilayah('sender', c);
					}
				}
			});
		}

		// Helper: auto-fill cascade wilayah sender dari data customer
		function fillWilayah(type, c) {
			const selProv = $(`#${type}_provinsi`);
			const selKota = $(`#${type}_kota`);
			const selKec = $(`#${type}_kecamatan`);
			const selKel = $(`#${type}_kelurahan`);

			// 1. Set & trigger provinsi
			selProv.val(c.provinsi_id).trigger('change');

			// 2. Tunggu kota selesai load, lalu set
			const waitKota = setInterval(function() {
				if (selKota.find(`option[value="${c.kota_id}"]`).length) {
					clearInterval(waitKota);
					selKota.val(c.kota_id).trigger('change');

					// 3. Tunggu kecamatan selesai load
					const waitKec = setInterval(function() {
						if (selKec.find(`option[value="${c.kecamatan_id}"]`).length) {
							clearInterval(waitKec);
							selKec.val(c.kecamatan_id).trigger('change');

							// 4. Tunggu kelurahan selesai load
							const waitKel = setInterval(function() {
								if (selKel.find(`option[value="${c.kelurahan_id}"]`).length) {
									clearInterval(waitKel);
									selKel.val(c.kelurahan_id).trigger('change');
								}
							}, 100);
						}
					}, 100);
				}
			}, 100);
		}

		initCustomerAutocomplete();

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

		const form = document.getElementById('form-booking-intl');
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
		// initWilayah('receiver');
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
