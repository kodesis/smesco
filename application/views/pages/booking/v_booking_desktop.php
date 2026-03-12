<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <?php
                if ($keyword) {
                ?>
                    <h4 class="card-title">Search results for the keyword <strong>'<?= (isset($keyword)) ? $keyword : ''; ?>'</strong></h4>

                <?php
                } ?>
                <div class="card-actions">
                    <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#download-excel">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                            <path d="M10 12l4 5" />
                            <path d="M10 17l4 -5" />
                        </svg>
                        Rekap booking
                    </a>
                    <a href="#" class="btn btn-green d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#download-excel" aria-label="Rekap booking" title="Rekap booking">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                            <path d="M10 12l4 5" />
                            <path d="M10 17l4 -5" />
                        </svg>
                    </a>
                    <?php
                    if ($this->session->userdata('role_id') == '3') {
                    ?>
                        <a href="<?= base_url('booking/create_booking') ?>" class="btn btn-primary d-none d-sm-inline-block" aria-label="Create new booking" title="Create new booking">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Create new</a>
                        <a href="<?= base_url('booking/create_booking') ?>" class="btn btn-primary d-sm-none btn-icon" aria-label="Create new booking" title="Create new booking">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                        </a>
                    <?php
                    } ?>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th class="w-1">#</th>
                            <th class="">No. Resi</th>
                            <?php
                            if ($this->session->userdata('role_id') == '2' or $this->session->userdata('username') == 'krx0005') {
                            ?>
                                <th class="w-1">AWB</th>
                                <th class="">Customer</th>
                            <?php
                            } ?>
                            <th class="w-1">Origin</th>
                            <th class="w-1">Dest</th>
                            <th class="w-1">Qty</th>
                            <th class="w-1">Chwt</th>
                            <th class="w-1">Total</th>
                            <th class="w-1">Payment</th>
                            <th class="w-1">Pickup</th>
                            <th>Status</th>
                            <?php
                            if ($this->session->userdata('role_id') == '2' or $this->session->userdata('username') == 'krx0005') {
                            ?>
                                <th class="w-1">Warehouse</th>
                                <th class="w-1">Arr.</th>
                            <?php
                            } ?>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($bookings) {
                            $no = 1;

                            foreach ($bookings as $b) :

                                $checked = ($b->status_bayar == '1') ? 'checked' : '';
                                $checked_warehouse = ($b->confirm_arr_warehouse == '1') ? 'checked' : '';
                                $checked_destination = ($b->confirm_arrival == '1') ? 'checked' : '';
                                $checked_pickup = ($b->confirm_pickup == '1') ? 'checked' : '';
                                $disabled_check_payment = ($this->session->userdata('partner_id') == $b->partner_id) ? '' : 'disabled'; ?>

                                <tr>
                                    <td class="text-end"><?= $no++; ?>.</td>
                                    <td><?= $b->no_resi ?></td>
                                    <?php
                                    if ($this->session->userdata('role_id') == '2' or $this->session->userdata('username') == 'krx0005') {
                                    ?>
                                        <td><?= ($b->awb) ? $b->awb : "-" ?></td>
                                        <td><?= $b->nama_pendaftar ?></td>
                                    <?php
                                    } ?>
                                    <td><?= $b->origin ?></td>
                                    <td><?= $b->destination ?></td>
                                    <!-- <td><?= $b->commodity ?></td> -->
                                    <td class="text-end"><?= number_format($b->qty) ?></td>
                                    <td class="text-end"><?= number_format($b->chargeable) ?></td>
                                    <td class="text-end"><?= number_format($b->nominal) ?></td>
                                    <td>
                                        <label class="form-check form-switch">
                                            <input class="form-check-input check_payment" type="checkbox" id='checkbox_<?= $b->no_resi ?>' <?= $checked . ' ' . $disabled_check_payment ?> />
                                            <span class="form-check-label"><?= ($b->status_bayar == '1') ? 'Paid' : 'Unpaid' ?></span>
                                        </label>
                                    </td>
                                    <td>
                                        <label class="form-check form-switch">
                                            <input class="form-check-input check_pickup" type="checkbox" id='pickup_<?= $b->no_resi ?>' <?= $checked_pickup ?> />
                                            <span class="form-check-label"><?= ($b->confirm_pickup == '1') ? 'Sudah' : 'Belum' ?></span>
                                        </label>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                        $status_badges = [
                                            '4' => ['text' => 'Sudah tiba di tujuan', 'color' => 'bg-lime'],
                                            '3' => ['text' => 'Menuju tujuan pengiriman', 'color' => 'bg-orange'],
                                            '2' => ['text' => 'Pengantaran ke gudang', 'color' => 'bg-cyan'],
                                            '0' => ['text' => 'Dalam proses', 'color' => 'bg-yellow'],
                                        ];

                                        if (isset($status_badges[$b->status_tracking])) {
                                            $status = $status_badges[$b->status_tracking];
                                        ?>
                                            <span class="badge <?= $status['color']; ?> w-100"><?= $status['text']; ?></span>
                                        <?php
                                        }
                                        ?>
                                    </td>

                                    <?php
                                    if ($this->session->userdata('role_id') == '2' or $this->session->userdata('username') == 'krx0005') {
                                    ?>
                                        <td>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input check_warehouse" data-agent_id="<?= $b->agent_id ?>" type="checkbox" id='arrWarehouse_<?= $b->no_resi ?>' <?= $checked_warehouse ?> />
                                                <span class="form-check-label"><?= ($b->confirm_arr_warehouse == '1') ? 'Sudah' : 'Belum' ?></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input check_destination" data-arr_warehouse="<?= $b->confirm_arr_warehouse ?>" type="checkbox" id='arrDestination_<?= $b->no_resi ?>' <?= $checked_destination ?> />
                                                <span class="form-check-label"><?= ($b->confirm_arrival == '1') ? 'Sudah' : 'Belum' ?></span>
                                            </label>
                                        </td>
                                    <?php
                                    }
                                    ?>
                                    <td class="">
                                        <a href="<?= base_url('booking/print_resi/' . $b->no_resi) ?>" target="_blank" class="btn btn-primary btn-sm ms-auto mb-1">Print</a>
                                        <?php
                                        if ($this->session->userdata('role_id') == '2' or $this->session->userdata('username') == 'krx0005') {
                                        ?>
                                            <a href="<?= base_url('booking/detailResi/') . $b->no_resi ?>" class="btn btn-primary btn-sm ms-auto mb-1">Detail</a>
                                            <button href="#" class="btn btn-danger btn-sm ms-auto mb-1">Void</button>

                                            <?php
                                            if ($b->url_tracking) {
                                            ?>
                                                <a href="<?= $b->url_tracking ?>" class="btn btn-info btn-sm ms-auto mb-1" target="_blank">Track Barang</a>
                                            <?php
                                            }
                                        } else {
                                            if ($b->status_bayar == '0') {
                                            ?>
                                                <a href="<?= base_url('booking/editResi/' . $b->no_resi) ?>" class="btn btn-warning btn-sm ms-auto mb-1">Edit</a>
                                                <button href="#" class="btn btn-danger btn-sm ms-auto mb-1">Void</button>
                                        <?php
                                            }
                                        } ?>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        } else {
                            ?>
                            <tr>
                                <td colspan="<?= ($this->session->userdata('role_id') == '3') ? '11' : '13' ?>">Tidak ada data yang ditampilkan.</td>
                            </tr>
                        <?php
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php $this->load->view('pages/layouts/_pagination') ?>
        </div>
    </div>
</div>