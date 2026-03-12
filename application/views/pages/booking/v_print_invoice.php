<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title_pdf; ?></title>
    <style>
        body {

            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        #table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #table td,
        #table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #table tr:hover {
            background-color: #ddd;
        }

        #table th {
            padding-top: 10px;
            padding-bottom: 10px;
            text-align: left;
            background-color: #32a834;
            color: white;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        table.table-top {
            /* border: 1px #000 solid;0 */
            width: 100%;
            /* text-align: center; */
            border-collapse: collapse;
        }

        .table-top td {
            border: 1px #000 solid;
            padding: 5px;
        }

        .table-top th {
            background-color: #ddd;
            font-weight: bold;
            border: 1px #000 solid;
            padding: 5px;
            text-align: center;
        }

        table.table-customer {
            /* border: 1px #000 solid;0 */
            width: 50%;
            /* text-align: center; */
            border-collapse: collapse;
        }


        .table-customer td {
            padding: 2px;
        }

        .w-100 {
            width: 100px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mb-50 {
            margin-bottom: 50px;
        }
    </style>
</head>

<body>

    <table style="margin-bottom: -10px; width: 100%">
        <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <img src="<?= base_url(); ?>assets/logo/logo-smesco.png" style="width: 150px;" alt="">
                    <h3>Smesco Express</h3>
                    <p>
                        Jl. Andara Raya No.1 A, Pondok Labu,
                        Kec. Cilandak, Kota Jakarta Selatan, <br>
                        Daerah Khusus Ibukota Jakarta 12450 <br>
                    </p>
                </td>
                <td class="text-right" style="vertical-align: top; width: 300px;">
                    <h3>INVOICE</h3>

                    <table class="table-top">
                        <tr>
                            <th class="bg-hover">Date</th>
                            <th class="bg-hover">Booking #</th>
                        </tr>
                        <tr>
                            <td class="text-center"><?= format_indo(date('Y-m-d', strtotime($booking['created_at']))) ?></td>
                            <td class="text-center"><?= $booking['no_booking'] ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="width: 400px;">
                </td>

            </tr>
        </tbody>
    </table>
    <h4 style="margin-bottom: 2px;">Bill to</h4>
    <table class="table-customer mb-20">
        <tr>
            <td class="bg-hover w-100">Name</td>
            <td class="bg-hover">: <?= $booking['nama_customer'] ?></td>
        </tr>
        <tr>
            <td class="bg-hover">Address</td>
            <td class="bg-hover">: <?= $booking['alamat_customer'] ?></td>
        </tr>
        <tr>
            <td class="bg-hover">Telp</td>
            <td class="bg-hover">: <?= $booking['telepon_customer'] ?></td>
        </tr>
    </table>
    <table class="table-top mb-50">
        <thead>
            <tr>
                <th>#</th>
                <th>Resi</th>
                <th>Qty</th>
                <th>Chargeable</th>
                <th>Price</th>
                <th>Pickup</th>
                <th>Dooring</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $subtotal = 0;
            $ppn = 0;
            $grandtotal = 0;
            foreach ($resi as $d) :
            ?>
                <tr>
                    <td class="text-right"><?= $no++ ?>.</td>
                    <td><?= $d->slug ?></td>
                    <td class="text-right"><?= number_format($d->qty) ?></td>
                    <td class="text-right"><?= number_format($d->chargeable) ?></td>
                    <td class="text-right"><?= number_format($d->nominal) ?></td>
                    <td class="text-right"><?= number_format($d->pickup_price) ?></td>
                    <td class="text-right"><?= number_format($d->dooring_price) ?></td>
                    <td class="text-right"><?= number_format($d->total_amount) ?></td>
                </tr>
            <?php
                $subtotal += $d->total_amount;
            endforeach;
            $ppn = round($subtotal * 0.011);
            $grandtotal = $subtotal + $ppn;
            ?>
            <tr>
                <td colspan="7" class="">SUBTOTAL</td>
                <td class="text-right"><?= number_format($subtotal) ?></td>
            </tr>
            <tr>
                <td colspan="7" class="">PPn 1.1%</td>
                <td class="text-right"><?= number_format($ppn) ?></td>
            </tr>
            <tr>
                <td colspan="7" class="">GRAND TOTAL</td>
                <td class="text-right"><b><?= number_format($grandtotal) ?></b></td>
            </tr>
            <tr>
                <td colspan="8"><b><?= terbilang($grandtotal) ?> Rupiah</b></td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; border: 0px #000 solid;">
        <tr>
            <td style="vertical-align: top;">
                <h4>Payment Information: </h4>
                <p style="margin-top: 0px;">Payment for this invoice should be <br>
                    transferred to the account:
                <table>
                    <tr>
                        <td>Bank ***</td>
                    </tr>
                    <tr>
                        <td>Bank name</td>
                        <td>: ***</td>
                    </tr>
                    <tr>
                        <td>Account</td>
                        <td>: ***</td>
                    </tr>
                </table>
                </p>
            </td>
            <td style="vertical-align: top; width: 200px">
                <h4>Regards,</h4>
                <h4 style=" text-decoration: underline; margin-top: 50px">Finance</h4>
                <!-- <p style="margin-top: -10px;">Finance Dept.</p> -->
            </td>
        </tr>
    </table>


</body>

</html>