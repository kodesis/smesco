const flashdata = $(".flash-data").data("flashdata");
if (flashdata) {
	Swal.fire({
		title: "Success!! ",
		text: flashdata,
		// type: "success",
		icon: "success",
	});
}

const flashdata_error = $(".flash-data-error").data("flashdata");
if (flashdata_error) {
	Swal.fire({
		title: "Error!! ",
		text: flashdata_error,
		// type: "error",
		icon: "error",
	});
}

// jquery tolong carikan btn-delete yang ketika diklik jalankan fungsi berikut ini
$(document).ready(function () {

	function checkScreen() {
		var mode = window.innerWidth < 768 ? 'mobile' : 'desktop';

		// Cek apakah mode berubah atau pertama kali akses
		if (mode !== localStorage.getItem("viewMode") || !localStorage.getItem("viewMode")) {
			localStorage.setItem("viewMode", mode);
			loadOrders(mode);
		}
	}


	$(".btn-delete").on("click", function (e) {
		e.preventDefault();
		const href = $(this).attr("href");

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, delete it!",
		}).then((result) => {
			if (result.isConfirmed) {
				document.location.href = href;
			}
		});
	});

	// jquery tolong carikan btn-process yang ketika diklik jalankan fungsi berikut ini
	$(".btn-process").on("click", function (e) {
		e.preventDefault();
		const href = ($(this).attr("href")) ? $(this).attr("href") : $(this).data("href");

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, process it!",
		}).then((result) => {
			if (result.isConfirmed) {

				Swal.fire({
					title: "Loading...",
					timerProgressBar: true,
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					},
				});

				document.location.href = href;
			}
		});
	});

	// sweetalert logout
	$(".btn-logout").on("click", function (e) {
		e.preventDefault();
		const href = $(this).attr("href");

		Swal.fire({
			title: "Are you sure?",
			// text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, sign me out!",
		}).then((result) => {
			if (result.isConfirmed) {

				Swal.fire({
					title: "Loading...",
					timerProgressBar: true,
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					},
				});
				document.location.href = href;
			}
		});
	});

	$(".btn-confirm").on("click", function (e) {
		e.preventDefault();
		const form = $(this).parents("form");

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, confirm!",
		}).then((result) => {
			if (result.isConfirmed) form.submit();
		});
	});

	$(document).on("click", ".btn-submit", function (e) {
		e.preventDefault();
		const form = $(this).parents("form");


		// Validasi semua input form
		let inputs = form.find('input, select');
		let valid = true;

		inputs.each(function () {
			if (!$(this).val()) {
				$(this).addClass('is-invalid');
				valid = false;
			} else {
				$(this).removeClass('is-invalid').addClass('is-valid');
			}
		});

		// Jika ada input yang tidak valid, cegah submit dan tampilkan peringatan
		if (!valid) {
			Swal.fire({
				icon: 'error',
				text: 'Please fill out all required fields!'
			});
			return;
		}

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, confirm!",
		}).then((result) => {
			if (result.isConfirmed) {

				form.on("submit", function () {
					$(".btn-submit").prop('disabled', true);
					Swal.fire({
						title: "Loading...",
						timerProgressBar: true,
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						},
					});
				});

				form.submit();
			}
		});
	});

	$(".btn-submit-detail").on("click", function (e) {
		e.preventDefault(); // Mencegah form agar tidak di-submit secara otomatis

		const form = $(this).parents("form");
		var previousRow = $('.baris').last();
		var isEmpty = false;

		// Cek apakah ada input kosong di baris terakhir
		previousRow.find('input[type="text"]').each(function () {
			if ($(this).val().trim() === '') {
				isEmpty = true;
				return false;
			}
		});

		// Jika ada input kosong, tampilkan SweetAlert untuk memperingatkan pengguna
		if (isEmpty) {
			Swal.fire({
				icon: 'warning',
				title: 'Oops...',
				text: 'Harap isi minimal 1 baris detail!'
			});
			return;
		}

		// Validasi semua input form
		let inputs = form.find('input, select');
		let valid = true;

		inputs.each(function () {
			if (!$(this).val()) {
				$(this).addClass('is-invalid');
				valid = false;
			} else {
				$(this).removeClass('is-invalid').addClass('is-valid');
			}
		});

		// Validasi khusus untuk Select2 customer_id
		let customerValid = true;
		if (!$("#customer_id").val()) {
			$("#customer_id")
				.next(".select2-container")
				.find(".select2-selection")
				.addClass("is-invalid");
			customerValid = false;
		} else {
			$("#customer_id")
				.next(".select2-container")
				.find(".select2-selection")
				.removeClass("is-invalid")
				.addClass("is-valid");
		}

		// Jika ada input yang tidak valid, cegah submit dan tampilkan peringatan
		if (!valid || !customerValid) {
			Swal.fire({
				icon: "error",
				text: "Please fill out all required fields!",
			});
			return;
		}

		// Tampilkan SweetAlert untuk meminta konfirmasi dari pengguna
		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, confirm!"
		}).then((result) => {
			// Jika pengguna mengonfirmasi, submit form secara manual
			if (result.isConfirmed) {
				// Nonaktifkan tombol submit untuk menghindari double submit
				$(".btn-submit-detail").prop('disabled', true);

				// Tampilkan SweetAlert untuk loading
				Swal.fire({
					title: "Loading...",
					timerProgressBar: true,
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					},
				});

				// Submit form
				form.submit();
			}
		});
	});



	function handleChange() {
		tentukanChargeable();
		// hitungNominal();

		var origin = $("#origin").val();
		var destination = $("#destination").val();

		if (origin && destination) {
			fetchPrice(origin, destination);
		}
	}

	// $('#berat_timbang').on('input', handleChange);
	$("#berat_timbang").on("input", function () {
		let berat = $(this).val();
		if (berat === "") berat = 0;

		// Update text di sidebar
		$("#berat_timbang_summary").text(formatNumber(parseFloat(berat.replace(/\,/g, '')) || 0));
		
		handleChange();
	});
	$('#jenis_pengiriman').on('change', handleChange);


	$("#origin").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + 'booking/autocompleteOrigin',
				dataType: "json",
				data: {
					term: request.term
				},
				success: function (data) {
					response(data);
				}
			});
		},
		minLength: 2,
		select: function (event, ui) {
			$("#origin").val(ui.item.value);

			// Cek jika kedua inputan origin dan destination sudah diisi
			var origin = $("#origin").val();
			var destination = $("#destination").val();
			// var jenis_pengiriman = $("#jenis_pengiriman").val();

			if (origin && destination) {
				fetchPrice(origin, destination);
			}
		}
	});
	$("#destination").autocomplete({
		source: function (request, response) {

			var jenis_pengiriman = $("#jenis_pengiriman").val();
			$.ajax({
				url: base_url + 'booking/autocompleteDestination',
				dataType: "json",
				data: {
					term: request.term,
					jenis: jenis_pengiriman
				},
				success: function (data) {
					response(data);
				}
			});
		},
		minLength: 2,
		select: function (event, ui) {
			$("#destination").val(ui.item.value);

			// Cek jika kedua inputan origin dan destination sudah diisi
			var origin = $("#origin").val();
			var destination = $("#destination").val();
			// var jenis_pengiriman = $("#jenis_pengiriman").val();

			if (origin && destination) {
				fetchPrice(origin, destination);
			}
		}
	});

	// Fungsi untuk melakukan AJAX request dan mendapatkan harga
	function fetchPrice(origin, destination) {
		var chargeable = $("#chargeable").val();
		// var jenis_pengiriman = $("#jenis_pengiriman").val();
		$.ajax({
			type: 'POST',
			url: base_url + 'booking/getPrice',
			data: {
				origin: origin,
				destination: destination,
				// jenis_pengiriman: jenis_pengiriman,
				chargeable: chargeable,
			},
			cache: false,
			success: function (response) {
				var data = JSON.parse(response);

				var per_kg = parseFloat(data.per_kg) || 0; // Handle null or NaN
				var harga_up = parseFloat(data.harga_up) || 0; // Handle null or NaN
				var harga_jual = parseFloat(data.harga_jual) || 0; // Handle null or NaN

				if (!isNaN(harga_up) && harga_up > 0) {
					$('#harga').removeClass('is-invalid').addClass('is-valid');
				} else {
					$('#harga').removeClass('is-valid').addClass('is-invalid');
				}

				$('#harga').val(per_kg);
				$('#harga_jual').val(harga_jual);
				$('#nominal').val(formatNumber(harga_up));
					
				$("#harga_label_summary").text(formatNumber(per_kg.toFixed(2)));
				$("#nominal_summary").text(formatNumber(harga_up.toFixed(2)));
				// nominal_summary

				var jenis_label = {
					D: "Domestik",
					IR: "International Regular",
					IE: "International Economic",
					IP: "International Premium",
				};

				console.log(data.jenis);
				$("#jenis_pengiriman").val(data.jenis);
				$("#jenis_label").val(jenis_label[data.jenis] || "-");
				// hitungNominal();
			},
			error: function () {
				console.log('Price not found');
				$('#harga').removeClass('is-valid').addClass('is-invalid');
			}
		});
	}

	// function hitungNominal() {
	// 	var chargeable = parseFloat($('#chargeable').val().replace(/\,/g, '')) || 0;
	// 	var harga = parseFloat($('#harga').val()) || 0;

	// 	var nominal = Math.round(harga); // default
	// 	var jenis = $('#jenis_pengiriman').val();

	// 	// Untuk IE, nominal bisa jadi hasil perkalian (kalau >= 21 kg)
	// 	if (jenis === 'IE' && chargeable >= 21) {
	// 		nominal = Math.round(chargeable * harga);
	// 	}

	// 	$('#nominal').val(formatNumber(nominal));
	// }

	// function hitungNominal() {
	// 	var chargeable = parseFloat($('#chargeable').val().replace(/\,/g, '')) || 0;
	// 	var harga = parseFloat($('#harga').val()) || 0;

	// 	var nominal = Math.round(harga);
	// 	$('#nominal').val(formatNumber(nominal));
	// }



	$(document).on('change input', 'input[name="panjang[]"], input[name="lebar[]"], input[name="tinggi[]"], input[name="jumlah[]"]', function (event) {
		$(this).val(function (index, value) {
			return value.replace(/(?!\.)\D/g, "")
				.replace(/(?<=\..*)\./g, "")
				.replace(/(?<=\.\d\d).*/g, "")
				.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		});

		var row = $(this).closest('.baris');

		hitungTotal(row);
		updateTotalRow();
	});

	function hitungTotal(row) {
		var panjang = row.find('input[name="panjang[]"]').val().replace(/\,/g, '');
		var lebar = row.find('input[name="lebar[]"]').val().replace(/\,/g, '');
		var tinggi = row.find('input[name="tinggi[]"]').val().replace(/\,/g, '');
		var jumlah = row.find('input[name="jumlah[]"]').val().replace(/\,/g, '');

		panjang = (panjang) || 0;
		lebar = (lebar) || 0;
		tinggi = (tinggi) || 0;
		jumlah = (jumlah) || 1;

		var volume = ((Number(panjang) * Number(lebar) * Number(tinggi)) / 5000) * Number(jumlah);

		row.find('input[name="volume[]"]').val(formatNumber(volume.toFixed(2)));
		updateTotalRow();
		tentukanChargeable();
		handleChange();
	}

	function updateTotalRow() {
		var total_koli = 0;
		var total_volume = 0;

		$(".baris").each(function () {
			var jumlah = $(this).find('input[name="jumlah[]"]').val().replace(/\,/g, ''); // Ambil nilai jumlah dari setiap baris
			var volume = $(this).find('input[name="volume[]"]').val().replace(/\,/g, ''); // Ambil nilai chargeable dari setiap baris

			jumlah = parseFloat(jumlah); // Ubah string ke angka float
			volume = parseFloat(volume); // Ubah string ke angka float

			if (!isNaN(jumlah)) { // Pastikan total adalah angka
				total_koli += jumlah; // Tambahkan nilai total ke total_koli
			}
			if (!isNaN(volume)) { // Pastikan total adalah angka
				total_volume += volume; // Tambahkan nilai total ke total_koli
			}
		});
		$('#total_qty').val(formatNumber(total_koli.toFixed(0))); // Atur nilai input #nominal dengan total_pos_fix
		$('#total_volume').val(formatNumber(total_volume.toFixed(2))); // Atur nilai input #total_chargeable dengan total_chwt
		
		$("#total_volume_summary").text(formatNumber(total_volume.toFixed(2)));

		tentukanChargeable();
		handleChange();
	}

	function tentukanChargeable() {
		var chargeable;

		var jenis_pengiriman = $("#jenis_pengiriman").val();

		// 1. Ambil nilai dan BERSIHKAN dari koma/titik ribuan
		var beratTimbangRaw = $("#berat_timbang").val().replace(/[,.]/g, "");
		var berat_timbang = parseFloat(beratTimbangRaw) || 0;

		var volumeRaw = $("#total_volume").val().replace(/[,.]/g, "");
		var volume = parseFloat(volumeRaw) || 0;

		// 2. Bandingkan
		if (berat_timbang >= volume) {
			chargeable = berat_timbang;
		} else {
			chargeable = volume;
		}

		// 3. Logic Domestik minimal 10 kg
		if (jenis_pengiriman === "D") {
			chargeable = chargeable < 10 ? 10 : chargeable;
		}

		// Bulatkan ke atas (Ceil) untuk chargeable (misal 10.2 kg jadi 11 kg)
		chargeable = Math.ceil(chargeable);

		// 4. Update ke tampilan & input hidden (tanpa koma untuk dikirim via AJAX)
		$("#chargeable").val(chargeable); // Ini dikirim via AJAX, jadi JANGAN DIBIKIN KOMA
		$("#chargeable_summary").text(formatNumber(chargeable)); // Ini buat visual aja

		// hitungNominal(); <-- Kalau butuh, buka komennya
	}

	function formatNumber(number) {
		return new Intl.NumberFormat("en-US").format(number);
	}

	var rowCount = 1; // Inisialisasi row
	function updateRowNumbers() {
		$('#table-body .baris').each(function (index) {
			$(this).find('.nomor-urut').text(index + 1 + '.');
		});
	}
	updateRowNumbers();

	$('#addRow').on('click', function () {
		var isEmpty = false;

		// Cek apakah ada input yang kosong di semua baris sebelumnya
		$('.baris').each(function () {
			var inputs = $(this).find('input[type="text"]');

			inputs.each(function () {
				if ($(this).val().trim() === '') {
					isEmpty = true;
					return false; // Berhenti iterasi jika ditemukan input kosong
				}
			});

			if (isEmpty) return false; // Jika ada input kosong, hentikan loop
		});

		// Jika ada input yang kosong, tampilkan pesan peringatan
		if (isEmpty) {
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
			});
			return; // Hentikan penambahan baris baru
		}

		// Salin baris terakhir
		var previousRow = $('.baris').last();
		var newRow = previousRow.clone(true, true);

		// Kosongkan nilai input di baris baru
		newRow.find('input').val('');
		newRow.find('input[name="panjang[]"]').val('0');
		newRow.find('input[name="lebar[]"]').val('0');
		newRow.find('input[name="tinggi[]"]').val('0');
		newRow.find('input[name="jumlah[]"]').val('1');
		newRow.find('input[name="volume[]"]').val('0');

		rowCount++;

		// Tambahkan baris baru setelah baris terakhir
		previousRow.after(newRow);

		updateRowNumbers();
	});


	// Tambahkan event listener untuk tombol hapus row
	$(document).on("click", ".hapusRow", function (e) {
		// Tambahkan parameter 'e' di sini
		e.preventDefault(); // <--- INI KUNCINYA, BRO! Mencegah form submit otomatis

		if ($(".baris").length > 1) {
			// Sisakan minimal satu baris
			$(this).closest(".baris").remove();
			updateTotalRow();
			updateRowNumbers();
		} else {
			Swal.fire({
				icon: "error",
				title: "Gak bisa, bro!",
				text: "Minimal harus ada satu baris detail barang.",
			});
		}
	});

	$("#nama_pengirim").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + "booking/autocompleteCustomer",
				dataType: "json",
				data: {
					term: request.term
				},
				success: function (data) {
					response(data);
				}
			});
		},
		minLength: 2,
		select: function (event, ui) {
			$("#nama_pengirim").val(ui.item.nama_customer);
			$("#telepon_pengirim").val(ui.item.telepon_customer);
			$("#alamat_pengirim").val(ui.item.alamat_customer);
		}
	});

	$("#nama_penerima").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + "booking/autocompleteCustomer",
				dataType: "json",
				data: {
					term: request.term
				},
				success: function (data) {
					response(data);
				}
			});
		},
		minLength: 2,
		select: function (event, ui) {
			$("#nama_penerima").val(ui.item.nama_customer);
			$("#telepon_penerima").val(ui.item.telepon_customer);
			$("#alamat_penerima").val(ui.item.alamat_customer);
		}
	});

	$('.select2').select2();

	// Menggunakan mask untuk input uang
	$('.angka').mask('000.000.000.000.000', {
		reverse: true
	});

	// Tambahkan ini di document ready
	$('#is_berharga').on('change', function() {
		if($(this).is(':checked')) {
			$('#input_nilai_barang').show(300); // Muncul pelan
			$('#status_berharga').text('Ya (Berharga)');
			$('#nilai_barang').focus();
		} else {
			$('#input_nilai_barang').hide(200); // Sembunyi
			$('#status_berharga').text('Tidak');
			$('#nilai_barang').val('0'); // Reset ke 0
		}
	});
});

