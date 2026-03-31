<!-- v_tracking.php -->
<style>
    :root {
        --navy: #193b5c;
        --navy-dark: #0f2740;
        --navy-light: #1e4a73;
        --slate: #4d545e;
        --slate-light: #6b7480;
        --off-white: #f7f8fa;
        --border: #e2e6ea;
        --accent: #e8f0f8;
        --muted: #adb5bd;
    }

    /* ── Page Hero ───────────────────────────────── */
    .page-hero {
        background: linear-gradient(rgba(6,3,21,.5), rgba(6,3,21,.5)),
                    url(<?= base_url() ?>assets/front/img/about-us.jpg) center center no-repeat;
        background-size: cover;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .page-hero::after {
        content: '';
        position: absolute;
        right: -80px;
        top: -80px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        pointer-events: none;
    }

    .page-hero h1 {
        font-family: 'Roboto', sans-serif;
        font-size: 3rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
    }

    .page-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
    .page-hero .breadcrumb-item a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 13px; transition: color 0.2s; }
    .page-hero .breadcrumb-item a:hover { color: #fff; }
    .page-hero .breadcrumb-item.active { color: rgba(255,255,255,0.9); font-size: 13px; }
    .page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.35); }

    /* ── Main page ───────────────────────────────── */
    .tracking-page { background: var(--off-white); padding: 48px 0 64px; }

    /* ── Form cari resi ──────────────────────────── */
    .track-form-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 32px 36px;
        margin-bottom: 32px;
    }

    .track-form-card .card-eyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--navy);
        margin-bottom: 4px;
    }

    .track-form-card h3 {
        font-family: 'Roboto', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--navy-dark);
        margin-bottom: 20px;
    }

    .track-input-wrap { display: flex; gap: 10px; align-items: stretch; }

    .track-input {
        flex: 1;
        border: 1px solid var(--border);
        border-radius: 4px;
        height: 48px;
        font-size: 15px;
        padding: 0 16px;
        color: var(--navy-dark);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .track-input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(25,59,92,0.08); }

    .btn-track-submit {
        background: var(--navy);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        padding: 0 28px;
        border-radius: 4px;
        border: none;
        height: 48px;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-track-submit:hover { background: var(--navy-light); }

    /* ── Status badge ────────────────────────────── */
    .resi-status-badge {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .resi-status-badge.status-0 { background: rgba(255,255,255,0.15); color: #fff; }
    .resi-status-badge.status-1 { background: #6c757d; color: #fff; }
    .resi-status-badge.status-2 { background: #ffc107; color: #000; }
    .resi-status-badge.status-3 { background: #20c997; color: #fff; }
    .resi-status-badge.status-4 { background: #0dcaf0; color: #000; }
    .resi-status-badge.status-5 { background: #fd7e14; color: #fff; }
    .resi-status-badge.status-6 { background: #198754; color: #fff; }

    /* ── Info resi card ──────────────────────────── */
    .resi-info-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .resi-info-header {
        background: var(--navy);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .resi-info-header .resi-number {
        font-family: 'Roboto', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.5px;
    }

    .resi-info-body { padding: 20px 24px; }

    .resi-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
    }

    .resi-meta-item .meta-label {
        font-size: 11px;
        color: var(--slate-light);
        margin-bottom: 3px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .resi-meta-item .meta-value {
        font-size: 14px;
        color: var(--navy-dark);
        font-weight: 600;
        line-height: 1.3;
    }

    /* ── Timeline card ───────────────────────────── */
    .timeline-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 28px 32px;
    }

    .timeline-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .timeline-card-header h5 {
        font-family: 'Roboto', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--navy-dark);
        margin: 0;
    }

    .toggle-mode { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--slate-light); }

    .toggle-switch { position: relative; width: 36px; height: 20px; cursor: pointer; }
    .toggle-switch input { display: none; }
    .toggle-switch .slider { position: absolute; inset: 0; background: var(--border); border-radius: 20px; transition: 0.3s; }
    .toggle-switch .slider::before { content: ''; position: absolute; width: 14px; height: 14px; background: #fff; border-radius: 50%; left: 3px; top: 3px; transition: 0.3s; }
    .toggle-switch input:checked + .slider { background: var(--navy); }
    .toggle-switch input:checked + .slider::before { transform: translateX(16px); }

    /* ── Timeline ────────────────────────────────── */
    .timeline { position: relative; padding-left: 0; list-style: none; margin: 0; }

    .timeline::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border);
    }

    .tl-item { display: flex; gap: 20px; align-items: flex-start; padding-bottom: 28px; position: relative; }
    .tl-item:last-child { padding-bottom: 0; }

    .tl-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        transition: all 0.3s;
    }

    .tl-item.done .tl-icon { background: var(--navy); border: 2px solid var(--navy); }
    .tl-item.done .tl-icon svg { stroke: #fff; }
    .tl-item.active .tl-icon { background: #fff; border: 2px solid var(--navy); box-shadow: 0 0 0 4px rgba(25,59,92,0.1); }
    .tl-item.active .tl-icon svg { stroke: var(--navy); }
    .tl-item.pending .tl-icon { background: #fff; border: 2px solid var(--border); }
    .tl-item.pending .tl-icon svg { stroke: var(--muted); }

    .tl-content { flex: 1; padding-top: 6px; }

    .tl-title { font-size: 14px; font-weight: 700; margin-bottom: 2px; line-height: 1.3; }
    .tl-item.done .tl-title, .tl-item.active .tl-title { color: var(--navy-dark); }
    .tl-item.pending .tl-title { color: var(--muted); }

    .tl-subtitle { font-size: 12px; font-style: italic; margin-bottom: 6px; }
    .tl-item.done .tl-subtitle, .tl-item.active .tl-subtitle { color: var(--slate-light); }
    .tl-item.pending .tl-subtitle { color: var(--muted); }

    .tl-timestamp {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 4px;
        margin-bottom: 6px;
    }

    .tl-item.done .tl-timestamp { background: var(--accent); color: var(--navy); }
    .tl-item.active .tl-timestamp { background: #fff3cd; color: #856404; }
    .tl-item.pending .tl-timestamp { background: var(--off-white); color: var(--muted); }

    .tl-detail {
        font-size: 12px;
        color: var(--slate);
        line-height: 1.6;
        margin-top: 4px;
        padding: 8px 12px;
        background: var(--off-white);
        border-radius: 4px;
        border-left: 3px solid var(--border);
    }

    .tl-item.done .tl-detail { border-left-color: var(--navy); }
    .tl-item.active .tl-detail { border-left-color: #ffc107; }
    .tl-item.pending.hide-pending { display: none; }

    /* ── Sidebar ─────────────────────────────────── */
    .sidebar-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 20px 24px;
        margin-bottom: 16px;
    }

    .sidebar-card h6 {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--navy);
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }

    .sidebar-field { display: flex; flex-direction: column; margin-bottom: 12px; }
    .sidebar-field:last-child { margin-bottom: 0; }
    .sidebar-field .sf-label { font-size: 11px; color: var(--slate-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .sidebar-field .sf-value { font-size: 13px; color: var(--navy-dark); font-weight: 600; line-height: 1.4; }

    .route-arrow {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 14px;
        background: var(--accent);
        border-radius: 6px;
        margin-bottom: 14px;
    }

    .route-arrow .city { font-size: 14px; font-weight: 700; color: var(--navy-dark); flex: 1; }

    /* ── Timestamp summary table ─────────────────── */
    .ts-summary { background: var(--off-white); border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
    .ts-summary table { width: 100%; font-size: 12px; border-collapse: collapse; }
    .ts-summary table tr + tr { border-top: 1px solid var(--border); }
    .ts-summary table td { padding: 8px 12px; vertical-align: middle; }
    .ts-summary table td:first-child { color: var(--slate-light); font-weight: 600; width: 44%; }
    .ts-summary table td:last-child { color: var(--navy-dark); font-weight: 600; }
    .ts-summary .ts-done td:last-child { color: var(--navy); }
    .ts-summary .ts-pending td { color: var(--muted) !important; }

    @media (max-width: 768px) {
        .page-hero h1 { font-size: 2rem; }
        .page-hero { padding: 60px 0; }
        .track-form-card, .timeline-card { padding: 20px 16px; }
        .track-input-wrap { flex-direction: column; }
        .track-input, .btn-track-submit { width: 100%; }
        .timeline::before { left: 16px; }
    }
</style>

<!-- PAGE HERO -->
<div class="page-hero">
    <div class="container">
        <h1>Lacak Kiriman</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tracking</li>
            </ol>
        </nav>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="tracking-page">
    <div class="container">

        <!-- Form Cari Resi -->
        <div class="track-form-card wow fadeIn" data-wow-delay="0.1s">
            <div class="card-eyebrow">Smesco Express</div>
            <h3>Lacak Status Kiriman Anda</h3>
            <form method="POST" action="<?= base_url('home/track') ?>">
                <div class="track-input-wrap">
                    <input type="text"
                           name="nomor_resi"
                           id="nomor_resi"
                           class="track-input"
                           placeholder="Masukkan nomor resi, contoh: SMC2603250001"
                           value="<?= $this->input->post('nomor_resi') ?>"
                           autocomplete="off"
                           required>
                    <button type="submit" class="btn-track-submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;margin-right:6px;vertical-align:middle"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Lacak Sekarang
                    </button>
                </div>
                <div style="margin-top:14px;">
                    <div class="g-recaptcha" data-sitekey="6LcrQFQqAAAAAEa0DkcI1dUU0mxEt48SZ6LXMiic"></div>
                </div>
            </form>
        </div>

        <?php if ($this->session->flashdata('message')) : ?>
            <div class="alert alert-info" style="border-radius:6px;font-size:14px;">
                <?= $this->session->flashdata('message') ?>
            </div>
        <?php endif; ?>

        <?php if ($resi) :
            $status = (int) $resi['status_tracking'];

            // ── Status labels (0-6) ──────────────────
            $status_labels = [
                0 => 'Pesanan Dibuat',
                1 => 'Menunggu Penjemputan',
                2 => 'Barang Dijemput',
                3 => 'Tiba di Gudang Asal',
                4 => 'Dalam Pengiriman',
                5 => 'Tiba di Gudang Tujuan',
                6 => 'Terkirim ke Penerima',
            ];
            $status_label = $status_labels[$status] ?? 'Dalam Proses';

            // ── Helper format timestamp ──────────────
            function fmt_ts($val) {
                if (!$val || $val === '0000-00-00' || $val === '0000-00-00 00:00:00') return null;
                $ts = strtotime($val);
                if (!$ts) return null;
                $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                $d = (int)date('d', $ts);
                $m = $bulan[(int)date('m', $ts)];
                $y = date('Y', $ts);
                $show_time = strlen(trim($val)) > 10;
                $t = $show_time ? ', ' . date('H:i', $ts) . ' WIB' : '';
                return "$d $m $y$t";
            }

            // ── Timestamps per step (0-6) ────────────
            $timestamps = [
                0 => fmt_ts($resi['created_at']),
                1 => fmt_ts($resi['tanggal_pickup']),
                2 => fmt_ts($resi['tanggal_pickup']),
                3 => fmt_ts($resi['tanggal_tiba_gudang_asal'] ?? null),
                4 => fmt_ts($resi['tanggal_berangkat'] ?? null),
                5 => fmt_ts($resi['tanggal_tiba_gudang_tujuan'] ?? null),
                6 => fmt_ts($resi['tanggal_tiba_tujuan'] ?? null),
            ];

            // ── Steps config (7 steps, id 0-6) ──────
            $steps = [
                [
                    'id'        => 0,
                    'label'     => 'Pesanan Dibuat',
                    'label_en'  => 'Order Created',
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                    'detail'    => 'Booking telah dibuat dan sedang diproses oleh tim Smesco Express.',
                    'detail_en' => 'Booking has been created and is being processed by Smesco Express team.',
                ],
                [
                    'id'        => 1,
                    'label'     => 'Menunggu Penjemputan',
                    'label_en'  => 'Awaiting Pickup',
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'detail'    => 'Driver telah ditugaskan dan akan segera menjemput paket Anda.',
                    'detail_en' => 'Driver has been assigned and will pick up your package shortly.',
                ],
                [
                    'id'        => 2,
                    'label'     => 'Barang Dijemput',
                    'label_en'  => 'Package Picked Up',
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
                    'detail'    => 'Paket telah dijemput oleh driver dan sedang dalam perjalanan ke gudang.',
                    'detail_en' => 'Package has been picked up by driver and is on its way to the warehouse.',
                ],
                [
                    'id'        => 3,
                    'label'     => 'Tiba di Gudang Asal',
                    'label_en'  => 'Arrived at Origin Warehouse',
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
                    'detail'    => ($resi['moda_pengiriman'] && $resi['tanggal_berangkat'])
                        ? 'Paket akan dikirim via ' . ucfirst($resi['moda_pengiriman']) . ' pada ' . (fmt_ts($resi['tanggal_berangkat']) ?? '-') . ' dari ' . $resi['origin'] . ' menuju ' . $resi['destination'] . '.'
                        : 'Paket telah tiba di gudang asal dan menunggu keberangkatan.',
                    'detail_en' => ($resi['moda_pengiriman'] && $resi['tanggal_berangkat'])
                        ? 'Package will be shipped via ' . ucfirst($resi['moda_pengiriman']) . ' on ' . (fmt_ts($resi['tanggal_berangkat']) ?? '-') . ' from ' . $resi['origin'] . ' to ' . $resi['destination'] . '.'
                        : 'Package has arrived at origin warehouse, awaiting departure.',
                ],
                [
                    'id'        => 4,
                    'label'     => 'Dalam Pengiriman',
                    'label_en'  => 'In Transit',
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>',
                    'detail'    => 'Paket sedang dalam pengiriman menuju ' . ($resi['destination'] ?? 'kota tujuan') . ' via ' . ($resi['moda_pengiriman'] ? ucfirst($resi['moda_pengiriman']) : 'moda pengiriman') . '.',
                    'detail_en' => 'Package is in transit to ' . ($resi['destination'] ?? 'destination city') . ' via ' . ($resi['moda_pengiriman'] ? ucfirst($resi['moda_pengiriman']) : 'transport mode') . '.',
                ],
                [
                    'id'        => 5,
                    'label'     => 'Tiba di ' . ($resi['gudang_tujuan'] ?: 'Gudang Tujuan'),
                    'label_en'  => 'Arrived at ' . ($resi['gudang_tujuan'] ?: 'Destination Warehouse'),
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/><circle cx="18" cy="5" r="3"/></svg>',
                    'detail'    => 'Paket telah tiba di ' . ($resi['gudang_tujuan'] ?: 'gudang tujuan') . ' dan sedang disiapkan untuk pengiriman ke alamat penerima.',
                    'detail_en' => 'Package has arrived at ' . ($resi['gudang_tujuan'] ?: 'destination warehouse') . ' and is being prepared for delivery.',
                ],
                [
                    'id'        => 6,
                    'label'     => 'Terkirim ke Penerima',
                    'label_en'  => 'Delivered to Recipient',
                    'icon'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
                    'detail'    => 'Paket telah tiba dan diserahkan ke ' . ($resi['nama_penerima'] ?? 'penerima') . ' di ' . ($resi['alamat_penerima'] ?? 'alamat penerima') . '.',
                    'detail_en' => 'Package has been delivered to ' . ($resi['nama_penerima'] ?? 'recipient') . ' at ' . ($resi['alamat_penerima'] ?? 'recipient address') . '.',
                ],
            ];
        ?>

        <div class="row g-4">

            <!-- ── Kolom kiri: Timeline ──────────── -->
            <div class="col-lg-8">

                <!-- Resi Info Header -->
                <div class="resi-info-card wow fadeIn" data-wow-delay="0.1s">
                    <div class="resi-info-header">
                        <div>
                            <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-bottom:3px;letter-spacing:1px;text-transform:uppercase;">Nomor Resi</div>
                            <div class="resi-number"><?= $resi['no_resi'] ?></div>
                        </div>
                        <span class="resi-status-badge status-<?= $status ?>">
                            <?= $status_label ?>
                        </span>
                    </div>
                    <div class="resi-info-body">
                        <div class="resi-meta-grid">
                            <div class="resi-meta-item">
                                <div class="meta-label">Pengirim</div>
                                <div class="meta-value"><?= $resi['nama_pengirim'] ?></div>
                            </div>
                            <div class="resi-meta-item">
                                <div class="meta-label">Penerima</div>
                                <div class="meta-value"><?= $resi['nama_penerima'] ?></div>
                            </div>
                            <div class="resi-meta-item">
                                <div class="meta-label">Rute</div>
                                <div class="meta-value"><?= $resi['origin'] ?> &rarr; <?= $resi['destination'] ?></div>
                            </div>
                            <div class="resi-meta-item">
                                <div class="meta-label">Moda</div>
                                <div class="meta-value"><?= $resi['moda_pengiriman'] ? ucfirst($resi['moda_pengiriman']) : '-' ?></div>
                            </div>
                            <div class="resi-meta-item">
                                <div class="meta-label">Berat</div>
                                <div class="meta-value"><?= $resi['chargeable'] ?> kg</div>
                            </div>
                            <div class="resi-meta-item">
                                <div class="meta-label">Dibuat</div>
                                <div class="meta-value"><?= fmt_ts($resi['created_at']) ?? '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Card -->
                <div class="timeline-card wow fadeIn" data-wow-delay="0.15s">
                    <div class="timeline-card-header">
                        <h5>Riwayat Pengiriman</h5>
                        <div class="toggle-mode">
                            <span>Semua step</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="toggle-pending">
                                <span class="slider"></span>
                            </label>
                            <span id="toggle-label">Hanya dilalui</span>
                        </div>
                    </div>

                    <ul class="timeline" id="timeline-list">
                        <?php foreach ($steps as $step) :
                            $sid   = $step['id'];
                            $state = ($status > $sid) ? 'done' : (($status === $sid) ? 'active' : 'pending');
                            $ts_val = $timestamps[$sid] ?? null;
                        ?>
                        <li class="tl-item <?= $state ?> <?= $state === 'pending' ? 'hide-pending' : '' ?>">
                            <div class="tl-icon">
                                <?= $step['icon'] ?>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title"><?= $step['label'] ?></div>
                                <div class="tl-subtitle"><?= $step['label_en'] ?></div>

                                <div class="tl-timestamp">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?php if ($ts_val) : ?>
                                        <?= $ts_val ?>
                                    <?php elseif ($state === 'pending') : ?>
                                        Menunggu
                                    <?php else : ?>
                                        &mdash;
                                    <?php endif; ?>
                                </div>

                                <?php if ($state !== 'pending') : ?>
                                    <div class="tl-detail">
                                        <?= $step['detail'] ?>
                                        <br>
                                        <span style="font-style:italic;color:var(--slate-light)">
                                            <?= $step['detail_en'] ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>

            <!-- ── Kolom kanan: Sidebar ──────────── -->
            <div class="col-lg-4">

                <!-- Rute -->
                <div class="sidebar-card wow fadeIn" data-wow-delay="0.1s">
                    <h6>Informasi Rute</h6>
                    <div class="route-arrow">
                        <div class="city"><?= $resi['origin'] ?></div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--slate-light);flex-shrink:0"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        <div class="city" style="text-align:right"><?= $resi['destination'] ?></div>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Gudang Tujuan</span>
                        <span class="sf-value"><?= $resi['gudang_tujuan'] ?: '-' ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Moda Pengiriman</span>
                        <span class="sf-value"><?= $resi['moda_pengiriman'] ? ucfirst($resi['moda_pengiriman']) : '-' ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Tanggal Berangkat</span>
                        <span class="sf-value"><?= fmt_ts($resi['tanggal_berangkat']) ?? '-' ?></span>
                    </div>
                </div>

                <!-- Kronologi Waktu -->
                <div class="sidebar-card wow fadeIn" data-wow-delay="0.15s">
                    <h6>Kronologi Waktu</h6>
                    <div class="ts-summary">
                        <table>
                            <?php
                            $ts_rows = [
                                ['Booking dibuat',          $timestamps[0]],
                                ['Jadwal penjemputan',      $timestamps[1]],
                                ['Barang dijemput',         $timestamps[2]],
                                ['Tiba gudang asal',        $timestamps[3]],
                                ['Berangkat',               $timestamps[4]],
                                ['Tiba gudang tujuan',      $timestamps[5]],
                                ['Terkirim ke penerima',    $timestamps[6]],
                            ];
                            foreach ($ts_rows as $row) :
                                $cls = $row[1] ? 'ts-done' : 'ts-pending';
                            ?>
                            <tr class="<?= $cls ?>">
                                <td><?= $row[0] ?></td>
                                <td><?= $row[1] ?? '—' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <!-- Detail Paket -->
                <div class="sidebar-card wow fadeIn" data-wow-delay="0.2s">
                    <h6>Detail Paket</h6>
                    <div class="sidebar-field">
                        <span class="sf-label">Pengirim</span>
                        <span class="sf-value"><?= $resi['nama_pengirim'] ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Alamat Pengirim</span>
                        <span class="sf-value"><?= $resi['alamat_pengirim'] ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Penerima</span>
                        <span class="sf-value"><?= $resi['nama_penerima'] ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Alamat Penerima</span>
                        <span class="sf-value"><?= $resi['alamat_penerima'] ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Komoditi</span>
                        <span class="sf-value"><?= $resi['commodity'] ?></span>
                    </div>
                    <div class="sidebar-field">
                        <span class="sf-label">Berat Chargeable</span>
                        <span class="sf-value"><?= $resi['chargeable'] ?> kg</span>
                    </div>
                </div>

            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
$(function () {
    var $toggle = $('#toggle-pending');
    var $label  = $('#toggle-label');

    $toggle.prop('checked', false);
    $label.text('Hanya dilalui');

    $toggle.on('change', function () {
        var showAll = $(this).is(':checked');
        $label.text(showAll ? 'Tampil semua' : 'Hanya dilalui');

        if (showAll) {
            $('.tl-item.pending').removeClass('hide-pending');
        } else {
            $('.tl-item.pending').addClass('hide-pending');
        }
    });
});
</script>
