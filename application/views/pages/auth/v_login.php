<div class="card-body">
    <h2 class="h2 text-center mb-4">Login to your account</h2>

    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('success') ?>"></div>
    <?= $this->session->flashdata('message_name'); ?>

    <form method="POST" action="<?= base_url('auth') ?>" autocomplete="off" novalidate>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="Your username" autocomplete="off" value="<?= set_value('username') ?>" autofocus>
            <?= form_error('username', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="mb-2">
            <label class="form-label">
                Password
            </label>
            <div class="input-group input-group-flat">
                <input type="password" name="password" id="password" class="form-control" placeholder="Your password" autocomplete="off">
                <span class="input-group-text">
                    <a href="#" id="toggle-password" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                        </svg>
                    </a>
                </span>
            </div>
            <?= form_error('password', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </div>
    </form>
</div>