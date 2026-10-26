<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
/*
|--------------------------------------------------------------------------
| Agent Kecamatan - Create International Shipment
|--------------------------------------------------------------------------
| Field input sengaja dipertahankan sama dengan create_intl.php
| agar logic pricing / shipment tetap kompatibel.
|
| Expected variables:
| - $sess
| - $pricing_origin      (selalu JAKARTA; dikirim controller dari M_Shipment::KEC_PRICING_ORIGIN)
| - $agent_origin        (kecamatan asal agen; hanya tampilan, disimpan server ke shipments.agent_origin)
| - $agent_origin_name   (cities.name kecamatan; tampilan)
| - $services
| - $commodities
| - $addons
| - $countries (optional, jika tersedia)
|--------------------------------------------------------------------------
*/

$pricing_origin    = !empty($pricing_origin) ? $pricing_origin : 'JAKARTA';
$agent_origin      = !empty($agent_origin) ? $agent_origin : '-';
$agent_origin_name = !empty($agent_origin_name) ? $agent_origin_name : $agent_origin;
?>

<?php $this->load->view('app/pages/agent_kecamatan/_mobile_css'); ?>

<div class="page-header d-print-none mb-3">
	<div class="container-xl">
		<div class="d-flex align-items-center">
			<div class="me-3">
				<span class="avatar avatar-lg bg-primary-lt">
					<?= tabler_icon('plane-departure') ?>
				</span>
			</div>
			<div>
				<h2 class="page-title mb-1">Kirim Barang Internasional</h2>
				<div class="text-muted">
					Buat pengiriman internasional baru melalui Agen Kecamatan.
				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php
		$success = $this->session->flashdata('success');
		$error   = $this->session->flashdata('error');
		?>

		<?php if ($success): ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?= $success; ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		<?php endif; ?>

		<?php if ($error): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?= $error; ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		<?php endif; ?>

		<?= form_open_multipart('agentkecamatan/save', ['id' => 'form-booking-intl']) ?>

		<div class="row row-cards">

			<!-- =====================================================
			     LEFT / MAIN CONTENT
			====================================================== -->
			<div class="col-lg-8">
				<div class="row row-cards">

					<!-- =================================================
					     RUTE & LAYANAN
					================================================== -->
					<div class="col-12">
						<div class="card border-primary">
							<div class="card-header">
								<div>
									<h3 class="card-title">
										<?= tabler_icon('route', 'me-2') ?> Rute Pengiriman
									</h3>
									<div class="text-muted small mt-1">Tentukan negara tujuan dan layanan pengiriman.</div>
								</div>
							</div>

							<div class="card-body">
								<div class="row g-3">
									<!-- ASAL -->
									<div class="col-md-4">
										<label class="form-label">Kecamatan Asal Agen</label>
										<div class="form-control bg-light d-flex align-items-center">
											<?= tabler_icon('building-airport', 'me-2 text-primary') ?>
											<strong><?= html_escape($agent_origin_name) ?></strong>
										</div>
										<input type="hidden" name="origin" id="origin" value="<?= html_escape($pricing_origin) ?>">
										<div class="form-hint">Asal otomatis mengikuti agen. Tarif dihitung dari <?= html_escape($pricing_origin) ?>.</div>
									</div>

									<!-- SERVICE -->
									<div class="col-md-4">
										<label class="form-label required">Layanan Pengiriman</label>
										<select name="service_type_id" id="service_type_id" class="form-select trigger-price select2" required>
											<!-- <option value="">- Pilih Layanan -</option> -->
											<?php foreach ($services as $s): ?>
												<option value="<?= $s->id ?>">
													<?= html_escape($s->code) ?> - <?= html_escape($s->name) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- TUJUAN -->
									<div class="col-md-4">
										<label class="form-label required">Negara Tujuan</label>
										<select name="destination_country" id="destination" class="form-select trigger-price select2" required>
											<option value="">- Pilih Negara Tujuan -</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- =================================================
					     DATA PENGIRIM
					================================================== -->
					<div class="col-md-6">
						<div class="card h-100">
							<div class="card-header">
								<div>
									<h3 class="card-title">
										<?= tabler_icon('user', 'me-2') ?> Data Pengirim
									</h3>
									<div class="text-muted small mt-1">Orang atau perusahaan yang mengirim barang.</div>
								</div>
							</div>

							<div class="card-body">
								<!-- PHONE -->
								<div class="mb-3">
									<label class="form-label required">No. WhatsApp</label>
									<input type="tel" inputmode="tel" name="sender_phone" id="telepon_pengirim" class="form-control" placeholder="Contoh: 081234567890" required>
									<div class="form-hint">Dapat digunakan untuk mencari data pelanggan.</div>
								</div>

								<!-- NAMA -->
								<div class="mb-3">
									<label class="form-label required">Nama Pengirim</label>
									<input type="text" name="sender_name" id="nama_pengirim" class="form-control" placeholder="Budi Santoso / PT. Maju Jaya" oninput="this.value = this.value.toUpperCase()" required>
								</div>

								<!-- NIK -->
								<div class="mb-3">
									<label class="form-label">NIK</label>
									<input type="tel" inputmode="numeric" name="sender_nik" id="nik_pengirim" class="form-control" maxlength="16" placeholder="16 digit nomor KTP" oninput="this.value = this.value.replace(/\D/g, '')">
									<div class="form-hint">Opsional.</div>
								</div>

								<!-- PROVINSI -->
								<div class="mb-3">
									<label class="form-label required">Provinsi</label>
									<select name="sender_provinsi" id="sender_provinsi" class="form-select select2-wilayah" required>
										<option value="">- Pilih Provinsi -</option>
									</select>
								</div>

								<!-- KOTA + KECAMATAN -->
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

								<!-- KELURAHAN -->
								<div class="mb-3">
									<label class="form-label required">Kelurahan / Desa</label>
									<select name="sender_kelurahan" id="sender_kelurahan" class="form-select select2-wilayah" required disabled>
										<option value="">- Pilih Kelurahan -</option>
									</select>
								</div>

								<!-- ALAMAT -->
								<div class="mb-0">
									<label class="form-label required">Detail Jalan / Gedung / RT RW</label>
									<textarea name="sender_address_detail" id="alamat_pengirim" class="form-control" rows="3" placeholder="Contoh: Jl. Sudirman No. 12, Gedung X Lt. 3" oninput="this.value = this.value.toUpperCase()" required></textarea>
								</div>
							</div>
						</div>
					</div>

					<!-- =================================================
					     DATA PENERIMA
					================================================== -->
					<div class="col-md-6">
						<div class="card h-100">
							<div class="card-header bg-success-lt">
								<div>
									<h3 class="card-title text-success">
										<?= tabler_icon('user-check', 'me-2') ?> Data Penerima
									</h3>
									<div class="text-success small mt-1">Penerima barang di negara tujuan.</div>
								</div>
							</div>

							<div class="card-body">
								<!-- NAMA -->
								<div class="mb-3">
									<label class="form-label required">Nama Penerima / Perusahaan</label>
									<input type="text" name="receiver_name" id="nama_penerima" class="form-control" placeholder="Nama penerima / perusahaan" oninput="this.value = this.value.toUpperCase()" required>
								</div>

								<!-- PHONE -->
								<div class="mb-3">
									<label class="form-label required">No. Telepon / WhatsApp</label>
									<input type="tel" inputmode="tel" name="receiver_phone" id="telepon_penerima" class="form-control" placeholder="Contoh: +60123456789" required>
									<div class="form-hint">Gunakan kode negara.</div>
								</div>

								<!-- CITY + ZIP -->
								<div class="row g-2 mb-3">
									<div class="col-8">
										<label class="form-label required">Kota / State / Province</label>
										<input type="text" name="receiver_city" class="form-control" placeholder="KUALA LUMPUR / CALIFORNIA" oninput="this.value = this.value.toUpperCase()" required>
									</div>

									<div class="col-4">
										<label class="form-label required">Zip / Postal Code</label>
										<input type="text" name="receiver_zipcode" class="form-control" placeholder="50450" required>
									</div>
								</div>

								<!-- ADDRESS -->
								<div class="mb-0">
									<label class="form-label required">Alamat Lengkap</label>
									<textarea name="receiver_address_detail" id="alamat_penerima" class="form-control" rows="5" placeholder="Jalan, blok, nomor unit, gedung, dll." oninput="this.value = this.value.toUpperCase()" required></textarea>
								</div>
							</div>
						</div>
					</div>

					<!-- =================================================
					     DETAIL BARANG
					================================================== -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div>
									<h3 class="card-title">
										<?= tabler_icon('package', 'me-2') ?> Detail Barang
									</h3>
									<div class="text-muted small mt-1">Pastikan isi dan nilai barang sesuai dengan kondisi sebenarnya.</div>
								</div>
							</div>

							<div class="card-body">
								<div class="row">
									<!-- KOMODITI -->
									<div class="col-md-6 mb-3">
										<label class="form-label required">Kategori Komoditi (IATA)</label>
										<select name="commodity_id" class="form-select select2" required>
											<option value="">- Pilih Kategori -</option>
											<?php foreach ($commodities as $cmd): ?>
												<option value="<?= $cmd->id ?>">
													[<?= html_escape($cmd->code) ?>] <?= html_escape($cmd->name) ?>
												</option>
											<?php endforeach; ?>
										</select>
										<div class="form-hint">Pilih kategori utama untuk menentukan penanganan kargo.</div>
									</div>

									<!-- DETAIL BARANG -->
									<div class="col-md-6 mb-3">
										<label class="form-label required">Isi Barang</label>
										<input type="text" name="commodity_detail" class="form-control" placeholder="Contoh: IKAN TUNA SEGAR, SPAREPART MOTOR" oninput="this.value = this.value.toUpperCase()" required>
										<div class="form-hint">Keterangan barang yang akan tertera di resi.</div>
									</div>

									<!-- ENGLISH DESCRIPTION -->
									<div class="col-md-6 mb-3">
										<label class="form-label">English Description</label>
										<input type="text" name="commodity_detail_en" class="form-control" placeholder="e.g. CASSAVA CHIPS, COTTON T-SHIRT" oninput="this.value = this.value.toUpperCase()">
										<div class="form-hint">Wajib menggunakan bahasa Inggris.</div>
									</div>

									<!-- CUSTOMS VALUE -->
									<div class="col-md-6 mb-3">
										<label class="form-label">Nilai Barang - Customs Value</label>
										<div class="input-group">
											<span class="input-group-text">US$</span>
											<input type="text" name="customs_value_usd" class="form-control indo-format" placeholder="0">
										</div>
										<div class="form-hint">Estimasi nilai barang untuk kebutuhan kepabeanan.</div>
									</div>

									<!-- VALUABLE -->
									<div class="col-md-6 mb-3">
										<div class="p-3 border rounded h-100">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" name="is_valuable" id="is_valuable" value="1" disabled>
												<label class="form-check-label text-danger fw-bold" for="is_valuable">
													<?= tabler_icon('shield-check', 'me-1') ?> Proteksi Barang Berharga
												</label>
											</div>
											<div class="text-muted small mt-1">Fitur proteksi barang berharga belum tersedia.</div>

											<div class="d-none mt-3" id="wrap_goods_value">
												<label class="form-label text-danger">Estimasi Nilai Barang (Rp)</label>
												<div class="input-group">
													<span class="input-group-text">Rp</span>
													<input type="text" name="goods_value" id="goods_value" class="form-control border-danger indo-format" placeholder="0">
												</div>
											</div>
										</div>
									</div>

									<!-- FOTO -->
									<div class="col-md-6 mb-3">
										<div class="border rounded p-3 h-100">
											<label class="form-label required">
												<?= tabler_icon('camera', 'me-1') ?> Foto Barang
											</label>
											<input type="file" name="shipment_photo" id="shipment_photo" class="form-control" accept="image/jpeg, image/png, image/jpg" required>
											<div class="form-hint">JPG, JPEG, PNG. Maksimal 2MB.</div>

											<div class="mt-3 d-none" id="preview-container">
												<img id="photo-preview" src="" alt="Preview Foto Barang" class="img-fluid rounded border" style="max-height: 200px;">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- =================================================
					     BERAT & DIMENSI
					================================================== -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div>
									<h3 class="card-title">
										<?= tabler_icon('scale', 'me-2') ?> Berat & Ukuran Barang
									</h3>
									<div class="text-muted small mt-1">Masukkan berat timbangan dan ukuran setiap koli.</div>
								</div>
							</div>

							<div class="card-body">
								<!-- BERAT -->
								<div class="row g-3 mb-3">
									<div class="col-md-6">
										<label class="form-label required">Total Berat Aktual</label>
										<div class="input-group input-group-lg">
											<input type="text" name="actual_weight" id="actual_weight" class="form-control calc-weight indo-format" placeholder="0,00" required>
											<span class="input-group-text">Kg</span>
										</div>
										<div class="form-hint">Berat berdasarkan timbangan.</div>
									</div>

									<div class="col-md-6">
										<label class="form-label">Total Koli</label>
										<div class="input-group input-group-lg">
											<input type="text" id="total_koli_display" class="form-control bg-light fw-bold" value="1" readonly tabindex="-1">
											<span class="input-group-text">Pcs</span>
										</div>
									</div>
								</div>

								<hr>

								<!-- DIMENSI -->
								<div class="d-flex justify-content-between align-items-center mb-2">
									<div>
										<div class="fw-bold">Dimensi Koli</div>
										<div class="text-muted small">Panjang × Lebar × Tinggi</div>
									</div>
									<button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-dim">
										<?= tabler_icon('plus', 'me-1') ?> Tambah Koli
									</button>
								</div>

								<div id="dimension-container">
									<div class="dim-row border rounded p-2 mb-2">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<span class="small fw-bold text-muted dim-title">Koli #1</span>
										</div>
										<div class="row g-2">
											<div class="col-4">
												<label class="form-label small mb-1">P (cm)</label>
												<input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format">
											</div>
											<div class="col-4">
												<label class="form-label small mb-1">L (cm)</label>
												<input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format">
											</div>
											<div class="col-4">
												<label class="form-label small mb-1">T (cm)</label>
												<input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format">
											</div>
											<div class="col-12">
												<label class="form-label small mb-1">Jumlah koli dengan ukuran ini</label>
												<input type="text" inputmode="numeric" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="1" required>
											</div>
										</div>
									</div>
								</div>

								<hr>

								<!-- HASIL BERAT -->
								<div class="row g-2 text-center mt-3">
									<div class="col-6">
										<div class="border rounded p-3">
											<div class="text-muted small">Berat Volume</div>
											<div class="h2 mb-0">
												<span id="lbl_volume">0,00</span>
												<small class="text-muted">Kg</small>
											</div>
										</div>
									</div>

									<div class="col-6">
										<div class="border border-primary rounded p-3 bg-primary-lt">
											<div class="text-primary small fw-bold">Berat untuk Ongkir</div>
											<div class="h2 mb-0 text-primary">
												<span id="lbl_chargeable">0,00</span>
												<small>Kg</small>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- =================================================
					     LAYANAN TAMBAHAN
					================================================== -->
					<div class="col-12">
						<div class="card border-info">
							<div class="card-header bg-info-lt">
								<div>
									<h3 class="card-title text-info">
										<?= tabler_icon('box', 'me-2') ?> Layanan Tambahan
									</h3>
									<div class="text-info small mt-1">Pilih jika customer membutuhkan layanan tambahan.</div>
								</div>
							</div>

							<div class="card-body">
								<div class="row g-3">
									<?php foreach ($addons as $addon): ?>
										<?php
										$icon  = 'plus';
										$desc  = '';
										$color = 'blue';

										if ($addon->code == 'REPACK') {
											$icon  = 'package';
											$color = 'blue';
											$desc  = 'Perhitungan berdasarkan volume koli.';
										} elseif ($addon->code == 'KAYU') {
											$icon  = 'box';
											$color = 'brown';
											$desc  = 'Packing tambahan untuk keamanan barang.';
										} elseif ($addon->code == 'BUBBLE') {
											$icon  = 'circles';
											$color = 'teal';
											$desc  = 'Rp ' . number_format($addon->base_factor, 0, ',', '.') . ' / koli';
										}
										?>

										<div class="col-md-4">
											<label class="form-selectgroup-item flex-fill h-100">
												<input type="checkbox" name="addons[]" value="<?= html_escape($addon->code) ?>" class="form-selectgroup-input chk-addon" data-method="<?= html_escape($addon->calc_method) ?>" data-factor="<?= $addon->base_factor ?>" data-min-charge="<?= (float) ($addon->min_charge ?? 0) ?>" data-name="<?= html_escape($addon->name) ?>">
												<div class="form-selectgroup-label d-flex align-items-center p-3 h-100">
													<div class="me-3">
														<span class="form-selectgroup-check"></span>
													</div>
													<div class="form-selectgroup-label-content d-flex align-items-center">
														<span class="bg-<?= $color ?>-lt avatar me-3">
															<?= tabler_icon($icon) ?>
														</span>
														<div class="text-start">
															<div class="fw-bold mb-1"><?= html_escape($addon->name) ?></div>
															<div class="text-muted small"><?= html_escape($desc) ?></div>
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

					<!-- =================================================
					     PEMBAYARAN
					================================================== -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div>
									<h3 class="card-title">
										<?= tabler_icon('wallet', 'me-2') ?> Pembayaran
									</h3>
									<div class="text-muted small mt-1">Tentukan metode pembayaran customer.</div>
								</div>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-md-6">
										<label class="form-label required">Metode Pembayaran</label>
										<select name="payment_type" class="form-select" required>
											<option value="">Pilih metode pembayaran</option>
											<option value="TRANSFER">TRANSFER</option>
											<option value="CASH">TUNAI</option>
										</select>
										<div class="form-hint">Pembayaran akan dikonfirmasi oleh petugas setelah pengiriman dibuat.</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!-- =====================================================
			     RIGHT / SUMMARY
			====================================================== -->
			<div class="col-lg-4">
				<div class="sticky-lg-top" style="top: 20px; z-index: 1;">

					<!-- =================================================
					     SUMMARY TARIF
					================================================== -->
					<div class="card bg-primary-lt mb-3" id="card-summary">
						<div class="card-header border-0">
							<div>
								<h3 class="card-title">
									<?= tabler_icon('receipt', 'me-2') ?> Ringkasan Biaya
								</h3>
								<div class="text-muted small">Estimasi biaya pengiriman.</div>
							</div>
						</div>

						<div class="card-body pt-0">
							<!-- HARGA / KG -->
							<div class="d-flex justify-content-between align-items-center mb-3">
								<span class="text-muted">Tarif / Kg</span>
								<span class="fw-bold h3 mb-0" id="lbl_price">Rp 0</span>
							</div>

							<hr class="my-3">

							<!-- SHIPPING -->
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-dark small">Biaya Pengiriman</span>
								<span class="fw-bold text-dark small" id="lbl_shipping_cost">Rp 0</span>
							</div>

							<!-- PICKUP - DISIMPAN UNTUK KOMPATIBILITAS -->
							<div class="d-none" id="row_pickup_cost">
								<span id="lbl_pickup_cost">Rp 0</span>
							</div>

							<!-- TESS -->
							<div class="d-flex justify-content-between align-items-center mb-2 d-none" id="row_tess_cost">
								<span class="text-dark small">Biaya Tambahan</span>
								<span class="fw-bold text-warning small" id="lbl_tess_cost">Rp 0</span>
							</div>

							<!-- ADDONS -->
							<div id="addon_summary_container"></div>

							<hr class="my-3">

							<!-- GRAND TOTAL -->
							<div class="d-flex justify-content-between align-items-center">
								<span class="h3 mb-0">Total</span>
								<span class="h1 mb-0 text-primary" id="lbl_total">Rp 0</span>
							</div>

							<!-- ALERT PRICE -->
							<div id="alert-price" class="alert alert-danger mt-3 d-none" style="padding: 0.6rem; font-size: 0.8rem;">
								Tarif pengiriman tidak ditemukan untuk rute ini.
							</div>
						</div>
					</div>

					<!-- =================================================
					     LARTAS
					================================================== -->
					<div class="card border-warning mb-3">
						<div class="card-body">
							<input type="hidden" name="is_lartas_agreed" value="1">
							<div class="d-flex align-items-start">
								<div class="me-2 text-warning">
									<?= tabler_icon('shield-check') ?>
								</div>
								<div>
									<div class="fw-bold text-dark mb-1">Verifikasi Barang</div>
									<div class="text-muted small">
										Petugas menyatakan telah memverifikasi bahwa barang tidak termasuk
										<a href="#" data-bs-toggle="modal" data-bs-target="#modal-lartas" class="text-warning fw-bold text-decoration-underline">Barang Terlarang / LARTAS</a>.
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- =================================================
					     SUBMIT
					================================================== -->
					<button type="submit" id="btn-submit" class="btn btn-primary btn-lg w-100" disabled>
						<?= tabler_icon('device-floppy', 'me-2') ?> Simpan Pengiriman
					</button>

					<div class="text-center text-muted small mt-2">
						Setelah disimpan, pembayaran dapat dikonfirmasi dan label dapat dicetak.
					</div>

					<!-- =================================================
					     PICKUP - HIDDEN
					     Field tetap dipertahankan agar kompatibel.
					================================================== -->
					<div class="d-none">
						<input type="checkbox" name="use_pickup" id="use_pickup" value="1">
						<select name="pickup_rate_id" id="pickup_rate_id">
							<option value=""></option>
						</select>
						<div id="pickup_detail"></div>
					</div>

				</div>
			</div>

		</div>

		<div class="sticky-bar d-lg-none">
			<div class="d-flex align-items-center gap-3">
				<div class="flex-fill">
					<div class="text-muted small">Total <a href="#card-summary" class="ms-1">Rincian</a></div>
					<div class="h3 mb-0 text-primary" id="lbl_total_m">Rp 0</div>
					<div id="bar-hint" class="small text-danger d-none">Tarif tidak ditemukan</div>
				</div>
				<button type="submit" id="btn-submit-m" class="btn btn-primary btn-lg w-100 d-none d-lg-block" disabled>
					<?= tabler_icon('device-floppy', 'me-1') ?> Simpan
				</button>
			</div>
		</div>

		<?= form_close() ?>

	</div>
