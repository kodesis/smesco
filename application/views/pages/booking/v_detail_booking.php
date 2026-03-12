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
                <form action="<?= base_url('booking/simpanDetailAwb/') . $booking['Id'] ?>" method="post" class="card" id="formBooking">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="mb-3">
                                    <label for="no_booking" class="form-label">Booking code</label>
                                    <input type="text" name="no_booking" id="no_booking" class="form-control" value="<?= $booking['no_booking'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Customer</label>
                                    <select class="form-select" name="customer_id" id="customer_id" required>
                                        <?php
                                        foreach ($customers as $c) :
                                            if ($booking['customer_id'] == $c->id) { ?>
                                                <option value="<?= $c->id ?>"><?= $c->nama_customer ?></option>
                                            <?php
                                            } ?>
                                        <?php
                                        endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-3">
                                    <label for="service" class="form-label">Service</label>
                                    <select name="service" id="service" class="form-select">
                                        <option value="">:: Choose service </option>
                                        <option <?= ($booking['opsi_service'] == "door-to-port") ? 'selected' : '' ?> value="door-to-port">Door to Port</option>
                                        <option <?= ($booking['opsi_service'] == "port-to-port") ? 'selected' : '' ?> value="port-to-port">Port to Port</option>
                                        <option <?= ($booking['opsi_service'] == "port-to-door") ? 'selected' : '' ?> value="port-to-door">Port to Door</option>
                                        <option <?= ($booking['opsi_service'] == "door-to-door") ? 'selected' : '' ?> value="door-to-door">Door to Door</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="awb_num" class="form-label">AWB</label>
                                    <input type="text" name="awb_num" id="awb_num" class="form-control" value="<?= $booking['awb'] ?>" placeholder="Enter awb number" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-3">
                                    <label for="commodity" class="form-label">Commodity</label>
                                    <input type="text" name="commodity" id="commodity" class="form-control" value="<?= $booking['commodity'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-2">
                                    <label for="origin" class="form-label">Origin</label>
                                    <input type="text" name="origin" id="origin" class="form-control" value="<?= $booking['origin'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" name="destination" id="destination" class="form-control" value="<?= $booking['destination'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-3">
                                    <label for="total_qty" class="form-label">Total qty</label>
                                    <input type="text" name="total_qty" id="total_qty" class="form-control" value="<?= $booking['total_qty'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-3">
                                    <label for="total_chargeable" class="form-label">Total chargeable</label>
                                    <input type="text" name="total_chargeable" id="total_chargeable" class="form-control" value="<?= $booking['total_chargeable'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price from <?= $booking['origin'] ?> to <?= $booking['destination'] ?></label>
                                    <input type="text" name="price" id="price" class="form-control angka" value="<?= number_format($price['all_in_smu'], 0) ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-3">
                                    <label for="dooring" class="form-label">Dooring</label>
                                    <input type="text" name="dooring" id="dooring" class="form-control angka" value="<?= number_format($price['dooring'], 0) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="w-8">#</th>
                                        <th class="w-8">Qty</th>
                                        <th class="w-8">Chargeable</th>
                                        <th class="">City of destination</th>
                                        <th>Agent destination</th>
                                        <th class="w-8">Price</th>
                                        <th class="w-8">Pickup Price</th>
                                        <th class="w-8">Dooring Price</th>
                                        <th class="w-8">Total
                                            <a href="#" class="" title="Price + pickup + dooring" data-bs-toggle="tooltip" data-bs-placement="top" style="color: #667382">
                                                <!-- Download SVG icon from http://tabler-icons.io/i/moon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-square-rounded">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M12 9h.01" />
                                                    <path d="M11 12h1v4h1" />
                                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                                                </svg>
                                            </a>
                                        </th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <?php
                                    if ($details) {
                                        foreach ($details as $d) : ?>
                                            <tr class="baris">
                                                <td class="text-end">
                                                    <input type="hidden" name="status_tracking[]" id="status_tracking[]" readonly value="<?= $d->status_tracking ?>">
                                                    <input type="text" name="nomor_urut[]" id="nomor_urut[]" class="form-control nomor-urut" readonly value="<?= $d->no_urut ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="qty[]" id="qty[]" class="form-control angka" placeholder="Enter qty number..." required value="<?= $d->qty ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="chargeable[]" id="chargeable[]" class="form-control angka" placeholder="Enter chargeable..." oninput="this.value = this.value.toUpperCase()" required value="<?= $d->chargeable ?>">
                                                </td>
                                                <td>
                                                    <textarea type="text" name="kota_tujuan[]" id="kota_tujuan[]" class="form-control" placeholder="Enter city of destination..." oninput="this.value = this.value.toUpperCase()" rows="1" required><?= $d->kota_tujuan ?></textarea>
                                                </td>
                                                <td>
                                                    <select name="agent_id[]" id="agent_id[]" class="form-select" required>
                                                        <option value="">:: Choose agent</option>
                                                        <?php
                                                        foreach ($agents as $c) : ?>
                                                            <option <?= ($c->id == $d->agent_id) ? 'selected' : '' ?> value="<?= $c->id ?>"><?= $c->nama_agent ?></option>
                                                        <?php
                                                        endforeach; ?>

                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="nominal[]" id="nominal[]" class="form-control angka" placeholder="Enter price..." oninput="this.value = this.value.toUpperCase()" required readonly value="<?= number_format($d->nominal) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="pickup_price[]" id="pickup_price[]" class="form-control angka" placeholder="Enter pickup price..." oninput="this.value = this.value.toUpperCase()" required value="<?= number_format($d->pickup_price) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="dooring_price[]" id="dooring_price[]" class="form-control angka" placeholder="Enter dooring price..." oninput="this.value = this.value.toUpperCase()" required value="<?= number_format($d->dooring_price) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="total_amount[]" id="total_amount[]" class="form-control angka" placeholder="Enter dooring price..." oninput="this.value = this.value.toUpperCase()" required readonly value="<?= number_format($d->total_amount) ?>">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger hapusRow">Hapus</button>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr class="baris">
                                            <td class="text-end">
                                                <input type="text" name="nomor_urut[]" id="nomor_urut[]" class="form-control nomor-urut" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="qty[]" id="qty[]" class="form-control angka" placeholder="Enter qty number..." required>
                                            </td>
                                            <td>
                                                <input type="text" name="chargeable[]" id="chargeable[]" class="form-control angka" placeholder="Enter chargeable..." oninput="this.value = this.value.toUpperCase()" required>
                                            </td>
                                            <td>
                                                <input type="text" name="kota_tujuan[]" id="kota_tujuan[]" class="form-control" placeholder="Enter city of destination..." oninput="this.value = this.value.toUpperCase()" required>
                                            </td>
                                            <td>
                                                <select name="agent_id[]" id="agent_id[]" class="form-select" required>
                                                    <option value="">:: Choose agent</option>
                                                    <?php
                                                    foreach ($agents as $c) : ?>
                                                        <option value="<?= $c->id ?>"><?= $c->nama_agent ?></option>
                                                    <?php
                                                    endforeach; ?>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="nominal[]" id="nominal[]" class="form-control angka" placeholder="Enter price..." oninput="this.value = this.value.toUpperCase()" value="" required readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pickup_price[]" id="pickup_price[]" class="form-control angka" placeholder="Enter pickup price..." oninput="this.value = this.value.toUpperCase()" value="" required>
                                            </td>
                                            <td>
                                                <input type="text" name="dooring_price[]" id="dooring_price[]" class="form-control angka" placeholder="Enter dooring price..." oninput="this.value = this.value.toUpperCase()" value="" required readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="total_amount[]" id="total_amount[]" class="form-control angka" placeholder="Enter dooring price..." oninput="this.value = this.value.toUpperCase()" value="" required readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger hapusRow">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    if (!$details) {
                    ?>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="button" class="btn btn-secondary" id="addRow">Add new row</button>
                                <button type="submit" class="btn btn-primary ms-auto btn-submit-detail">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    <?php
                    } ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url(); ?>assets/dashboard/js/jquery.mask.js"></script>

