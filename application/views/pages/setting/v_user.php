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
                        <form action="<?= base_url('setting/user') ?>" method="post" autocomplete="off" novalidate>
                            <?php $this->load->view('pages/layouts/_search') ?>
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
                        <a href="<?= base_url('dashboard/reset/user') ?>" class="btn btn-warning d-none d-sm-inline-block" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Reset</a>
                        <a href="<?= base_url('dashboard/reset/user') ?>" class="btn btn-warning d-sm-none btn-icon" aria-label="Reset search keyword" title="Reset search" data-bs-toggle="tooltip" data-bs-placement="top">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                        </a>
                    <?php
                    } ?>
                    <a href=<a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-add">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add new
                    </a>
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-add" aria-label="Create new report">
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
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class=""></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($users) {
                                    $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * $per_page) + 1) : '1';

                                    foreach ($users as $c) : ?>
                                        <tr>
                                            <td class="text-end"><?= $no++; ?>.</td>
                                            <td><?= $c->name ?></td>
                                            <td><?= ($c->username) ?></td>
                                            <td><?= $c->email ?></td>
                                            <td><?= $c->role ?></td>
                                            <td>
                                                <button type="button" class="btn btn-ghost-primary btn-sm editData" data-id="<?= $c->username ?>" data-nama="<?= $c->name ?>">Edit</button>
                                                <a href="<?= base_url('setting/user_access/') . $c->username ?>" class="btn btn-ghost-success btn-sm">User Access</a>
                                                <a href='<?= base_url('setting/' . ($c->is_active == "1" ? "hold" : "activate") . '/' . $c->username) ?>' class='btn btn-ghost-<?= $c->is_active == "1" ? "danger" : "secondary" ?> btn-sm btn-process'><?= $c->is_active == "1" ? "Hold" : "Activate" ?></a>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6">No data available to display</td>
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
<div class="modal modal-blur fade" id="modal-add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('setting/addUser') ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label for="role" class="form-label">User role</label>
                            <select name="role" id="role" class="form-select" onchange="showFormAdd()">
                                <option value="">:: Pilih user role</option>
                                <option value="2">Staf Smesco Express</option>
                                <option value="3">Mitra</option>
                                <option value="4">Driver</option>
                            </select>
                        </div>
                    </div>
                    <div id="staff" style="display: none">
                        <div class="row">
                            <div class="col-md-6 col-12 mb-2">
                                <label for="nama_user" class="form-label">Nama user</label>
                                <input type="text" name="nama_user" id="nama_user" class="form-control" required>
                            </div>
                            <div class="col-md-6 col-12 mb-2">
                                <label for="email_user" class="form-label">Email user</label>
                                <input type="email" name="email_user" id="email_user" class="form-control" required>
                            </div>
                            <div class="col-md-6 col-12 mb-2">
                                <label for="no_handphone" class="form-label">No. HP</label>
                                <input type="text" name="no_handphone" id="no_handphone" class="form-control" required>
                            </div>
                            <div class="col-md-6 col-12 mb-2">
                                <label for="username" class="form-label">Nama akun</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text"> @ </span>
                                    <input type="text" class="form-control" name="username" id="username" placeholder="username" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div id="partner" style="display: none">
                            <div class="row">
                                <div class="col-md-6 col-12 mb-2">
                                    <label for="mitra" class="form-label">Mitra</label>
                                    <select name="mitra" id="mitra" class="form-select">
                                        <option value="">:: Pilih mitra</option>
                                        <?php
                                        foreach ($partners as $p) : ?>
                                            <option value="<?= $p->Id ?>"><?= $p->nama_pendaftar ?></option>
                                        <?php
                                        endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 mb-2">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"> @ </span>
                                        <input type="text" class="form-control" placeholder="username" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="yourPassword" class="form-label">Password</label>
                                    <div class="input-group input-group-flat">
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Your password" autocomplete="off">
                                        <span class="input-group-text">
                                            <a href="#" id="toggle-password" class="link-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                    <small id="password-error" class="text-danger" style="display:none;">Password must be at least 8 characters long, contain a number, and an uppercase letter.</small>
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="yourPassword2" class="form-label">Confirmation Password</label>
                                    <div class="input-group input-group-flat">
                                        <input type="password" name="password2" id="password2" class="form-control" placeholder="Confirm password" autocomplete="off">
                                        <span class="input-group-text">
                                            <a href="#" id="toggle-password-2" class="link-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                    <small id="confirm-password-error" class="text-danger" style="display:none;">Passwords do not match.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-confirm">Create</button>
                </div>
            </form>
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
<script>
    function showFormAdd() {
        var role = document.getElementById('role').value;
        var staffSection = document.getElementById('staff');
        var partnerSection = document.getElementById('partner');

        if (role === '2' || role == '4') {
            staffSection.style.display = 'block';
            partnerSection.style.display = 'none';
        } else if (role === '3') {
            staffSection.style.display = 'none';
            partnerSection.style.display = 'block';
        } else {
            staffSection.style.display = 'none';
            partnerSection.style.display = 'none';
        }
    }

    // Toggle password visibility for first password field
    document.getElementById('toggle-password').addEventListener('click', function(e) {
        e.preventDefault();
        const passwordField = document.getElementById('password');
        const passwordFieldType = passwordField.getAttribute('type');
        passwordField.setAttribute('type', passwordFieldType === 'password' ? 'text' : 'password');
    });

    // Toggle password visibility for confirmation password field
    document.getElementById('toggle-password-2').addEventListener('click', function(e) {
        e.preventDefault();
        const passwordField2 = document.getElementById('password2');
        const passwordFieldType2 = passwordField2.getAttribute('type');
        passwordField2.setAttribute('type', passwordFieldType2 === 'password' ? 'text' : 'password');
    });

    // Password validation function
    function validatePassword() {
        const password = document.getElementById('password').value;
        const passwordError = document.getElementById('password-error');
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/; // At least 1 uppercase, 1 number, 8 characters

        if (passwordRegex.test(password)) {
            passwordError.style.display = 'none';
            return true;
        } else {
            passwordError.style.display = 'block';
            return false;
        }
    }

    // Check if passwords match
    function validatePasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password2').value;
        const confirmPasswordError = document.getElementById('confirm-password-error');

        if (password === confirmPassword) {
            confirmPasswordError.style.display = 'none';
            return true;
        } else {
            confirmPasswordError.style.display = 'block';
            return false;
        }
    }

    // Attach input event listeners for real-time validation
    document.getElementById('password').addEventListener('input', function() {
        validatePassword();
        validatePasswordMatch();
    });

    document.getElementById('password2').addEventListener('input', validatePasswordMatch);


    $(document).ready(function() {
        $(document).on('click', '.editData', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');

            $('#editData .modal-title').text('Edit data ' + nama);

            $.ajax({
                url: "<?= site_url('user/formEdit') ?>",
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