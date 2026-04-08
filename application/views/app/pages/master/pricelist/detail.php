<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= site_url('master/pricelist') ?>">Pricelist</a></li>
						<li class="breadcrumb-item active">Detail Pricelist</li>
					</ol>
				</nav>
				<h2 class="page-title">Detail Rute & Harga</h2>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('master/edit_pricelist/' . $pricelist->id) ?>" class="btn btn-primary">
					<?= tabler_icon('pencil', 'me-2') ?>Edit Data
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

		<div class="row">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Informasi Rute</h3>
					</div>
					<div class="card-body">
						<div class="datagrid">
							<div class="datagrid-item">
								<div class="datagrid-title">Kota Asal (Origin)</div>
								<div class="datagrid-content"><?= htmlspecialchars($pricelist->origin) ?></div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Kota Tujuan (Destination)</div>
								<div class="datagrid-content"><?= htmlspecialchars($pricelist->destination) ?></div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Tipe Layanan</div>
								<div class="datagrid-content">
									<span class="badge bg-blue-lt"><?= htmlspecialchars($pricelist->service_name ?? 'ID: ' . $pricelist->service_type_id) ?></span>
								</div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Harga per Kg</div>
								<div class="datagrid-content text-success font-weight-bold">
									Rp <?= number_format($pricelist->price_per_kg, 0, ',', '.') ?>
								</div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Minimum Berat</div>
								<div class="datagrid-content"><?= floatval($pricelist->min_weight_kg) ?> Kg</div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Status</div>
								<div class="datagrid-content">
									<span class="badge <?= $pricelist->is_active ? 'bg-success' : 'bg-danger' ?>">
										<?= $pricelist->is_active ? 'Aktif' : 'Nonaktif' ?>
									</span>
								</div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Dibuat Pada</div>
								<div class="datagrid-content"><?= date('d M Y H:i', strtotime($pricelist->created_at)) ?></div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Terakhir Diupdate</div>
								<div class="datagrid-content"><?= date('d M Y H:i', strtotime($pricelist->updated_at)) ?></div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Dibuat oleh</div>
								<div class="datagrid-content"><?= htmlspecialchars($pricelist->created_by_name ?? 'N/A') ?></div>
							</div>
						</div>
					</div>
					<div class="card-footer d-flex">
						<a href="<?= site_url('master/toggle_status_pricelist/' . $pricelist->id) ?>" class="btn btn-outline-warning" onclick="return confirm('Ubah status Pricelist ini?')">
							<?= tabler_icon('refresh', 'me-2') ?>Toggle Status
						</a>
						<a href="<?= site_url('master/delete_pricelist/' . $pricelist->id) ?>" class="btn btn-outline-danger ms-auto" onclick="return confirm('Hapus Pricelist ini secara permanen?')">
							<?= tabler_icon('trash', 'me-2') ?>Hapus Pricelist
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