</div>

<!-- =============================================================
     MODAL LARTAS
============================================================== -->
<div class="modal modal-blur fade" id="modal-lartas" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header bg-red-lt">
				<h5 class="modal-title text-danger fw-bold">
					<?= tabler_icon('alert-triangle', 'me-1') ?> Daftar Barang Larangan & Terbatas
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body text-dark">
				<p class="mb-3">
					Sesuai dengan regulasi keselamatan penerbangan, kepabeanan, dan ketentuan yang berlaku,
					pastikan barang yang dikirim tidak termasuk barang terlarang atau terbatas.
				</p>

				<h6 class="fw-bold text-danger mb-2">A. Barang yang Perlu Diperhatikan</h6>

				<div class="list-group list-group-flush list-group-hoverable mb-4">
					<div class="list-group-item px-0 py-2">
						<div class="d-flex align-items-start">
							<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
								<?= tabler_icon('tree') ?>
							</span>
							<div>
								<div class="fw-bold text-dark">Barang Bidang Kehutanan</div>
								<div class="text-muted small">
									Periksa ketentuan ekspor untuk kayu, produk kehutanan, dan bahan mentah terkait.
								</div>
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
								<div class="text-muted small">
									Periksa persyaratan ekspor untuk produk pertanian, tanaman, bibit, dan hasil alam.
								</div>
							</div>
						</div>
					</div>

					<div class="list-group-item px-0 py-2">
						<div class="d-flex align-items-start">
							<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
								<?= tabler_icon('diamond') ?>
							</span>
							<div>
								<div class="fw-bold text-dark">Barang Pertambangan</div>
								<div class="text-muted small">
									Periksa ketentuan ekspor untuk mineral dan hasil pertambangan.
								</div>
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
								<div class="text-muted small">
									Benda bersejarah dan artefak dapat memiliki ketentuan khusus dan tidak boleh dikirim tanpa dokumen yang sesuai.
								</div>
							</div>
						</div>
					</div>
				</div>

				<h6 class="fw-bold text-danger mb-2">B. Keamanan Penerbangan & Kargo</h6>

				<div class="list-group list-group-flush list-group-hoverable mb-4">
					<div class="list-group-item px-0 py-2">
						<div class="d-flex align-items-start">
							<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
								<?= tabler_icon('pill') ?>
							</span>
							<div>
								<div class="fw-bold text-dark">Narkotika & Barang Ilegal</div>
								<div class="text-muted small">
									Dilarang mengirim narkotika, psikotropika, barang ilegal, atau barang tanpa izin yang diwajibkan.
								</div>
							</div>
						</div>
					</div>

					<div class="list-group-item px-0 py-2">
						<div class="d-flex align-items-start">
							<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
								<?= tabler_icon('flame') ?>
							</span>
							<div>
								<div class="fw-bold text-dark">Dangerous Goods</div>
								<div class="text-muted small">
									Termasuk bahan mudah terbakar, gas bertekanan, bahan peledak, zat beracun, korosif, dan material berbahaya lainnya.
								</div>
							</div>
						</div>
					</div>

					<div class="list-group-item px-0 py-2">
						<div class="d-flex align-items-start">
							<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
								<?= tabler_icon('swords') ?>
							</span>
							<div>
								<div class="fw-bold text-dark">Senjata & Benda Terlarang</div>
								<div class="text-muted small">
									Senjata api, senjata tajam tertentu, replika senjata, dan barang sejenis memerlukan ketentuan khusus.
								</div>
							</div>
						</div>
					</div>

					<div class="list-group-item px-0 py-2">
						<div class="d-flex align-items-start">
							<span class="text-danger bg-red-lt p-1 rounded me-3 mt-1">
								<?= tabler_icon('paw') ?>
							</span>
							<div>
								<div class="fw-bold text-dark">Hewan, Tumbuhan & Bahan Biologis</div>
								<div class="text-muted small">
									Barang hidup, spesimen biologis, tanaman tertentu, dan produk karantina memerlukan persyaratan khusus.
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="alert alert-danger mt-4 mb-0 small">
					<strong>Peringatan:</strong> Pengirim bertanggung jawab atas kebenaran isi barang dan kelengkapan dokumen yang diperlukan.
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal">
					Saya Mengerti
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const pembagi_volume = 5000;

		/* =========================================================
		   DOM ELEMENTS + STATE
		   FIX: harus dideklarasikan PALING ATAS, sebelum Select2 /
		   loadDestinations(), supaya tidak kena TDZ (const/let
		   diakses sebelum inisialisasi) saat checkPrice() terpanggil.
		========================================================= */
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

		// di blok DOM ELEMENTS
		const btnSubmitM = document.getElementById('btn-submit-m');
		const lblTotalM = document.getElementById('lbl_total_m');
		const barHint = document.getElementById('bar-hint');

		function setPriceAlert(show) {
			alertPrice.classList.toggle('d-none', !show);
			barHint.classList.toggle('d-none', !show);
		}

		let currentPricePerKg = 0;
		let vendorSurchargePerKg = 0;
		let minWeightKg = 1;

		/* =========================================================
		   SELECT2
		========================================================= */
		if (jQuery().select2) {
			$('.select2').select2({
				width: '100%'
			});
			$('.select2').on('change', function() {
				checkPrice();
			});
		}

		/* =========================================================
		   ORIGIN OTOMATIS
		========================================================= */
		const origin = $('#origin').val();

		/* =========================================================
		   LOAD DESTINATION
		   FIX: .trigger('change') dihapus (tidak perlu, memicu
		   checkPrice() sia-sia saat load awal).
		========================================================= */
		function loadDestinations() {
			const destSelect = $('#destination');
			destSelect.html('<option value="">- Pilih Negara Tujuan -</option>');

			if (!origin) return;

			$.ajax({
				url: "<?= site_url('master/ajax_get_international_destination_by_origin') ?>",
				type: "GET",
				dataType: "json",
				data: {
					origin: origin
				},
				success: function(res) {
					let html = '<option value="">- Pilih Negara Tujuan -</option>';

					if (Array.isArray(res)) {
						res.forEach(function(item) {
							html += `<option value="${item.destination}">${item.destination}</option>`;
						});
					}

					destSelect.html(html);

					if (destSelect.hasClass('select2-hidden-accessible')) {
						destSelect.select2('destroy');
					}

					destSelect.select2({
						width: '100%'
					});
				},
				error: function() {
					Swal.fire('Gagal', 'Data negara tujuan tidak dapat dimuat.', 'error');
				}
			});
		}

		loadDestinations();

		/* =========================================================
		   NUMBER HELPER
		========================================================= */
		function parseIndoNumber(str) {
			if (!str) return 0;
			let cleaned = str.toString().replace(/\./g, '').replace(/,/g, '.');
			return Math.round((parseFloat(cleaned) || 0) * 100) / 100;
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

		/* =========================================================
		   INDONESIAN INPUT MASK
		========================================================= */
		function maskHandler(e) {
			let val = this.value.replace(/[^0-9,]/g, '');
			let parts = val.split(',');

			if (parts.length > 2) {
				val = parts[0] + ',' + parts.slice(1).join('');
			}

			let splitVal = val.split(',');
			splitVal[0] = splitVal[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			this.value = splitVal.join(',');
		}

		function applyIndoMask() {
			document.querySelectorAll('.indo-format').forEach(function(el) {
				el.setAttribute('inputmode', 'decimal');
				el.removeEventListener('input', maskHandler);
				el.addEventListener('input', maskHandler);
			});
		}

		applyIndoMask();

		function renumberDims() {
			document.querySelectorAll('.dim-row').forEach((row, i) => {
				row.querySelector('.dim-title').textContent = 'Koli #' + (i + 1);
			});
		}

		document.getElementById('btn-add-dim').addEventListener('click', function() {
			const newRow = document.createElement('div');
			newRow.className = 'dim-row border rounded p-2 mb-2';
			newRow.innerHTML = `
		<div class="d-flex justify-content-between align-items-center mb-2">
			<span class="small fw-bold text-muted dim-title">Koli</span>
			<button type="button" class="btn btn-sm btn-ghost-danger btn-remove-dim">Hapus</button>
		</div>
		<div class="row g-2">
			<div class="col-4"><label class="form-label small mb-1">P (cm)</label>
				<input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format"></div>
			<div class="col-4"><label class="form-label small mb-1">L (cm)</label>
				<input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format"></div>
			<div class="col-4"><label class="form-label small mb-1">T (cm)</label>
				<input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format"></div>
			<div class="col-12"><label class="form-label small mb-1">Jumlah koli dengan ukuran ini</label>
				<input type="text" inputmode="numeric" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="1" required></div>
		</div>`;
			document.getElementById('dimension-container').appendChild(newRow);
			applyIndoMask();
			renumberDims();
			calculateAll();
		});

		document.body.addEventListener('click', function(e) {
			const button = e.target.closest('.btn-remove-dim');
			if (!button) return;
			const row = button.closest('.dim-row');
			if (row) {
				row.remove();
				renumberDims();
				calculateAll();
				checkPrice();
			}
		});

		/* =========================================================
		   CALCULATE ALL
		========================================================= */
		function calculateAll() {
			let actual = parseIndoNumber(inputActual.value);
			let totalVolume = 0;
			let totalKoli = 0;
			let totalAddonFee = 0;
			let activeAddons = [];

			/* -----------------------------------------------------
			   HITUNG VOLUME & KOLI
			----------------------------------------------------- */
			document.querySelectorAll('.dim-row').forEach(function(row) {
				let p = parseIndoNumber(row.querySelector('.dim-p').value);
				let l = parseIndoNumber(row.querySelector('.dim-l').value);
				let t = parseIndoNumber(row.querySelector('.dim-t').value);
				let q = parseIndoNumber(row.querySelector('.dim-qty').value);

				if (q > 0) {
					totalKoli += q;
					totalVolume += ((p * l * t) / pembagi_volume) * q;
				}
			});

			/* -----------------------------------------------------
			   ADDON
			----------------------------------------------------- */
			document.querySelectorAll('.chk-addon:checked').forEach(function(chk) {
				let method = chk.dataset.method;
				let factor = parseFloat(chk.dataset.factor);
				let minCharge = parseFloat(chk.dataset.minCharge) || 0;
				let name = chk.dataset.name;
				let feePerItem = 0;

				document.querySelectorAll('.dim-row').forEach(function(row) {
					let p = parseIndoNumber(row.querySelector('.dim-p').value);
					let l = parseIndoNumber(row.querySelector('.dim-l').value);
					let t = parseIndoNumber(row.querySelector('.dim-t').value);
					let q = parseIndoNumber(row.querySelector('.dim-qty').value);

					if (q > 0) {
						// min_charge berlaku PER KOLI (sama dengan M_Shipment::_kec_calc_addons)
						let perKoli = null;
						if (method === 'VOLUME') {
							perKoli = p * l * t * factor;
						} else if (method === 'VOLUME_PLUS') {
							perKoli = (p + 10) * (l + 10) * (t + 10) * factor;
						} else if (method === 'PER_KOLI') {
							perKoli = factor;
						}

						if (perKoli !== null) {
							feePerItem += Math.max(perKoli, minCharge) * q;
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

			/* -----------------------------------------------------
			   TOTAL KOLI
			----------------------------------------------------- */
			totalKoliDisplay.value = totalKoli > 0 ? totalKoli : 1;

			/* -----------------------------------------------------
			   VOLUME
			----------------------------------------------------- */
			lblVolume.innerText = formatIndoNumber(totalVolume.toFixed(2));

			/* -----------------------------------------------------
			   CHARGEABLE
			----------------------------------------------------- */
			let chargeable = Math.max(actual, totalVolume);
			if (chargeable < minWeightKg) chargeable = minWeightKg;
			chargeable = Math.ceil(chargeable);

			lblChargeable.innerText = formatIndoNumber(chargeable);

			/* -----------------------------------------------------
			   SHIPPING COST
			----------------------------------------------------- */
			let shippingCost = chargeable * currentPricePerKg;
			document.getElementById('lbl_shipping_cost').innerText = formatRp(shippingCost);

			/* -----------------------------------------------------
			   PICKUP (Field tetap dipertahankan untuk kompatibilitas)
			----------------------------------------------------- */
			let pickupFee = 0;
			if (usePickupCheck && usePickupCheck.checked && pickupSelect && pickupSelect.value !== "") {
				const selectedOption = pickupSelect.options[pickupSelect.selectedIndex];
				pickupFee = parseFloat(selectedOption.dataset.price || 0);
			}

			/* -----------------------------------------------------
			   TESS / SURCHARGE
			----------------------------------------------------- */
			let tessFee = 0;
			const rowTess = document.getElementById('row_tess_cost');

			if (vendorSurchargePerKg > 0 && shippingCost > 0) {
				let calcTess = chargeable * vendorSurchargePerKg;
				tessFee = Math.max(calcTess, 100000);

				document.getElementById('lbl_tess_cost').innerText = formatRp(tessFee);
				rowTess.classList.remove('d-none');
			} else {
				rowTess.classList.add('d-none');
			}

			/* -----------------------------------------------------
			   ADDON SUMMARY
			----------------------------------------------------- */
			const addonContainer = document.getElementById('addon_summary_container');
			addonContainer.innerHTML = '';

			if (activeAddons.length > 0) {
				let htmlList = `<div class="mt-2 mb-1 text-muted fw-bold small">Layanan Tambahan</div>`;
				activeAddons.forEach(function(item) {
					htmlList += `
					<div class="d-flex justify-content-between align-items-center mb-1">
						<span class="text-muted small ps-2">&bull; ${item.name}</span>
						<span class="fw-bold text-warning small">+ ${formatRp(item.fee)}</span>
					</div>
				`;
				});
				addonContainer.innerHTML = htmlList;
			}

			/* -----------------------------------------------------
			   GRAND TOTAL
			----------------------------------------------------- */
			let grandTotal = shippingCost + pickupFee + totalAddonFee + tessFee;
			lblTotal.innerText = formatRp(grandTotal);
			lblTotalM.innerText = formatRp(grandTotal);
			const noPrice = shippingCost <= 0;
			btnSubmit.disabled = noPrice;
			btnSubmitM.disabled = noPrice;
			/* -----------------------------------------------------
			   ENABLE SUBMIT
			----------------------------------------------------- */
			btnSubmit.disabled = shippingCost <= 0;
		}

		/* =========================================================
		   ADDON EVENT
		========================================================= */
		document.querySelectorAll('.chk-addon').forEach(function(chk) {
			chk.addEventListener('change', calculateAll);
		});

		/* =========================================================
		   CHECK PRICE
		========================================================= */
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

				fetch("<?= site_url('agentkecamatan/ajax_cek_harga') ?>", {
						method: 'POST',
						body: formData,
						headers: {
							"X-Requested-With": "XMLHttpRequest"
						}
					})
					.then(response => response.json())
					.then(function(res) {
						if (res.status) {
							alertPrice.classList.add('d-none');
							currentPricePerKg = parseFloat(res.data.price_per_kg);
							vendorSurchargePerKg = parseFloat(res.data.vendor_surcharge_per_kg || 0);
							minWeightKg = parseFloat(res.data.min_weight_kg);

							const icon = res.data.category === 'INTERNATIONAL' ? ' ✈️' : ' 🚛';
							lblPrice.innerText = formatRp(currentPricePerKg) + icon;
							calculateAll();
						} else {
							alertPrice.classList.remove('d-none');
							currentPricePerKg = 0;
							vendorSurchargePerKg = 0;
							calculateAll();
						}
					})
					.catch(function(error) {
						console.error('AJAX harga Kecamatan:', error);
						alertPrice.classList.remove('d-none');
						currentPricePerKg = 0;
						vendorSurchargePerKg = 0;
						calculateAll();
					});
			} else {
				currentPricePerKg = 0;
				vendorSurchargePerKg = 0;
				calculateAll();
			}
		}

		/* =========================================================
		   CUSTOMER AUTOCOMPLETE
		========================================================= */
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

					$('#telepon_pengirim').val(c.phone);
					$('#nama_pengirim').val(c.name);
					$('#nik_pengirim').val(c.nik || '');
					$('#alamat_pengirim').val(c.address_detail || '');

					if (c.provinsi_id) {
						fillWilayah('sender', c);
					}
				}
			});
		}

		/* =========================================================
		   FILL WILAYAH CUSTOMER
		   FIX: polling dibatasi (maks 50x @100ms = 5 detik) supaya
		   tidak berjalan selamanya kalau AJAX gagal / ID tidak ada.
		========================================================= */
		function waitForOption(select, value, callback, maxTries) {
			let tries = 0;
			maxTries = maxTries || 50;

			const timer = setInterval(function() {
				tries++;

				if (select.find(`option[value="${value}"]`).length) {
					clearInterval(timer);
					callback();
				} else if (tries >= maxTries) {
					clearInterval(timer);
					console.warn('fillWilayah: opsi tidak ditemukan untuk value', value);
				}
			}, 100);
		}

		function fillWilayah(type, c) {
			const selProv = $(`#${type}_provinsi`);
			const selKota = $(`#${type}_kota`);
			const selKec = $(`#${type}_kecamatan`);
			const selKel = $(`#${type}_kelurahan`);

			selProv.val(c.provinsi_id).trigger('change');

			waitForOption(selKota, c.kota_id, function() {
				selKota.val(c.kota_id).trigger('change');

				waitForOption(selKec, c.kecamatan_id, function() {
					selKec.val(c.kecamatan_id).trigger('change');

					waitForOption(selKel, c.kelurahan_id, function() {
						selKel.val(c.kelurahan_id).trigger('change');
					});
				});
			});
		}

		initCustomerAutocomplete();

		/* =========================================================
		   GENERAL INPUT EVENT
		========================================================= */
		document.body.addEventListener('input', function(e) {
			if (e.target.classList.contains('calc-weight')) {
				calculateAll();
				if (e.target.id === 'actual_weight' || e.target.closest('.dim-row')) {
					checkPrice();
				}
			}
		});

		/* =========================================================
		   ADD DIMENSION
		========================================================= */
		document.getElementById('btn-add-dim').addEventListener('click', function() {
			const container = document.getElementById('dimension-container');
			const newRow = document.createElement('div');
			newRow.className = 'row g-1 dim-row mb-2';
			newRow.innerHTML = `
			<div class="col-3">
				<input type="text" name="dim_length[]" class="form-control calc-weight dim-p indo-format" placeholder="P">
			</div>
			<div class="col-3">
				<input type="text" name="dim_width[]" class="form-control calc-weight dim-l indo-format" placeholder="L">
			</div>
			<div class="col-3">
				<div class="input-group">
					<input type="text" name="dim_height[]" class="form-control calc-weight dim-t indo-format" placeholder="T">
					<button type="button" class="btn btn-danger btn-remove-dim">
						<?= tabler_icon('x') ?>
					</button>
				</div>
			</div>
			<div class="col-3">
				<input type="text" name="dim_qty[]" class="form-control calc-weight dim-qty indo-format" value="1" placeholder="Qty" required>
			</div>
		`;

			container.appendChild(newRow);
			applyIndoMask();
			calculateAll();
		});

		/* =========================================================
		   REMOVE DIMENSION
		========================================================= */
		document.body.addEventListener('click', function(e) {
			const button = e.target.closest('.btn-remove-dim');
			if (!button) return;

			const row = button.closest('.dim-row');
			if (row) {
				row.remove();
				calculateAll();
				checkPrice();
			}
		});

		/* =========================================================
		   VALUABLE GOODS
		========================================================= */
		const valuable = document.getElementById('is_valuable');
		if (valuable) {
			valuable.addEventListener('change', function() {
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
		}

		/* =========================================================
		   PHOTO PREVIEW
		========================================================= */
		const MAX_SIDE = 1600,
			JPEG_QUALITY = 0.8;

		function compressImage(file) {
			return new Promise(function(resolve) {
				const img = new Image();
				const url = URL.createObjectURL(file);
				img.onload = function() {
					const r = Math.min(1, MAX_SIDE / Math.max(img.width, img.height));
					const c = document.createElement('canvas');
					c.width = Math.round(img.width * r);
					c.height = Math.round(img.height * r);
					c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
					URL.revokeObjectURL(url);
					c.toBlob(function(b) {
						resolve(b ? new File([b], file.name.replace(/\.\w+$/, '') + '.jpg', {
							type: 'image/jpeg'
						}) : file);
					}, 'image/jpeg', JPEG_QUALITY);
				};
				img.onerror = function() {
					resolve(file);
				};
				img.src = url;
			});
		}

		const shipmentPhoto = document.getElementById('shipment_photo');
		if (shipmentPhoto) {
			shipmentPhoto.addEventListener('change', async function() {
				const previewContainer = document.getElementById('preview-container');
				const photoPreview = document.getElementById('photo-preview');

				if (!this.files || this.files.length === 0) {
					previewContainer.classList.add('d-none');
					return;
				}

				let file = this.files[0];
				if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
					Swal.fire('Format Tidak Valid', 'Hanya JPG, JPEG, dan PNG yang diizinkan.', 'error');
					this.value = '';
					previewContainer.classList.add('d-none');
					return;
				}

				file = await compressImage(file);

				if (file.size > 2 * 1024 * 1024) {
					Swal.fire('File Terlalu Besar', 'Foto masih di atas 2MB setelah dikompres.', 'error');
					this.value = '';
					previewContainer.classList.add('d-none');
					return;
				}

				const dt = new DataTransfer();
				dt.items.add(file);
				this.files = dt.files; // ganti file asli dengan versi terkompres

				const reader = new FileReader();
				reader.onload = e => {
					photoPreview.src = e.target.result;
					previewContainer.classList.remove('d-none');
				};
				reader.readAsDataURL(file);
			});
		}

		/* =========================================================
		   FORM SUBMIT
		========================================================= */
		const form = document.getElementById('form-booking-intl');
		form.addEventListener('submit', function(e) {
			e.preventDefault();

			if (!form.checkValidity()) {
				form.reportValidity();
				return;
			}

			/* -----------------------------------------------
			   VALIDASI BERAT
			------------------------------------------------ */
			const actual = parseIndoNumber(inputActual.value);
			if (actual <= 0) {
				Swal.fire('Berat Belum Diisi', 'Masukkan berat aktual barang terlebih dahulu.', 'warning');
				inputActual.focus();
				return;
			}

			/* -----------------------------------------------
			   VALIDASI TUJUAN
			------------------------------------------------ */
			const destination = $('#destination').val();
			if (!destination) {
				Swal.fire('Tujuan Belum Dipilih', 'Pilih negara tujuan pengiriman.', 'warning');
				return;
			}

			/* -----------------------------------------------
			   KONFIRMASI
			------------------------------------------------ */
			Swal.fire({
				title: 'Simpan Pengiriman?',
				text: 'Pastikan data pengirim, penerima, barang, berat, dan tujuan sudah benar.',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Ya, Simpan',
				cancelButtonText: 'Periksa Lagi',
				reverseButtons: true
			}).then(function(result) {
				if (!result.isConfirmed) return;

				/* -------------------------------------------
				   LOADING
				-------------------------------------------- */
				Swal.fire({
					title: 'Menyimpan Pengiriman...',
					text: 'Mohon tunggu sebentar.',
					allowOutsideClick: false,
					allowEscapeKey: false,
					didOpen: function() {
						Swal.showLoading();
					}
				});

				form.submit();
			});
		});

		/* =========================================================
		   INITIAL CALCULATION
		========================================================= */
		calculateAll();
	});

	/* =============================================================
	   WILAYAH INDONESIA
	============================================================= */
	function initWilayah(type) {
		const selProv = $(`#${type}_provinsi`);
		const selKota = $(`#${type}_kota`);
		const selKec = $(`#${type}_kecamatan`);
		const selKel = $(`#${type}_kelurahan`);

		function initS2(el) {
			if (el.hasClass('select2-hidden-accessible')) {
				el.select2('destroy');
			}
			el.select2({
				width: '100%'
			});
		}

		/* ---------------------------------------------------------
		   PROVINSI
		--------------------------------------------------------- */
		$.ajax({
			url: "<?= site_url('master/ajax_get_provinsi') ?>",
			type: "GET",
			dataType: "json",
			success: function(res) {
				let html = '<option value="">- Pilih Provinsi -</option>';
				res.forEach(function(item) {
					html += `<option value="${item.id}">${item.nama_provinsi}</option>`;
				});
				selProv.html(html);
				initS2(selProv);
			}
		});

		/* ---------------------------------------------------------
		   PROVINSI → KOTA
		--------------------------------------------------------- */
		selProv.on('change', function() {
			let id = $(this).val();

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
					res.forEach(function(item) {
						html += `<option value="${item.id}">${item.nama_kota}</option>`;
					});
					selKota.html(html).prop('disabled', false);
					initS2(selKota);
				}
			});
		});

		/* ---------------------------------------------------------
		   KOTA → KECAMATAN
		--------------------------------------------------------- */
		selKota.on('change', function() {
			let id = $(this).val();

			selKec.html('<option value="">- Pilih Kecamatan -</option>').prop('disabled', true);
			selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);

			if (!id) return;

			$.ajax({
				url: "<?= site_url('master/ajax_get_kecamatan/') ?>" + id,
				type: "GET",
				dataType: "json",
				success: function(res) {
					let html = '<option value="">- Pilih Kecamatan -</option>';
					res.forEach(function(item) {
						html += `<option value="${item.id}">${item.nama_kecamatan}</option>`;
					});
					selKec.html(html).prop('disabled', false);
					initS2(selKec);
				}
			});
		});

		/* ---------------------------------------------------------
		   KECAMATAN → KELURAHAN
		--------------------------------------------------------- */
		selKec.on('change', function() {
			let id = $(this).val();

			selKel.html('<option value="">- Pilih Kelurahan -</option>').prop('disabled', true);

			if (!id) return;

			$.ajax({
				url: "<?= site_url('master/ajax_get_kelurahan/') ?>" + id,
				type: "GET",
				dataType: "json",
				success: function(res) {
					let html = '<option value="">- Pilih Kelurahan -</option>';
					res.forEach(function(item) {
						html += `<option value="${item.id}">${item.nama_kelurahan}</option>`;
					});
					selKel.html(html).prop('disabled', false);
					initS2(selKel);
				}
			});
		});
	}

	/* =============================================================
	   INIT WILAYAH
	============================================================= */
	$(document).ready(function() {
		initWilayah('sender');
	});
</script>
