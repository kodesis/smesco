<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// ----------------------------------------------------------------
// Helper lokal untuk section divider
// ----------------------------------------------------------------
function section_label($icon, $title, $subtitle = '')
{
	$sub = $subtitle ? '<div class="text-muted small mt-1">' . $subtitle . '</div>' : '';
	return '
	<div class="d-flex align-items-center gap-2 mb-3 mt-1">
		<span class="text-secondary" style="opacity:.5">' . tabler_icon($icon) . '</span>
		<div>
			<div class="fw-semibold text-uppercase ls-1" style="font-size:11px;letter-spacing:.08em">' . $title . '</div>
			' . $sub . '
		</div>
		<div class="flex-fill ms-2" style="height:1px;background:var(--tblr-border-color);opacity:.5"></div>
	</div>';
}
?>

<!-- ===== PAGE HEADER ===== -->
<div class="page-header d-print-none mb-3">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title text-uppercase ls-1">Dashboard</h2>
				<div class="text-muted small mt-1">Monitoring sistem, pengguna, dan operasional bisnis.</div>
			</div>
			<div class="col-auto d-flex align-items-center gap-2">
				<span class="badge bg-green-lt py-1 px-2">
					<?= tabler_icon('circle-filled', 'me-1 text-green') ?>
					Live
				</span>
				<span class="text-muted small"><?= date('l, d F Y') ?></span>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<!-- ================================================================
		     ZONA 1 — OPERASIONAL
		     User, Agent, Shipment, Omzet
		================================================================ -->
		<?= section_label('layout-dashboard', 'Operasional', 'Ringkasan pengguna, agen, dan pengiriman') ?>

		<!-- Stat Row 1: User & Agent -->
		<!-- <div class="row row-deck row-cards mb-2">
			<div class="col-6 col-xl-3">
				<?php include('partials/_stat_card.php') ?>
				<?php /* expects: $icon, $color, $value, $label — diset inline */ ?>
			</div>
		</div> -->

		<!-- Karena partial _stat_card butuh var injection, kita render langsung di sini -->
		<div class="row row-deck row-cards mb-2">
			<?php
			$stat_cards_row1 = [
				['icon' => 'users',       'color' => 'primary', 'value' => number_format($total_users),        'label' => 'Total User',    'sub' => $total_global_users . ' Kribo · ' . $total_agent_users . ' Mitra'],
				['icon' => 'building',    'color' => 'green',   'value' => number_format($total_agents),       'label' => 'Agen Aktif',    'sub' => null],
				['icon' => 'package',     'color' => 'azure',   'value' => number_format($shipment_stats->shipment_today ?? 0),  'label' => 'Shipment Hari Ini', 'sub' => null],
				['icon' => 'truck-delivery', 'color' => 'orange', 'value' => number_format($shipment_stats->shipment_active ?? 0), 'label' => 'Shipment Aktif',    'sub' => null],
			];
			foreach ($stat_cards_row1 as $c): ?>
				<div class="col-6 col-xl-3">
					<div class="card card-sm">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col-auto">
									<span class="bg-<?= $c['color'] ?> text-white avatar"><?= tabler_icon($c['icon']) ?></span>
								</div>
								<div class="col">
									<div class="fw-bold fs-4"><?= $c['value'] ?></div>
									<div class="text-muted small"><?= $c['label'] ?></div>
									<?php if ($c['sub']): ?>
										<div class="text-muted" style="font-size:10px;margin-top:1px"><?= $c['sub'] ?></div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Stat Row 2: Finance & API -->
		<div class="row row-deck row-cards mb-4">
			<?php
			$stat_cards_row2 = [
				['icon' => 'coin',              'color' => 'teal',   'value' => 'Rp ' . number_format($shipment_stats->omzet_bulan_ini ?? 0, 0, ',', '.'), 'label' => 'Omzet Bulan Ini',      'sub' => null],
				['icon' => 'api',               'color' => 'purple', 'value' => number_format($api_hits_today),   'label' => 'API Hits Hari Ini',    'sub' => $total_api_clients . ' klien aktif'],
				['icon' => 'eye',               'color' => 'blue',   'value' => number_format($visitor_stats->total_today ?? 0),    'label' => 'Request Hari Ini',     'sub' => number_format($visitor_stats->unique_ip_today ?? 0) . ' IP unik'],
				['icon' => 'shield-exclamation', 'color' => 'red',    'value' => number_format($visitor_stats->suspicious_today ?? 0), 'label' => 'Request Mencurigakan', 'sub' => number_format($visitor_stats->bot_today ?? 0) . ' bot terdeteksi'],
			];
			foreach ($stat_cards_row2 as $c): ?>
				<div class="col-6 col-xl-3">
					<div class="card card-sm <?= ($c['color'] === 'red' && ($visitor_stats->suspicious_today ?? 0) > 0) ? 'border-danger' : '' ?>">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col-auto">
									<span class="bg-<?= $c['color'] ?> text-white avatar"><?= tabler_icon($c['icon']) ?></span>
								</div>
								<div class="col">
									<div class="fw-bold fs-4"><?= $c['value'] ?></div>
									<div class="text-muted small"><?= $c['label'] ?></div>
									<?php if ($c['sub']): ?>
										<div class="text-muted" style="font-size:10px;margin-top:1px"><?= $c['sub'] ?></div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- ================================================================
		     ZONA 2 — ANALITIK
		     Chart API trend + Chart visitor berdampingan
		================================================================ -->
		<?= section_label('chart-bar', 'Analitik', 'Tren API dan pengunjung 7 hari terakhir') ?>

		<div class="row row-deck row-cards mb-4">
			<!-- Chart API Hits -->
			<div class="col-md-6">
				<div class="card h-100">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('chart-line', 'me-2') ?>Tren Hit API</h3>
						<div class="card-options">
							<span class="badge bg-purple-lt"><?= $total_api_clients ?> Klien</span>
						</div>
					</div>
					<div class="card-body">
						<div id="chart-api-hits"></div>
					</div>
				</div>
			</div>

			<!-- Chart Visitor Trend -->
			<div class="col-md-6">
				<div class="card h-100">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('activity', 'me-2') ?>Tren Pengunjung</h3>
						<div class="card-options">
							<span class="badge bg-blue-lt me-1">Manusia</span>
							<span class="badge bg-yellow-lt me-1">Bot</span>
							<span class="badge bg-red-lt">Suspicious</span>
						</div>
					</div>
					<div class="card-body">
						<div id="chart-visitor-trend"></div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================
		     ZONA 3 — DATA TERBARU
		     Agen terbaru + User terbaru
		================================================================ -->
		<?= section_label('database', 'Data Terbaru', 'Agen dan pengguna yang baru ditambahkan') ?>

		<div class="row row-deck row-cards mb-4">

			<!-- Agen Terbaru -->
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('building', 'me-2') ?>Agen Terbaru</h3>
						<div class="card-options">
							<a href="<?= site_url('agents') ?>" class="btn btn-sm btn-ghost-primary">Lihat Semua</a>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>Nama Agen</th>
									<th>Kode</th>
									<th>Kota</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($recent_agents): foreach ($recent_agents as $a): ?>
										<tr>
											<td><a href="<?= site_url('agents/detail/' . $a->id) ?>"><?= trim($a->name) ?></a></td>
											<td><span class="badge bg-blue-lt"><?= trim($a->code) ?></span></td>
											<td class="text-muted small"><?= trim($a->nama_kota) ?></td>
											<td>
												<span class="badge <?= $a->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
													<?= $a->is_active ? 'Aktif' : 'Nonaktif' ?>
												</span>
											</td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="4" class="text-center text-muted py-4">Belum ada data agen</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- User Terbaru -->
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('users', 'me-2') ?>User Terbaru</h3>
						<div class="card-options">
							<a href="<?= site_url('users') ?>" class="btn btn-sm btn-ghost-primary">Lihat Semua</a>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>Nama</th>
									<th>Role</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($recent_users):
									$role_color_map = [
										'superadmin'    => 'red',
										'admin-kribo'   => 'orange',
										'finance-kribo' => 'yellow',
										'admin-mitra'   => 'blue',
										'staff-mitra'   => 'azure',
										'finance-mitra' => 'cyan',
										'tracker-mitra' => 'indigo',
									];
									$avatar_colors = [
										'red'       => ['bg' => '#fce8e8', 'color' => '#a52a2a'],
										'orange'    => ['bg' => '#fef0e6', 'color' => '#9d4a00'],
										'yellow'    => ['bg' => '#fdf5d3', 'color' => '#8a6800'],
										'blue'      => ['bg' => '#e6f1fb', 'color' => '#0c447c'],
										'azure'     => ['bg' => '#e8f4fd', 'color' => '#0d5d94'],
										'cyan'      => ['bg' => '#e3f5f9', 'color' => '#0a5f74'],
										'indigo'    => ['bg' => '#eaecfb', 'color' => '#3a3f9e'],
										'secondary' => ['bg' => '#f1f3f5', 'color' => '#6c757d'],
									];
									foreach ($recent_users as $u):
										$role_color = $role_color_map[$u->role_slug] ?? 'secondary';
										$ac         = $avatar_colors[$role_color];
										$words      = explode(' ', trim($u->name));
										$initials   = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
								?>
										<tr>
											<td>
												<div class="d-flex align-items-center">
													<span class="avatar avatar-sm me-3 fw-medium text-uppercase"
														style="background:<?= $ac['bg'] ?>;color:<?= $ac['color'] ?>;font-size:12px;position:relative;">
														<?= $initials ?>
														<span class="badge <?= $u->is_active ? 'bg-success' : 'bg-danger' ?>"
															style="position:absolute;bottom:1px;right:1px;width:8px;height:8px;border-radius:50%;padding:0;border:1.5px solid #fff;"></span>
													</span>
													<div>
														<a href="<?= site_url('users/detail/' . $u->id) ?>"><?= trim($u->name) ?></a>
														<div class="text-muted small"><?= trim($u->email) ?></div>
													</div>
												</div>
											</td>
											<td><span class="badge bg-<?= $role_color ?>-lt"><?= trim($u->role_name) ?></span></td>
											<td>
												<span class="badge <?= $u->is_active ? 'bg-success-lt' : 'bg-danger-lt' ?>">
													<?= $u->is_active ? 'Aktif' : 'Nonaktif' ?>
												</span>
											</td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="3" class="text-center text-muted py-4">Belum ada data user</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================
		     ZONA 4 — MONITORING & KEAMANAN
		     Activity log + Security side by side
		================================================================ -->
		<?= section_label('shield-lock', 'Monitoring & Keamanan', 'Log aktivitas sistem dan deteksi ancaman') ?>

		<div class="row row-deck row-cards mb-4">

			<!-- Activity Log -->
			<div class="col-lg-6">
				<div class="card h-100">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('history', 'me-2') ?>Aktivitas Sistem Terbaru</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>Waktu</th>
									<th>User</th>
									<th>Aksi</th>
									<th>IP</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($recent_logs): foreach ($recent_logs as $log): ?>
										<tr>
											<td class="text-muted small" style="white-space:nowrap"><?= date('d/m H:i', strtotime($log->created_at)) ?></td>
											<td class="fw-medium small"><?= trim($log->user_name ?? '—') ?></td>
											<td><span class="badge bg-azure-lt"><?= trim($log->action) ?></span></td>
											<td class="text-muted small font-monospace"><?= trim($log->ip_address) ?></td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="4" class="text-center text-muted py-4">Belum ada log aktivitas</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Security: Top IPs + Recent Suspicious -->
			<div class="col-lg-6 d-flex flex-column gap-3">

				<!-- Top Suspicious IPs -->
				<div class="card">
					<div class="card-header">
						<h3 class="card-title text-danger"><?= tabler_icon('shield-x', 'me-2') ?>Top IP Mencurigakan Hari Ini</h3>
						<?php if (($visitor_stats->suspicious_today ?? 0) > 0): ?>
							<div class="card-options">
								<span class="badge bg-danger"><?= $visitor_stats->suspicious_today ?> hit</span>
							</div>
						<?php endif; ?>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>IP Address</th>
									<th>Hit</th>
									<th>Method</th>
									<th>Terakhir</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($suspicious_ips): foreach ($suspicious_ips as $ip): ?>
										<tr>
											<td class="font-monospace small text-danger"><?= htmlspecialchars($ip->ip_address) ?></td>
											<td><span class="badge bg-red-lt"><?= $ip->hit_count ?>×</span></td>
											<td class="text-muted small"><?= htmlspecialchars(substr($ip->user_agent ?? '—', 0, 24)) ?>…</td>
											<td class="text-muted small"><?= date('H:i:s', strtotime($ip->last_seen)) ?></td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="4" class="text-center py-3">
											<span class="text-success"><?= tabler_icon('shield-check', 'me-1') ?>Tidak ada ancaman terdeteksi</span>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Recent Suspicious Requests -->
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><?= tabler_icon('alert-triangle', 'me-2') ?>Request Mencurigakan Terbaru</h3>
					</div>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<thead>
								<tr>
									<th>Waktu</th>
									<th>IP</th>
									<th>URI</th>
									<th>M</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($recent_suspicious): foreach ($recent_suspicious as $r): ?>
										<tr>
											<td class="text-muted small" style="white-space:nowrap"><?= date('d/m H:i', strtotime($r->created_at)) ?></td>
											<td class="font-monospace small"><?= htmlspecialchars($r->ip_address) ?></td>
											<td class="text-danger small" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($r->uri) ?>">
												<?= htmlspecialchars($r->uri) ?>
											</td>
											<td>
												<span class="badge <?= $r->method === 'POST' ? 'bg-orange-lt' : 'bg-blue-lt' ?>">
													<?= $r->method ?>
												</span>
											</td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="4" class="text-center text-muted py-3">Tidak ada request mencurigakan</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

			</div><!-- /col security -->
		</div><!-- /row monitoring -->

	</div><!-- /container-xl -->
