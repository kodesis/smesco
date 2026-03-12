<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $title ?>s
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 d-none d-sm-inline-block">
                        <form action="<?= base_url('setting/menu') ?>" method="post" autocomplete="off" novalidate>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                </span>
                                <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search in website">
                            </div>
                        </form>
                    </div>
                    <!-- Tombol Search untuk mobile -->
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                        <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </a>
                    <?php
                    if ($keyword) {
                    ?>
                        <a href="<?= base_url('dashboard/reset/menu') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Reset</a>
                        <a href="<?= base_url('dashboard/reset/menu') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                        </a>
                    <?php
                    } ?>
                    <a href=<a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#tambahMenu">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add new
                    </a>
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#tambahMenu" aria-label="Create new report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <?php
                    if ($keyword) {
                    ?>
                        <div class="card-header">
                            <h4 class="card-title">Search results for the keyword <strong>'<?= (isset($keyword)) ? $keyword : ''; ?>'</strong></h4>
                        </div>
                    <?php
                    } ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th class="w-25">Name</th>
                                    <th>Has submenu</th>
                                    <th>URL</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($menus) {
                                    $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * $per_page) + 1) : '1';

                                    foreach ($menus as $m) : ?>
                                        <tr>
                                            <td class="text-end"><?= $no++; ?>.</td>
                                            <td><?= $m->nama_menu ?></td>
                                            <td><?= ($m->has_child == '1') ? 'Yes' : 'No' ?></td>
                                            <td><?= $m->controller ?></td>
                                            <td>
                                                <button type="button" class="btn btn-ghost-primary btn-sm editData" data-id="<?= $m->slug ?>" data-nama="<?= $m->nama_menu ?>">Edit</button>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="5">No data available to display</td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php $this->load->view('pages/layouts/_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pencarian -->
<div class="modal modal-blur fade" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('user') ?>" method="post" autocomplete="off" novalidate>
                    <div class="input-group">
                        <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="editData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="tambahMenu" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelTitle">Add new menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('setting/addNewMenu') ?>" method="post" id="formUpload">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class='form-label' for="nama_menu">Menu name</label>
                                <input type="text" class="form-control" name="nama_menu" placeholder="ex: Setting" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class='form-label' for="url">URL</label>
                                <input name="url" id="url" class="form-control" placeholder="ex: setting/menu" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class='form-label' for="segment">Segment</label>
                                <input name="segment" id="segment" class="form-control" placeholder="ex: setting" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class='form-label' for="has_child">Has child</label>
                                <select name="has_child" id="has_child" class="form-select">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class='form-label' for="segment">Icon menu</label>
                                <textarea name="icon_svg" id="icon_svg" class="form-control" placeholder="Masukkan svg icon menu..."></textarea>
                            </div>
                        </div>
                        <div class="col-12" id="parent_id_wrapper">
                            <div class="mb-3">
                                <label class='form-label' for="parent_id">Parent ID</label>
                                <select name="parent_id" id="parent_id" class="choices form-select">
                                    <option value="">-- Select parent ID</option>
                                    <?php
                                    foreach ($menu as $me) :
                                    ?>
                                        <option value="<?= $me->Id ?>"><?= $me->nama_menu ?></option>
                                    <?php
                                    endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="col-12" id="child_menu" style="display: none;">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>URL</th>
                                        <th>Del.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="baris">
                                        <td>
                                            <input type="text" class="form-control" name="menu_child[]">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="url_child[]">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm hapusRow">Hapus</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm" id="addRow">Add new row</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary btn-confirm">Add new</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '.editData', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');

            $('#editData .modal-title').text('Edit data ' + nama);

            $.ajax({
                url: "<?= site_url('setting/formEditMenu') ?>",
                type: "POST",
                data: {
                    id: id,
                },
                success: function(data) {
                    $('#editData .modal-body').html(data);
                    $('#editData').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: "Error!! ",
                        text: 'Gagal mengambil data',
                        type: "error",
                        icon: "error",
                    });
                }
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const showSubmenus = document.querySelectorAll('.show-submenu');

        showSubmenus.forEach(function(showSubmenu) {
            showSubmenu.addEventListener('click', function() {
                const parentRow = showSubmenu.closest('.parent-row');
                const parentId = parentRow.dataset.id;
                const childRows = document.querySelectorAll('.submenu-of-' + parentId);

                childRows.forEach(function(childRow) {
                    childRow.style.display = childRow.style.display === 'none' ? 'table-row' : 'none';
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasChildSelect = document.getElementById('has_child');
        const parentIdWrapper = document.getElementById('parent_id_wrapper');
        const parentIdSelect = document.getElementById('parent_id');
        const childMenu = document.getElementById('child_menu');

        hasChildSelect.addEventListener('change', function() {
            if (hasChildSelect.value === '1') {
                parentIdWrapper.style.display = 'none'; // menyembunyikan parent_id
                parentIdSelect.value = ''; // mengatur nilai parent_id menjadi NULL
                childMenu.style.display = 'block';
            } else {
                parentIdWrapper.style.display = 'block'; // menampilkan parent_id jika has_child tidak dipilih
                childMenu.style.display = 'none';
            }
        });
    });
</script>
<script>
    var rowCount = 1; // Inisialisasi row

    $('#addRow').on('click', function() {
        // Periksa apakah ada input yang kosong di baris sebelumnya
        var previousRow = $('.baris').last();
        var inputs = previousRow.find('input[type="text"]');
        var isEmpty = false;

        inputs.each(function() {
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
        newRow.find('input[name="menu_child[]"]');
        newRow.find('input[name="url_child[]"]');

        // Perbarui tag <h4> pada baris baru dengan nomor urut yang baru
        rowCount++;

        // Tambahkan baris baru setelah baris terakhir
        previousRow.after(newRow);
    });
    $(document).on('click', '.hapusRow', function() {
        // Mendapatkan jumlah baris
        var rowCount = $('.baris').length;

        // Jika jumlah baris lebih dari satu, maka hapus baris
        if (rowCount > 1) {
            $(this).closest('.baris').remove();
        } else {
            // Jika jumlah baris hanya satu, tampilkan pesan atau lakukan tindakan lain
            alert('Tidak dapat menghapus baris terakhir.');
        }
    });
</script>