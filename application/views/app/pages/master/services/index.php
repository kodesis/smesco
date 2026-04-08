<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Master Layanan (Service Types)</h2>
				<div class="text-muted mt-1">Mengelola tipe layanan pengiriman kargo.</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('master/create_service') ?>" class="btn btn-primary">
					<?= tabler_icon('plus', 'me-2') ?>Tambah Layanan
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

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-hover">
					<thead>
						<tr>
							<th class="w-1">No</th>
							<th>Kode</th>
							<th>Nama Layanan</th>
							<th>Deskripsi</th>
							<th>Status</th>
							<th class="w-1">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($services)): $no = 1;
							foreach ($services as $s): ?>
								<tr>
									<td class="text-muted"><?= $no++ ?></td>
									<td><span class="badge bg-blue-lt"><?= htmlspecialchars($s->code) ?></span></td>
									<td class="font-weight-medium"><?= htmlspecialchars($s->name) ?></td>
									<td class="text-muted"><?= htmlspecialchars($s->description ?: '-') ?></td>
									<td>
										<span class="badge <?= $s->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
											<?= $s->is_active ? 'Aktif' : 'Nonaktif' ?>
										</span>
									</td>
									<td>
										<div class="btn-list flex-nowrap">
											<a href="<?= site_url('master/edit_service/' . $s->id) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
											<a href="<?= site_url('master/toggle_status_service/' . $s->id) ?>" class="btn btn-sm btn-outline-warning" title="Ubah Status" onclick="return confirm('Yakin ubah status?')"><?= tabler_icon('refresh', 'icon-sm') ?></a>
											<a href="<?= site_url('master/delete_service/' . $s->id) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin hapus data ini?')"><?= tabler_icon('trash', 'icon-sm') ?></a>
										</div>
									</td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="6" class="text-center py-4">Belum ada data layanan.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>
</div>