<script type="text/javascript" src="<?= base_url(); ?>assets/vendor/select2/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        // Menggunakan mask untuk input uang
        $('.angka').mask('000.000.000.000.000', {
            reverse: true
        });

        // Inisialisasi row
        var rowCount = 1;

        function updateRowNumbers() {
            $('#table-body .baris').each(function(index) {
                var number = index + 1;
                $(this).find('.nomor-urut').val(number.toString().padStart(2, '0'));
            });
        }

        updateRowNumbers();
        updateTotal();

        // Menambahkan baris baru ketika tombol 'addRow' diklik
        $('#addRow').on('click', function() {
            // Cek apakah ada input kosong di baris sebelumnya
            var previousRow = $('.baris').last();
            var isEmpty = false;

            previousRow.find('input[type="text"]').each(function() {
                if ($(this).val().trim() === '') {
                    isEmpty = true;
                    return false; // Berhenti iterasi jika ditemukan input kosong
                }
            });

            if (isEmpty) {
                Swal.fire({
                    icon: 'warning',
                    type: 'error',
                    title: 'Oops...',
                    text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
                });
                return;
            }

            // Clone baris terakhir dan kosongkan input
            var newRow = previousRow.clone();
            newRow.find('input').val(''); // Kosongkan input
            newRow.find('textarea').val(''); // Kosongkan textarea

            // Ganti ID elemen yang di-clone jika diperlukan
            newRow.find('input').each(function() {
                var name = $(this).attr('name');
                $(this).attr('id', name + rowCount); // Mengganti ID
            });
            // newRow.find('.select2').select2();

            rowCount++;
            previousRow.after(newRow);
            updateRowNumbers();
            updateTotal();

            newRow.find('.angka').mask('000.000.000.000.000', {
                reverse: true
            });
        });

        // Hapus baris ketika tombol hapus diklik
        $(document).on('click', '.hapusRow', function() {
            if ($('.baris').length > 1) {
                $(this).closest('tr.baris').remove();
                updateRowNumbers();
                updateTotal();
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Baris tidak bisa dihapus, harus ada minimal satu baris.',
                });
            }
        });

        // Update total qty ketika ada perubahan nilai di input qty
        $(document).on('input', 'input[name="qty[]"], input[name="chargeable[]"], input[name="pickup_price[]"], input[name="dooring_price[]"]', function() {
            var total_qty = 0;
            var total_chargeable = 0;
            var max_qty = $("#total_qty").val().replace(/\./g, ''); // Batas maksimum total qty
            var max_chargeable = $("#total_chargeable").val().replace(/\./g, ''); // Batas maksimum total qty

            // Hitung total qty saat ini dari semua baris
            $(".baris").each(function() {
                var qty = $(this).find('input[name="qty[]"]').val().replace(/\./g, '');
                var chargeable = $(this).find('input[name="chargeable[]"]').val().replace(/\./g, '');

                qty = parseFloat(qty.replace(/,/g, ''));
                if (!isNaN(qty)) {
                    total_qty += qty;
                }

                chargeable = parseFloat(chargeable.replace(/,/g, ''));
                if (!isNaN(chargeable)) {
                    total_chargeable += chargeable;
                }
            });

            // Dapatkan nilai yang diinput pada baris ini
            var input_qty = parseFloat($(this).val().replace(/,/g, ''));
            var input_chargeable = parseFloat($(this).val().replace(/,/g, ''));

            // Periksa apakah total_qty melebihi batas maksimum
            if (total_qty > max_qty) {
                var sisa_qty = max_qty - (total_qty - input_qty); // Hitung sisa yang diperbolehkan
                $(this).val(sisa_qty); // Sesuaikan input qty ke nilai sisa yang diizinkan
                Swal.fire({
                    icon: 'warning',
                    title: 'Melebihi Batas',
                    text: 'Total qty resi tidak boleh melebihi qty booking.'
                });
            }
            if (total_chargeable > max_chargeable) {
                var sisa_chargeable = max_chargeable - (total_chargeable - input_chargeable); // Hitung sisa yang diperbolehkan
                $(this).val(sisa_chargeable); // Sesuaikan input chargeable ke nilai sisa yang diizinkan
                Swal.fire({
                    icon: 'warning',
                    title: 'Melebihi Batas',
                    text: 'Total chargeable resi tidak boleh melebihi chargeable booking.'
                });
            }

            var row = $(this).closest('.baris');
            updateTotal();
            hitungTotal(row);
        });

        function hitungTotal(row) {
            var price = $("#price").val();
            price = price ? price.replace(/\./g, '') : '0';

            var dooring = $("#dooring").val();
            dooring = dooring ? dooring.replace(/\./g, '') : '0';

            var chwt = row.find('input[name="chargeable[]"]').val();
            chwt = chwt ? chwt.replace(/\./g, '') : '0';

            var pickup_price = row.find('input[name="pickup_price[]"]').val();
            pickup_price = pickup_price ? pickup_price.replace(/\./g, '') : '0';

            var dooring_price = row.find('input[name="dooring_price[]"]').val();
            dooring_price = dooring_price ? dooring_price.replace(/\./g, '') : '0';

            chwt = parseInt(chwt) || 0;
            pickup_price = parseInt(pickup_price) || 0;
            dooring_price = parseInt(dooring_price) || 0;

            var total_nominal = chwt * price; // Ensure `price` is defined in the scope
            var total_dooring = chwt * dooring;
            var total_amount = total_nominal + pickup_price + total_dooring;

            row.find('input[name="nominal[]"]').val(formatNumber(total_nominal));
            row.find('input[name="dooring_price[]"]').val(formatNumber(total_dooring));
            row.find('input[name="total_amount[]"]').val(formatNumber(total_amount));
        }


        function formatNumber(number) {
            // Pisahkan bagian integer dan desimal
            let parts = number.toString().split(".");

            // Format bagian integer dengan pemisah ribuan
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Gabungkan bagian integer dan desimal dengan koma sebagai pemisah desimal
            return parts.join(",");
        }

        $('#price').on('input keyup', function() {
            var value = $(this).val().trim(); // Hapus spasi di awal dan akhir nilai
            var formattedValue = parseFloat(value.split('.').join(''));

            if (!isNaN(formattedValue)) {
                var formattedNumber = formatNumber(formattedValue);
                $(this).val(formattedNumber);
            } else {
                $(this).val('');
            }

            // Panggil fungsi update untuk setiap baris
            $(".baris").each(function() {
                var row = $(this);
                hitungTotal(row); // Panggil hitungTotal pada setiap baris
            });
        });

        $('#dooring').on('input keyup', function() {
            var value = $(this).val().trim(); // Hapus spasi di awal dan akhir nilai
            var formattedValue = parseFloat(value.split('.').join(''));

            if (!isNaN(formattedValue)) {
                var formattedNumber = formatNumber(formattedValue);
                $(this).val(formattedNumber);
            } else {
                $(this).val('');
            }

            // Panggil fungsi update untuk setiap baris
            $(".baris").each(function() {
                var row = $(this);
                hitungTotal(row); // Panggil hitungTotal pada setiap baris
            });
        });


        function updateTotal() {
            var total_qty = 0;
            var total_chargeable = 0;
            var total_amount = 0;

            $(".baris").each(function() {
                var qty = parseFloat($(this).find('input[name="qty[]"]').val().replace(/\./g, '') || 0);
                var chargeable = parseFloat($(this).find('input[name="chargeable[]"]').val().replace(/\./g, '') || 0);
                var pickup_price = parseFloat($(this).find('input[name="pickup_price[]"]').val().replace(/\./g, '') || 0);
                var dooring_price = parseFloat($(this).find('input[name="dooring_price[]"]').val().replace(/\./g, '') || 0);

                // Total nominal dihitung sebagai (qty * price) atau chargeable * price, tergantung pada logika
                // total_nominal += qty * price;

                // Total amount bisa dihitung berdasarkan komponen lain seperti pickup_price dan dooring_price
                // total_amount += pickup_price + dooring_price;
                // total_amount = chargeable * pickup_price;



                // if (!isNaN(qty)) { // Pastikan qty adalah angka
                //     total_qty += qty; // Tambahkan nilai qty ke total_qty
                // }

                // if (!isNaN(chargeable)) { // Pastikan chargeable adalah angka
                //     total_chargeable += chargeable; // Tambahkan nilai chargeable ke total_chargeable
                // }

                // if (!isNaN(nominal)) { // Pastikan nominal adalah angka
                //     subtotal += nominal; // Tambahkan nilai nominal ke total_nominal
                // }
            });
            var row = $(this).closest('.baris');
            // updateTotal();
            hitungTotal(row);

            // ppn = subtotal * 0.11;
            // grandtotal = subtotal + ppn;

            // $('#total_amount[]').val(formatNumber(total_amount));
            // $('#total_chargeable').val(formatNumber(total_chargeable));
            // $('#subtotal').val(formatNumber(subtotal));
            // $('#ppn').val(formatNumber(ppn));
            // $('#grandtotal').val(formatNumber(grandtotal));
        }
    });
</script>