<?php
declare(strict_types=1);
?>

<section class="mv-register-page">

    <header class="mv-register-hero">

        <div class="mv-register-hero-content">

            <div class="mv-register-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-user-shield"></i>
            </div>

            <div class="mv-register-hero-copy">

                <h1>
                    <?= htmlspecialchars(
                        $t('register.hero.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h1>

                <p>
                    <?= htmlspecialchars(
                        $t('register.hero.subtitle'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </div>

        <div
            class="mv-register-hero-visual"
            aria-hidden="true"
        >
            <img
                src="/themes/default/assets/images/register/account-access-flow.webp"
                alt=""
                loading="eager"
                decoding="async"
            >
        </div>

    </header>

    <div class="mv-register-grid">

        <section class="mv-register-card">

            <div class="mv-register-card-icon" aria-hidden="true">
                <i class="fa-solid fa-id-card"></i>
            </div>

            <div>

                <h2>
                    <?= htmlspecialchars(
                        $t('register.identity.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <p>
                    <?= htmlspecialchars(
                        $t('register.identity.text'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </section>

        <section class="mv-register-card">

            <div class="mv-register-card-icon" aria-hidden="true">
                <i class="fa-solid fa-lock"></i>
            </div>

            <div>

                <h2>
                    <?= htmlspecialchars(
                        $t('register.security.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <p>
                    <?= htmlspecialchars(
                        $t('register.security.text_1'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p>
                    <?= htmlspecialchars(
                        $t('register.security.text_2'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p>
                    <?= htmlspecialchars(
                        $t('register.security.text_3'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </section>

    </div>

    <section class="mv-register-features">

        <h2>
            <?= htmlspecialchars(
                $t('register.features.title'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h2>

        <ul>

            <?php foreach (
                [
                    '0',
                    '1',
                    '2',
                    '3',
                    '4',
                    '5',
                ] as $item
            ): ?>

                <li>
                    <i
                        class="fa-solid fa-circle-check"
                        aria-hidden="true"
                    ></i>

                    <span>
                        <?= htmlspecialchars(
                            $t('register.features.items.' . $item),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </li>

            <?php endforeach; ?>

        </ul>

    </section>

    <section class="mv-register-actions">

        <div>

            <h2>
                <?= htmlspecialchars(
                    $t('register.existing_account.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h2>

            <p>
                <?= htmlspecialchars(
                    $t('register.existing_account.text'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

        <div class="mv-register-action-links">

            <a
                href="/oauth/login"
                class="mv-register-login"
            >
                <i
                    class="fa-solid fa-right-to-bracket"
                    aria-hidden="true"
                ></i>

                <?= htmlspecialchars(
                    $t('register.buttons.login'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </a>

            <a
                href="https://www.simosnap.org/register"
                class="mv-register-create"
                target="_blank"
                rel="noopener noreferrer"
            >
                <?= htmlspecialchars(
                    $t('register.buttons.register'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

                <i
                    class="fa-solid fa-arrow-up-right-from-square"
                    aria-hidden="true"
                ></i>
            </a>

        </div>

    </section>

</section>
