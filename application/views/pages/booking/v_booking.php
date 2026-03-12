<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= ($this->session->userdata('role_id') == '3') ? 'My ' : '' ?><?= $title ?>s
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 d-none d-sm-inline-block">
                        <form action="<?= base_url('booking') ?>" method="post" autocomplete="off" novalidate>
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
                        <a href="<?= base_url('dashboard/reset/booking') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Reset</a>
                        <a href="<?= base_url('dashboard/reset/booking') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
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
</div>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <?= $this->session->flashdata('message_warning'); ?>

        <div id="bookingContainer">

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
                <form action="<?= base_url('booking') ?>" method="post" autocomplete="off" novalidate>
                    <div class="input-group">
                        <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
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
                <form method="POST" action="<?= base_url('booking/downloadRekapExcel') ?>" autocomplete="off" novalidate>
                    <div class="row">
                        <?php
                        if ($this->session->userdata('role_id') == '2') {
                        ?>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="date_from" class="form-label">Mitra</label>
                                    <select name="partner_id" id="partner_id" class="form-control select2">
                                        <option value="">:: Pilih mitra</option>
                                        <?php
                                        foreach ($partners as $p) :
                                        ?>
                                            <option value="<?= $p->Id ?>"><?= $p->nama_pendaftar . ' - ' . $p->kode_gerai ?></option>
                                        <?php
                                        endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                        } ?>
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
        $(document).on('change', '.check_payment', function() {
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
        $(document).on('change', '.check_pickup', function() {
            let checkbox = $(this);
            let id = checkbox.attr('id').replace('pickup_', '');
            let status = checkbox.is(':checked') ? '1' : '0';

            function generateRandomCode(length) {
                let code = '';
                let characters = '0123456789';
                let charactersLength = characters.length;
                for (let i = 0; i < length; i++) {
                    code += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return code;
            }

            let randomCode = generateRandomCode(6);

            Swal.fire({
                title: 'Konfirmasi penjemputan',
                html: `
                    <p>Masukkan kode konfirmasi berikut untuk melanjutkan:</p>
                    <p><strong>${randomCode}</strong></p>
                    <input id="kode_konfirmasi_input" class="form-control mb-2" placeholder="Masukkan kode konfirmasi">
                    <input id="foto_pickup_input" type="file" accept="image/*" class="form-control mb-2">
                    <div id="preview_container" style="display:none;">
                        <img id="preview_image" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;" />
                    </div>
                `,
                didOpen: () => {
                    // Pasang event listener untuk preview gambar
                    document.getElementById('foto_pickup_input').addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewContainer = document.getElementById('preview_container');
                                const previewImage = document.getElementById('preview_image');
                                previewImage.src = e.target.result;
                                previewContainer.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                },
                preConfirm: () => {
                    const inputKode = document.getElementById('kode_konfirmasi_input').value;
                    const fileInput = document.getElementById('foto_pickup_input').files[0];

                    if (!inputKode) {
                        Swal.showValidationMessage('Kode konfirmasi tidak boleh kosong!');
                    } else if (inputKode !== randomCode) {
                        Swal.showValidationMessage('Kode konfirmasi salah!');
                    } else {
                        return {
                            inputKode,
                            fileInput: fileInput || null
                        };
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Konfirmasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('id', id);
                    formData.append('status', status);

                    if (result.value.fileInput) {
                        formData.append('foto_pickup', result.value.fileInput);
                    }

                    Swal.fire({
                        title: "Loading...",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    $.ajax({
                        url: '<?= base_url("booking/confirmPickup/") ?>',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire("Sukses!", response.message, "success").then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", response.message || "Gagal memperbarui status!", "error").then(() => {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire("Error!", "Terjadi kesalahan saat menghubungi server.", "error").then(() => {
                                window.location.reload();
                            });
                        }
                    });
                } else {
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            });
        });

        $(document).on('change', '.check_warehouse', function() {
            let checkbox = $(this);
            let id = checkbox.attr('id').replace('arrWarehouse_', '');
            let status = checkbox.is(':checked') ? '1' : '0';
            let agent_id = checkbox.data('agent_id');

            // Validasi agent_id sebelum melanjutkan
            if (!agent_id || agent_id === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Agent tujuan belum dipilih!',
                });
                // Batalkan perubahan checkbox
                checkbox.prop('checked', !checkbox.is(':checked'));
                return; // Berhenti jika agent_id tidak valid
            }

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
                title: 'Konfirmasi tiba di gudang',
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
                        url: '<?= base_url("booking/confirmWarehouse/") ?>', // Ganti dengan URL endpoint Anda
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
        $(document).on('change', '.check_destination', function() {
            let checkbox = $(this);
            let id = checkbox.attr('id').replace('arrDestination_', '');
            let status = checkbox.is(':checked') ? '1' : '0';
            let arr_warehouse = checkbox.data('arr_warehouse');

            // Validasi arr_warehouse sebelum melanjutkan
            if (!arr_warehouse || arr_warehouse === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Resi belum dikonfirmasi tiba di gudang!',
                });
                // Batalkan perubahan checkbox
                checkbox.prop('checked', !checkbox.is(':checked'));
                return; // Berhenti jika arr_warehouse tidak valid
            }

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
                title: 'Konfirmasi tiba di alamat penerima',
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
                        url: '<?= base_url("booking/confirmArrDestination/") ?>', // Ganti dengan URL endpoint Anda
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

<script>
    $(document).ready(function() {
        function checkScreen() {
            var mode = window.innerWidth < 768 ? 'mobile' : 'desktop';

            // Cek apakah mode berubah atau pertama kali akses
            if (mode !== localStorage.getItem("viewMode") || !localStorage.getItem("viewMode")) {
                localStorage.setItem("viewMode", mode);
                loadOrders(mode);
            }
        }

        function loadOrders(mode) {
            $.ajax({
                url: "<?= base_url('booking/load_data') ?>",
                type: "GET",
                data: {
                    mode: mode
                },
                beforeSend: function() {
                    $("#bookingContainer").html(`
                        <div class="page page-center">
                            <div class="container container-slim py-4">
                                <div class="text-center">
                                    <div class="mb-3">
                                        
                                        <a href="." class="navbar-brand navbar-brand-autodark"><img src="<?= base_url() ?>assets/logo/logo-smesco.png" width="100" alt=""></a>
                                    </div>
                                    <div class="text-secondary mb-3">Preparing data</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-indeterminate"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                },
                success: function(response) {
                    $("#bookingContainer").html(response);
                },
                error: function() {
                    $("#bookingContainer").html("<p>Error loading data</p>");
                }
            });
        }

        // Jalankan saat pertama kali halaman dimuat
        var initialMode = window.innerWidth < 768 ? 'mobile' : 'desktop';
        localStorage.setItem("viewMode", initialMode);
        loadOrders(initialMode);

        $(window).resize(function() {
            checkScreen();
        });
    });
</script>