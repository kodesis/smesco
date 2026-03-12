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
                        <form action="<?= base_url('customer') ?>" method="post" autocomplete="off" novalidate>
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
                    <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#download-excel">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                            <path d="M10 12l4 5" />
                            <path d="M10 17l4 -5" />
                        </svg>
                        Download excel
                    </a>
                    <a href="#" class="btn btn-green d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#download-excel" aria-label="Create new report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                            <path d="M10 12l4 5" />
                            <path d="M10 17l4 -5" />
                        </svg>
                    </a>
                </div>
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
                    <!-- <div class="card-header">
                        <h6 class="card-title"><strong>50 transaksi</strong> terakhir</h6>
                    </div> -->
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th class="w-1">No. Resi</th>
                                    <th>Tanggal</th>
                                    <th class="w-1">Koli</th>
                                    <th class="w-1">Chargeable</th>
                                    <th>Nominal</th>
                                    <th>Topup</th>
                                    <th>Sisa saldo</th>
                                    <!-- <th class="w-1"></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * 10) + 1) : '1';

                                foreach ($deposits as $c) :
                                    if ($c->kode_topup) { ?>
                                        <tr>
                                            <td class="text-end"><?= $no++; ?>.</td>
                                            <td class="text-center" colspan="5"><?= $c->kode_topup ?> - <?= format_indo($c->topup_date) ?></td>
                                            <!-- <td class="text-end">-</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end">-</td> -->
                                            <td class="text-end"><?= number_format($c->nominal_topup) ?></td>
                                            <td class="text-end"><?= number_format($c->sisa_saldo) ?></td>
                                        </tr>
                                    <?php
                                    } else { ?>
                                        <tr>
                                            <td class="text-end"><?= $no++; ?>.</td>
                                            <td><?= $c->no_resi ?></td>
                                            <td><?= format_indo($c->tanggal_resi) ?></td>
                                            <td class="text-end"><?= number_format($c->qty) ?></td>
                                            <td class="text-end"><?= number_format($c->chargeable) ?></td>
                                            <td class="text-end"><?= number_format($c->usage_saldo) ?></td>
                                            <td class="text-end">-</td>
                                            <td class="text-end"><?= number_format($c->sisa_saldo) ?></td>
                                        </tr>
                                    <?php
                                    } ?>
                                <?php
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="download-excel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unduh excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= base_url('dashboard/downloadDepositExcel') ?>" autocomplete="off" novalidate>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">Dari</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">Sampai</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-footer text-end">
                        <button type="submit" class="btn btn-primary ms-auto">Unduh</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '.editData', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');

            $('#editData .modal-title').text('Edit data ' + nama);

            $.ajax({
                url: "<?= site_url('customer/formEdit') ?>",
                type: "POST",
                data: {
                    id: id,
                },
                success: function(data) {
                    $('#editData .modal-body').html(data);
                    $('#editData').modal('show');
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