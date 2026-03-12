<div class="row">
    <div class="col-auto">
        <select name="show_per_page" class="form-select" onchange="this.form.submit()">
            <option <?= ($per_page == '10') ? 'selected' : '' ?> value="10">10</option>
            <option <?= ($per_page == '25') ? 'selected' : '' ?> value="25">25</option>
            <option <?= ($per_page == '50') ? 'selected' : '' ?> value="50">50</option>
            <option <?= ($per_page == '100') ? 'selected' : '' ?> value="100">100</option>
        </select>
    </div>
    <div class="col-auto">
        <input type="text" value="<?= $keyword ?>" class="form-control" name="keyword" placeholder="Search…" aria-label="Search in website">
    </div>
</div>