<section class="mv-admin-login">

    <div class="mv-admin-login-card">

        <div class="mv-admin-login-header">

            <div class="mv-admin-login-icon" aria-hidden="true">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <div>
                <h1>
                    <?= htmlspecialchars(
                        $t('admin.login.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h1>

                <p>
                    <?= htmlspecialchars(
                        $t('admin.login.description'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            </div>

        </div>

        <?php if (!empty($errors)): ?>

            <div class="mv-alert mv-alert-danger">

                <?php foreach ($errors as $error): ?>

                    <p>
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <form
            method="post"
            action="/admin/login"
            class="mv-admin-login-form"
        >

            <label class="mv-admin-login-field">

                <span>
                    <?= htmlspecialchars(
                        $t('admin.login.username'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

                <div class="mv-admin-login-input">

                    <i
                        class="fa-solid fa-user"
                        aria-hidden="true"
                    ></i>

                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars(
                            (string) ($old['username'] ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        autocomplete="username"
                        required
                    >

                </div>

            </label>

            <label class="mv-admin-login-field">

                <span>
                    <?= htmlspecialchars(
                        $t('admin.login.password'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

                <div class="mv-admin-login-input">

                    <i
                        class="fa-solid fa-key"
                        aria-hidden="true"
                    ></i>

                    <input
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >

                </div>

            </label>

            <button
                type="submit"
                class="mv-admin-login-submit"
            >
                <span>
                    <?= htmlspecialchars(
                        $t('admin.login.submit'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

                <i
                    class="fa-solid fa-arrow-right"
                    aria-hidden="true"
                ></i>
            </button>

        </form>

    </div>

</section>
