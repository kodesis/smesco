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
			<div class="col-md-12">
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">Informasi Rute</h3>
					</div>
					<div class="card-body">
						<div class="datagrid">
							<div class="datagrid-item">
								<div class="datagrid-title">Kategori</div>
								<div class="datagrid-content">
									<span class="badge <?= $pricelist->category === 'INTERNATIONAL' ? 'bg-purple' : 'bg-azure' ?>-lt">
										<?= $pricelist->category ?>
									</span>
								</div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Rute</div>
								<div class="datagrid-content fw-bold"><?= $pricelist->origin ?> → <?= $pricelist->destination ?></div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Layanan</div>
								<div class="datagrid-content"><?= $pricelist->service_name ?></div>
							</div>
							<div class="datagrid-item">
								<div class="datagrid-title">Min. Charge</div>
								<div class="datagrid-content"><?= floatval($pricelist->min_weight_kg) ?> Kg</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Informasi Harga</h3>
					</div>

					<?php if ($pricelist->is_tiered == 0): ?>
						<div class="card-body">
							<div class="datagrid">
								<div class="datagrid-item <?= hide_if_not_admin() ?>">
									<div class="datagrid-title">Harga Modal (Kribo)</div>
									<div class="datagrid-content text-danger">Rp <?= number_format($pricelist->price_kribo) ?></div>
								</div>
								<div class="datagrid-item">
									<div class="datagrid-title">Harga Jual (Smesco)</div>
									<div class="datagrid-content text-success h3 mb-0">Rp <?= number_format($pricelist->price_smesco) ?></div>
								</div>
								<div class="datagrid-item <?= hide_if_not_admin() ?>">
									<div class="datagrid-title">Potensi Margin</div>
									<div class="datagrid-content fw-bold">Rp <?= number_format($pricelist->price_smesco - $pricelist->price_kribo) ?></div>
								</div>
							</div>
						</div>
					<?php else: ?>
						<div class="table-responsive">
							<table class="table table-vcenter card-table">
								<thead>
									<tr>
										<th>Range Berat</th>
										<th class="text-end <?= hide_if_not_admin() ?>">Modal (Kribo)</th>
										<th class="text-end">Jual (Smesco)</th>
										<th class="text-end <?= hide_if_not_admin() ?>">Margin</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($tiers as $t): ?>
										<tr>
											<td><?= $t->min_weight ?> - <?= $t->max_weight ?> Kg</td>
											<td class="text-end text-danger <?= hide_if_not_admin() ?>">Rp <?= number_format($t->price_kribo) ?></td>
											<td class="text-end text-success fw-bold">Rp <?= number_format($t->price_smesco) ?></td>
											<td class="text-end fw-bold <?= hide_if_not_admin() ?>">Rp <?= number_format($t->price_smesco - $t->price_kribo) ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
