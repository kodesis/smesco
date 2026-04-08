<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					Detail User
				</h2>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="row mb-4">
			<div class="col-12">
				<div class="alert alert-warning" role="alert">
					<?= tabler_icon('map-pin', 'me-2') ?>
					<strong>Tracker Mitra</strong> — Modul tracking kiriman akan aktif di Phase 3.
					Anda dapat melihat ringkasan status agen Anda di sini.
				</div>
			</div>
		</div>

		<!-- Agent info -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="card card-body">
					<div class="row align-items-center">
						<div class="col-auto">
							<span class="avatar avatar-lg bg-orange text-white">
								<?= tabler_icon('map-pin') ?>
							</span>
						</div>
						<div class="col">
							<h3 class="mb-0"><?= esc($agent_info->name ?? 'Agen Anda') ?></h3>
							<div class="text-muted">
								Kode: <strong><?= esc($agent_info->code ?? '—') ?></strong>
								· <?= esc($agent_info->city ?? '—') ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Placeholder tracking stats -->
		<div class="row row-deck row-cards">
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm border-dashed">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-blue text-white avatar"><?= tabler_icon('package') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium text-muted">—</div>
								<div class="text-muted">Paket Menunggu Konfirmasi</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm border-dashed">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-green text-white avatar"><?= tabler_icon('circle-check') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium text-muted">—</div>
								<div class="text-muted">Paket Terkirim Hari Ini</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm border-dashed">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-yellow text-white avatar"><?= tabler_icon('truck-delivery') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium text-muted">—</div>
								<div class="text-muted">Dalam Perjalanan</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm border-dashed">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-auto">
								<span class="bg-red text-white avatar"><?= tabler_icon('alert-circle') ?></span>
							</div>
							<div class="col">
								<div class="font-weight-medium text-muted">—</div>
								<div class="text-muted">Perlu Perhatian</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Placeholder table kiriman -->
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('package', 'me-2') ?>Daftar Kiriman — Coming Soon</h3>
					</div>
					<div class="card-body">
						<div class="text-center py-5">
							<div class="mb-3">
								<?= tabler_icon('package', 'icon-lg text-muted') ?>
							</div>
							<h3 class="text-muted">Modul Tracking Belum Aktif</h3>
							<p class="text-muted mb-0">Fitur ini akan tersedia pada Phase 3 pengembangan sistem.</p>
							<div class="mt-3">
								<span class="badge bg-yellow-lt">Segera Hadir</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
