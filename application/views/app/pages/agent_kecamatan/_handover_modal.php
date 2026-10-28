<div class="modal fade" id="modal-handover" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
		<form class="modal-content" id="form-handover">
			<div class="modal-header">
				<h5 class="modal-title">Serahkan ke Forwarder (<span id="handover-count">0</span> resi)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label required">AWB Forwarder</label>
					<input type="text" name="origin_courier_awb" autocapitalize="characters" class="form-control" required oninput="this.value=this.value.toUpperCase()">
					<div class="form-hint">Satu AWB berlaku untuk semua resi dalam serah-terima ini.</div>
				</div>
				<div class="mb-3">
					<label class="form-label">Cabang / Lokasi Forwarder</label>
					<input type="text" name="destination_hub" autocapitalize="characters" class="form-control" placeholder="INDAH KARGO KEC. ...">
				</div>
				<!-- <div class="row g-2">
					<div class="col-12 col-sm-6">
						<label class="form-label">Nama Kurir</label>
						<input type="text" name="forwarder_name" autocapitalize="characters" class="form-control" autocapitalize="characters">
					</div>
					<div class="col-12 col-sm-6">
						<label class="form-label">No. HP Kurir</label>
						<input type="tel" inputmode="tel" name="forwarder_phone" class="form-control">
					</div>
				</div> -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="submit" class="btn btn-primary btn-lg" id="btn-submit-handover">Serahkan</button>
			</div>
		</form>
	</div>
</div>

<script>
	let handoverIds = [];

	function openHandover(ids) {
		handoverIds = ids;
		$('#handover-count').text(ids.length);
		$('#form-handover')[0].reset();
		$('#modal-handover').modal('show');
	}

	$(document).on('submit', '#form-handover', function(e) {
		e.preventDefault();
		const btn = $('#btn-submit-handover').prop('disabled', true);
		const data = $(this).serializeArray();
		handoverIds.forEach(id => data.push({
			name: 'ids[]',
			value: id
		}));

		$.post("<?= site_url('agentkecamatan/handover_courier') ?>", data, function(res) {
			if (res.status) {
				$('#modal-handover').modal('hide');
				Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
			} else {
				Swal.fire('Gagal', res.message, 'error');
				btn.prop('disabled', false);
			}
		}, 'json').fail(function(xhr) {
			Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan saat handover.', 'error');
			btn.prop('disabled', false);
		});
	});
</script>
