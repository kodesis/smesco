<?php
/*
|--------------------------------------------------------------------------
| app/pages/agent_kecamatan/print.php
|--------------------------------------------------------------------------
| Cetak label A6 khusus Agen Kecamatan: 2 resi sekali cetak.
|
|   RESI 1 : KECAMATAN -> JAKARTA   (customer copy ada di halaman pertama resi 1)
|   RESI 2 : JAKARTA   -> TUJUAN    (label existing, tampilan sama dengan shipment/print)
|
| Urutan halaman: semua koli resi 1, lalu semua koli resi 2.
| Piece ID / barcode sama di kedua resi (satu shipment, satu set koli).
|
| Variabel dari AgentKecamatan::print_label():
|   $resi, $labels, $total_koli, $qr_tracking, $logo_base64,
|   $agent_origin_name, $agent_origin_code, $hub_name, $hub_address
*/
$h           = function ($v) {
	return html_escape((string) $v);
};
$total_pages = 2 * count($labels);
$page_no     = 0;
$destination = strtoupper((string) $resi['destination']);
$asal_label  = strtoupper((string) $agent_origin_name);
?>
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
			width: 100%;
			padding: 15px;
			color: #000;
		}

		.resi-container {
			width: 350px;
			border: 2px solid #000;
			padding: 5px;
			margin-bottom: 10px;
		}

		/* Penanda resi 1 / resi 2 */
		.leg-banner {
			background: #000;
			color: #fff;
			text-align: center;
			font-size: 10px;
			font-weight: bold;
			letter-spacing: 1px;
			padding: 4px 0;
			margin-bottom: 8px;
		}

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

	<?php /* ===================== RESI 1 : KECAMATAN -> JAKARTA ===================== */ ?>
	<?php foreach ($labels as $key => $l): $page_no++; ?>
		<div class="label-wrapper" <?= $page_no < $total_pages ? ' style="page-break-after: always;"' : '' ?>>
			<div class="resi-container">
				<!-- <div class="leg-banner">RESI 1/2 : <?= $h($asal_label) ?> KE JAKARTA</div> -->

				<div class="header">
					<div class="header-logo">
						<img src="<?= $logo_base64 ?>" style="width: 180px;">
					</div>
					<div class="header-qr">
						<img src="<?= $l['qr_internal'] ?>" style="width: 70px; height: 70px;">
						<div style="font-size: 7px; color: #555;"><?= $h($l['piece_id']) ?></div>
						<div style="font-size: 7px; color: #555; font-weight: bold;">KOLI: <?= (int) $l['koli_ke'] ?>/<?= (int) $total_koli ?></div>
					</div>
				</div>

				<div class="main-info">
					<div class="info-box">
						<div style="font-size: 9px; margin-bottom: 2px;">TUJUAN:</div>
						<div class="destinasi uppercase">JAKARTA</div>
						<div style="font-size: 8px; margin-top: 3px;">Asal: <?= $h($asal_label) ?></div>
					</div>
					<div class="info-box berat">
						<div style="font-size: 9px; margin-bottom: 2px;">BERAT:</div>
						<div><?= $h($resi['chargeable_weight']) ?> KG</div>
					</div>
				</div>

				<table class="detail-table">
					<tr>
						<td width="30%">Deskripsi Barang</td>
						<td width="70%" class="text-bold">: <?= $h($resi['commodity_detail']) ?></td>
					</tr>
					<tr>
						<td>Jumlah Paket</td>
						<td class="text-bold">: <?= (int) $total_koli ?> Koli</td>
					</tr>
					<tr>
						<td>Tujuan Akhir</td>
						<td class="text-bold">: <?= $h($destination) ?></td>
					</tr>
					<tr>
						<td>Via</td>
						<td class="text-bold">: INDAH KARGO BAMBU APUS</td>
					</tr>
				</table>

				<div class="address-container">
					<div class="address-box">
						<span class="addr-title">PENGIRIM</span>
						<div class="text-bold"><?= $h($resi['sender_name']) ?> (<?= $h($resi['sender_phone']) ?>)</div>
						<div style="font-size: 9px;"><?= $h($resi['sender_address']) ?></div>
					</div>
					<div class="address-box">
						<span class="addr-title">PENERIMA (HUB JAKARTA)</span>
						<div class="text-bold"><?= $h($hub_name) ?></div>
						<?php if (!empty($hub_address)): ?>
							<div style="font-size: 9px;"><?= $h($hub_address) ?></div>
						<?php endif; ?>
						<div style="font-size: 8px; margin-top: 3px;">Penerima akhir: <?= $h($resi['receiver_name']) ?>, <?= $h($destination) ?></div>
					</div>
				</div>

				<div class="barcode-area">
					<img src="<?= $l['barcode'] ?>">
					<div class="no-resi-text"><?= $h($l['piece_id']) ?></div>
				</div>

				<?php if ($key === 0): ?>
					<div class="cut-line"><span>Gunting di sini untuk arsip customer</span></div>

					<div class="slip-footer">
						<div class="text-bold text-center" style="letter-spacing: 1px;">BUKTI PENGIRIMAN - CUSTOMER COPY</div>
						<table class="slip-table">
							<tr>
								<td width="70%" style="vertical-align: top;">
									No. Resi: <b style="font-size: 11px;"><?= $h($resi['no_resi']) ?></b><br>
									Tujuan: <?= $h($resi['destination']) ?><br>
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
						Label Pengiriman Smesco Express - Resi 1 (Kecamatan ke Jakarta)
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>

	<?php /* ===================== RESI 2 : JAKARTA -> TUJUAN (layout existing) ===================== */ ?>
	<?php foreach ($labels as $key => $l): $page_no++; ?>
		<div class="label-wrapper" <?= $page_no < $total_pages ? ' style="page-break-after: always;"' : '' ?>>
			<div class="resi-container">
				<!-- <div class="leg-banner">RESI 2/2 : JAKARTA KE <?= $h($destination) ?></div> -->

				<div class="header">
					<div class="header-logo">
						<img src="<?= $logo_base64 ?>" style="width: 180px;">
					</div>
					<div class="header-qr">
						<img src="<?= $l['qr_internal'] ?>" style="width: 70px; height: 70px;">
						<div style="font-size: 7px; color: #555;"><?= $h($l['piece_id']) ?></div>
						<div style="font-size: 7px; color: #555; font-weight: bold;">KOLI: <?= (int) $l['koli_ke'] ?>/<?= (int) $total_koli ?></div>
					</div>
				</div>

				<div class="main-info">
					<div class="info-box">
						<div style="font-size: 9px; margin-bottom: 2px;">TUJUAN:</div>
						<div class="destinasi uppercase"><?= $h($resi['destination']) ?></div>

						<div style="font-size: 8px; margin-top: 3px;">Asal: JAKARTA</div>
					</div>
					<div class="info-box berat">
						<div style="font-size: 9px; margin-bottom: 2px;">BERAT:</div>
						<div><?= $h($resi['chargeable_weight']) ?> KG</div>
					</div>
				</div>

				<table class="detail-table">
					<tr>
						<td width="30%">Deskripsi Barang</td>
						<td width="70%" class="text-bold">: <?= $h($resi['commodity_detail']) ?></td>
					</tr>
					<tr>
						<td>Jumlah Paket</td>
						<td class="text-bold">: <?= (int) $total_koli ?> Koli</td>
					</tr>
					<tr>
						<td>Layanan</td>
						<td class="text-bold">: <?= $h($resi['service_name'] ?? 'REGULAR AIR FREIGHT') ?></td>
					</tr>
				</table>

				<div class="address-container">
					<div class="address-box">
						<span class="addr-title">PENGIRIM</span>
						<div class="text-bold"><?= $h($resi['sender_name']) ?> (<?= $h($resi['sender_phone']) ?>)</div>
						<div style="font-size: 9px;"><?= $h($resi['sender_address']) ?></div>
					</div>
					<div class="address-box">
						<span class="addr-title">PENERIMA</span>
						<div class="text-bold"><?= $h($resi['receiver_name']) ?> (<?= $h($resi['receiver_phone']) ?>)</div>
						<div style="font-size: 9px;"><?= $h($resi['receiver_address']) ?></div>
					</div>
				</div>

				<div class="barcode-area">
					<img src="<?= $l['barcode'] ?>">
					<div class="no-resi-text"><?= $h($l['piece_id']) ?></div>
				</div>

				<div style="text-align: center; padding: 10px; border-top: 1px dashed #ccc; color: #888; font-style: italic;">
					Label Pengiriman Smesco Express - Resi 2 (Jakarta ke Tujuan)
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</body>

</html>