</div><!-- /page-body -->


<!-- ================================================================
     SCRIPTS — ApexCharts
================================================================ -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
	(function() {
		// ── Shared helpers ──────────────────────────────────────────
		function buildDays(count) {
			const days = [],
				keys = [];
			for (let i = count - 1; i >= 0; i--) {
				const d = new Date();
				d.setDate(d.getDate() - i);
				keys.push(d.toISOString().slice(0, 10));
				days.push(d.toLocaleDateString('id-ID', {
					day: '2-digit',
					month: 'short'
				}));
			}
			return {
				days,
				keys
			};
		}

		const sharedChart = {
			toolbar: {
				show: false
			},
			fontFamily: 'inherit',
			animations: {
				enabled: true,
				speed: 600
			},
		};

		const sharedGrid = {
			borderColor: '#f1f3f5',
			strokeDashArray: 4,
			padding: {
				left: 4,
				right: 4
			},
		};

		// ── Chart 1: API Hits ────────────────────────────────────────
		(function() {
			const raw = <?= $api_trend_json ?>;
			const {
				days,
				keys
			} = buildDays(7);
			const counts = keys.map(k => {
				const f = raw.find(r => r.date === k);
				return f ? parseInt(f.total) : 0;
			});

			new ApexCharts(document.getElementById('chart-api-hits'), {
				chart: {
					...sharedChart,
					type: 'area',
					height: 200
				},
				series: [{
					name: 'API Hits',
					data: counts
				}],
				xaxis: {
					categories: days,
					labels: {
						style: {
							fontSize: '11px'
						}
					}
				},
				yaxis: {
					min: 0,
					labels: {
						formatter: v => Math.round(v).toLocaleString('id-ID')
					}
				},
				fill: {
					type: 'gradient',
					gradient: {
						shadeIntensity: 1,
						opacityFrom: 0.35,
						opacityTo: 0.03
					}
				},
				stroke: {
					curve: 'smooth',
					width: 2
				},
				colors: ['#6366f1'],
				dataLabels: {
					enabled: false
				},
				tooltip: {
					y: {
						formatter: v => v.toLocaleString('id-ID') + ' hits'
					}
				},
				grid: sharedGrid,
			}).render();
		})();

		// ── Chart 2: Visitor Trend ───────────────────────────────────
		(function() {
			const raw = <?= $visitor_trend_json ?>;
			const {
				days,
				keys
			} = buildDays(7);
			const human = keys.map(k => {
				const f = raw.find(r => r.date === k);
				return f ? parseInt(f.human) : 0;
			});
			const bot = keys.map(k => {
				const f = raw.find(r => r.date === k);
				return f ? parseInt(f.bot) : 0;
			});
			const suspicious = keys.map(k => {
				const f = raw.find(r => r.date === k);
				return f ? parseInt(f.suspicious) : 0;
			});

			new ApexCharts(document.getElementById('chart-visitor-trend'), {
				chart: {
					...sharedChart,
					type: 'bar',
					height: 200,
					stacked: true
				},
				series: [{
						name: 'Manusia',
						data: human
					},
					{
						name: 'Bot',
						data: bot
					},
					{
						name: 'Suspicious',
						data: suspicious
					},
				],
				xaxis: {
					categories: days,
					labels: {
						style: {
							fontSize: '11px'
						}
					}
				},
				colors: ['#4299e1', '#f6c23e', '#e53e3e'],
				dataLabels: {
					enabled: false
				},
				legend: {
					position: 'top',
					fontSize: '11px'
				},
				plotOptions: {
					bar: {
						borderRadius: 3,
						columnWidth: '55%'
					}
				},
				tooltip: {
					y: {
						formatter: v => v.toLocaleString('id-ID') + ' req'
					}
				},
				grid: sharedGrid,
			}).render();
		})();
	})();
</script>
