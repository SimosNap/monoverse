<?php
declare(strict_types=1);

/** @var array $channelInfo */
/** @var array $channelFeatures */
/** @var bool $isLogged */

$channelInfo = is_array($channelInfo ?? null)
    ? $channelInfo
    : [];

$channelFeatures = is_array($channelFeatures ?? null)
    ? $channelFeatures
    : [];

$escape = static fn (mixed $value): string => htmlspecialchars(
    (string) $value,
    ENT_QUOTES,
    'UTF-8'
);

$users = (int) ($channelInfo['users'] ?? 0);
$usersMax = (int) ($channelInfo['users_max'] ?? 0);

$founder = trim(
    (string) ($channelInfo['chan_founder'] ?? '')
);

$channel = trim(
    (string) ($channelInfo['channel'] ?? '')
);
?>

<section class="mv-town-channel-card">

    <header class="mv-town-channel-card-header">

        <div class="mv-town-channel-card-icon" aria-hidden="true">
            <i class="fa-solid fa-chart-simple"></i>
        </div>

        <div>
            <h3>
                <?= $escape(
                    $t('landing_chat.channel_card.community')
                ) ?>
            </h3>

            <?php if ($channel !== ''): ?>

                <p>
                    <?= $escape($channel) ?>
                </p>

            <?php endif; ?>
        </div>

    </header>

    <div class="mv-town-channel-stats">

        <div class="mv-town-channel-stat">

            <i
                class="fa-solid fa-users"
                aria-hidden="true"
            ></i>

            <div>
                <strong><?= $users ?></strong>

                <span>
                    <?= $escape(
                        $users === 1
                            ? $t(
                                'landing_chat.channel_card.users.one'
                            )
                            : $t(
                                'landing_chat.channel_card.users.many'
                            )
                    ) ?>
                </span>
            </div>

        </div>

        <?php if ($usersMax > 0): ?>

            <div class="mv-town-channel-stat">

                <i
                    class="fa-solid fa-trophy"
                    aria-hidden="true"
                ></i>

                <div>
                    <strong><?= $usersMax ?></strong>

                    <span>
                        <?= $escape(
                            $t(
                                'landing_chat.channel_card.peak_users'
                            )
                        ) ?>
                    </span>
                </div>

            </div>

        <?php endif; ?>

        <?php if ($founder !== ''): ?>

            <div class="mv-town-channel-stat">

                <i
                    class="fa-solid fa-user-shield"
                    aria-hidden="true"
                ></i>

                <div>
                    <strong>
                        <?= $escape($founder) ?>
                    </strong>

                    <span>
                        <?= $escape(
                            $t(
                                'landing_chat.channel_card.founder'
                            )
                        ) ?>
                    </span>
                </div>

            </div>

        <?php endif; ?>

    </div>

    <?php if (empty($isLogged)): ?>

        <div class="mv-town-channel-login">

            <a href="/oauth/login">

                <i
                    class="fa-solid fa-right-to-bracket"
                    aria-hidden="true"
                ></i>

                <span>
                    <?= $escape(
                        $t('landing_chat.channel_card.login')
                    ) ?>
                </span>

            </a>

        </div>

    <?php endif; ?>

    <?php if ($channelFeatures !== []): ?>

        <div class="mv-town-channel-features">

            <h4>
                <?= $escape(
                    $t(
                        'landing_chat.channel_card.how_it_works'
                    )
                ) ?>
            </h4>

            <ul>

                <?php foreach ($channelFeatures as $feature): ?>

                    <?php
                    $icon = trim(
                        (string) ($feature['icon'] ?? 'fa-circle-check')
                    );

                    $title = trim(
                        (string) ($feature['title'] ?? '')
                    );

                    $description = trim(
                        (string) ($feature['description'] ?? '')
                    );
                    ?>

                    <?php if ($title !== ''): ?>

                        <li>

                            <i
                                class="fa-solid <?= $escape($icon) ?>"
                                aria-hidden="true"
                            ></i>

                            <div>
                                <strong>
                                    <?= $escape($title) ?>
                                </strong>

                                <?php if ($description !== ''): ?>

                                    <span>
                                        <?= $escape($description) ?>
                                    </span>

                                <?php endif; ?>
                            </div>

                        </li>

                    <?php endif; ?>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>

</section>
