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
                        <form action="<?= base_url('booking/list_detail') ?>" method="post" autocomplete="off" novalidate>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                </span>
                                <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search in website">
                            </div>
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
                    <a href="<?= base_url('dashboard/reset/detail-awb') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                        Reset</a>
                    <a href="<?= base_url('dashboard/reset/detail-awb') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                    </a>
                    <a href="<?= base_url('booking/create_booking') ?>" class="btn btn-primary d-none d-sm-inline-block" aria-label="Redirect to new booking form" title="Add new booking" data-bs-toggle="tooltip" data-bs-placement="top">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add new</a>
                    <a href="<?= base_url('booking/create_booking') ?>" class="btn btn-primary d-sm-none btn-icon" aria-label="Redirect to new booking form" title="Add new booking" data-bs-toggle="tooltip" data-bs-placement="top">
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
                    <?php
                    if ($keyword) {
                    ?>
                        <div class="card-header">
                            <h4 class="card-title">Search results for the keyword <strong>'<?= (isset($keyword)) ? $keyword : ''; ?>'</strong></h4>
                        </div>
                    <?php
                    } ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th class="">Resi</th>
                                    <th class="">Customer</th>
                                    <th class="w-1">Qty</th>
                                    <th class="w-1">Chargeable</th>
                                    <th>Dest.</th>
                                    <th>City</th>
                                    <th>Nominal</th>
                                    <th>Payment status</th>
                                    <th></th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($awbs) {

                                    $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * 10) + 1) : '1';

                                    foreach ($awbs as $b) :

                                        $checked = ($b->status_bayar == '1') ? 'checked' : ''; ?>
                                        <tr>
                                            <td class="text-end"><?= $no++; ?>.</td>
                                            <td><?= $b->slug_item ?></td>
                                            <td><?= $b->nama_customer ?></td>
                                            <td><?= $b->qty ?></td>
                                            <td><?= $b->chargeable ?></td>
                                            <td><?= $b->destination ?></td>
                                            <td><?= $b->kota_tujuan ?></td>
                                            <td class="text-end"><?= number_format($b->total_amount) ?></td>
                                            <td>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id='checkbox_<?= $b->slug_item ?>' <?= $checked ?> />
                                                    <span class="form-check-label"><?= ($b->status_bayar == '1') ? 'Paid' : 'Unpaid' ?></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($b->status_tracking == '4') {
                                                ?>
                                                    <span class="badge bg-azure w-100">Sudah tiba di tujuan</span>

                                                <?php
                                                } else {
                                                ?>

                                                    <span class="badge bg-orange">In delivery</span>
                                                    <!-- <button type="button" class="btn btn-primary btn-sm set-pickup <?= ($b->status_tracking == '0') ? '' : 'disabled' ?>" data-id="<?= $b->slug_item ?>">Set pickup</button> -->
                                                    <!-- <a class="btn btn-secondary btn-sm <?= ($b->status_tracking == '1') ? '' : 'disabled' ?> btn-process" href="<?= base_url('booking/confirmPickup/') . $b->slug_item ?>">Confirm pickup</a> -->
                                                    <!-- <button type="button" class="btn btn-warning btn-sm arr-warehouse <?= ($b->status_tracking == '2') ? '' : 'disabled' ?>" data-id="<?= $b->slug_item ?>">Tiba di gudang</button> -->
                                                    <a class="btn btn-success btn-sm <?= ($b->status_tracking == '3') ? '' : 'disabled' ?> btn-process" href="<?= base_url('booking/arriveDestination/') . $b->slug_item ?>">Tiba di tujuan</a>
                                                <?php
                                                } ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('booking/print_resi/' . $b->slug_item) ?>" target="_blank" class="btn btn-primary btn-sm">Print</a>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6">Tidak ada data yang ditampilkan.</td>
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
    </div>
</div>

<!-- Modal untuk menampilkan detail -->

<div class="modal modal-blur fade" id="setPickup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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

<!-- Modal Pencarian -->
<div class="modal modal-blur fade" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('booking/list_detail') ?>" method="post" autocomplete="off" novalidate>
                    <div class="input-group">
                        <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click', '.set-pickup', function() {
            var id = $(this).data('id');

            $('#setPickup .modal-title').text('Set pickup barang ' + id);

            $.ajax({
                url: "<?= site_url('booking/formPickup') ?>",
                type: "POST",
                data: {
                    id: id,
                },
                success: function(data) {
                    $('#setPickup .modal-body').html(data);
                    $('#setPickup').modal('show');
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
        $(document).on('click', '.arr-warehouse', function() {
            var id = $(this).data('id');

            $('#setPickup .modal-title').text('Confirm arrival at warehouse ' + id);

            $.ajax({
                url: "<?= site_url('booking/formConfirmWarehouse') ?>",
                type: "POST",
                data: {
                    id: id,
                },
                success: function(data) {
                    $('#setPickup .modal-body').html(data);
                    $('#setPickup').modal('show');
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
        $(document).on('change', '.form-check-input', function() {
            let checkbox = $(this);
            let id = checkbox.attr('id').replace('checkbox_', '');
            let status = checkbox.is(':checked') ? '1' : '0';

            // Fungsi untuk generate kode acak
            function generateRandomCode(length) {
                let code = '';
                let characters = '0123456789';
                let charactersLength = characters.length;
                for (let i = 0; i < length; i++) {
                    code += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return code;
            }

            // Generate kode acak dengan panjang 6 digit
            let randomCode = generateRandomCode(6);

            // Tampilkan modal konfirmasi dengan kode acak
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `Masukkan kode konfirmasi berikut untuk melanjutkan: <strong>${randomCode}</strong>`, // Tampilkan kode acak di modal
                input: 'text',
                inputPlaceholder: 'Masukkan kode konfirmasi',
                showCancelButton: true,
                confirmButtonText: 'Konfirmasi',
                cancelButtonText: 'Batal',
                preConfirm: (inputValue) => {
                    // Validasi input
                    if (!inputValue) {
                        Swal.showValidationMessage('Kode konfirmasi tidak boleh kosong!');
                    } else if (inputValue !== randomCode) {
                        Swal.showValidationMessage('Kode konfirmasi salah!');
                    } else {
                        return inputValue; // Kembalikan nilai input jika valid
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading Swal
                    Swal.fire({
                        title: "Loading...",
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                    });

                    // Kirimkan status baru ke server
                    $.ajax({
                        url: '<?= base_url("booking/updateStatusBayar/") ?>', // Ganti dengan URL endpoint Anda
                        method: 'POST',
                        data: {
                            id: id,
                            status: status
                        },
                        success: function(response) {
                            Swal.close(); // Tutup loading Swal
                            if (response.success) {
                                Swal.fire({
                                    title: "Success!!",
                                    text: response.message,
                                    icon: "success",
                                }).then(function() {
                                    // Reload halaman setelah sukses
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!!",
                                    text: 'Gagal memperbarui status!',
                                    icon: "error",
                                }).then(function() {
                                    // Reload halaman setelah gagal
                                    window.location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.close(); // Tutup loading Swal jika terjadi kesalahan
                            Swal.fire({
                                title: "Error!!",
                                text: 'Terjadi kesalahan saat menghubungi server.',
                                icon: "error",
                            }).then(function() {
                                // Reload halaman jika terjadi error
                                window.location.reload();
                            });
                        }
                    });
                } else {
                    // Jika modal dibatalkan, kembalikan status checkbox ke semula
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            });
        });


    });
</script>