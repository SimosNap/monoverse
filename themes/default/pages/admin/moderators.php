<?php
declare(strict_types=1);

$moderators = is_array($moderators ?? null)
    ? $moderators
    : [];

$users = is_array($users ?? null)
    ? $users
    : [];

$moderatorCount = count($moderators);
?>

<section class="mv-admin-page">

    <header class="mv-admin-page-header">

        <div>

            <p class="mv-admin-page-kicker">
                Community
            </p>

            <h1>
                Moderatori
            </h1>

            <p>
                Gestisci gli utenti autorizzati alla moderazione della community.
            </p>

        </div>

    </header>

    <section class="mv-admin-card mv-admin-add-card">

        <div class="mv-admin-card-heading">

            <div>

                <h2>
                    Aggiungi moderatore
                </h2>

                <p>
                    Seleziona uno degli utenti registrati in Monoverse.
                </p>

            </div>

        </div>

        <?php if ($users !== []): ?>

            <form
                method="post"
                action="/admin/moderators/add"
                class="mv-admin-form mv-admin-add-form">

                <div class="mv-admin-field">

                    <label for="oauth_sub">
                        Utente
                    </label>

                    <select
                        id="oauth_sub"
                        name="oauth_sub"
                        required>

                        <option value="">
                            Seleziona un utente...
                        </option>

                        <?php foreach ($users as $user): ?>

                            <?php
                            $userSub = trim(
                                (string) ($user['oauth_sub'] ?? '')
                            );

                            $userName = trim(
                                (string) ($user['username'] ?? '')
                            );
                            ?>

                            <?php if ($userSub !== '' && $userName !== ''): ?>

                                <option
                                    value="<?= htmlspecialchars(
                                        $userSub,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                    <?= htmlspecialchars(
                                        $userName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </option>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </select>

                </div>

                <button
                    type="submit"
                    class="mv-admin-button">

                    Aggiungi moderatore

                </button>

            </form>

        <?php else: ?>

            <div class="mv-admin-inline-notice">

                <p>
                    Tutti gli utenti registrati disponibili sono già stati
                    assegnati come moderatori.
                </p>

            </div>

        <?php endif; ?>

    </section>

    <section class="mv-admin-list-section">

        <header class="mv-admin-list-header">

            <div>

                <h2>
                    Moderatori assegnati
                </h2>

                <p>
                    <?= $moderatorCount === 1
                        ? '1 moderatore configurato'
                        : htmlspecialchars(
                            (string) $moderatorCount,
                            ENT_QUOTES,
                            'UTF-8'
                        ) . ' moderatori configurati' ?>
                </p>

            </div>

        </header>

        <?php if ($moderators === []): ?>

            <div class="mv-empty-state">

                <h2>
                    Nessun moderatore
                </h2>

                <p>
                    Non è stato ancora assegnato alcun moderatore.
                </p>

            </div>

        <?php else: ?>

            <div class="mv-admin-entity-list">

                <?php foreach ($moderators as $moderator): ?>

                    <?php
                    $sub = trim(
                        (string) ($moderator['oauth_sub'] ?? '')
                    );

                    $username = trim(
                        (string) ($moderator['username'] ?? '')
                    );

                    $avatar = trim(
                        (string) ($moderator['avatar_url'] ?? '')
                    );

                    $role = trim(
                        (string) ($moderator['role'] ?? 'moderator')
                    );

                    $roleLabel = $role === 'moderator'
                        ? 'Moderatore'
                        : $role;

                    $enabled = !empty($moderator['enabled']);

                    $createdAt = (int) (
                        $moderator['created_at'] ?? 0
                    );

                    $createdLabel = $createdAt > 0
                        ? date('d/m/Y H:i', $createdAt)
                        : 'Data non disponibile';

                    $displayUsername = $username !== ''
                        ? $username
                        : 'Utente';
                    ?>

                    <article class="mv-admin-entity-card">

                        <div class="mv-admin-entity-main">

                            <div class="mv-admin-avatar">

                                <?php if ($avatar !== ''): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            $avatar,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt="">

                                <?php else: ?>

                                    <span>
                                        <?= htmlspecialchars(
                                            mb_strtoupper(
                                                mb_substr(
                                                    $displayUsername,
                                                    0,
                                                    1
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                            <div class="mv-admin-entity-content">

                                <div class="mv-admin-entity-title-row">

                                    <div>

                                        <h3>
                                            <?= htmlspecialchars(
                                                $displayUsername,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h3>

                                        <p>
                                            <?= htmlspecialchars(
                                                $roleLabel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                    </div>

                                    <span class="mv-admin-badge <?= $enabled
                                        ? 'is-active'
                                        : 'is-disabled' ?>">

                                        <span aria-hidden="true"></span>

                                        <?= $enabled
                                            ? 'Attivo'
                                            : 'Disabilitato' ?>

                                    </span>

                                </div>

                                <dl class="mv-admin-entity-meta">

                                    <div>

                                        <dt>
                                            Aggiunto
                                        </dt>

                                        <dd>
                                            <?= htmlspecialchars(
                                                $createdLabel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </dd>

                                    </div>

                                </dl>

                            </div>

                        </div>

                        <footer class="mv-admin-entity-actions">

                            <?php if ($enabled): ?>

                                <form
                                    method="post"
                                    action="/admin/moderators/disable">

                                    <input
                                        type="hidden"
                                        name="oauth_sub"
                                        value="<?= htmlspecialchars(
                                            $sub,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                                    <button
                                        type="submit"
                                        class="mv-admin-button is-secondary">

                                        Disabilita

                                    </button>

                                </form>

                            <?php else: ?>

                                <form
                                    method="post"
                                    action="/admin/moderators/enable">

                                    <input
                                        type="hidden"
                                        name="oauth_sub"
                                        value="<?= htmlspecialchars(
                                            $sub,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                                    <button
                                        type="submit"
                                        class="mv-admin-button">

                                        Abilita

                                    </button>

                                </form>

                            <?php endif; ?>

                            <form
                                method="post"
                                action="/admin/moderators/remove"
                                onsubmit="return confirm(
                                    'Rimuovere definitivamente questo moderatore?'
                                );">

                                <input
                                    type="hidden"
                                    name="oauth_sub"
                                    value="<?= htmlspecialchars(
                                        $sub,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                <button
                                    type="submit"
                                    class="mv-admin-button is-danger">

                                    Rimuovi

                                </button>

                            </form>

                        </footer>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</section>