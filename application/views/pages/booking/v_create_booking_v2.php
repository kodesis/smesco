<!-- v_create_booking_v2.php -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<style>
	/* Memaksa dropdown autocomplete selalu di urutan paling depan */
	.ui-autocomplete {
		z-index: 9999 !important;
		/* Menang telak dari sticky-top */
		max-height: 250px;
		/* Biar kalau kotanya banyak, bisa di-scroll */
		overflow-y: auto;
		overflow-x: hidden;
		background-color: #ffffff;
		border: 1px solid #cbd5e1;
		border-radius: 4px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	}

	/* Mempercantik hover saat milih kota */
	.ui-menu-item-wrapper.ui-state-active {
		background-color: #0054a6 !important;
		/* Warna biru tabler */
		color: #ffffff !important;
		border: none !important;
	}
</style>

<!-- Page header -->
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					<?= $title ?>s
				</h2>
			</div>
			<!-- Page title actions -->
			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a href="<?= base_url('booking') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Create new report">
						<!-- Download SVG icon from http://tabler-icons.io/i/plus -->
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
							<path stroke="none" d="M0 0h24v24H0z" fill="none" />
							<path d="M5 12l14 0" />
							<path d="M5 12l6 6" />
							<path d="M5 12l6 -6" />
						</svg>
						Back</a>
					<a href="<?= base_url('booking') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Create new report">
						<!-- Download SVG icon from http://tabler-icons.io/i/plus -->
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
							<path stroke="none" d="M0 0h24v24H0z" fill="none" />
							<path d="M5 12l14 0" />
							<path d="M5 12l6 6" />
							<path d="M5 12l6 -6" />
						</svg>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Page body -->