function reloadTable() {
	$(tableUser).DataTable().ajax.reload();
}

function swal_success(message) {
	Swal.fire({
		title: "Success!! ",
		text: message,
		icon: "success",
	});
}

function swal_error(message) {
	Swal.fire({
		title: "Failed ",
		text: message,
		icon: "error",
	});
}


function startTime() {
	const today = new Date();
	let day = today.getDay();
	let date = today.getDate();
	let month = today.getMonth();
	let year = today.getFullYear();
	let h = today.getHours();
	let m = today.getMinutes();
	let s = today.getSeconds();
	m = checkTime(m);
	s = checkTime(s);

	let days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
	let months = [
		"Januari",
		"Februari",
		"Maret",
		"April",
		"Mei",
		"Juni",
		"Juli",
		"Agustus",
		"September",
		"Oktober",
		"November",
		"Desember",
	];

	const dayName = days[day];
	const monthName = months[month];
	document.getElementById("timer").innerHTML =
		dayName +
		", " +
		date +
		" " +
		monthName +
		" " +
		year +
		" " +
		h +
		":" +
		m +
		":" +
		s +
		" WIB";
	setTimeout(startTime, 1000);
}

function startTimeEnglish() {
	const today = new Date();
	let day = today.getDay();
	let date = today.getDate();
	let month = today.getMonth();
	let year = today.getFullYear();
	let h = today.getHours();
	let m = today.getMinutes();
	let s = today.getSeconds();
	m = checkTime(m);
	s = checkTime(s);

	let days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	let months = [
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December",
	];


	const dayName = days[day];
	const monthName = months[month];
	document.getElementById("timer").innerHTML =
		dayName +
		", " +
		date +
		" " +
		monthName +
		" " +
		year +
		" " +
		h +
		":" +
		m +
		":" +
		s +
		" WIB";
	setTimeout(startTimeEnglish, 1000);
}

function checkTime(i) {
	if (i < 10) {
		i = "0" + i;
	} // add zero in front of numbers < 10
	return i;
}

const d = new Date();
let year = d.getFullYear();
document.getElementById("tahun").innerHTML = year;
