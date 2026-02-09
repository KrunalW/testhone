<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <div class="container d-flex justify-content-center p-5">
        <div class="card col-12 col-md-7 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4 text-center"><?= lang('Auth.register') ?></h5>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= esc($error) ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= esc(session('errors')) ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <form action="<?= url_to('register') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Full Name -->
                        <div class="col-md-6 mb-3">
                            <label for="fullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fullName" name="full_name"
                                   placeholder="Enter your full name" value="<?= old('full_name') ?>" required>
                        </div>

                        <!-- Age -->
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="age" name="age"
                                   placeholder="Enter your age" min="1" max="150"
                                   value="<?= old('age') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Mobile Number -->
                        <div class="col-md-6 mb-3">
                            <label for="mobileNumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="mobileNumber" name="mobile_number"
                                   placeholder="10-digit mobile number"
                                   pattern="[0-9]{10}"
                                   title="Please enter a valid 10-digit mobile number"
                                   value="<?= old('mobile_number') ?>" required>
                            <small class="form-text text-muted">Enter 10-digit mobile number without +91</small>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="your.email@example.com"
                                   value="<?= old('email') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="open" <?= old('category') === 'open' ? 'selected' : '' ?>>Open</option>
                                <option value="sc/st" <?= old('category') === 'sc/st' ? 'selected' : '' ?>>SC/ST</option>
                                <option value="obc" <?= old('category') === 'obc' ? 'selected' : '' ?>>OBC</option>
                                <option value="vj/nt" <?= old('category') === 'vj/nt' ? 'selected' : '' ?>>VJ/NT</option>
                                <option value="nt-b" <?= old('category') === 'nt-b' ? 'selected' : '' ?>>NT-B</option>
                                <option value="nt-c" <?= old('category') === 'nt-c' ? 'selected' : '' ?>>NT-C</option>
                                <option value="nt-d" <?= old('category') === 'nt-d' ? 'selected' : '' ?>>NT-D</option>
                                <option value="sebc" <?= old('category') === 'sebc' ? 'selected' : '' ?>>SEBC</option>
                                <option value="ews" <?= old('category') === 'ews' ? 'selected' : '' ?>>EWS</option>
                            </select>
                        </div>

                        <!-- Preferred Language -->
                        <div class="col-md-6 mb-3">
                            <label for="preferredLanguage" class="form-label">Preferred Language <span class="text-danger">*</span></label>
                            <select class="form-select" id="preferredLanguage" name="preferred_language" required>
                                <option value="english" <?= old('preferred_language', 'english') === 'english' ? 'selected' : '' ?>>English</option>
                                <option value="marathi" <?= old('preferred_language') === 'marathi' ? 'selected' : '' ?>>Marathi (मराठी)</option>
                            </select>
                            <small class="form-text text-muted">You can change this later in settings</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <!-- Username -->
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="Choose a username"
                                   value="<?= old('username') ?>" required>
                            <small class="form-text text-muted">Use for login</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Create a strong password" required>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>

                        <!-- Password Confirm -->
                        <div class="col-md-6 mb-3">
                            <label for="passwordConfirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="passwordConfirm" name="password_confirm"
                                   placeholder="Re-enter your password" required>
                        </div>
                    </div>

                    <div class="d-grid col-12 col-md-6 mx-auto mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>

                    <p class="text-center mt-3">
                        <?= lang('Auth.haveAccount') ?>
                        <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a>
                    </p>

                </form>
            </div>
        </div>
    </div>

<style>
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.text-danger {
    color: #dc3545;
}
.card {
    border-radius: 10px;
}
.card-title {
    color: #667eea;
    font-weight: 600;
}
</style>

<?= $this->endSection() ?>