<div class="page-body">
	<div class="container-xl">
		<form action="<?= base_url('booking/store_booking') ?>" method="post" id="formBooking">
			<div class="row row-cards">

				<div class="col-lg-8">
					<div class="card">
						<div class="card-body">
							<div class="row g-3 mb-4">
								<div class="col-md-6 col-12">
									<label class="form-label text-primary fw-bold">Jenis Barang (Commodity)</label>
									<input type="text" name="jenis_barang" id="jenis_barang" class="form-control" placeholder="Contoh: PAKET BAJU" oninput="this.value = this.value.toUpperCase()">
								</div>
								<div class="col-md-3 col-6">
									<label class="form-label">Barang Berharga?</label>
									<label class="form-check form-switch mt-2">
										<input class="form-check-input" type="checkbox" name="is_berharga" id="is_berharga" value="1">
										<span class="form-check-label" id="status_berharga">Tidak</span>
									</label>
								</div>
								<div class="col-md-3 col-6" id="input_nilai_barang" style="display: none;">
									<label class="form-label text-danger">Nilai Barang (Rp)</label>
									<input type="text" name="nilai_barang" id="nilai_barang" class="form-control angka" value="0">
								</div>
							</div>

							<hr class="my-4">

							<div class="row">
								<div class="col-md-6 border-end">
									<h4 class="mb-3 text-secondary">Data Pengirim</h4>
									<div class="mb-3">
										<label class="form-label">Nama Pengirim</label>
										<input type="text" name="nama_pengirim" id="nama_pengirim" class="form-control" oninput="this.value = this.value.toUpperCase()">
									</div>
									<div class="mb-3">
										<label class="form-label">No. WA Pengirim</label>
										<input type="text" name="telepon_pengirim" id="telepon_pengirim" class="form-control">
									</div>
									<div class="mb-3">
										<label class="form-label">Alamat Pengirim</label>
										<textarea name="alamat_pengirim" id="alamat_pengirim" class="form-control" rows="3" oninput="this.value = this.value.toUpperCase()"></textarea>
									</div>
								</div>

								<div class="col-md-6">
									<h4 class="mb-3 text-secondary px-lg-3">Data Penerima</h4>
									<div class="mb-3 px-lg-3">
										<label class="form-label">Nama Penerima</label>
										<input type="text" name="nama_penerima" id="nama_penerima" class="form-control" oninput="this.value = this.value.toUpperCase()">
									</div>
									<div class="mb-3 px-lg-3">
										<label class="form-label">No. WA Penerima</label>
										<input type="text" name="telepon_penerima" id="telepon_penerima" class="form-control">
									</div>
									<div class="mb-3 px-lg-3">
										<label class="form-label">Alamat Penerima</label>
										<textarea name="alamat_penerima" id="alamat_penerima" class="form-control" rows="3" oninput="this.value = this.value.toUpperCase()"></textarea>
									</div>
								</div>
							</div>

							<hr class="my-4">
							<div class="row mb-3">
								<div class="col-md-4">
									<label class="form-label font-weight-bold text-primary">Berat Timbang Aktual (kg)</label>
									<input type="text" name="berat_timbang" id="berat_timbang" class="form-control border-primary" placeholder="0">
								</div>
							</div>

							<h4 class="mb-3">Input Dimensi Paket</h4>
							<div class="table-responsive">
								<table class="table table-bordered table-vcenter">
									<thead>
										<tr class="bg-light">
											<th class="w-1 text-center">#</th>
											<th>P (cm)</th>
											<th>L (cm)</th>
											<th>T (cm)</th>
											<th>Koli</th>
											<th>Volume (m³)</th>
											<th class="w-1"></th>
										</tr>
									</thead>
									<tbody id="table-body">
										<tr class="baris">
											<td class="nomor-urut text-muted text-center">1.</td>
											<td><input type="text" name="panjang[]" class="form-control"></td>
											<td><input type="text" name="lebar[]" class="form-control"></td>
											<td><input type="text" name="tinggi[]" class="form-control"></td>
											<td><input type="text" name="jumlah[]" class="form-control" value="1"></td>
											<td><input type="text" name="volume[]" class="form-control bg-light" value="0" readonly></td>
											<td>
												<button type="button" class="btn btn-danger btn-icon hapusRow">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
														<path stroke="none" d="M0 0h24v24H0z" fill="none" />
														<path d="M4 7l16 0" />
														<path d="M10 11l0 6" />
														<path d="M14 11l0 6" />
														<path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
														<path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
													</svg>
												</button>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="7" class="text-end">
												<button type="button" class="btn btn-outline-secondary btn-sm" id="addRow">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
														<path stroke="none" d="M0 0h24v24H0z" fill="none" />
														<path d="M12 5l0 14" />
														<path d="M5 12l14 0" />
													</svg>
													Tambah Baris
												</button>
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="sticky-top" style="top: 20px;">
						<div class="card card-md border-primary shadow-sm">
							<div class="card-status-top bg-primary"></div>
							<div class="card-body">
								<h3 class="card-title text-center mb-4">Order Summary</h3>

								<div class="mb-3">
									<label class="form-label fw-bold">Rute Pengiriman</label>

									<input type="text" name="origin" id="origin" class="form-control mb-2" placeholder="Asal (Origin)" oninput="this.value = this.value.toUpperCase()" required>

									<div class="text-center my-2 text-muted">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
											<path stroke="none" d="M0 0h24v24H0z" fill="none" />
											<path d="M12 5l0 14" />
											<path d="M18 13l-6 6" />
											<path d="M6 13l6 6" />
										</svg>
									</div>

									<input type="text" name="destination" id="destination" class="form-control" placeholder="Tujuan (Destination)" oninput="this.value = this.value.toUpperCase()" required>

									<div class="mt-2">
										<input type="hidden" name="jenis_pengiriman" id="jenis_pengiriman" value="">
										<input type="text" id="jenis_label" class="form-control bg-light text-center fw-bold text-blue" value="Pilih Tujuan" readonly>
									</div>
								</div>

								<div class="p-3 bg-light rounded border mb-4">
									<div class="d-flex justify-content-between mb-2">
										<span class="text-secondary small">Berat Timbang:</span>
										<span class="fw-bold"><span id="berat_timbang_summary">0</span> kg</span>
									</div>
									<div class="d-flex justify-content-between mb-2">
										<span class="text-secondary small">Total Volume:</span>
										<span class="fw-bold"><span id="total_volume_summary">0</span> m³</span>
									</div>
									<div class="d-flex justify-content-between border-top pt-2">
										<span class="text-danger fw-bold">Chargeable:</span>
										<span class="text-danger fw-bold"><span id="chargeable_summary">0</span> kg</span>
										<input type="hidden" name="chargeable" id="chargeable" value="0">
										<input type="hidden" name="total_qty" id="total_qty" value="1">
										<input type="hidden" name="total_volume" id="total_volume" value="0">
									</div>
								</div>

								<div class="mb-4">
									<div class="d-flex justify-content-between align-items-center mb-1">
										<label class="form-label mb-0 small">Harga per Kg</label>
										<input type="hidden" name="harga" id="harga" value="0">
										<input type="hidden" name="harga_jual" id="harga_jual" value="0">
										<span class="fw-bold">Rp <span id="harga_label_summary">0</span></span>
									</div>
									<hr class="my-2">
									<label class="form-label text-primary fw-bold mb-1">Total Bayar</label>
									<h2 class="text-primary fw-bolder mb-0">Rp <span id="nominal_summary">0</span></h2>
									<input type="hidden" name="nominal" id="nominal" value="0">
								</div>

								<button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm" id="btnSimpan">
									Simpan Booking
								</button>
							</div>
						</div>
					</div>
				</div>

			</div>
		</form>
	</div>
</div>
