<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/select2/css/select2.min.css">

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $title ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('booking') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Create new report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg>
                        Back</a>
                    <a href="<?= base_url('booking') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Create new report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
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
                <form action="<?= base_url('booking/store_booking') ?>" method="post" class="card" id="formBooking">
                    <div class="card-body">
                        <h5></h5>
                        <?php
                        $role_id = $this->session->userdata('role_id');


                        if ($role_id == '3') {
                            $user_id = $this->session->userdata('user_id');
                            $id_customer = $this->session->userdata('customer_id');
                            // $id_customer = $this->M_Auth->getUserById($user_id)['customer_id']; 
                        ?>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="jenis_pengiriman" class="form-label">Jenis pengiriman</label>
                                        <select name="jenis_pengiriman" id="jenis_pengiriman" class="form-control">
                                            <option value="D">Domestik</option>
                                            <option value="I">Internasional</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Jenis barang</label>
                                        <input type="text" name="jenis_barang" id="jenis_barang" class="form-control" placeholder="Masukkan jenis barang" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nama pengirim</label>
                                        <input type="text" name="nama_pengirim" id="nama_pengirim" class="form-control" placeholder="Masukkan nama pengirim" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">No Whatsapp pengirim</label>
                                        <input type="text" name="telepon_pengirim" id="telepon_pengirim" class="form-control" placeholder="Masukkan no whatsapp pengirim">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Alamat pengirim</label>
                                        <textarea type="text" name="alamat_pengirim" id="alamat_pengirim" class="form-control" placeholder="Masukkan alamat pengirim" oninput="this.value = this.value.toUpperCase()"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nama penerima</label>
                                        <input type="text" name="nama_penerima" id="nama_penerima" class="form-control" placeholder="Masukkan nama penerima" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">No Whatsapp penerima</label>
                                        <input type="text" name="telepon_penerima" id="telepon_penerima" class="form-control" placeholder="Masukkan no whatsapp penerima">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Alamat penerima</label>
                                        <textarea type="text" name="alamat_penerima" id="alamat_penerima" class="form-control" placeholder="Masukkan alamat penerima" oninput="this.value = this.value.toUpperCase()"></textarea>
                                    </div>
                                </div>
                                <!-- <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label for="provinsi" class="form-label">Provinsi</label>
                                        <select name="provinsi" id="provinsi" class="form-select" required>
                                            <option value="">:: Pilih provinsi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label for="kota" class="form-label">Kota/Kabupaten</label>
                                        <select name="kota" id="kota" class="form-select" required>
                                            <option value="">:: Pilih kota/kabupaten</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <select name="kecamatan" id="kecamatan" class="form-select" required>
                                            <option value="">:: Pilih kecamatan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label for="kelurahan" class="form-label">Kelurahan</label>
                                        <select name="kelurahan" id="kelurahan" class="form-select" required>
                                            <option value="">:: Pilih kelurahan</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Berat timbang</label>
                                        <input type="text" name="berat_timbang" id="berat_timbang" class="form-control" placeholder="Masukkan berat timbang">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Total Koli</label>
                                        <input type="text" name="total_qty" id="total_qty" class="form-control angka" placeholder="Masukkan jumlah barang" value="1" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Total Volume</label>
                                        <input type="text" name="total_volume" id="total_volume" class="form-control angka" value="0" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Chargeable</label>
                                        <input type="text" name="chargeable" id="chargeable" class="form-control angka" value="0" readonly>
                                    </div>
                                </div>
                                <!-- <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Panjang</label>
                                        <input type="text" name="panjang" id="panjang" class="form-control angka" placeholder="Masukkan panjang">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Lebar</label>
                                        <input type="text" name="lebar" id="lebar" class="form-control angka" placeholder="Masukkan lebar">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tinggi</label>
                                        <input type="text" name="tinggi" id="tinggi" class="form-control angka" placeholder="Masukkan tinggi">
                                    </div>
                                </div> -->

                            </div>
                            <hr>
                            <div class="row">
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
                                        <input type="text" name="nominal" id="nominal" class="form-control angka" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
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
                        <?php
                        } ?>
                    </div>
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary ms-auto btn-submit">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url(); ?>assets/dashboard/js/jquery.mask.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/vendor/select2/js/select2.min.js"></script>
<!-- jQuery UI (required for autocomplete) -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- jQuery UI CSS (for autocomplete styling) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
    $(document).ready(function() {
        $("#nama_pengirim").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?php echo site_url('booking/autocompleteCustomer'); ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#nama_pengirim").val(ui.item.nama_customer);
                $("#telepon_pengirim").val(ui.item.telepon_customer);
                $("#alamat_pengirim").val(ui.item.alamat_customer);
            }
        });

        $("#nama_penerima").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?php echo site_url('booking/autocompleteCustomer'); ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#nama_penerima").val(ui.item.nama_customer);
                $("#telepon_penerima").val(ui.item.telepon_customer);
                $("#alamat_penerima").val(ui.item.alamat_customer);
            }
        });

        $('.select2').select2();

        // Menggunakan mask untuk input uang
        $('.angka').mask('000.000.000.000.000', {
            reverse: true
        });

        $('#berat_timbang').on('input', function() {
            tentukanChargeable();
            hitungNominal();


            var origin = $("#origin").val();
            var destination = $("#destination").val();
            // var jenis_pengiriman = $("#jenis_pengiriman").val();

            if (origin && destination) {
                fetchPrice(origin, destination);
            }
        });

        $('#jenis_pengiriman').on('change', function() {
            tentukanChargeable();

            var origin = $("#origin").val();
            var destination = $("#destination").val();
            // var jenis_pengiriman = $("#jenis_pengiriman").val();

            if (origin && destination) {
                fetchPrice(origin, destination);
            }
        });

        $(document).on('change click keyup input paste', 'input[name="panjang[]"], input[name="lebar[]"], input[name="tinggi[]"], input[name="jumlah[]"]', function(event) {
            $(this).val(function(index, value) {
                return value.replace(/(?!\.)\D/g, "")
                    .replace(/(?<=\..*)\./g, "")
                    .replace(/(?<=\.\d\d).*/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });

            var row = $(this).closest('.baris');

            hitungTotal(row);
            updateTotalRow();
        });

        function hitungTotal(row) {
            var panjang = row.find('input[name="panjang[]"]').val().replace(/\,/g, '');
            var lebar = row.find('input[name="lebar[]"]').val().replace(/\,/g, '');
            var tinggi = row.find('input[name="tinggi[]"]').val().replace(/\,/g, '');
            var jumlah = row.find('input[name="jumlah[]"]').val().replace(/\,/g, '');

            panjang = (panjang) || 0;
            lebar = (lebar) || 0;
            tinggi = (tinggi) || 0;
            jumlah = (jumlah) || 1;

            var volume = ((Number(panjang) * Number(lebar) * Number(tinggi)) / 5000) * Number(jumlah);

            row.find('input[name="volume[]"]').val(formatNumber(volume.toFixed(2)));
            updateTotalRow();
            tentukanChargeable()
        }

        function updateTotalRow() {
            var total_koli = 0;
            var total_volume = 0;

            $(".baris").each(function() {
                var jumlah = $(this).find('input[name="jumlah[]"]').val().replace(/\,/g, ''); // Ambil nilai jumlah dari setiap baris
                var volume = $(this).find('input[name="volume[]"]').val().replace(/\,/g, ''); // Ambil nilai chargeable dari setiap baris

                jumlah = parseFloat(jumlah); // Ubah string ke angka float
                volume = parseFloat(volume); // Ubah string ke angka float

                if (!isNaN(jumlah)) { // Pastikan total adalah angka
                    total_koli += jumlah; // Tambahkan nilai total ke total_koli
                }
                if (!isNaN(volume)) { // Pastikan total adalah angka
                    total_volume += volume; // Tambahkan nilai total ke total_koli
                }
            });
            $('#total_qty').val(formatNumber(total_koli.toFixed(0))); // Atur nilai input #nominal dengan total_pos_fix
            $('#total_volume').val(formatNumber(total_volume.toFixed(2))); // Atur nilai input #total_chargeable dengan total_chwt

            tentukanChargeable();
        }

        function tentukanChargeable() {
            var chargeable;

            var jenis_pengiriman = $("#jenis_pengiriman").val();

            var berat_timbang = parseFloat($('#berat_timbang').val().replace(/\,/g, '')) || 0;

            var volume = parseFloat($('#total_volume').val().replace(/\,/g, '')) || 0;

            if (berat_timbang >= volume) {
                chargeable = berat_timbang;
            } else {
                chargeable = volume;
            }

            if (jenis_pengiriman == 'D') {
                chargeable = (chargeable < 10) ? 10 : chargeable;
            } else {
                chargeable = chargeable;
            }

            $('#chargeable').val(formatNumber(chargeable));

            hitungNominal();
        }

        function formatNumber(number) {
            // Pisahkan bagian integer dan desimal
            let parts = number.toString().split(",");

            // Format bagian integer dengan pemisah ribuan
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            // Gabungkan bagian integer dan desimal dengan koma sebagai pemisah desimal
            return parts.join(",");
        }

        $("#origin").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?php echo site_url('booking/autocompleteOrigin'); ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#origin").val(ui.item.value);

                // Cek jika kedua inputan origin dan destination sudah diisi
                var origin = $("#origin").val();
                var destination = $('#kota').find(":selected").data("nama");
                // var jenis_pengiriman = $("#jenis_pengiriman").val();

                if (origin && destination) {
                    fetchPrice(origin, destination);
                }
            }
        });

        $("#destination").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?php echo site_url('booking/autocompleteDestination'); ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#destination").val(ui.item.value);

                // Cek jika kedua inputan origin dan destination sudah diisi
                var origin = $("#origin").val();
                var destination = $("#destination").val();
                // var jenis_pengiriman = $("#jenis_pengiriman").val();

                if (origin && destination) {
                    fetchPrice(origin, destination);
                }
            }
        });

        // Fungsi untuk melakukan AJAX request dan mendapatkan harga
        function fetchPrice(origin, destination) {
            var chargeable = $("#chargeable").val();
            var jenis_pengiriman = $("#jenis_pengiriman").val();
            $.ajax({
                type: 'POST',
                url: '<?= base_url('booking/getPrice'); ?>',
                data: {
                    origin: origin,
                    destination: destination,
                    jenis_pengiriman: jenis_pengiriman,
                    // chargeable: chargeable,
                },
                cache: false,
                success: function(response) {
                    console.log(response);
                    var data = JSON.parse(response);

                    var harga_up = parseFloat(data.harga_up) || 0; // Handle null or NaN
                    var harga_jual = parseFloat(data.harga_jual) || 0; // Handle null or NaN

                    if (!isNaN(harga_up) && harga_up > 0) {
                        $('#harga').removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $('#harga').removeClass('is-valid').addClass('is-invalid');
                    }

                    $('#harga').val(harga_up);
                    $('#harga_jual').val(harga_jual);
                    hitungNominal();
                },
                error: function() {
                    console.log('Price not found');
                    $('#harga').removeClass('is-valid').addClass('is-invalid');
                }
            });
        }

        function hitungNominal() {
            var chargeable = parseFloat($('#chargeable').val().replace(/\./g, '')) || 0;
            // var harga = parseFloat($('#harga').val().replace(/\./g, '')) || 0;
            var harga = parseFloat($('#harga').val()) || 0;

            var nominal;

            nominal = Math.round(chargeable * harga);
            $('#nominal').val(formatNumber(nominal));
        }

        $(document).on("click", ".btn-submit", function(e) {
            e.preventDefault();
            const form = $(this).parents("form");


            // Validasi semua input form
            let inputs = form.find('input, select, textarea');
            let valid = true;

            inputs.each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                }
            });

            // Jika ada input yang tidak valid, cegah submit dan tampilkan peringatan
            if (!valid) {
                Swal.fire({
                    icon: 'error',
                    text: 'Please fill out all required fields!'
                });
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, confirm!",
            }).then((result) => {
                if (result.isConfirmed) {

                    form.on("submit", function() {
                        $(".btn-submit").prop('disabled', true);
                        Swal.fire({
                            title: "Loading...",
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    });

                    form.submit();
                }
            });
        });

        var rowCount = 1; // Inisialisasi row
        function updateRowNumbers() {
            $('#table-body .baris').each(function(index) {
                $(this).find('.nomor-urut').text(index + 1 + '.');
            });
        }
        updateRowNumbers();

        $('#addRow').on('click', function() {
            // Periksa apakah ada input yang kosong di baris sebelumnya
            var previousRow = $('.baris').last();
            var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
            var isEmpty = false;

            inputs.each(function() {
                if ($(this).val().trim() === '') {
                    isEmpty = true;
                    return false; // Berhenti iterasi jika ditemukan input kosong
                }
            });

            // Jika ada input yang kosong, tampilkan pesan peringatan
            if (isEmpty) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
                });
                return; // Hentikan penambahan baris baru
            }

            // Salin baris terakhir
            var newRow = previousRow.clone();

            // Kosongkan nilai input di baris baru
            newRow.find('input').val('');
            newRow.find('input[name="panjang[]"]').val('0');
            newRow.find('input[name="lebar[]"]').val('0');
            newRow.find('input[name="tinggi[]"]').val('0');
            newRow.find('input[name="jumlah[]"]').val('1');
            newRow.find('input[name="volume[]"]').val('0');

            // Perbarui tag <h4> pada baris baru dengan nomor urut yang baru
            rowCount++;

            // Tambahkan baris baru setelah baris terakhir
            previousRow.after(newRow);

            updateRowNumbers();
        });

        // Tambahkan event listener untuk tombol hapus row
        $(document).on('click', '.hapusRow', function() {
            $(this).closest('.baris').remove();
            updateTotalRow(); // Perbarui total belanja setelah menghapus baris

            updateRowNumbers();
        });

        $.ajax({
            type: 'POST',
            url: '<?= base_url('registration/getProvinsi') ?>',
            cache: false,
            success: function(msg) {
                $('#provinsi').html(msg);
            }
        });

        $('#alamat_email').on('input', function() {
            var email = $(this).val();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('registration/checkEmail') ?>',
                data: {
                    email: email
                },
                cache: false,
                success: function(response) {
                    var isEmailAvailable = response == 0;
                    console.log(isEmailAvailable)

                    $('#alamat_email')
                        .toggleClass('is-invalid', !isEmailAvailable)
                        .toggleClass('is-valid', isEmailAvailable);
                },
                error: function() {
                    console.log('Error while checking email');
                }
            });
        });


        $('#provinsi').change(function() {
            var provinsi = $('#provinsi').val();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('registration/getKota') ?>',
                data: {
                    provinsi: provinsi,
                },
                cache: false,
                success: function(msg) {
                    $('#kota').html(msg);
                }
            })
        });

        $('#kota').change(function() {
            var kota = $('#kota').val();
            var nama = $('#kota').find(":selected").data("nama");


            var origin = $("#origin").val();


            if (origin && nama) {
                fetchPrice(origin, nama);
            }

            $.ajax({
                type: 'POST',
                url: '<?= base_url('registration/getKecamatan') ?>',
                data: {
                    kota: kota,
                },
                cache: false,
                success: function(msg) {
                    $('#kecamatan').html(msg);
                    console.log(nama)
                }
            })
        });

        $('#kecamatan').change(function() {
            var kecamatan = $('#kecamatan').val();

            $.ajax({
                type: 'POST',
                url: '<?= base_url('registration/getKelurahan') ?>',
                data: {
                    kecamatan: kecamatan,
                },
                cache: false,
                success: function(msg) {
                    $('#kelurahan').html(msg);
                }
            })
        });


        // $('#kota').on('change', function() {
        //     var kota = $('#kota').val();
        //     console.log(kota)
        // });
    });
</script>