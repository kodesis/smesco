<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Manajemen Agen</h2>
				<div class="text-muted mt-1">
					<?= number_format($total) ?> agen terdaftar
				</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('agents/create') ?>" class="btn btn-primary">
					<?= tabler_icon('plus', 'me-2') ?>Tambah Agen
				</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<?php if ($this->session->flashdata('success')): ?>
			<div class="alert alert-success alert-dismissible mb-4" role="alert">
				<div class="d-flex align-items-center">
					<div><?= tabler_icon('circle-check', 'me-2') ?></div>
					<div><?= $this->session->flashdata('success') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>
		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger alert-dismissible mb-4" role="alert">
				<div class="d-flex align-items-center">
					<div><?= tabler_icon('alert-circle', 'me-2') ?></div>
					<div><?= $this->session->flashdata('error') ?></div>
				</div>
				<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
			</div>
		<?php endif; ?>

		<div class="row row-cards">
			<div class="col-12">
				<div class="card">

					<div class="card-header">
						<h3 class="card-title">Daftar Agen</h3>
					</div>

					<div class="card-body border-bottom py-3">
						<div class="d-flex align-items-center justify-content-between">
							<div class="text-muted small d-none d-sm-block">
								Menampilkan daftar agen / mitra.
							</div>
							<div class="ms-auto">
								<form method="get" action="<?= site_url('agents') ?>" class="d-inline-block">
									<div class="input-group">
										<span class="input-group-text bg-transparent">
											<?= tabler_icon('search', 'icon text-muted') ?>
										</span>
										<input type="text" name="q" class="form-control"
											placeholder="Nama agen, kode, atau kota..."
											value="<?= ($search) ? htmlspecialchars($search) : '' ?>" style="min-width: 250px;">
										<button type="submit" class="btn btn-primary">Cari</button>
										<?php if ($search): ?>
											<a href="<?= site_url('agents') ?>" class="btn btn-outline-secondary" title="Reset Pencarian">
												<?= tabler_icon('x') ?>
											</a>
										<?php endif; ?>
									</div>
								</form>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table">
							<thead>
								<tr>
									<th>Agen</th>
									<th>Lokasi</th>
									<th class="text-nowrap">Kontak</th>
									<th class="text-nowrap">User</th>
									<th class="text-nowrap">Status</th>
									<th class="text-nowrap">Terdaftar</th>
									<th class="w-1 text-nowrap">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($agents): ?>
									<?php foreach ($agents as $a): ?>
										<tr>
											<td>
												<div class="d-flex align-items-center">
													<span class="avatar avatar-sm me-3 bg-blue-lt text-blue flex-shrink-0">
														<?= tabler_icon('building') ?>
													</span>
													<div>
														<div class="font-weight-medium">
															<a href="<?= site_url('agents/detail/' . $a->id) ?>" class="text-body">
																<?= htmlspecialchars($a->name) ?>
															</a>
														</div>
														<div class="text-muted small mt-1">
															<span class="badge bg-blue-lt" title="Kode Agen"><?= htmlspecialchars($a->code) ?></span>
														</div>
													</div>
												</div>
											</td>
											<td>
												<div class="text-wrap" style="max-width: 250px;"
													<?php if (mb_strlen($a->address) > 50) echo 'title="' . htmlspecialchars($a->address) . '" style="cursor: help;"'; ?>>

													<?= htmlspecialchars(mb_strlen($a->address) > 50 ? mb_substr($a->address, 0, 50) . '...' : $a->address) ?>

												</div>
												<?php if ($a->province_name): ?>
													<div class="text-muted small mt-1"><?= htmlspecialchars($a->regency_name . ', ' . $a->province_name) ?></div>
												<?php endif; ?>
											</td>
											<td class="text-nowrap">
												<?php if ($a->phone): ?>
													<div class="text-muted small">
														<?= tabler_icon('phone', 'icon-sm me-1') ?><?= htmlspecialchars($a->phone) ?>
													</div>
												<?php endif; ?>
												<?php if ($a->email): ?>
													<div class="text-muted small mt-1">
														<?= tabler_icon('mail', 'icon-sm me-1') ?><?= htmlspecialchars($a->email) ?>
													</div>
												<?php endif; ?>
												<?php if (! $a->phone && ! $a->email): ?>
													<span class="text-muted">—</span>
												<?php endif; ?>
											</td>
											<td class="text-nowrap">
												<span class="badge bg-purple-lt"><?= number_format($a->total_users) ?> user</span>
											</td>
											<td class="text-nowrap">
												<span class="badge <?= $a->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
													<?= $a->is_active ? 'Aktif' : 'Nonaktif' ?>
												</span>
											</td>
											<td class="text-muted small text-nowrap">
												<?= date('d M Y', strtotime($a->created_at)) ?>
											</td>
											<td class="text-end">

												<div class="d-flex align-items-center justify-content-end gap-1">
													<a href="<?= site_url('agents/detail/' . $a->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Detail"
														data-bs-toggle="tooltip" data-bs-placement="top">
														<?= tabler_icon('eye') ?>
													</a>
													<a href="<?= site_url('agents/edit/' . $a->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Edit"
														data-bs-toggle="tooltip" data-bs-placement="top">
														<?= tabler_icon('pencil') ?>
													</a>
													<a href="<?= site_url('agents/toggle_status/' . $a->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-secondary"
														title="Toggle Status"
														data-bs-toggle="tooltip" data-bs-placement="top"
														onclick="return confirm('Ubah status user ini?')">
														<?= tabler_icon('refresh') ?>
													</a>
													<a href="<?= site_url('agents/delete/' . $a->id) ?>"
														class="btn btn-sm btn-icon btn-ghost-danger"
														title="Hapus"
														data-bs-toggle="tooltip" data-bs-placement="top"
														onclick="return confirm('Hapus user <?= htmlspecialchars($a->name, ENT_QUOTES) ?>? Tindakan ini tidak dapat dibatalkan.')">
														<?= tabler_icon('trash') ?>
													</a>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="7" class="text-center py-5">
											<div class="empty">
												<div class="empty-icon">
													<?= tabler_icon('building-off', 'icon-lg') ?>
												</div>
												<p class="empty-title">Data tidak ditemukan</p>
												<p class="empty-subtitle text-muted">
													<?= $search
														? 'Tidak ada agen yang cocok dengan kata kunci "<strong>' . htmlspecialchars($search) . '</strong>".'
														: 'Belum ada agen yang terdaftar di dalam sistem.' ?>
												</p>
												<div class="empty-action">
													<?php if ($search): ?>
														<a href="<?= site_url('agents') ?>" class="btn btn-primary">
															<?= tabler_icon('search', 'me-2') ?>Reset Pencarian
														</a>
													<?php else: ?>
														<a href="<?= site_url('agents/create') ?>" class="btn btn-primary">
															<?= tabler_icon('plus', 'me-2') ?>Tambah Agen Pertama
														</a>
													<?php endif; ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

					<?php $this->load->view('app/layouts/_pagination', compact(
						'total',
						'page',
						'per_page',
						'offset',
						'total_pages'
					)); ?>

				</div>
			</div>
		</div>

	</div>
</div>
