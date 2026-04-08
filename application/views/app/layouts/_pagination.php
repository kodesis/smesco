<?php

/**
 * Partial View: _pagination.php
 * Lokasi: application/views/app/partials/_pagination.php
 *
 * Variabel yang dibutuhkan (semua tersedia otomatis jika $data
 * di-merge dengan hasil $this->paginate() dari MY_Controller):
 *
 *   $total       — total seluruh records
 *   $page        — halaman aktif saat ini
 *   $per_page    — jumlah item per halaman
 *   $offset      — index record pertama di halaman ini (0-based)
 *   $total_pages — total halaman
 *   $base_url    — URL dasar pagination, sudah include query params, diakhiri '&page='
 *
 * Cara pakai di view manapun:
 *   $this->load->view('app/partials/_pagination', $data);
 */

// Jangan render apapun kalau tidak ada data
if (empty($total) || empty($total_pages)) return;

$start = $total ? ($offset + 1) : 0;
$end   = min($offset + $per_page, $total);
?>

<div class="card-footer d-flex align-items-center">

	<!-- Info: "Menampilkan X–Y dari Z entries" -->
	<p class="m-0 text-muted small">
		Menampilkan
		<strong><?= number_format($start) ?></strong>–<strong><?= number_format($end) ?></strong>
		dari <strong><?= number_format($total) ?></strong> data
	</p>

	<?php if ($total_pages > 1): ?>
		<ul class="pagination m-0 ms-auto">

			<!-- Tombol Prev -->
			<li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
				<a class="page-link" href="<?= ($page > 1) ? $base_url . ($page - 1) : '#' ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
						fill="none" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
						<path d="M15 6l-6 6l6 6" />
					</svg>
					prev
				</a>
			</li>

			<?php
			// Sliding window: tampilkan 2 halaman di kiri & kanan halaman aktif
			$range    = 2;
			$start_pg = max(1, $page - $range);
			$end_pg   = min($total_pages, $page + $range);
			?>

			<!-- Halaman pertama + ellipsis kalau window tidak mulai dari 1 -->
			<?php if ($start_pg > 1): ?>
				<li class="page-item">
					<a class="page-link" href="<?= $base_url ?>1">1</a>
				</li>
				<?php if ($start_pg > 2): ?>
					<li class="page-item disabled">
						<span class="page-link">…</span>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<!-- Halaman di dalam window -->
			<?php for ($i = $start_pg; $i <= $end_pg; $i++): ?>
				<li class="page-item <?= ($i === (int) $page) ? 'active' : '' ?>">
					<a class="page-link" href="<?= $base_url . $i ?>"><?= $i ?></a>
				</li>
			<?php endfor; ?>

			<!-- Ellipsis + halaman terakhir kalau window tidak sampai akhir -->
			<?php if ($end_pg < $total_pages): ?>
				<?php if ($end_pg < $total_pages - 1): ?>
					<li class="page-item disabled">
						<span class="page-link">…</span>
					</li>
				<?php endif; ?>
				<li class="page-item">
					<a class="page-link" href="<?= $base_url . $total_pages ?>"><?= $total_pages ?></a>
				</li>
			<?php endif; ?>

			<!-- Tombol Next -->
			<li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
				<a class="page-link" href="<?= ($page < $total_pages) ? $base_url . ($page + 1) : '#' ?>">
					next
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
						fill="none" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
						<path d="M9 6l6 6l-6 6" />
					</svg>
				</a>
			</li>

		</ul>
	<?php endif; ?>

</div>
