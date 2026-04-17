<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title"><?= tabler_icon('file-spreadsheet', 'me-2 text-success') ?> Preview Manifest Penjemputan</h2>
				<div class="text-muted mt-1">Pilih barang yang akan diangkut oleh kurir bandara.</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('shipment') ?>" class="btn btn-outline-secondary">
					<?= tabler_icon('arrow-left', 'me-1') ?> Kembali
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
		<?php endif; ?>

		<form action="<?= site_url('shipment/save_manifest') ?>" method="POST" id="form-manifest">

			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Daftar Barang Muatan (Telah di-Scan Supir)</h3>
					<div class="card-actions">
						<button type="submit" class="btn btn-success" id="btn-export">
							<?= tabler_icon('file-export') ?> Generate Excel
						</button>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-vcenter card-table table-striped">
						<thead>
							<tr>
								<th class="w-1">
									<input class="form-check-input m-0 align-middle" type="checkbox" id="check-all" title="Pilih Semua">
								</th>
								<th>No. Resi</th>
								<th>Tujuan</th>
								<th>Pengirim</th>
								<th>Isi Barang</th>
								<th class="text-center">Koli</th>
								<th class="text-center">Berat (Kg)</th>
							</tr>
						</thead>
						<tbody>
							<?php if (empty($shipments)): ?>
								<tr>
									<td colspan="7" class="text-center text-muted py-4">
										Tidak ada barang yang berstatus Pickup saat ini.
									</td>
								</tr>
							<?php else: ?>
								<?php foreach ($shipments as $s): ?>
									<tr>
										<td>
											<input class="form-check-input m-0 align-middle chk-item" type="checkbox" name="shipment_ids[]" value="<?= $s->id ?>">
										</td>
										<td class="fw-bold"><?= $s->no_resi ?></td>
										<td><span class="badge bg-primary-lt"><?= $s->destination ?></span></td>
										<td><?= html_escape($s->sender_name) ?></td>
										<td class="text-muted small"><?= html_escape($s->commodity_detail) ?></td>
										<td class="text-center"><?= $s->koli ?></td>
										<td class="text-center"><?= $s->chargeable_weight ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="card mt-3">
				<div class="card-header bg-dark-lt">
					<h3 class="card-title text-dark"><?= tabler_icon('truck-loading') ?> Data Penjemputan (Surat Jalan)</h3>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label required">Nama Penjemput / Supir (Forwarder)</label>
							<input type="text" name="forwarder_name" class="form-control" placeholder="Contoh: Pak Budi (Driver Smesco)" required>
						</div>
						<div class="col-md-6">
							<label class="form-label">No. HP Penjemput</label>
							<input type="text" name="forwarder_phone" class="form-control" placeholder="Opsional">
						</div>
						<div class="col-md-6">
							<label class="form-label required">Nama PIC Penerima (Gudang Bandara)</label>
							<input type="text" name="receiver_name" class="form-control" placeholder="Contoh: Staff Gudang CGK" required>
						</div>
						<div class="col-md-6">
							<label class="form-label">Alamat / Lokasi Tujuan Manifest</label>
							<input type="text" name="receiver_address" class="form-control" placeholder="Contoh: Cargo Area Bandara Soetta" required>
						</div>
					</div>
				</div>
				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary" id="btn-export">
						<?= tabler_icon('printer') ?> Simpan & Cetak Surat Jalan
					</button>
				</div>
			</div>
		</form>

	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const checkAll = document.getElementById('check-all');
		const checkItems = document.querySelectorAll('.chk-item');
		const btnExport = document.getElementById('btn-export');

		// Fitur Check All
		if (checkAll) {
			checkAll.addEventListener('change', function() {
				checkItems.forEach(item => {
					item.checked = this.checked;
				});
				toggleExportButton();
			});
		}

		// Ceklis per item
		checkItems.forEach(item => {
			item.addEventListener('change', function() {
				// Update status Check All jika ada yang uncheck
				if (!this.checked) checkAll.checked = false;

				// Jika semua dicentang manual, centang Check All
				if (document.querySelectorAll('.chk-item:checked').length === checkItems.length) {
					checkAll.checked = true;
				}
				toggleExportButton();
			});
		});

		// Disable tombol Generate jika tidak ada yang dicentang
		function toggleExportButton() {
			const checkedCount = document.querySelectorAll('.chk-item:checked').length;
			if (checkedCount === 0) {
				btnExport.disabled = true;
				btnExport.innerHTML = `<?= tabler_icon('file-export') ?> Pilih Data Dulu`;
			} else {
				btnExport.disabled = false;
				btnExport.innerHTML = `<?= tabler_icon('file-export') ?> Generate Excel (${checkedCount} Resi)`;
			}
		}

		// Set default state
		toggleExportButton();
	});
</script>
