<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Helvetica', Arial, sans-serif;
			font-size: 8px;
			/* Sedikit naik dari 9px karena label printer lebih tajam */
			width: 100%;
			/* Biarkan sistem PDF yang mengatur lebar A6 */
			padding: 15px;
			/* Margin luar agar tidak terpotong saat feeding kertas */
			color: #000;
		}

		.resi-container {
			width: 350px;
			border: 2px solid #000;
			padding: 5px;
		}

		/* Helpers */
		.text-bold {
			font-weight: bold;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.uppercase {
			text-transform: uppercase;
		}

		/* Header */
		.header {
			display: table;
			width: 100%;
			margin-bottom: 10px;
		}

		.header-logo {
			display: table-cell;
			vertical-align: middle;
			width: 65%;
		}

		.header-qr {
			display: table-cell;
			vertical-align: middle;
			width: 35%;
			text-align: right;
		}

		/* Main Info Block */
		.main-info {
			display: table;
			width: 100%;
			border-top: 2px solid #000;
			border-bottom: 2px solid #000;
			margin: 8px 0;
			table-layout: fixed;
		}

		.info-box {
			display: table-cell;
			padding: 8px 5px;
			vertical-align: top;
		}

		.destinasi {
			font-size: 18px;
			/* Lebih besar untuk kemudahan sortir */
			font-weight: bold;
			line-height: 1;
		}

		.berat {
			font-size: 16px;
			font-weight: bold;
			text-align: right;
		}

		/* Detail Table */
		.detail-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 8px;
		}

		.detail-table td {
			padding: 3px 5px;
			border-bottom: 1px solid #eee;
		}

		/* Address Box - STABILIZER */
		.address-container {
			display: table;
			width: 100%;
			border: 1px solid #000;
			table-layout: fixed;
			/* WAJIB: Agar kolom kiri & kanan fix 50% */
		}

		.address-box {
			display: table-cell;
			width: 50%;
			padding: 6px;
			vertical-align: top;
			border-right: 1px solid #000;
			word-wrap: break-word;
			/* Agar alamat panjang tidak tembus ke samping */
		}

		.address-box:last-child {
			border-right: none;
		}

		.addr-title {
			font-size: 8px;
			font-weight: bold;
			text-decoration: underline;
			margin-bottom: 3px;
			display: block;
		}

		/* Barcode Area */
		.barcode-area {
			padding: 15px 0;
			text-align: center;
		}

		.barcode-area img {
			width: 85%;
			height: 45px;
		}

		.no-resi-text {
			font-size: 13px;
			font-weight: bold;
			letter-spacing: 3px;
			margin-top: 4px;
		}

		/* Cut Line */
		.cut-line {
			border-top: 1px dashed #444;
			margin: 10px 0;
			position: relative;
			text-align: center;
		}

		.cut-line span {
			background: #fff;
			padding: 0 12px;
			font-size: 8px;
			color: #444;
			position: absolute;
			top: -7px;
			left: 50%;
			transform: translateX(-50%);
		}

		/* Customer Slip */
		.slip-footer {
			font-size: 9px;
		}

		.slip-table {
			width: 100%;
			margin-top: 8px;
		}
	</style>
</head>

<body>

	<div class="resi-container">
		<div class="header">
			<div class="header-logo">
				<img src="<?= base_url('assets/logo/icon-smesco.png') ?>" style="width: 100px;">
				<!-- <div style="font-size: 9px; font-weight: bold; margin-top: 5px;">KRIBO EXPRESS x SMESCO</div> -->
			</div>
			<div class="header-qr">
				<img src="<?= $qr_code ?>" style="width: 85px; height: 85px;">
				<div style="font-size: 7px; color: #555;">Scan to Track</div>
			</div>
		</div>

		<div class="main-info">
			<div class="info-box">
				<div style="font-size: 9px; margin-bottom: 2px;">TUJUAN:</div>
				<div class="destinasi uppercase"><?= $resi['destination'] ?></div>
			</div>
			<div class="info-box berat">
				<div style="font-size: 9px; margin-bottom: 2px;">BERAT:</div>
				<div><?= $resi['chargeable'] ?> KG</div>
			</div>
		</div>

		<table class="detail-table">
			<tr>
				<td width="30%">Deskripsi Barang</td>
				<td width="70%" class="text-bold">: <?= $resi['commodity'] ?></td>
			</tr>
			<tr>
				<td>Jumlah Paket</td>
				<td class="text-bold">: <?= $resi['qty'] ?> Koli</td>
			</tr>
			<tr>
				<td>Layanan</td>
				<td class="text-bold">: REGULAR AIR FREIGHT</td>
			</tr>
		</table>

		<div class="address-container">
			<div class="address-box">
				<span class="addr-title">PENGIRIM</span>
				<div class="text-bold"><?= $resi['nama_pengirim'] ?> (<?= $resi['telepon_pengirim'] ?>)</div>
				<div style="font-size: 9px;"><?= $resi['alamat_pengirim'] ?></div>
			</div>
			<div class="address-box">
				<span class="addr-title">PENERIMA</span>
				<div class="text-bold"><?= $resi['nama_penerima'] ?> (<?= $resi['telepon_penerima'] ?>)</div>
				<div style="font-size: 9px;"><?= $resi['alamat_penerima'] ?></div>
			</div>
		</div>

		<div class="barcode-area">
			<img src="<?= $barcode_smu ?>">
			<div class="no-resi-text"><?= $resi['no_resi'] ?></div>
		</div>

		<div class="cut-line"><span>Gunting di sini untuk arsip customer</span></div>

		<div class="slip-footer">
			<div class="text-bold text-center" style="letter-spacing: 1px;">BUKTI PENGIRIMAN - CUSTOMER COPY</div>
			<table class="slip-table">
				<tr>
					<td width="70%" style="vertical-align: top;">
						No. Resi: <b style="font-size: 11px;"><?= $resi['no_resi'] ?></b><br>
						Tujuan: <?= $resi['destination'] ?><br>
						Total Biaya: <b>Rp <?= number_format($resi['nominal']) ?></b><br>
						<i style="font-size: 8px;">*Simpan resi ini untuk bukti komplain</i>
					</td>
					<td width="30%" class="text-right">
						<img src="<?= $qr_code ?>" style="width: 50px;"><br>
						<span style="font-size: 7px;">Lacak Kiriman Anda</span>
					</td>
				</tr>
			</table>
		</div>
	</div>

</body>

</html>
