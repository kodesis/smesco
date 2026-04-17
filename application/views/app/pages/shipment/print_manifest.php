<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Surat Jalan - <?= $manifest->no_manifest ?></title>
	<style>
		/* CSS Khusus Printer Dot Matrix / Continuous Form */
		@page {
			margin: 10px;
		}

		/* Margin super tipis */
		body {
			font-family: 'Courier New', Courier, monospace;
			/* Font khas resi/struk */
			font-size: 13px;
			color: #000;
			background: #fff;
			margin: 0;
			padding: 10px;
		}

		.header {
			text-align: center;
			margin-bottom: 20px;
			border-bottom: 2px dashed #000;
			padding-bottom: 10px;
		}

		.logo {
			font-size: 24px;
			font-weight: bold;
			letter-spacing: 2px;
		}

		.sub-logo {
			font-size: 14px;
		}

		.doc-title {
			font-size: 18px;
			font-weight: bold;
			text-decoration: underline;
			margin-top: 10px;
		}

		.info-table {
			width: 100%;
			margin-bottom: 15px;
			font-size: 12px;
		}

		.info-table td {
			padding: 2px;
			vertical-align: top;
		}

		.data-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
			font-size: 12px;
		}

		.data-table th,
		.data-table td {
			border: 1px solid #000;
			padding: 6px;
			text-align: left;
		}

		.data-table th {
			text-align: center;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.font-bold {
			font-weight: bold;
		}

		.signature-area {
			width: 100%;
			margin-top: 30px;
			font-size: 12px;
		}

		.signature-box {
			width: 33.33%;
			float: left;
			text-align: center;
		}

		.signature-line {
			margin-top: 70px;
			text-decoration: underline;
			font-weight: bold;
		}

		.clearfix::after {
			content: "";
			clear: both;
			display: table;
		}

		/* Sembunyikan elemen ini dari layar, muncul hanya saat diprint */
		@media screen {
			body {
				max-width: 800px;
				margin: 20px auto;
				border: 1px solid #ccc;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			}

			.btn-print {
				display: block;
				width: 100%;
				padding: 10px;
				background: #0052cc;
				color: #fff;
				text-align: center;
				text-decoration: none;
				font-weight: bold;
				margin-bottom: 20px;
			}
		}

		@media print {
			.btn-print {
				display: none;
			}
		}
	</style>
</head>

<body>

	<a href="#" class="btn-print" onclick="window.print()">🖨️ CETAK SURAT JALAN</a>

	<div class="header">
		<div class="logo">SMESCO EXPRESS</div>
		<div class="sub-logo">Solusi Logistik Terpercaya</div>
		<div class="doc-title">SURAT JALAN / MANIFEST PICKUP</div>
	</div>

	<table class="info-table">
		<tr>
			<td width="15%">No. Manifest</td>
			<td width="35%">: <b><?= $manifest->no_manifest ?></b></td>
			<td width="15%">Pengirim (Agen)</td>
			<td width="35%">: <b><?= html_escape($manifest->agent_name ?? 'PUSAT') ?></b></td>
		</tr>
		<tr>
			<td>Tanggal</td>
			<td>: <?= date('d M Y H:i', strtotime($manifest->tanggal)) ?></td>
			<td>Forwarder</td>
			<td>: <?= html_escape($manifest->forwarder_name) ?> (<?= html_escape($manifest->forwarder_phone) ?>)</td>
		</tr>
		<tr>
			<td>Tujuan</td>
			<td>: <b><?= html_escape($manifest->receiver_name) ?></b></td>
			<td>Alamat Tujuan</td>
			<td>: <?= html_escape($manifest->receiver_address) ?></td>
		</tr>
	</table>

	<table class="data-table">
		<thead>
			<tr>
				<th width="5%">NO</th>
				<th width="20%">NO. RESI</th>
				<th width="15%">TUJUAN</th>
				<th width="30%">PENGIRIM</th>
				<th width="10%">KOLI</th>
				<th width="10%">BERAT</th>
				<th width="10%">CEK</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$no = 1;
			$t_koli = 0;
			$t_berat = 0;
			// Looping data item resi lu di sini
			foreach ($items as $item):
				$t_koli += $item->koli;
				$t_berat += $item->chargeable_weight;
			?>
				<tr>
					<td class="text-center"><?= $no++ ?></td>
					<td class="font-bold"><?= $item->no_resi ?></td>
					<td class="text-center"><?= $item->destination ?></td>
					<td><?= html_escape($item->sender_name) ?></td>
					<td class="text-center"><?= $item->koli ?></td>
					<td class="text-center"><?= $item->chargeable_weight ?> Kg</td>
					<td class="text-center">[&nbsp;&nbsp;&nbsp;&nbsp;]</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="4" class="text-right font-bold">TOTAL KESELURUHAN</td>
				<td class="text-center font-bold"><?= $t_koli ?></td>
				<td class="text-center font-bold"><?= $t_berat ?> Kg</td>
				<td></td>
			</tr>
		</tbody>
	</table>

	<div class="signature-area clearfix">
		<div class="signature-box">
			Diserahkan Oleh,<br><b>(Smesco Express)</b>
			<div class="signature-line">Staff Operasional</div>
		</div>
		<div class="signature-box">
			Dibawa Oleh,<br><b>(Forwarder / Supir)</b>
			<div class="signature-line"><?= strtoupper(html_escape($manifest->forwarder_name)) ?></div>
		</div>
		<div class="signature-box">
			Diterima Oleh,<br><b>(Gudang Tujuan)</b>
			<div class="signature-line"><?= strtoupper(html_escape($manifest->receiver_name)) ?></div>
		</div>
	</div>

	<script>
		window.onload = function() {
			window.print();
		}
	</script>
</body>

</html>
