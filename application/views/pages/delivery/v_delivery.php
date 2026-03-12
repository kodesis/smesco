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
                    <a href="<?= base_url('booking/create_booking') ?>" class="btn btn-primary d-none d-sm-inline-block" aria-label="Create new report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add new</a>
                    <a href="<?= base_url('booking/create_booking') ?>" class="btn btn-primary d-sm-none btn-icon" aria-label="Create new report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
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
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th class="">AWB</th>
                                    <th>Customer</th>
                                    <th class="w-1">Origin</th>
                                    <th class="w-1">Destination</th>
                                    <th>Commodity</th>
                                    <th class=""></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($deliveries) {
                                    $no = 1;

                                    foreach ($deliveries as $b) : ?>
                                        <tr>
                                            <td class="text-end"><?= $no++; ?>.</td>
                                            <td><?= $b->awb ?></td>
                                            <td><?= $b->nama_customer ?></td>
                                            <td><?= $b->origin ?></td>
                                            <td><?= $b->destination ?></td>
                                            <td><?= $b->commodity ?></td>
                                            <td class="">
                                                <a href="<?= base_url('booking/detailBooking/') . $b->awb ?>" class="btn btn-primary btn-sm ms-auto">Detail</a>
                                                <button href="#" class="btn btn-danger btn-sm ms-auto">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="7">Tidak ada data yang ditampilkan.</td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>