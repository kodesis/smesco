<div class="container-fluid page-about py-5">
    <div class="container py-5">
        <h2 class="display-3 text-white mb-3 animated slideInDown">Solusi Transportasi dan <br>Logistik Cepat</h2>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Cek ongkir</li>
            </ol>
        </nav>
    </div>
</div>
<div class="container-xxl py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-12">
                <div class="bg-light p-5 wow fadeIn" data-wow-delay="0.5s">

                    <h1 class="mb-5">Cek ongkir</h1>
                    <div class="row g-3 mb-5">
                        <div class="col-12 col-md-4">
                            <label for="nama_pendaftar" class="form-label text-bold">Jenis pengiriman <span class="text-primary">*</span></label>
                            <select name="jenis_pengiriman" id="jenis_pengiriman" class="form-select border-0" style="height: 55px;">
                                <option value="D">Domestik</option>
                                <option value="I">Internasional</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="origin" class="form-label text-bold">Asal <span class="text-primary">*</span></label>
                            <input type="text" name="origin" id="origin" class="form-control border-0" placeholder="Masukkan kota asal" oninput="this.value = this.value.toUpperCase()" style="height: 55px;">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="destination" class="form-label text-bold">Tujuan <span class="text-primary">*</span></label>
                            <input type="text" name="destination" id="destination" class="form-control border-0" placeholder="Masukkan kota tujuan" oninput="this.value = this.value.toUpperCase()" style="height: 55px;">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="berat_timbang" class="form-label text-bold">Berat timbang <span class="text-primary">*</span></label>
                            <input type="text" name="berat_timbang" id="berat_timbang" class="form-control border-0" placeholder="Masukkan berat timbang" style="height: 55px;">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="total_koli" class="form-label text-bold">Total koli <span class="text-primary">*</span></label>
                            <input type="text" name="total_koli" id="total_koli" class="form-control border-0" placeholder="Total koli" style="height: 55px;" value="1" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="total_volume" class="form-label text-bold">Total volume <span class="text-primary">*</span></label>
                            <input type="text" name="total_volume" id="total_volume" class="form-control border-0" placeholder="Total volume" style="height: 55px;" value="0" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="chargeable" class="form-label text-bold">Total chargeable <span class="text-primary">*</span></label>
                            <input type="text" name="chargeable" id="chargeable" class="form-control border-0" placeholder="Total chargeable" style="height: 55px;" value="0" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="harga" class="form-label text-bold">Per kg <span class="text-primary">*</span></label>
                            <input type="text" name="harga" id="harga" class="form-control border-0" style="height: 55px;" value="0" readonly>
                            <div class="invalid-feedback">Harga tidak tersedia</div>
                            <div class="valid-feedback">Harga tersedia</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="nominal" class="form-label text-bold">Nominal <span class="text-primary">*</span></label>
                            <input type="text" name="nominal" id="nominal" class="form-control border-0" style="height: 55px;" value="0" readonly>
                        </div>
                        <div class="col-12">

                            <label for="" class="form-label text-bold">Input dimensi</label>
                            <table class="table ">
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
</div>
</div>


<!-- Google reCAPTCHA v2 Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>