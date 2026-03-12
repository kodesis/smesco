<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= base_url() ?>assets/logo/icon-smesco.png" rel="icon">
    <title>Resi Pengiriman</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            width: 300px;
            /* width: 396px; */
            /* Approx 105mm */
            height: 450px;
            /* height: 559px; */
            /* Approx 148mm */
            padding: 40px;
            /* Approx 10mm */
        }

        .resi-container {
            width: 100%;
            height: 100%;
            border: 1px solid #000;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 150px;
        }

        .barcode {
            text-align: center;
            margin-bottom: 15px;
        }

        .barcode img {
            max-width: 110px;
            height: auto;
        }

        table.detail {
            width: 100%;
            font-size: 10px;
            margin-top: 7px;
            border-collapse: collapse;
        }

        table.detail h3 {
            font-size: 12px;
            margin-bottom: 3px;
        }

        table.detail p {
            margin-bottom: 5px;
        }

        table.detail tr td {
            vertical-align: top;
            padding: 5px 10px;
        }

        table.detail tr td:first-child {
            width: 50%;
        }

        .text-center {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="resi-container">
        <!-- <div class="header">
            <img src="<?= base_url('assets/logo/logo-smesco.png') ?>" alt="Logo Perusahaan" class="logo">
        </div>

        <div class="barcode">
            <img src="<?= $qr_code ?>" alt="Barcode Resi">
            <p><?= $resi['no_resi'] ?></p>
        </div> -->

        <table class="detail">
            <tr>
                <td class="text-center">
                    <img src="<?= base_url('assets/logo/logo-01.png') ?>" alt="Logo Perusahaan" class="logo" style="width: 80px; margin-bottom: 40px;">
                </td>
                <td class="text-center">
                    <img src="<?= $qr_code ?>" alt="Barcode Resi" style="width: 110px">
                    <p><?= $resi['no_resi'] ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Jumlah Barang:</h3>
                    <p><?= $resi['qty'] ?> Koli</p>
                </td>
                <td>
                    <h3>Berat:</h3>
                    <p><?= $resi['chargeable'] ?> KG</p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Deskripsi Barang:</h3>
                    <p><?= $resi['commodity'] ?></p>
                </td>
                <td>
                    <h3>Biaya Kiriman:</h3>
                    <p>Rp. <?= number_format($resi['nominal']) ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Asal:</h3>
                    <p><?= $resi['origin'] ?></p>
                </td>
                <td>
                    <h3>Tujuan:</h3>
                    <p><?= $resi['destination'] ?></p>
                </td>
            </tr>
            <tr style="border: 1px #000 solid;">
                <td colspan="2">
                    <h3>Pengirim:</h3>
                    <p><?= $pengirim ?></p>
                    <p><?= $resi['alamat_pengirim'] ?></p>
                </td>
            </tr>
            <tr style="border: 1px #000 solid;">
                <td colspan="2">
                    <h3>Penerima:</h3>
                    <p><?= $penerima ?></p>
                    <p><?= $resi['alamat_penerima'] ?></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>