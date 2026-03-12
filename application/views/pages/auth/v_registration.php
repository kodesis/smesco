<div class="card mb-3">
    <div class="card-body">

        <div class="pt-4 pb-2">
            <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
            <p class="text-center small">Enter your personal details to create account</p>
        </div>

        <form class="row g-3" method="POST" action="<?= base_url('auth/registration') ?>">
            <div class="col-12">
                <label for="yourName" class="form-label">Your Name</label>
                <input type="text" name="name" class="form-control" id="yourName" value="<?= set_value('name') ?>">
                <?= form_error('name', '<small class="text-danger">', '</small>'); ?>
            </div>

            <div class="col-12">
                <label for="yourEmail" class="form-label">Your Email</label>
                <input type="text" name="email" class="form-control" id="yourEmail" value="<?= set_value('email') ?>">
                <?= form_error('email', '<small class="text-danger">', '</small>'); ?>
            </div>

            <div class="col-12">
                <label for="yourUsername" class="form-label">Username</label>
                <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                    <input type="text" name="username" class="form-control" id="yourUsername" value="<?= set_value('username') ?>">
                </div>
                <?= form_error('username', '<small class="text-danger">', '</small>'); ?>
            </div>

            <div class="col-12">
                <label for="yourPassword" class="form-label">Password</label>
                <input type="password" name="password1" class="form-control" id="password1">
                <?= form_error('password1', '<small class="text-danger">', '</small>'); ?>
            </div>

            <div class="col-12">
                <label for="yourPassword" class="form-label">Confirmation Password</label>
                <input type="password" name="password2" class="form-control" id="password2">
            </div>

            <div class="col-12">
                <button class="btn btn-primary w-100" type="submit">Create Account</button>
            </div>
            <div class="col-12">
                <p class="small mb-0">Already have an account? <a href="<?= base_url('auth') ?>">Log in!</a></p>
            </div>
        </form>

    </div>
</div>