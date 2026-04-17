<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-4">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title"><?= tabler_icon('file-description', 'me-2 text-primary') ?> Daftar Surat Jalan (Manifest)</h2>
				<div class="text-muted mt-1">Riwayat penyerahan barang ke supir / forwarder.</div>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<a href="<?= site_url('shipment/preview_manifest') ?>" class="btn btn-warning">
					<?= tabler_icon('plus', 'me-1') ?> Buat Manifest Baru
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
		<?php if ($this->session->flashdata('success')): ?>
			<div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
		<?php endif; ?>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-striped">
					<thead>
						<tr>
							<th>Tanggal</th>
							<th>No. Manifest</th>
							<th>Supir / Forwarder</th>
							<th>Tujuan (Bandara)</th>
							<th class="text-center">Total Resi</th>
							<th>Status</th>
							<th class="w-1">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($manifests)): ?>
							<tr>
								<td colspan="7" class="text-center text-muted py-4">
									Belum ada data Surat Jalan.
								</td>
							</tr>
						<?php else: ?>
							<?php foreach ($manifests as $m): ?>
								<tr>
									<td class="text-nowrap text-muted">
										<?= date('d M Y', strtotime($m->tanggal)) ?><br>
										<small><?= date('H:i', strtotime($m->tanggal)) ?> WIB</small>
									</td>
									<td class="fw-bold"><?= $m->no_manifest ?></td>
									<td>
										<div class="font-weight-medium"><?= html_escape($m->forwarder_name) ?></div>
										<div class="text-muted small"><?= html_escape($m->forwarder_phone) ?></div>
									</td>
									<td><?= html_escape($m->receiver_name) ?></td>
									<td class="text-center">
										<span class="badge bg-blue-lt"><?= $m->total_resi ?> AWB</span>
									</td>
									<td>
										<?php if ($m->status === 'PRINTED'): ?>
											<span class="badge bg-green">Printed</span>
										<?php else: ?>
											<span class="badge bg-secondary"><?= $m->status ?></span>
										<?php endif; ?>
									</td>
									<td>
										<div class="btn-list flex-nowrap">
											<a href="<?= site_url('shipment/print_manifest/' . $m->id) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Print Ulang">
												<?= tabler_icon('printer') ?> Print
											</a>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>
</div>
