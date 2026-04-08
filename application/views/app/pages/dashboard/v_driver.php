<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-navy">Dashboard Driver</h2>
				<p class="text-muted small">Semangat, Bro! Pantau jemputanmu hari ini.</p>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row mb-4">
			<div class="col-12">
				<a href="<?= site_url('shipment/pickup_scan') ?>" class="card card-link bg-primary text-primary-fg py-3">
					<div class="card-body text-center">
						<div class="mb-3">
							<?= tabler_icon('barcode', 'icon-lg') ?>
						</div>
						<div class="h1 mb-0">MULAI SCAN PICKUP</div>
						<div class="opacity-50">Scan label koli saat di lokasi Mitra</div>
					</div>
				</a>
			</div>
		</div>

		<div class="row row-cards mb-4">
			<div class="col-6">
				<div class="card">
					<div class="card-body p-3 text-center">
						<div class="text-muted small uppercase fw-bold mb-1">Belum Jemput</div>
						<div class="h2 mb-0 text-yellow"><?= number_format($stats->belum_pickup) ?></div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="card">
					<div class="card-body p-3 text-center">
						<div class="text-muted small uppercase fw-bold mb-1">Sudah Pickup</div>
						<div class="h2 mb-0 text-success"><?= number_format($stats->sudah_pickup) ?></div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Pickup Terakhir</h3>
			</div>
			<div class="list-group list-group-flush list-group-hoverable">
				<?php if (!empty($recent_pickups)): foreach ($recent_pickups as $p): ?>
						<div class="list-group-item">
							<div class="row align-items-center">
								<div class="col-auto">
									<span class="avatar bg-green-lt"><?= tabler_icon('truck') ?></span>
								</div>
								<div class="col text-truncate">
									<a href="#" class="text-body d-block fw-bold"><?= $p->no_resi ?></a>
									<div class="text-muted text-truncate mt-n1"><?= $p->receiver_name ?> - <?= $p->destination ?></div>
								</div>
								<div class="col-auto">
									<span class="badge bg-green"></span>
								</div>
							</div>
						</div>
					<?php endforeach;
				else: ?>
					<div class="p-4 text-center text-muted italic">Belum ada aktivitas pickup.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
