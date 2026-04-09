<?php defined('BASEPATH') or exit('No direct script access allowed');
$sess     = $this->session->userdata('user');
$is_kribo = in_array($sess['role_slug'], ['superadmin', 'admin-kribo', 'finance-kribo']);
$status_filter = $status ?? '';
?>

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
        <div class="card">

            <!-- Card Header: Search + Filter Tabs -->
            <div class="card-header d-flex flex-wrap align-items-center gap-2">

                <!-- Tab Filter Status -->
                <ul class="nav nav-tabs card-header-tabs me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === '' ? 'active' : '' ?>"
                           href="<?= site_url('master/pricelist') ?>?q=<?= urlencode($search ?? '') ?>">
                            Semua
                            <span class="badge bg-secondary ms-1"><?= number_format($total) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === '1' ? 'active' : '' ?>"
                           href="<?= site_url('master/pricelist') ?>?status=1&q=<?= urlencode($search ?? '') ?>">
                            Aktif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter === '0' ? 'active' : '' ?>"
                           href="<?= site_url('master/pricelist') ?>?status=0&q=<?= urlencode($search ?? '') ?>">
                            Nonaktif
                        </a>
                    </li>
                </ul>

                <!-- Search -->
                <form method="get" action="<?= site_url('master/pricelist') ?>" class="d-flex gap-1">
                    <?php if ($status_filter !== ''): ?>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                    <?php endif; ?>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <?= tabler_icon('search', 'icon text-muted') ?>
                        </span>
                        <input type="text" name="q"
                               class="form-control border-start-0 ps-0"
                               placeholder="Asal, tujuan..."
                               value="<?= htmlspecialchars($search ?? '') ?>"
                               style="min-width: 180px;">
                        <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                        <?php if ($search): ?>
                            <a href="<?= site_url('master/pricelist') ?><?= $status_filter !== '' ? '?status=' . $status_filter : '' ?>"
                               class="btn btn-sm btn-danger" title="Reset">
                                <?= tabler_icon('x') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table">
                    <thead>
                        <tr>
                            <th>Rute & Layanan</th>
                            <th class="text-end">Min. Berat</th>
                            <?php if ($is_kribo): ?>
                                <th class="text-end">Harga Beli</th>
                            <?php endif; ?>
                            <th class="text-end">Harga Jual</th>
                            <th>Status</th>
                            <th class="text-muted small">Diperbarui</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pricelists): ?>
                            <?php foreach ($pricelists as $p): ?>
                                <tr>
                                    <!-- Rute & Layanan -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge <?= $p->category === 'INTERNATIONAL' ? 'bg-purple' : 'bg-azure' ?>-lt">
                                                <?= $p->category ?>
                                            </span>
                                            <span class="badge bg-light text-dark small">
                                                <?= $p->service_code ?> — <?= $p->service_name ?>
                                            </span>
                                        </div>
                                        <strong><?= $p->origin ?> → <?= $p->destination ?></strong>
                                    </td>

                                    <!-- Min Berat -->
                                    <td class="text-end text-muted small">
                                        <?= number_format($p->min_weight_kg, 1) ?> kg
                                    </td>

                                    <!-- Harga Beli (Kribo only) -->
                                    <?php if ($is_kribo): ?>
                                        <td class="text-end">
                                            <?php if ($p->is_tiered): ?>
                                                <span class="text-muted small fst-italic">Lihat detail</span>
                                            <?php else: ?>
                                                <span class="text-muted">Rp <?= number_format($p->price_kribo) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>

                                    <!-- Harga Jual -->
                                    <td class="text-end">
                                        <?php if ($p->is_tiered): ?>
                                            <span class="badge bg-purple-lt">
                                                <?= tabler_icon('layers-difference', 'icon-sm me-1') ?> Tiered
                                            </span>
                                        <?php else: ?>
                                            <strong class="text-primary">Rp <?= number_format($p->price_smesco) ?></strong>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        <span class="badge <?= $p->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
                                            <?= $p->is_active ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>

                                    <!-- Diperbarui -->
                                    <td class="text-muted small">
                                        <?= $p->created_by_name ?? 'System' ?><br>
                                        <span class="text-muted"><?= date('d M Y', strtotime($p->updated_at)) ?></span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="<?= site_url('master/detail_pricelist/' . $p->id) ?>"
                                               class="btn btn-sm btn-icon btn-ghost-secondary"
                                               data-bs-toggle="tooltip" title="Detail">
                                                <?= tabler_icon('eye') ?>
                                            </a>
                                            <a href="<?= site_url('master/edit_pricelist/' . $p->id) ?>"
                                               class="btn btn-sm btn-icon btn-ghost-secondary"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <?= tabler_icon('pencil') ?>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-ghost-secondary btn-toggle-status"
                                                    data-href="<?= site_url('master/toggle_status_pricelist/' . $p->id) ?>"
                                                    data-status="<?= $p->is_active ?>"
                                                    data-route="<?= htmlspecialchars($p->origin . ' → ' . $p->destination) ?>"
                                                    data-bs-toggle="tooltip"
                                                    title="Toggle Status">
                                                <?= tabler_icon('refresh') ?>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-ghost-danger btn-hapus"
                                                    data-href="<?= site_url('master/delete_pricelist/' . $p->id) ?>"
                                                    data-route="<?= htmlspecialchars($p->origin . ' → ' . $p->destination, ENT_QUOTES) ?>"
                                                    data-bs-toggle="tooltip"
                                                    title="Hapus">
                                                <?= tabler_icon('trash') ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $is_kribo ? 7 : 6 ?>" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <?= tabler_icon('mood-empty', 'icon-lg') ?>
                                        </div>
                                        <p class="empty-title">Data tidak ditemukan</p>
                                        <p class="empty-subtitle text-muted">
                                            <?php if ($search): ?>
                                                Tidak ada pricelist yang cocok dengan kata kunci
                                                "<strong><?= htmlspecialchars($search) ?></strong>".
                                            <?php elseif ($status_filter !== ''): ?>
                                                Tidak ada pricelist dengan status
                                                <strong><?= $status_filter === '1' ? 'Aktif' : 'Nonaktif' ?></strong>.
                                            <?php else: ?>
                                                Belum ada pricelist yang terdaftar.
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($search || $status_filter !== ''): ?>
                                            <div class="empty-action">
                                                <a href="<?= site_url('master/pricelist') ?>" class="btn btn-primary">
                                                    <?= tabler_icon('x', 'me-2') ?>Reset Filter
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

            <!-- Pagination -->
            <?php $this->load->view('app/layouts/_pagination', compact(
                'total', 'page', 'per_page', 'offset', 'total_pages', 'base_url'
            )); ?>

        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal modal-blur fade" id="modalImportPricelist" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <?= form_open_multipart('master/import_excel_pricelist') ?>
            <div class="modal-header">
                <h5 class="modal-title"><?= tabler_icon('file-spreadsheet', 'me-2') ?> Import Pricelist Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <div class="d-flex">
                        <div><?= tabler_icon('info-circle', 'me-2') ?></div>
                        <div>
                            <h4 class="alert-title">Format Wajib!</h4>
                            <div class="text-muted small">
                                Pastikan data sesuai template. Kolom <strong>Service_Type_ID</strong>
                                harus diisi ID angka, bukan nama service.
                            </div>
                            <a href="<?= site_url('master/download_template_pricelist') ?>"
                               class="btn btn-sm btn-info mt-3">
                                <?= tabler_icon('file-download', 'me-1') ?> Download Template
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mb-3 mt-4">
                    <div class="form-label required">Pilih File Excel (.xls, .xlsx, .csv)</div>
                    <input type="file" class="form-control" name="file_excel"
                           accept=".xls,.xlsx,.csv" required>
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Toggle Status pakai SweetAlert
    document.querySelectorAll('.btn-toggle-status').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const href   = this.dataset.href;
            const route  = this.dataset.route;
            const active = this.dataset.status === '1';

            Swal.fire({
                title: (active ? 'Nonaktifkan' : 'Aktifkan') + ' pricelist ini?',
                text: route,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    });

    // Hapus pakai SweetAlert
    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const href  = this.dataset.href;
            const route = this.dataset.route;

            Swal.fire({
                title: 'Hapus pricelist ini?',
                html: 'Rute <strong>' + route + '</strong> akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                confirmButtonColor: '#d63939',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    });

});
</script>
