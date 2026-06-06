<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header d-print-none mb-3">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">Console Konsolidasi Koli Udara</h2>
				<div class="text-muted small mt-1">
					AWB: <span class="fw-bold text-azure"><?= $awb->awb_number ?></span> |
					Flight: <span class="fw-bold"><?= $awb->flight_number ?></span> (<?= $awb->origin ?> &rarr; <?= $awb->destination ?>)
				</div>
			</div>
			<div class="col-auto ms-auto">
				<?php if ($awb->status === 'DRAFT'): ?>
					<button type="button" id="btn-add-karung" class="btn btn-success fw-bold">
						<?= tabler_icon('package', 'me-1') ?> Bikin Karung Baru
					</button>

					<button type="button" id="btn-finalize-awb" class="btn btn-primary fw-bold ms-2">
						<?= tabler_icon('lock-check', 'me-1') ?> Kunci & Manifested
					</button>
				<?php endif; ?>
				<a href="<?= site_url('shipment/manifest') ?>" class="btn btn-outline-secondary ms-2">Selesai Console</a>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">

		<div class="row row-cards" id="console-koli-wrapper">
			<?php
			if (empty($kolis)): ?>
				<div class="col-12" id="empty-bag-alert">
					<div class="alert alert-warning text-center py-4 mb-0">
						<?= tabler_icon('info-circle', 'me-1') ?> Belum ada karung koli dibuat untuk penerbangan ini. Klik tombol <strong>Bikin Karung Baru</strong> di atas untuk memulai kemas barang!
					</div>
				</div>
				<?php else: foreach ($kolis as $k):

					$card_border = 'border-azure';
					$bg_header   = 'bg-azure-lt';

					if ($k->status === 'DEPARTED') {
						$card_border = 'border-success';
						$bg_header = 'bg-success-lt';
					}
					if ($k->status === 'OFFLOADED') {
						$card_border = 'border-danger';
						$bg_header = 'bg-danger-lt';
					} ?>
					<div class="col-md-6 koli-card-node" id="node-koli-<?= $k->id ?>">
						<div class="card border-azure shadow-sm">
							<div class="card-header bg-azure-lt d-flex justify-content-between align-items-center py-2">
								<div>
									<h3 class="card-title fw-bold text-azure"><?= $k->koli_number ?></h3>
									<!-- <span class="small text-muted">Total Timbangan: <strong class="text-dark"><span class="weight-display-id-<?= $k->id ?>"><?= number_format($k->actual_weight, 2) ?></span> Kg</strong></span> -->
								</div>
								<?php if ($k->status === 'OFFLOADED'): ?>
									<button type="button" class="btn btn-sm btn-danger fw-bold btn-trigger-reroute" data-koli-id="<?= $k->id ?>" data-koli-number="<?= $k->koli_number ?>">
										<?= tabler_icon('arrows-left-right', 'me-1') ?> Re-Route
									</button>
								<?php else: ?>
									<span class="badge bg-azure"><?= count($k->items) ?> Resi Inside</span>
								<?php endif; ?>
							</div>

							<div class="card-body p-2 bg-light">
								<?php if ($awb->status === 'DRAFT' && $k->status === 'DRAFT'): ?>
									<div class="input-group input-group-sm mb-2">
										<span class="input-group-text bg-white"><?= tabler_icon('barcode') ?></span>
										<input type="text" class="form-control form-control-sm scan-resi-trigger" data-koli-id="<?= $k->id ?>" placeholder="Scan Barcode / Ketik No. Resi di sini..." autocomplete="off">
									</div>
								<?php endif; ?>

								<div class="table-responsive bg-white rounded border" style="max-height: 250px; overflow-y: auto;">
									<table class="table table-vcenter card-table table-striped table-sm text-nowrap" id="table-items-koli-<?= $k->id ?>">
										<thead class="bg-light sticky-top">
											<tr style="font-size: 0.75rem;">
												<th>No. Resi</th>
												<th>Tujuan</th>
												<!-- <th class="text-center">Koli</th> -->
												<!-- <th class="text-end">Berat</th> -->
											</tr>
										</thead>
										<tbody>
											<?php if (empty($k->items)): ?>
												<tr class="empty-row-placeholder">
													<td colspan="2" class="text-center text-muted py-3 small">Karung kosong, silakan scan resi.</td>
												</tr>
												<?php else: foreach ($k->items as $item): ?>
													<tr>
														<td class="fw-bold font-monospace small"><?= $item->no_resi ?></td>
														<td><span class="badge bg-secondary-lt"><?= $item->destination ?></span></td>
														<!-- <td class="text-center small"><?= $item->koli ?> Pcs</td> -->
														<!-- <td class="text-end small fw-semibold"><?= number_format($item->chargeable_weight, 1) ?> Kg</td> -->
													</tr>
											<?php endforeach;
											endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
			<?php endforeach;
			endif; ?>
		</div>

	</div>
