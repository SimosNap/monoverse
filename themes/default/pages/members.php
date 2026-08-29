<?php
declare(strict_types=1);

$members = is_array($members ?? null)
    ? $members
    : [];

$currentPage = max(
    1,
    (int) ($currentPage ?? 1)
);

$totalPages = max(
    1,
    (int) ($totalPages ?? 1)
);

$totalMembers = max(
    0,
    (int) ($totalMembers ?? 0)
);

$search = trim(
    (string) ($search ?? '')
);

$widgetAreas = is_array($widgetAreas ?? null)
    ? $widgetAreas
    : [];

$widgetsBeforeContent = trim(
    (string) ($widgetAreas['beforeContent'] ?? '')
);

$widgetsSidebar = trim(
    (string) ($widgetAreas['sidebar'] ?? '')
);

$widgetsAfterContent = trim(
    (string) ($widgetAreas['afterContent'] ?? '')
);

$hasSidebar = $widgetsSidebar !== '';
?>

<section
    class="mv-members-page <?= $hasSidebar
        ? 'has-sidebar'
        : 'is-full-width' ?>"
    data-members-page
>

    <div class="mv-members-layout">

        <main class="mv-members-main">

            <?php if ($widgetsBeforeContent !== ''): ?>

                <section
                    class="mv-block-area mv-members-widget-area mv-members-widget-area-before"
                    aria-label="<?= htmlspecialchars(
                        $t('members.areas.before'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    <?= $widgetsBeforeContent ?>
                </section>

            <?php endif; ?>

            <?php if ($members === []): ?>

                <div class="mv-empty-state">

                    <span class="mv-empty-state-icon">

                        <i
                            class="fa-regular fa-address-card"
                            aria-hidden="true"
                        ></i>

                    </span>

                    <?php if ($search !== ''): ?>

                        <h2>
                            <?= htmlspecialchars(
                                $t('members.empty.search_title'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars(
                                $t('members.empty.search_text_before'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <strong>
                                <?= htmlspecialchars(
                                    $search,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>.
                        </p>

                        <a href="/members">
                            <?= htmlspecialchars(
                                $t('members.empty.show_all'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>

                    <?php else: ?>

                        <h2>
                            <?= htmlspecialchars(
                                $t('members.empty.title'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars(
                                $t('members.empty.text'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="mv-members-toolbar">

                    <div class="mv-members-summary">

                        <strong>
                            <?= number_format(
                                $totalMembers,
                                0,
                                ',',
                                '.'
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $totalMembers === 1
                                    ? $t('members.summary.one_profile')
                                    : $t('members.summary.many_profiles'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <?php if ($search !== ''): ?>

                            <span class="mv-members-search-info">

                                <?= htmlspecialchars(
                                    $t('members.summary.results_for'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                                <strong>
                                    <?= htmlspecialchars(
                                        $search,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                            </span>

                        <?php endif; ?>

                    </div>

                    <div
                        class="mv-members-view-switcher"
                        role="group"
                        aria-label="<?= htmlspecialchars(
                            $t('members.view.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                        <button
                            type="button"
                            class="mv-members-view-button is-active"
                            data-members-view="grid"
                            aria-label="<?= htmlspecialchars(
                                $t('members.view.grid_label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            title="<?= htmlspecialchars(
                                $t('members.view.grid_label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            aria-pressed="true"
                        >
                            <i
                                class="fa-solid fa-table-cells-large"
                                aria-hidden="true"
                            ></i>

                            <span>
                                <?= htmlspecialchars(
                                    $t('members.view.grid'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>
                        </button>

                        <button
                            type="button"
                            class="mv-members-view-button"
                            data-members-view="list"
                            aria-label="<?= htmlspecialchars(
                                $t('members.view.list_label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            title="<?= htmlspecialchars(
                                $t('members.view.list_label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            aria-pressed="false"
                        >
                            <i
                                class="fa-solid fa-list"
                                aria-hidden="true"
                            ></i>

                            <span>
                                <?= htmlspecialchars(
                                    $t('members.view.list'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>
                        </button>

                    </div>

                </div>

                <div
                    class="mv-members-grid"
                    data-members-list
                >

                    <?php foreach ($members as $member): ?>

                        <?php
                        $username = trim(
                            (string) (
                                $member['username']
                                ?? $t('members.user')
                            )
                        );

                        $avatar = trim(
                            (string) ($member['avatar_url'] ?? '')
                        );

                        $motto = trim(
                            (string) ($member['motto'] ?? '')
                        );

                        $memberInterests = is_array(
                            $member['interests'] ?? null
                        )
                            ? array_slice(
                                array_values(
                                    array_filter(
                                        $member['interests'],
                                        static fn (mixed $interest): bool =>
                                            trim((string) $interest) !== ''
                                    )
                                ),
                                0,
                                3
                            )
                            : [];

                        $hasVisibleAvatar = !empty(
                            $member['show_avatar']
                        ) && $avatar !== '';
                        ?>

                        <article class="mv-member-card">

                            <a
                                class="mv-member-card-link"
                                href="/profile/<?= rawurlencode($username) ?>"
                            >

                                <div class="mv-member-avatar">

                                    <?php if ($hasVisibleAvatar): ?>

                                        <img
                                            src="<?= htmlspecialchars(
                                                $avatar,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt=""
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <span>
                                            <?= htmlspecialchars(
                                                mb_strtoupper(
                                                    mb_substr(
                                                        $username,
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

                                <div class="mv-member-card-content">

                                    <div class="mv-member-card-heading">

                                        <h2>
                                            <?= htmlspecialchars(
                                                $username,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h2>

                                        <i
                                            class="fa-solid fa-arrow-right"
                                            aria-hidden="true"
                                        ></i>

                                    </div>

                                    <?php if ($motto !== ''): ?>

                                        <p class="mv-member-motto">
                                            <?= htmlspecialchars(
                                                $motto,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                    <?php else: ?>

                                        <p class="mv-member-motto is-empty">
                                            <?= htmlspecialchars(
                                                $t('members.card.no_intro'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                    <?php endif; ?>

                                    <?php if ($memberInterests !== []): ?>

                                        <div class="mv-member-interests">

                                            <?php foreach ($memberInterests as $interest): ?>

                                                <span>
                                                    <?= htmlspecialchars(
                                                        (string) $interest,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>

                                            <?php endforeach; ?>

                                        </div>

                                    <?php endif; ?>

                                    <span class="mv-member-card-action">

                                        <span>
                                            <?= htmlspecialchars(
                                                $t('members.card.view_profile'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                        <i
                                            class="fa-solid fa-arrow-right"
                                            aria-hidden="true"
                                        ></i>

                                    </span>

                                </div>

                            </a>

                        </article>

                    <?php endforeach; ?>

                </div>

                <?php if ($totalPages > 1): ?>

                    <?php
                    $firstVisiblePage = max(
                        1,
                        $currentPage - 2
                    );

                    $lastVisiblePage = min(
                        $totalPages,
                        $currentPage + 2
                    );

                    $pageQuery = $search !== ''
                        ? '&q=' . rawurlencode($search)
                        : '';
                    ?>

                    <nav
                        class="mv-members-pagination"
                        aria-label="<?= htmlspecialchars(
                            $t('members.pagination.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                        <div class="mv-members-pagination-side">

                            <?php if ($currentPage > 1): ?>

                                <a
                                    href="/members?page=<?= $currentPage - 1 ?><?= $pageQuery ?>"
                                >
                                    <i
                                        class="fa-solid fa-arrow-left"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        <?= htmlspecialchars(
                                            $t('members.pagination.previous'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </a>

                            <?php endif; ?>

                        </div>

                        <div class="mv-members-pagination-pages">

                            <?php if ($firstVisiblePage > 1): ?>

                                <a href="/members?page=1<?= $pageQuery ?>">
                                    1
                                </a>

                                <?php if ($firstVisiblePage > 2): ?>

                                    <span aria-hidden="true">…</span>

                                <?php endif; ?>

                            <?php endif; ?>

                            <?php for (
                                $pageNumber = $firstVisiblePage;
                                $pageNumber <= $lastVisiblePage;
                                $pageNumber++
                            ): ?>

                                <a
                                    href="/members?page=<?= $pageNumber ?><?= $pageQuery ?>"
                                    class="<?= $pageNumber === $currentPage
                                        ? 'is-current'
                                        : '' ?>"
                                    <?= $pageNumber === $currentPage
                                        ? 'aria-current="page"'
                                        : '' ?>
                                >
                                    <?= $pageNumber ?>
                                </a>

                            <?php endfor; ?>

                            <?php if ($lastVisiblePage < $totalPages): ?>

                                <?php if (
                                    $lastVisiblePage < $totalPages - 1
                                ): ?>

                                    <span aria-hidden="true">…</span>

                                <?php endif; ?>

                                <a
                                    href="/members?page=<?= $totalPages ?><?= $pageQuery ?>"
                                >
                                    <?= $totalPages ?>
                                </a>

                            <?php endif; ?>

                        </div>

                        <div class="mv-members-pagination-side is-next">

                            <?php if ($currentPage < $totalPages): ?>

                                <a
                                    href="/members?page=<?= $currentPage + 1 ?><?= $pageQuery ?>"
                                >
                                    <span>
                                        <?= htmlspecialchars(
                                            $t('members.pagination.next'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <i
                                        class="fa-solid fa-arrow-right"
                                        aria-hidden="true"
                                    ></i>
                                </a>

                            <?php endif; ?>

                        </div>

                    </nav>

                <?php endif; ?>

            <?php endif; ?>

            <?php if ($widgetsAfterContent !== ''): ?>

                <section
                    class="mv-block-area mv-members-widget-area mv-members-widget-area-after"
                    aria-label="<?= htmlspecialchars(
                        $t('members.areas.after'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    <?= $widgetsAfterContent ?>
                </section>

            <?php endif; ?>

        </main>

        <?php if ($hasSidebar): ?>

            <aside
                class="mv-members-sidebar"
                aria-label="<?= htmlspecialchars(
                    $t('members.areas.sidebar'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

                <form
                    class="mv-members-search"
                    method="get"
                    action="/members"
                    role="search"
                    autocomplete="off"
                >

                    <div class="mv-members-search-field">

                        <i
                            class="fa-solid fa-magnifying-glass"
                            aria-hidden="true"
                        ></i>

                        <input
                            type="search"
                            name="q"
                            value="<?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            placeholder="<?= htmlspecialchars(
                                $t('members.search.placeholder'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            aria-label="<?= htmlspecialchars(
                                $t('members.search.label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            autocomplete="off"
                            autocapitalize="none"
                            spellcheck="false"
                            enterkeyhint="search"
                            data-1p-ignore
                            data-lpignore="true"
                        >

                        <?php if ($search !== ''): ?>

                            <a
                                class="mv-members-search-reset"
                                href="/members"
                                aria-label="<?= htmlspecialchars(
                                    $t('members.search.reset'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                title="<?= htmlspecialchars(
                                    $t('members.search.reset'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                <i
                                    class="fa-solid fa-xmark"
                                    aria-hidden="true"
                                ></i>
                            </a>

                        <?php endif; ?>

                    </div>

                    <button type="submit">

                        <i
                            class="fa-solid fa-magnifying-glass"
                            aria-hidden="true"
                        ></i>

                        <span>
                            <?= htmlspecialchars(
                                $t('members.search.submit'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </button>

                </form>

                <div class="mv-block-area mv-members-widget-area mv-members-widget-area-sidebar">
                    <?= $widgetsSidebar ?>
                </div>

            </aside>

        <?php endif; ?>

    </div>

</section>
