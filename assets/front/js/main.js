(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();


    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.sticky-top').css('top', '0px');
        } else {
            $('.sticky-top').css('top', '-100px');
        }
    });


    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";

    $(window).on("load resize", function () {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
                function () {
                    const $this = $(this);
                    $this.addClass(showClass);
                    $this.find($dropdownToggle).attr("aria-expanded", "true");
                    $this.find($dropdownMenu).addClass(showClass);
                },
                function () {
                    const $this = $(this);
                    $this.removeClass(showClass);
                    $this.find($dropdownToggle).attr("aria-expanded", "false");
                    $this.find($dropdownMenu).removeClass(showClass);
                }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });


    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
        return false;
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        items: 1,
        dots: false,
        loop: true,
        nav: true,
        navText: [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: false,
        smartSpeed: 1000,
        center: true,
        dots: true,
        loop: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });

})(jQuery);


$(document).ready(function () {
    $('#berat_timbang').on('input', function () {
        tentukanChargeable();
        hitungNominal();


        var origin = $("#origin").val();
        var destination = $("#destination").val();
        // var jenis_pengiriman = $("#jenis_pengiriman").val();

        if (origin && destination) {
            fetchPrice(origin, destination);
        }
    });

    $('#jenis_pengiriman').on('change', function () {
        tentukanChargeable();

        var origin = $("#origin").val();
        var destination = $("#destination").val();
        // var jenis_pengiriman = $("#jenis_pengiriman").val();

        if (origin && destination) {
            fetchPrice(origin, destination);
        }
    });

    $("#origin").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'home/autocompleteOrigin',
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
            var destination = $('#kota').find(":selected").data("nama");
            // var jenis_pengiriman = $("#jenis_pengiriman").val();

            if (origin && destination) {
                fetchPrice(origin, destination);
            }
        }
    });
    $("#destination").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'home/autocompleteDestination',
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
            url: base_url + 'home/getPrice',
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
                // hitungNominal();
            },
            error: function () {
                console.log('Price not found');
                $('#harga').removeClass('is-valid').addClass('is-invalid');
            }
        });
    }

    function hitungNominal() {
        var chargeable = parseFloat($('#chargeable').val().replace(/\,/g, '')) || 0;
        // var harga = parseFloat($('#harga').val().replace(/\,/g, '')) || 0;
        var harga = parseFloat($('#harga').val()) || 0;

        var nominal;

        nominal = Math.round(chargeable * harga);
        $('#nominal').val(formatNumber(nominal));
    }

    $(document).on('change click keyup input paste', 'input[name="panjang[]"], input[name="lebar[]"], input[name="tinggi[]"], input[name="jumlah[]"]', function (event) {
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
        tentukanChargeable()
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

        tentukanChargeable();
    }

    function tentukanChargeable() {
        var chargeable;

        var jenis_pengiriman = $("#jenis_pengiriman").val();

        var berat_timbang = parseFloat($('#berat_timbang').val().replace(/\,/g, '')) || 0;

        var volume = parseFloat($('#total_volume').val().replace(/\,/g, '')) || 0;

        if (berat_timbang >= volume) {
            chargeable = berat_timbang;
        } else {
            chargeable = volume;
        }

        if (jenis_pengiriman == 'D') {
            chargeable = (chargeable < 10) ? 10 : chargeable;
        } else {
            chargeable = chargeable;
        }

        $('#chargeable').val(formatNumber(chargeable));

        hitungNominal();
    }

    function formatNumber(number) {
        // Pisahkan bagian integer dan desimal
        let parts = number.toString().split(",");

        // Format bagian integer dengan pemisah ribuan
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        // Gabungkan bagian integer dan desimal dengan koma sebagai pemisah desimal
        return parts.join(",");
    }



    var rowCount = 1; // Inisialisasi row
    function updateRowNumbers() {
        $('#table-body .baris').each(function (index) {
            $(this).find('.nomor-urut').text(index + 1 + '.');
        });
    }
    updateRowNumbers();

    $('#addRow').on('click', function () {
        // Periksa apakah ada input yang kosong di baris sebelumnya
        var previousRow = $('.baris').last();
        var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
        var isEmpty = false;

        inputs.each(function () {
            if ($(this).val().trim() === '') {
                isEmpty = true;
                return false; // Berhenti iterasi jika ditemukan input kosong
            }
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
        var newRow = previousRow.clone();

        // Kosongkan nilai input di baris baru
        newRow.find('input').val('');
        newRow.find('input[name="panjang[]"]').val('0');
        newRow.find('input[name="lebar[]"]').val('0');
        newRow.find('input[name="tinggi[]"]').val('0');
        newRow.find('input[name="jumlah[]"]').val('1');
        newRow.find('input[name="volume[]"]').val('0');

        // Perbarui tag <h4> pada baris baru dengan nomor urut yang baru
        rowCount++;

        // Tambahkan baris baru setelah baris terakhir
        previousRow.after(newRow);

        updateRowNumbers();
    });

    // Tambahkan event listener untuk tombol hapus row
    $(document).on('click', '.hapusRow', function () {
        $(this).closest('.baris').remove();
        updateTotalRow(); // Perbarui total belanja setelah menghapus baris

        updateRowNumbers();
    });

    $.ajax({
        type: 'POST',
        url: base_url + 'registration/getProvinsi',
        cache: false,
        success: function (msg) {
            $('#provinsi').html(msg);
        }
    });

    $('#alamat_email').on('input', function () {
        var email = $(this).val();

        $.ajax({
            type: 'POST',
            url: base_url + 'registration/checkEmail',
            data: {
                email: email
            },
            cache: false,
            success: function (response) {
                var isEmailAvailable = response == 0;

                $('#alamat_email')
                    .toggleClass('is-invalid', !isEmailAvailable)
                    .toggleClass('is-valid', isEmailAvailable);
            },
            error: function () {
                console.log('Error while checking email');
            }
        });
    });


    $('#provinsi').change(function () {
        var provinsi = $('#provinsi').val();

        $.ajax({
            type: 'POST',
            url: base_url + 'registration/getKota',
            data: {
                provinsi: provinsi,
            },
            cache: false,
            success: function (msg) {
                $('#kota').html(msg);
            }
        })
    });

    $('#kota').change(function () {
        var kota = $('#kota').val();
        var nama = $('#kota').find(":selected").data("nama");

        var origin = $("#origin").val();

        if (origin && nama) {
            fetchPrice(origin, nama);
        }

        $.ajax({
            type: 'POST',
            url: base_url + 'registration/getKecamatan',
            data: {
                kota: kota,
            },
            cache: false,
            success: function (msg) {
                $('#kecamatan').html(msg);
                // console.log(nama)
            }
        })
    });

    $('#kecamatan').change(function () {
        var kecamatan = $('#kecamatan').val();

        $.ajax({
            type: 'POST',
            url: base_url + 'registration/getKelurahan',
            data: {
                kecamatan: kecamatan,
            },
            cache: false,
            success: function (msg) {
                $('#kelurahan').html(msg);
            }
        })
    });
})