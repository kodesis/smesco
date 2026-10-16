<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<style>
		/* CSS ASLI LU TETAP SAMA */
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Helvetica', Arial, sans-serif;
			font-size: 8px;
			width: 100%;
			padding: 15px;
			color: #000;
		}

		/* PAGE BREAK: Agar tiap label ganti kertas otomatis saat print */
		@media print {
			.label-wrapper {
				page-break-after: always;
			}
		}

		.resi-container {
			width: 350px;
			border: 2px solid #000;
			padding: 5px;
			margin-bottom: 10px;
			/* Jarak antar label di layar */
		}

		/* KOLI INDICATOR: Tambahan kecil yang fungsional */
		.koli-tag {
			float: right;
			background: #000;
			color: #fff;
			padding: 2px 6px;
			font-weight: bold;
			font-size: 10px;
			margin-top: -2px;
		}

		/* Sisanya CSS Lu Gua Pertahankan 100% */
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
			font-weight: bold;
			line-height: 1;
		}

		.berat {
			font-size: 16px;
			font-weight: bold;
			text-align: right;
		}

		.detail-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 8px;
		}

		.detail-table td {
			padding: 3px 5px;
			border-bottom: 1px solid #eee;
		}

		.address-container {
			display: table;
			width: 100%;
			border: 1px solid #000;
			table-layout: fixed;
		}

		.address-box {
			display: table-cell;
			width: 50%;
			padding: 6px;
			vertical-align: top;
			border-right: 1px solid #000;
			word-wrap: break-word;
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

		.barcode-area {
			padding: 15px 0;
			text-align: center;
		}

		.barcode-area img {
			width: 85%;
			height: 35px;
		}

		.no-resi-text {
			font-size: 12px;
			font-weight: bold;
			letter-spacing: 3px;
			margin-top: 4px;
		}

		.cut-line {
			border-top: 1px dashed #444;
			margin: 5px 0;
			position: relative;
			text-align: center;
		}

		.cut-line span {
			background: #fff;
			padding: 0 5px;
			font-size: 8px;
			color: #444;
			position: absolute;
			top: -7px;
			left: 50%;
			transform: translateX(-50%);
		}

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

	<?php foreach ($labels as $key => $l): ?>
		<div class="label-wrapper">
			<div class="resi-container">
				<div class="header">
					<div class="header-logo">
						<img src="<?= $logo_base64 ?>" style="width: 180px;">
						<!-- <div class="koli-tag">KOLI: <?= $l['koli_ke'] ?>/<?= $total_koli ?></div> -->
					</div>
					<div class="header-qr">
						<img src="<?= $l['qr_internal'] ?>" style="width: 70px; height: 70px;">
						<div style="font-size: 7px; color: #555;"><?= $l['piece_id'] ?></div>

						<div style="font-size: 7px; color: #555; font-weight: bold;">KOLI: <?= $l['koli_ke'] ?>/<?= $total_koli ?></div>
					</div>
				</div>

				<div class="main-info">
					<div class="info-box">
						<div style="font-size: 9px; margin-bottom: 2px;">TUJUAN:</div>
						<div class="destinasi uppercase"><?= $resi['destination'] ?></div>
					</div>
					<div class="info-box berat">
						<div style="font-size: 9px; margin-bottom: 2px;">BERAT:</div>
						<div><?= $resi['chargeable_weight'] ?> KG</div>
					</div>
				</div>

				<table class="detail-table">
					<tr>
						<td width="30%">Deskripsi Barang</td>
						<td width="70%" class="text-bold">: <?= $resi['commodity_detail'] ?></td>
					</tr>
					<tr>
						<td>Jumlah Paket</td>
						<td class="text-bold">: <?= $total_koli ?> Koli</td>
					</tr>
					<tr>
						<td>Layanan</td>
						<td class="text-bold">: <?= $resi['service_name'] ?? 'REGULAR AIR FREIGHT' ?></td>
					</tr>
				</table>

				<div class="address-container">
					<div class="address-box">
						<span class="addr-title">PENGIRIM</span>
						<div class="text-bold"><?= $resi['sender_name'] ?> (<?= $resi['sender_phone'] ?>)</div>
						<div style="font-size: 9px;"><?= $resi['sender_address'] ?></div>
					</div>
					<div class="address-box">
						<span class="addr-title">PENERIMA</span>
						<div class="text-bold"><?= $resi['receiver_name'] ?> (<?= $resi['receiver_phone'] ?>)</div>
						<div style="font-size: 9px;"><?= $resi['receiver_address'] ?></div>
					</div>
				</div>

				<div class="barcode-area">
					<img src="<?= $l['barcode'] ?>">
					<div class="no-resi-text"><?= $l['piece_id'] ?></div>
				</div>

				<?php if ($key === 0): ?>
					<div class="cut-line"><span>Gunting di sini untuk arsip customer</span></div>

					<div class="slip-footer">
						<div class="text-bold text-center" style="letter-spacing: 1px;">BUKTI PENGIRIMAN - CUSTOMER COPY</div>
						<table class="slip-table">
							<tr>
								<td width="70%" style="vertical-align: top;">
									No. Resi: <b style="font-size: 11px;"><?= $resi['no_resi'] ?></b><br>
									Tujuan: <?= $resi['destination'] ?><br>
									Total Biaya: <b>Rp <?= number_format($resi['total_amount']) ?></b><br>
									<i style="font-size: 8px;">*Simpan resi ini untuk bukti komplain</i>
								</td>
								<td width="30%" class="text-right">
									<img src="<?= $qr_tracking ?>" style="width: 50px;"><br>
									<span style="font-size: 7px;">Lacak Kiriman Anda</span>
								</td>
							</tr>
						</table>
					</div>
				<?php else: ?>
					<div style="text-align: center; padding: 10px; border-top: 1px dashed #ccc; color: #888; font-style: italic;">
						Label Pengiriman Smesco Express - Piece Unit
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
	<script>
		window.onload = function() {
			window.print();
		};
	</script>
</body>

</html>
