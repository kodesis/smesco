<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $title ?>s
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 d-none d-sm-inline-block">
                        <form action="<?= base_url('partner') ?>" method="post" autocomplete="off" novalidate>
                            <?php $this->load->view('pages/layouts/_search') ?>
                        </form>
                    </div>
                    <!-- Tombol Search untuk mobile -->
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                        <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </a>

                    <?php
                    if ($keyword) {
                    ?>
                        <a href="<?= base_url('dashboard/reset/partner') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Reset</a>
                        <a href="<?= base_url('dashboard/reset/partner') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                        </a>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <?php
                        if ($keyword) {
                        ?>
                            <div class="card-header">
                                <h4 class="card-title">Search results for the keyword <strong>'<?= (isset($keyword)) ? $keyword : ''; ?>'</strong></h4>
                            </div>
                        <?php
                        } ?>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th class="w-1">#</th>
                                        <th class="">Kode gerai</th>
                                        <th class="">Nama</th>
                                        <th class="w-1">Jenis pengajuan</th>
                                        <th>No. Telp</th>
                                        <th>Alamat</th>
                                        <th>Saldo deposit</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($registrations) {
                                        $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * 10) + 1) : '1';

                                        foreach ($registrations as $c) :
                                            $saldo = $this->M_Partner->getSaldoAkhirPartner($c->Id);
                                            $saldo_akhir = isset($saldo['saldo_akhir']) ? $saldo['saldo_akhir'] : 0; ?>
                                            <tr>
                                                <td class="text-end"><?= $no++; ?>.</td>
                                                <td><?= ($c->kode_gerai) ? $c->kode_gerai : '-' ?></td>
                                                <td><?= $c->nama_pendaftar ?></td>
                                                <td><?= ucfirst($c->jenis_pengajuan) ?></td>
                                                <td><?= ($c->no_handphone) ?></td>
                                                <td><?= $c->alamat_lengkap ?></td>
                                                <td class="text-end <?= ($saldo_akhir < 500000) ? 'bg-yellow text-white' : 'bg-azure text-white' ?>">
                                                    Rp <?= number_format($saldo_akhir) ?>
                                                </td>

                                                <td>
                                                    <?php if ($c->hasil_review == 'diterima') : ?>
                                                        <?php if ($c->has_account == '0') : ?>
                                                            <button type="button" class="btn btn-ghost-primary btn-sm btn-process" data-href="<?= base_url('partner/createUser/' . $c->Id) ?>">Create account</button>
                                                        <?php else : ?>
                                                            <button type="button" class="btn btn-ghost-success btn-sm top-up-saldo" data-id="<?= $c->Id ?>" data-nama="<?= $c->nama_pendaftar ?>" data-gerai="<?= $c->kode_gerai ?>">Top up saldo</button>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <button type="button" class="btn btn-ghost-primary btn-sm review-pengajuan" data-id="<?= $c->Id ?>" data-nama="<?= $c->nama_pendaftar ?>">Review</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan=" 7">Tidak ada data yang ditampilkan
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php $this->load->view('pages/layouts/_pagination') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pencarian -->
    <div class="modal modal-blur fade" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('agent') ?>" method="post" autocomplete="off" novalidate>
                        <div class="input-group">
                            <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="review-pengajuan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="addNewAgent" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add new agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?= base_url('agent/store') ?>" autocomplete="off" novalidate>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="mb-3">
                                    <label for="nama_pendaftar" class="form-label">Nama agent</label>
                                    <input type="text" name="nama_pendaftar" id="nama_pendaftar" class="form-control" placeholder="Masukkan nama agent...">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="origin" class="form-label">Zona</label>
                                    <input type="text" name="origin" id="origin" class="form-control" placeholder="Masukkan zona... Ex: PKU, KNO">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="telepon_pendaftar" class="form-label">No. Whatsapp</label>
                                    <input type="text" name="telepon_pendaftar" id="telepon_pendaftar" class="form-control" placeholder="Masukkan ...">
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="mb-3">
                                    <label for="alamat_pendaftar" class="form-label">Alamat</label>
                                    <input type="text" name="alamat_pendaftar" id="alamat_pendaftar" class="form-control" placeholder="Masukkan ...">
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary btn-submit w-100">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="<?= base_url(); ?>assets/dashboard/js/jquery.mask.js"></script>
    <script>
        $(document).ready(function() {



            $(document).on('click', '.review-pengajuan', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');

                $('#review-pengajuan .modal-title').text('Review pengajuan ' + nama);

                $.ajax({
                    url: "<?= site_url('partner/review') ?>",
                    type: "POST",
                    data: {
                        id: id,
                        nama: nama,
                    },
                    success: function(data) {
                        $('#review-pengajuan .modal-body').html(data);
                        $('#review-pengajuan').modal('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            title: "Error!! ",
                            text: 'Gagal mengambil data',
                            type: "error",
                            icon: "error",
                        });
                    }
                });
            });

            $(document).on('click', '.top-up-saldo', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var gerai = $(this).data('gerai');

                $('#review-pengajuan .modal-title').text('Top up saldo ' + nama + ' (' + gerai + ')');

                $.ajax({
                    url: "<?= site_url('partner/topupSaldo') ?>",
                    type: "POST",
                    data: {
                        id: id,
                        nama: nama,
                    },
                    success: function(data) {
                        $('#review-pengajuan .modal-body').html(data);
                        $('#review-pengajuan').modal('show');
                        // Menggunakan mask untuk input uang
                        $('.angka').mask('000.000.000.000.000', {
                            reverse: true
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            title: "Error!! ",
                            text: 'Gagal mengambil data',
                            type: "error",
                            icon: "error",
                        });
                    }
                });
            });
        });
    </script>