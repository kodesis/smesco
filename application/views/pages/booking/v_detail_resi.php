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
                <form action="<?= base_url('booking/updateResi/' . $resi['no_resi']) ?>" method="post" class="card" id="formBooking">
                    <div class="card-body">
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
                                    <input type="text" name="jenis_barang" id="jenis_barang" class="form-control" placeholder="Masukkan jenis barang" oninput="this.value = this.value.toUpperCase()" value="<?= $resi['commodity'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama pengirim</label>
                                    <input type="text" name="nama_pengirim" id="nama_pengirim" class="form-control" placeholder="Masukkan nama pengirim" oninput="this.value = this.value.toUpperCase()" value="<?= $resi['nama_pengirim'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">No Whatsapp pengirim</label>
                                    <input type="text" name="telepon_pengirim" id="telepon_pengirim" class="form-control" placeholder="Masukkan no whatsapp pengirim" value="<?= $resi['telepon_pengirim'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Alamat pengirim</label>
                                    <textarea type="text" name="alamat_pengirim" id="alamat_pengirim" class="form-control" placeholder="Masukkan alamat pengirim" oninput="this.value = this.value.toUpperCase()"><?= $resi['alamat_pengirim'] ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama penerima</label>
                                    <input type="text" name="nama_penerima" id="nama_penerima" class="form-control" placeholder="Masukkan nama penerima" oninput="this.value = this.value.toUpperCase()" value="<?= $resi['nama_penerima'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">No Whatsapp penerima</label>
                                    <input type="text" name="telepon_penerima" id="telepon_penerima" class="form-control" placeholder="Masukkan no whatsapp penerima" value="<?= $resi['telepon_penerima'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Alamat penerima</label>
                                    <textarea type="text" name="alamat_penerima" id="alamat_penerima" class="form-control" placeholder="Masukkan alamat penerima" oninput="this.value = this.value.toUpperCase()"><?= $resi['alamat_penerima'] ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Total koli</label>
                                    <input type="text" name="total_qty" id="total_qty" class="form-control" placeholder="Masukkan jumlah barang" value="<?= $resi['qty'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Berat timbang</label>
                                    <input type="text" name="berat_timbang" id="berat_timbang" class="form-control" placeholder="Masukkan berat timbang" value="<?= number_format($resi['berat_timbang']) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Total volume</label>
                                    <input type="text" name="total_volume" id="total_volume" class="form-control" value="<?= number_format($resi['volume']) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Chargeable</label>
                                    <input type="text" name="chargeable" id="chargeable" class="form-control" value="<?= number_format($resi['chargeable']) ?>" readonly>
                                </div>
                            </div>
                            <!-- <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Panjang</label>
                                    <input type="text" name="panjang" id="panjang" class="form-control angka" placeholder="Masukkan panjang" value="<?= number_format($resi['panjang']) ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Lebar</label>
                                    <input type="text" name="lebar" id="lebar" class="form-control angka" placeholder="Masukkan lebar" value="<?= number_format($resi['lebar']) ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Tinggi</label>
                                    <input type="text" name="tinggi" id="tinggi" class="form-control angka" placeholder="Masukkan tinggi" value="<?= number_format($resi['tinggi']) ?>">
                                </div>
                            </div> -->
                            <hr>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="origin" class="form-label">Origin</label>
                                    <input type="text" name="origin" id="origin" class="form-control" placeholder="Masukkan origin" oninput="this.value = this.value.toUpperCase()" value="<?= $resi['origin'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" name="destination" id="destination" class="form-control" placeholder="Masukkan destination" oninput="this.value = this.value.toUpperCase()" value="<?= $resi['destination'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Per Kg</label>
                                    <input type="text" name="harga" id="harga" class="form-control" value="<?= ($resi['price_per_kg']) ?>" readonly>
                                    <div class="invalid-feedback">Harga tidak tersedia</div>
                                    <div class="valid-feedback">Harga tersedia</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Nominal</label>
                                    <input type="text" name="nominal" id="nominal" class="form-control" value="<?= number_format($resi['nominal']) ?>" readonly>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-4 col-12">
                                <div class="mb-3">
                                    <label for="awb" class="form-label">AWB</label>
                                    <input type="text" name="awb" id="awb" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Masukkan awb" value="<?= $resi['awb'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-3">
                                    <label for="tanggal_berangkat" class="form-label">Flight date</label>
                                    <input type="date" name="tanggal_berangkat" id="tanggal_berangkat" class="form-control" value="<?= $resi['tanggal_berangkat'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-3">
                                    <label for="gudang_tujuan" class="form-label">Gudang tujuan</label>
                                    <input type="text" name="gudang_tujuan" id="gudang_tujuan" class="form-control" oninput="this.value = this.value.toUpperCase()" placeholder="Masukkan gudang tujuan" value="<?= $resi['gudang_tujuan'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="driver" class="form-label">Driver</label>
                                    <select name="driver" id="driver" class="form-select">
                                        <option value="">::Pilih driver</option>
                                        <?php
                                        foreach ($drivers as $a) :
                                        ?>
                                            <option <?= ($resi['driver_pickup_id'] == $a->Id) ? 'selected' : '' ?> value="<?= $a->Id ?>"><?= $a->name ?></option>
                                        <?php
                                        endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="jadwal_pickup" class="form-label">Jadwal pengantaran</label>
                                    <input type="date" name="jadwal_pickup" id="jadwal_pickup" class="form-control" value="<?= $resi['jadwal_pickup'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="moda_pengiriman" class="form-label">Moda pengiriman</label>
                                    <select name="moda_pengiriman" id="moda_pengiriman" class="form-select">
                                        <option value="">::Pilih moda pengiriman</option>
                                        <option <?= ($resi['moda_pengiriman'] == 'kereta') ? 'selected' : '' ?> value="kereta">Kereta</option>
                                        <option <?= ($resi['moda_pengiriman'] == 'pesawat') ? 'selected' : '' ?> value="pesawat">Pesawat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="agent_id" class="form-label">Agen Tujuan</label>
                                    <select name="agent_id" id="agent_id" class="form-select select2" onchange="addNewAgent(this)">
                                        <option value="">::Pilih agen tujuan</option>
                                        <?php
                                        foreach ($agents as $a) :
                                        ?>
                                            <option <?= ($resi['agent_id'] == $a->id) ? 'selected' : '' ?> value="<?= $a->id  ?>"><?= $a->nama_agent ?></option>
                                        <?php
                                        endforeach; ?>
                                        <option value="__tambah__" style="background-color: red; color: white">:: ADD NEW AGENT ::</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="url_tracking" class="form-label">URL Tracking</label>
                                    <textarea name="url_tracking" id="url_tracking" class="form-control" placeholder="Masukkan URL Tracking"><?= $resi['url_tracking'] ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div id="newAgent" style="display: none;">
                            <div class="row">
                                <hr>
                                <h4>New Agent</h4>
                                <div class="col-md-4 col-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Name</label>
                                        <input type="text" name="nama_agent" id="nama_agent" class="form-control" placeholder="Enter customer's name..." oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Phone</label>
                                        <input type="text" name="telepon_agent" id="telepon_agent" class="form-control" placeholder="Enter customer's whatsapp number...">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Address</label>
                                        <input type="text" name="alamat_agent" id="alamat_agent" class="form-control" placeholder="Enter customer's address..." oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary ms-auto btn-submit-resi">
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

<script>
    function addNewAgent() {
        var option = document.getElementById("agent_id").value;

        if (option == "__tambah__") {
            $("#newAgent").css("display", "block");
        } else {
            $("#newAgent").css("display", "none");
        }
    }

    $(document).ready(function() {

        $('.select2').select2();

        // Menggunakan mask untuk input uang
        $('.angka').mask('000.000.000.000.000', {
            reverse: true
        });

        $('#berat_timbang').on('input', function() {
            tentukanChargeable();
            hitungNominal();
        });

        $('#panjang').on('input', function() {
            hitungDimensi();
            tentukanChargeable();
        });

        $('#lebar').on('input', function() {
            hitungDimensi();
            tentukanChargeable();
        });

        $('#tinggi').on('input', function() {
            hitungDimensi();
            tentukanChargeable();
        });

        function hitungDimensi() {
            var panjang = $('#panjang').val();
            panjang = panjang ? panjang.replace(/\./g, '') : '0';

            var lebar = $('#lebar').val();
            lebar = lebar ? lebar.replace(/\./g, '') : '0';

            var tinggi = $('#tinggi').val();
            tinggi = tinggi ? tinggi.replace(/\./g, '') : '0';


            var volume = (panjang * lebar * tinggi) / 5000;

            $('#volume').val(formatNumber(volume));

            tentukanChargeable();
        }

        function tentukanChargeable() {
            var chargeable;

            var berat_timbang = parseFloat($('#berat_timbang').val().replace(/\./g, '')) || 0;

            var volume = parseFloat($('#volume').val().replace(/\./g, '')) || 0;

            if (berat_timbang >= volume) {
                chargeable = berat_timbang;
            } else {
                chargeable = volume;
            }

            $('#chargeable').val(formatNumber(chargeable));

            hitungNominal();
        }

        function formatNumber(number) {
            // Pisahkan bagian integer dan desimal
            let parts = number.toString().split(".");

            // Format bagian integer dengan pemisah ribuan
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Gabungkan bagian integer dan desimal dengan koma sebagai pemisah desimal
            return parts.join(",");
        }

        $('#origin, #destination').on('input', function() {
            var origin = $('#origin').val();
            var destination = $('#destination').val();
            if (origin && destination) {
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('booking/getPrice'); ?>',
                    data: {
                        origin: origin,
                        destination: destination,
                    },
                    cache: false,
                    success: function(response) {
                        var harga = parseFloat(response);
                        console.log(harga)

                        if (!isNaN(harga) && harga > 0) {
                            $('#harga').removeClass('is-invalid').addClass('is-valid');
                        } else {
                            $('#harga').removeClass('is-valid').addClass('is-invalid');
                        }

                        $('#harga').val((harga));
                        hitungNominal();
                    },
                    error: function() {
                        console.log('Price not found');
                        $('#harga').removeClass('is-valid').addClass('is-invalid');
                    }
                })
            }
        });

        function hitungNominal() {
            var chargeable = parseFloat($('#chargeable').val().replace(/\./g, '')) || 0;
            // var harga = parseFloat($('#harga').val().replace(/\./g, '')) || 0;
            var harga = parseFloat($('#harga').val()) || 0;

            var nominal;

            nominal = Math.round(chargeable * harga);
            $('#nominal').val(formatNumber(nominal));
        }

        $(document).on("click", ".btn-submit-resi", function(e) {
            e.preventDefault();
            const form = $(this).parents("form");

            // Validasi semua input form
            let inputs = form.find('input, select, textarea');
            let valid = true;

            let agent_id = $('#agent_id').val();

            // Reset semua status validasi terlebih dahulu
            inputs.each(function() {
                $(this).removeClass('is-invalid').removeClass('is-valid');
            });

            inputs.each(function() {
                if (agent_id === '__tambah__') {
                    // Validasi hanya untuk input di dalam #newAgent jika agent_id adalah '__tambah__'
                    if ($(this).closest('#newAgent').length > 0 && !$(this).val()) {
                        $(this).addClass('is-invalid');
                        valid = false;
                    } else if ($(this).closest('#newAgent').length > 0) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                } else {
                    // Validasi input lainnya (selain yang ada di #newAgent)
                    if (!$(this).closest('#newAgent').length && !$(this).val()) {
                        $(this).addClass('is-invalid');
                        valid = false;
                    } else {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
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
                        $(".btn-submit-resi").prop('disabled', true);
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

    });
</script>