</div>

<div class="modal modal-blur fade" id="modal-reroute-koli" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger-lt">
				<h5 class="modal-title text-danger fw-bold"><?= tabler_icon('plane-inflight', 'me-1') ?> Alihkan Penerbangan Karung <span id="mdl-title-koli-num"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-execute-reroute">
				<input type="hidden" name="koli_id" id="mdl-reroute-koli-id">
				<div class="modal-body text-dark">

					<div class="mb-3">
						<label class="form-label required">Pilih Console Penerbangan Baru</label>
						<select name="target_awb_id" id="target_awb_id" class="form-select" required>
						</select>
					</div>

					<div class="mb-3 d-none" id="container-select-karung-target">
						<label class="form-label required">Tujuan Karung di Pesawat Baru</label>
						<select name="target_action_koli" id="target_action_koli" class="form-select" required>
							<option value="NEW_KOLI">-- Buat Karung Urutan Baru di Pesawat Ini --</option>
						</select>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-danger fw-bold"><?= tabler_icon('transfer-in', 'me-1') ?> Eksekusi Alihkan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		const awbId = "<?= $awb->id ?>";

		// 1. Trigger Bikin Karung Koli Baru
		$('#btn-add-karung').on('click', function() {
			$.post("<?= site_url('shipment/ajax_create_koli') ?>", {
				awb_id: awbId
			}, function(res) {
				if (res.status) {
					Swal.fire({
						title: 'Berhasil!',
						text: res.message,
						icon: 'success',
						timer: 1000,
						showConfirmButton: false
					}).then(() => {
						location.reload(); // Reload halaman untuk memunculkan card karung baru
					});
				} else {
					Swal.fire('Opps', res.message, 'error');
				}
			}, 'json');
		});

		// 2. Handle Scanner Event (Tekan Enter di Input Scan Resi)
		$(document).on('keypress', '.scan-resi-trigger', function(e) {
			if (e.which === 13) { // Deteksi tombol ENTER / scanner break
				e.preventDefault();

				const inputNode = $(this);
				const noResi = inputNode.val().trim();
				const koliId = inputNode.data('koli-id');

				if (noResi === '') return;

				// Kunci input sementara biar ga double post pas scan cepat
				inputNode.prop('disabled', true);

				$.post("<?= site_url('shipment/ajax_bind_resi_to_koli') ?>", {
					no_resi: noResi,
					awb_koli_id: koliId
				}, function(res) {
					inputNode.prop('disabled', false).val('').focus(); // Reset form & balikin focus scanner

					if (res.status) {
						// Hapus placeholder kosong jika ada
						$(`#table-items-koli-${koliId} tbody .empty-row-placeholder`).remove();

						// Suntik Baris Resi Baru ke dalam Tabel secara Real-time
						const newRow = `
                  <tr>
                     <td class="fw-bold font-monospace small text-success">${res.data.no_resi}</td>
                     <td><span class="badge bg-secondary-lt">${res.data.destination}</span></td>
                     <td class="text-center small">${res.data.koli} Pcs</td>
                     <td class="text-end small fw-semibold">${res.data.weight} Kg</td>
                  </tr>
               `;
						$(`#table-items-koli-${koliId} tbody`).prepend(newRow);

						// Update angka timbangan berat karung di atas card
						$(`.weight-display-id-${koliId}`).text(res.data.updated_koli_weight);

						// Efek suara sukses jika mau (opsional, bisa pakai Web Audio API bawaan browser)
						playBeepSuccess();
					} else {
						playBeepError();
						Swal.fire('Gagal Packing', res.message, 'error');
					}
				}, 'json').fail(function() {
					inputNode.prop('disabled', false).val('').focus();
					Swal.fire('Error', 'Koneksi ke server terputus bro!', 'error');
				});
			}
		});

		// Audio Feedback untuk Operator Scanner Gudang
		function playBeepSuccess() {
			const context = new(window.AudioContext || window.webkitAudioContext)();
			const osc = context.createOscillator();
			osc.type = 'sine';
			osc.frequency.setValueAtTime(800, context.currentTime); // Suara beep tinggi pendek
			osc.connect(context.destination);
			osc.start();
			osc.stop(context.currentTime + 0.1);
		}

		function playBeepError() {
			const context = new(window.AudioContext || window.webkitAudioContext)();
			const osc = context.createOscillator();
			osc.type = 'sawtooth';
			osc.frequency.setValueAtTime(220, context.currentTime); // Suara buzzer rendah tanda error
			osc.connect(context.destination);
			osc.start();
			osc.stop(context.currentTime + 0.3);
		}

		// Handle Klik Kunci & Manifested
		$('#btn-finalize-awb').on('click', function() {
			Swal.fire({
				title: 'Kunci Console AWB?',
				text: 'Proses scanning akan ditutup dan status AWB akan berubah menjadi MANIFESTED.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, Kunci!',
				confirmButtonColor: '#206bc4'
			}).then(result => {
				if (result.isConfirmed) {
					$.post("<?= site_url('shipment/ajax_finalize_awb') ?>", {
						awb_id: awbId
					}, function(res) {
						if (res.status) {
							Swal.fire('Berhasil!', res.message, 'success').then(() => {
								window.location.href = "<?= site_url('shipment/manifest') ?>"; // Lempar ke monitoring
							});
						} else {
							Swal.fire('Gagal', res.message, 'error');
						}
					}, 'json');
				}
			});
		});

		// Ketika tombol Re-Route di-klik
		$(document).on('click', '.btn-trigger-reroute', function() {
			const koliId = $(this).data('koli-id');
			const koliNum = $(this).data('koli-number');

			$('#mdl-reroute-koli-id').val(koliId);
			$('#mdl-title-koli-num').text(koliNum);
			$('#target_awb_id').html('<option value="">Memuat penerbangan aktif...</option>');
			$('#modal-reroute-koli').modal('show');

			// Ambil daftar Master AWB yang statusnya masih DRAFT (Siap menampung koli alihan)
			$.get("<?= site_url('shipment/ajax_get_draft_awb') ?>", function(res) {
				if (res.status) {
					let html = '<option value="">- Pilih AWB / Flight Penerbangan -</option>';
					res.data.forEach(awb => {
						html += `<option value="${awb.id}">${awb.awb_number} (${awb.flight_number}) | Rute: ${awb.origin}-${awb.destination}</option>`;
					});
					$('#target_awb_id').html(html);
				} else {
					$('#target_awb_id').html('<option value="">Tidak ada penerbangan berstatus DRAFT saat ini</option>');
				}
			}, 'json');
		});

		// Ketika AWB Target dipilih, load daftar karungnya
		$('#target_awb_id').on('change', function() {
			const targetAwbId = $(this).val();
			if (!targetAwbId) {
				$('#container-select-karung-target').addClass('d-none');
				return;
			}

			// Ambil daftar karung yang sudah ada di AWB target tersebut
			$.get("<?= site_url('shipment/ajax_get_koli_by_awb/') ?>" + targetAwbId, function(res) {
				let html = '<option value="NEW_KOLI">-- Buat Karung Urutan Baru di Pesawat Ini --</option>';
				if (res.status && res.data.length > 0) {
					res.data.forEach(k => {
						html += `<option value="${k.id}">Masukkan/Lebur ke dalam karung: ${k.koli_number}</option>`;
					});
				}
				$('#target_action_koli').html(html);
				$('#container-select-karung-target').removeClass('d-none');
			}, 'json');
		}); 

		// Submit Form Re-Route Execution
		$('#form-execute-reroute').on('submit', function(e) {
			e.preventDefault();
			const btnSubmit = $(this).find('button[type="submit"]');
			btnSubmit.prop('disabled', true).text('Mengalihkan...');

			$.post("<?= site_url('shipment/ajax_execute_reroute_koli') ?>", $(this).serialize(), function(res) {
				btnSubmit.prop('disabled', false).html('Eksekusi Alihkan');
				if (res.status) {
					$('#modal-reroute-koli').modal('hide');
					Swal.fire('Berhasil Dialihkan! ✈️', res.message, 'success').then(() => location.reload());
				} else {
					Swal.fire('Gagal Alihkan', res.message, 'error');
				}
			}, 'json');
		});
	});
</script>
