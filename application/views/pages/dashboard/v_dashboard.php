<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Dashboard
                </h2>
            </div>
        </div>
    </div>
</div>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <?= $this->session->flashdata('message_warning'); ?>
        <div class="row row-deck row-cards">
            <div class="col-12">
                <?php
                if ($this->session->userdata('role_id') == '3') {
                ?>
                    <div class="row row-cards">
                        <div class="col-md-3 col-6">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="<?= ($saldo < 500000) ? 'bg-danger' : 'bg-lime' ?> text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-report-money">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                                    <path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                                    <path d="M12 17v1m0 -8v1" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <a href="<?= base_url('dashboard/showDepositSummary/' . $this->session->userdata('partner_id')) ?>" style="text-decoration: none;">
                                                <div class="font-weight-medium">
                                                    Deposit
                                                </div>
                                                <div class="text-muted">
                                                    <?= ($saldo) ? number_format($saldo) : 0 ?>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mt-2 w-100">
                                <!-- <button type="button" class="btn btn-primary dropdown-toggle w-100" data-bs-toggle="dropdown">Buat Booking</button> -->

                                <a class="btn btn-primary w-100" href="<?= base_url('booking/create_booking') ?>">
                                    Buat booking
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9 col-12">
                            <div class="row row-cards">
                                <div class="col-12">
                                    <div class="card card-sm">
                                        <div class="card-stamp">
                                            <div class="card-stamp-icon bg-yellow">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calculator">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                    <path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" />
                                                    <path d="M8 14l0 .01" />
                                                    <path d="M12 14l0 .01" />
                                                    <path d="M16 14l0 .01" />
                                                    <path d="M8 17l0 .01" />
                                                    <path d="M12 17l0 .01" />
                                                    <path d="M16 17l0 .01" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <h3 class="card-title">Cek Ongkir</h3>
                                            <!-- <div class="row">

                                                <div class="col-md-6 col-12">
                                                    <div class="mb-3">
                                                        <label for="jenis_pengiriman" class="form-label">Jenis pengiriman</label>
                                                        <select name="jenis_pengiriman" id="jenis_pengiriman" class="form-control">
                                                            <option value="D">Domestik</option>
                                                            <option value="IR">Internasional Reguler</option>
                                                            <option value="IE">Internasional Economy</option>
                                                            <option value="IP">Internasional Premium</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> -->

                                            <div class="row">

                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Berat timbang</label>
                                                        <input type="text" name="berat_timbang" id="berat_timbang" class="form-control" placeholder="Masukkan berat timbang">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Total Koli</label>
                                                        <input type="text" name="total_qty" id="total_qty" class="form-control" placeholder="Masukkan jumlah barang" value="1" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Total Volume</label>
                                                        <input type="text" name="total_volume" id="total_volume" class="form-control" value="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Chargeable</label>
                                                        <input type="text" name="chargeable" id="chargeable" class="form-control" value="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <input type="hidden" name="harga_jual" id="harga_jual" class="form-control" value="0" readonly>
                                                        <label for="origin" class="form-label">Origin</label>
                                                        <input type="text" name="origin" id="origin" class="form-control" placeholder="Masukkan origin" oninput="this.value = this.value.toUpperCase()">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label for="destination" class="form-label">Destination</label>
                                                        <input type="text" name="destination" id="destination" class="form-control" placeholder="Masukkan destination" oninput="this.value = this.value.toUpperCase()">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label for="harga" class="form-label">Harga Satuan</label>
                                                        <input type="text" name="harga" id="harga" class="form-control" value="0" readonly>
                                                        <div class="invalid-feedback">Harga tidak tersedia</div>
                                                        <div class="valid-feedback">Harga tersedia</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nominal</label>
                                                        <input type="text" name="nominal" id="nominal" class="form-control" value="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <hr> -->
                                            <label for="" class="form-label">Input dimensi</label>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Panjang</th>
                                                        <th>Lebar</th>
                                                        <th>Tinggi</th>
                                                        <th>Koli</th>
                                                        <th>Volume</th>
                                                        <th>delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table-body">
                                                    <tr class="baris ">
                                                        <td class="nomor-urut"></td>
                                                        <td>
                                                            <input type="text" name="panjang[]" id="panjang[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lebar[]" id="lebar[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="tinggi[]" id="tinggi[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="jumlah[]" id="jumlah[]" class="form-control" value="1">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="volume[]" id="volume[]" class="form-control" value="0" readonly>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm hapusRow">Hapus</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="7" class="text-end">
                                                            <button type="button" class="btn btn-secondary btn-sm" id="addRow">Add new row</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                } else { ?>
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <a style="text-decoration: none;" href="#" class="" data-bs-toggle="modal" data-bs-target="#download-manifest-pickup">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trolley">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M11 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                        <path d="M6 16l3 2" />
                                                        <path d="M12 17l8 -12" />
                                                        <path d="M17 10l2 1" />
                                                        <path d="M9.592 4.695l3.306 2.104a1.3 1.3 0 0 1 .396 1.8l-3.094 4.811a1.3 1.3 0 0 1 -1.792 .394l-3.306 -2.104a1.3 1.3 0 0 1 -.396 -1.8l3.094 -4.81a1.3 1.3 0 0 1 1.792 -.394z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    <?= $booking['status_1'] ?> Resi
                                                </div>
                                                <div class="text-muted">
                                                    Penjemputan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck-delivery">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                    <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                                    <path d="M3 9l4 0" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <?= $booking['status_2'] ?> Resi
                                            </div>
                                            <div class="text-muted">
                                                Perjalanan ke gudang
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-twitter text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-warehouse">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 21v-13l9 -4l9 4v13" />
                                                    <path d="M13 13h4v8h-10v-6h6" />
                                                    <path d="M13 21v-9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v3" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <?= $booking['status_3'] ?> Resi
                                            </div>
                                            <div class="text-muted">
                                                Tiba di gudang
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-facebook text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <?= $booking['status_4'] ?> Resi
                                            </div>
                                            <div class="text-muted">
                                                Tiba di tujuan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row row-cards">
                                <div class="col-12">
                                    <div class="card card-sm">
                                        <div class="card-stamp">
                                            <div class="card-stamp-icon bg-yellow">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calculator">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                    <path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" />
                                                    <path d="M8 14l0 .01" />
                                                    <path d="M12 14l0 .01" />
                                                    <path d="M16 14l0 .01" />
                                                    <path d="M8 17l0 .01" />
                                                    <path d="M12 17l0 .01" />
                                                    <path d="M16 17l0 .01" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <h3 class="card-title">Cek Ongkir</h3>
                                            <div class="row">

                                                <div class="col-md-6 col-12">
                                                    <div class="mb-3">
                                                        <label for="jenis_pengiriman" class="form-label">Jenis pengiriman</label>
                                                        <select name="jenis_pengiriman" id="jenis_pengiriman" class="form-control">
                                                            <option value="D">Domestik</option>
                                                            <option value="I">Internasional</option>
                                                            <!-- <option value="IE">Internasional Economy</option>
                                                            <option value="IP">Internasional Premium</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Berat timbang</label>
                                                        <input type="text" name="berat_timbang" id="berat_timbang" class="form-control" placeholder="Masukkan berat timbang">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Total Koli</label>
                                                        <input type="text" name="total_qty" id="total_qty" class="form-control" placeholder="Masukkan jumlah barang" value="1" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Total Volume</label>
                                                        <input type="text" name="total_volume" id="total_volume" class="form-control" value="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Chargeable</label>
                                                        <input type="text" name="chargeable" id="chargeable" class="form-control" value="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <input type="hidden" name="harga_jual" id="harga_jual" class="form-control" value="0" readonly>
                                                        <label for="origin" class="form-label">Origin</label>
                                                        <input type="text" name="origin" id="origin" class="form-control" placeholder="Masukkan origin" oninput="this.value = this.value.toUpperCase()">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label for="destination" class="form-label">Destination</label>
                                                        <input type="text" name="destination" id="destination" class="form-control" placeholder="Masukkan destination" oninput="this.value = this.value.toUpperCase()">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label for="harga" class="form-label">Per Kg</label>
                                                        <input type="text" name="harga" id="harga" class="form-control" value="0" readonly>
                                                        <div class="invalid-feedback">Harga tidak tersedia</div>
                                                        <div class="valid-feedback">Harga tersedia</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nominal</label>
                                                        <input type="text" name="nominal" id="nominal" class="form-control" value="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <hr> -->
                                            <label for="" class="form-label">Input dimensi</label>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Panjang</th>
                                                        <th>Lebar</th>
                                                        <th>Tinggi</th>
                                                        <th>Koli</th>
                                                        <th>Volume</th>
                                                        <th>delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table-body">
                                                    <tr class="baris ">
                                                        <td class="nomor-urut"></td>
                                                        <td>
                                                            <input type="text" name="panjang[]" id="panjang[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="lebar[]" id="lebar[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="tinggi[]" id="tinggi[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="jumlah[]" id="jumlah[]" class="form-control" value="1">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="volume[]" id="volume[]" class="form-control" value="0" readonly>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm hapusRow">Hapus</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="7" class="text-end">
                                                            <button type="button" class="btn btn-secondary btn-sm" id="addRow">Add new row</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal modal-blur fade" id="download-manifest-pickup" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Unduh excel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="<?= base_url('booking/downloadManifestPickup') ?>" autocomplete="off" novalidate>
                                        <div class="row">
                                            <?php
                                            if ($this->session->userdata('role_id') == '2') {
                                            ?>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-3">
                                                        <label for="date_from" class="form-label">Driver</label>
                                                        <select name="driver_id" id="driver_id" class="form-control select2">
                                                            <option value="">:: Pilih driver</option>
                                                            <?php
                                                            foreach ($drivers as $p) :
                                                            ?>
                                                                <option value="<?= $p->Id ?>"><?= $p->name ?></option>
                                                            <?php
                                                            endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-3">
                                                        <label for="tanggal_pickup" class="form-label">Tanggal pickup</label>
                                                        <input type="date" name="tanggal_pickup" id="tanggal_pickup" class="form-control" required>
                                                    </div>
                                                </div>
                                            <?php
                                            } ?>
                                        </div>
                                        <div class="form-footer text-end">
                                            <button type="submit" class="btn btn-primary ms-auto">Unduh</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                } ?>

            </div>
        </div>
        <div class="row row-deck row-cards mt-2">
        </div>
    </div>
</div>