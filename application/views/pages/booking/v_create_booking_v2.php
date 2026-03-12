<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/select2/css/select2.min.css">

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
                        <?php
                        $role_id = $this->session->userdata('role_id');

                        if ($role_id == '3') {
                            $user_id = $this->session->userdata('user_id');
                            $id_customer = $this->session->userdata('customer_id'); ?>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="jenis_pengiriman" class="form-label">Jenis pengiriman</label>
                                        <select name="jenis_pengiriman" id="jenis_pengiriman" class="form-control">
                                            <option value="D">Domestik</option>
                                            <option value="I">International</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Jenis barang</label>
                                        <input type="text" name="jenis_barang" id="jenis_barang" class="form-control" placeholder="Masukkan jenis barang" oninput="this.value = this.value.toUpperCase()">
                                        <input type="hidden" name="saldo_mitra" id="saldo_mitra" class="form-control" value="<?= number_format($saldo_mitra) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Saldo</label>
                                        <input type="text" name="saldo_mitra" id="saldo_mitra" class="form-control" value="<?= number_format($saldo_mitra) ?>">
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
                                        <label for="harga" class="form-label">Harga satuan</label>
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
                            <button type="submit" class="btn btn-primary ms-auto">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>