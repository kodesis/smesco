<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Manajemen Pricelist</h2>
				<div class="text-muted mt-1">
					<?= number_format($total) ?> pricelist terdaftar
				</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImportPricelist">
						<?= tabler_icon('file-spreadsheet', 'me-2') ?>Import Excel
					</a>

					<a href="<?= site_url('master/create_pricelist') ?>" class="btn btn-primary">
						<?= tabler_icon('plus', 'me-2') ?>Tambah Pricelist
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-12">
				<div class="card">

					<div class="card-header">
						<div class="d-flex align-items-center gap-2 flex-wrap">
							<h3 class="card-title me-2">Daftar Harga</h3>
						</div>

						<div class="card-options">
							<!-- Search (preserve filter params) -->
							<form method="get" action="<?= site_url('master/pricelist') ?>">
								<div class="input-group">
									<span class="input-group-text bg-transparent border-end-0">
										<?= tabler_icon('search', 'icon text-muted') ?>
									</span>
									<input type="text" name="q" class="form-control form-control-sm border-start-0 ps-0"
										placeholder="Asal, tujuan, atau harga..."
										value="<?= htmlspecialchars($search ?? '') ?>"
										style="min-width: 200px;">
									<button type="submit" class="btn btn-sm btn-primary">Cari</button>
									<?php if ($search): ?>
										<a href="<?= site_url('master/pricelist') ?>" class="btn btn-sm btn-danger" title="Reset">
											<?= tabler_icon('x') ?>
										</a>
									<?php endif; ?>
								</div>
							</form>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table">
							<thead>
								<tr>
									<th>Rute & Service</th>
									<th>Kribo</th>
									<th>Smesco</th>
									<th>Status</th>
									<th>User</th>
									<th class="text-end">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($pricelists): ?>
									<?php foreach ($pricelists as $p): ?>
										<tr>
											<td>
												<span class="badge bg-azure-lt me-2"><?= $p->service_name ?></span><br><strong><?= $p->origin ?> → <?= $p->destination ?></strong>
											</td>
											<td class="text-muted text-end">Rp <?= number_format($p->price_kribo, 0, ',', '.') ?></td>
											<td class="fw-bold text-primary text-end">Rp <?= number_format($p->price_smesco, 0, ',', '.') ?></td>
											<td>
												<span class="badge <?= $p->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
													<?= $p->is_active ? 'Aktif' : 'Nonaktif' ?>
												</span>
											</td>
											<td class="text-muted small">
												Last updated by <?= $p->created_by_name ?? 'System' ?><br>
												<?= date('d M Y', strtotime($p->updated_at)) ?>
											</td>

											<td class="text-end">
												<div class="d-flex align-items-center justify-content-end gap-1">
													<a href="<?= site_url('master/detail_pricelist/' . $p->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Detail"
														data-bs-toggle="tooltip" data-bs-placement="top">
														<?= tabler_icon('eye') ?>
													</a>
													<a href="<?= site_url('master/edit_pricelist/' . $p->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Edit"
														data-bs-toggle="tooltip" data-bs-placement="top">
														<?= tabler_icon('pencil') ?>
													</a>
													<a href="<?= site_url('master/toggle_status_pricelist/' . $p->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Toggle Status"
														data-bs-toggle="tooltip" data-bs-placement="top"
														onclick="return confirm('Ubah status user ini?')">
														<?= tabler_icon('refresh') ?>
													</a>
													<a href="<?= site_url('master/delete_pricelist/' . $p->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-danger"
														title="Hapus"
														data-bs-toggle="tooltip" data-bs-placement="top"
														onclick="return confirm('Hapus rute ini <?= htmlspecialchars($p->origin . ' → ' . $p->destination, ENT_QUOTES) ?>? Tindakan ini tidak dapat dibatalkan.')">
														<?= tabler_icon('trash') ?>
													</a>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="6" class="text-center py-5">
											<div class="empty">
												<div class="empty-icon">
													<?= tabler_icon('mood-empty', 'icon-lg') ?>
												</div>
												<p class="empty-title">Data tidak ditemukan</p>
												<p class="empty-subtitle text-muted">
													<?= $search
														? 'Tidak ada pricelist yang cocok dengan kata kunci "<strong>' . htmlspecialchars($search) . '</strong>".'
														: 'Belum ada pricelist yang terdaftar di dalam sistem.' ?>
												</p>
												<?php if ($search): ?>
													<div class="empty-action">
														<a href="<?= site_url('master') ?>" class="btn btn-primary">
															<?= tabler_icon('x', 'me-2') ?>Reset Pencarian
														</a>
													</div>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
					<!-- Pagination Footer -->
					<?php $this->load->view('app/layouts/_pagination', compact(
						'total',
						'page',
						'per_page',
						'offset',
						'total_pages',
						'base_url'
					)); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modalImportPricelist" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<?= form_open_multipart('master/import_excel_pricelist') ?>
			<div class="modal-header">
				<h5 class="modal-title"><?= tabler_icon('file-spreadsheet', 'me-2') ?> Import Pricelist Masal</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

				<div class="alert alert-info" role="alert">
					<div class="d-flex">
						<div><?= tabler_icon('info-circle', 'me-2') ?></div>
						<div>
							<h4 class="alert-title">Format Wajib!</h4>
							<div class="text-muted small">
								Pastikan data Anda sesuai dengan format template kami. Kolom <strong>Service_Type_ID</strong> harus diisi dengan ID Angka (bukan nama service).
							</div>
							<a href="<?= site_url('master/download_template_pricelist') ?>" class="btn btn-sm btn-info mt-3">
								<?= tabler_icon('file-download', 'me-1') ?> Download Template
							</a>
						</div>
					</div>
				</div>

				<div class="mb-3 mt-4">
					<div class="form-label required">Pilih File Excel (.xls, .xlsx, .csv)</div>
					<input type="file" class="form-control" name="file_excel" accept=".xls,.xlsx,.csv" required>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
				<button type="submit" class="btn btn-success">
					<?= tabler_icon('upload', 'me-2') ?> Upload & Proses
				</button>
			</div>
			<?= form_close() ?>
		</div>
	</div>
</div>
