<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $title ?>
                </h2>
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
                    <div class="card-body">
                        <form action="<?= base_url('setting/update_access/' . $this->uri->segment(3)) ?>" method="post" id="form-input">
                            <div class="row">
                                <?php
                                foreach ($menu as $m) :

                                    $id = $m->Id;
                                    $submenus = $this->M_Setting->get_submenus($id);
                                    $menu_checked = '';
                                    if (!is_null($user_menu['access_menu'])) {
                                        $menu_checked = in_array($id, json_decode($user_menu['access_menu'], true)) ? 'checked' : '';
                                    } ?>
                                    <div class="col-md-3 col-6">
                                        <ul class="list-unstyled mb-2">
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check">
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="input_menu[]" id="checkbox-<?= $m->Id ?>" class="form-check-input menu-checkbox" <?= $menu_checked ?> value="<?= $m->Id ?>">
                                                        <label for="checkbox-<?= $m->Id ?>" class="fw-bold"><?= $m->nama_menu ?></label>
                                                    </div>
                                                </div>
                                                <ul class="list-unstyled mb-0">
                                                    <?php
                                                    foreach ($submenus as $s) :
                                                        $submenu_checked = '';
                                                        if (!is_null($user_menu['access_sub_menu'])) {
                                                            $submenu_checked = in_array($s->Id, json_decode($user_menu['access_sub_menu'], true)) ? 'checked' : '';
                                                        } ?>
                                                        <li class="me-2 mb-1">
                                                            <div class="form-check">
                                                                <div class="checkbox">
                                                                    <input type="checkbox" name="input_submenu[]" id="checkbox-<?= $s->Id ?>" class="form-check-input submenu-checkbox" data-parent-id="<?= $m->Id ?>" value="<?= $s->Id ?>" <?= $submenu_checked ?>>
                                                                    <label for="checkbox-<?= $s->Id ?>"><?= $s->nama_menu ?></label>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php
                                                    endforeach; ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                <?php
                                endforeach; ?>
                            </div>

                            <div class="row">
                                <div class="col-12 text-end">
                                    <a href="<?= base_url('setting/user') ?>" class="btn btn-warning">Back</a>
                                    <button type="submit" class="btn btn-primary btn-confirm">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
        const submenuCheckboxes = document.querySelectorAll('.submenu-checkbox');

        // Tambahkan event listener untuk setiap submenu checkbox
        submenuCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const parentId = checkbox.getAttribute('data-parent-id');
                const parentCheckbox = document.getElementById(`checkbox-${parentId}`);

                // Periksa apakah setiap submenu yang terkait dengan menu parent tersebut dicentang
                const allSubmenusChecked = Array.from(document.querySelectorAll(`.submenu-checkbox[data-parent-id="${parentId}"]`)).every(function(submenu) {
                    return submenu.checked;
                });

                // Jika salah satu submenu dicentang, centang juga menu parentnya
                if (allSubmenusChecked) {
                    parentCheckbox.checked = false;
                } else {
                    parentCheckbox.checked = true;
                }
            });
        });

        // Tambahkan event listener untuk setiap menu parent checkbox
        menuCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const parentId = checkbox.value;
                const submenuCheckboxes = document.querySelectorAll(`.submenu-checkbox[data-parent-id="${parentId}"]`);

                // Centang atau hilangkan centang dari setiap submenu tergantung pada status menu parent
                submenuCheckboxes.forEach(function(submenuCheckbox) {
                    submenuCheckbox.checked = checkbox.checked;
                });
            });
        });
    });
</script>