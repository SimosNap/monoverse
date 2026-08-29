<?php
declare(strict_types=1);

$pages = is_array($pages ?? null)
    ? $pages
    : [];

$statusLabels = [
    'draft' => $t('admin.pages.status.draft'),
    'published' => $t('admin.pages.status.published'),
    'private' => $t('admin.pages.status.private'),
];
?>

<div class="admin-page admin-pages-page">

    <header class="admin-page-header">

        <div>

            <h1>
                <?= htmlspecialchars(
                    $t('admin.pages.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars(
                    $t('admin.pages.description'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

        <a
            class="mv-admin-button is-primary"
            href="/admin/pages/create"
        >
            <i
                class="fa-solid fa-plus"
                aria-hidden="true"
            ></i>

            <?= htmlspecialchars(
                $t('admin.pages.actions.new'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>

    </header>

    <?php if ($pages === []): ?>

        <div class="admin-form-card admin-pages-empty">

            <i
                class="fa-solid fa-file-circle-plus"
                aria-hidden="true"
            ></i>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $t('admin.pages.empty.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <p>
                    <?= htmlspecialchars(
                        $t('admin.pages.empty.description'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </div>

    <?php else: ?>

        <div class="admin-form-card admin-pages-card">

            <div class="admin-pages-table-wrapper">

                <table class="admin-pages-table">

                    <thead>

                        <tr>

                            <th>
                                <?= htmlspecialchars(
                                    $t('admin.pages.table.page'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </th>

                            <th>
                                <?= htmlspecialchars(
                                    $t('admin.pages.table.status'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </th>

                            <th>
                                <?= htmlspecialchars(
                                    $t('admin.pages.table.widgets'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </th>

                            <th>
                                <span class="sr-only">
                                    <?= htmlspecialchars(
                                        $t('admin.pages.table.actions'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($pages as $page): ?>

                            <?php
                            $pageId = (int) ($page['id'] ?? 0);

                            $title = trim(
                                (string) ($page['title'] ?? '')
                            );

                            $slug = trim(
                                (string) ($page['slug'] ?? '')
                            );

                            $status = trim(
                                (string) ($page['status'] ?? 'draft')
                            );

                            $blockPageKey = trim(
                                (string) (
                                    $page['block_page_key']
                                    ?? ''
                                )
                            );

                            $statusLabel = $statusLabels[$status]
                                ?? ucfirst($status);
                            ?>

                            <tr>

                                <td>

                                    <div class="admin-pages-identity">

                                        <strong>
                                            <?= htmlspecialchars(
                                                $title,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <a
                                            href="/<?= rawurlencode($slug) ?>"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            /<?= htmlspecialchars(
                                                $slug,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            <i
                                                class="fa-solid fa-arrow-up-right-from-square"
                                                aria-hidden="true"
                                            ></i>
                                        </a>

                                    </div>

                                </td>

                                <td>

                                    <span class="admin-pages-status admin-pages-status-<?= htmlspecialchars(
                                        $status,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">
                                        <?= htmlspecialchars(
                                            $statusLabel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                </td>

                                <td>

                                    <div class="admin-pages-widget-links">

                                        <a
                                            href="/admin/blocks/area?page=<?= rawurlencode(
                                                $blockPageKey
                                            ) ?>&amp;area=content"
                                        >
                                            <i
                                                class="fa-solid fa-table-columns"
                                                aria-hidden="true"
                                            ></i>

                                            <?= htmlspecialchars(
                                                $t(
                                                    'admin.pages.areas.content'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </a>

                                        <a
                                            href="/admin/blocks/area?page=<?= rawurlencode(
                                                $blockPageKey
                                            ) ?>&amp;area=sidebar"
                                        >
                                            <i
                                                class="fa-solid fa-bars-staggered"
                                                aria-hidden="true"
                                            ></i>

                                            <?= htmlspecialchars(
                                                $t(
                                                    'admin.pages.areas.sidebar'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </a>

                                    </div>

                                </td>

                                <td>

                                    <div class="admin-pages-actions">

                                        <a
                                            href="/admin/pages/<?= $pageId ?>/edit"
                                        >
                                            <?= htmlspecialchars(
                                                $t(
                                                    'admin.pages.actions.edit'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </a>

                                        <form
                                            action="/admin/pages/<?= $pageId ?>/delete"
                                            method="post"
                                            onsubmit="return confirm(<?= htmlspecialchars(
                                                json_encode(
                                                    $t(
                                                        'admin.pages.confirm.delete'
                                                    ),
                                                    JSON_HEX_TAG
                                                    | JSON_HEX_AMP
                                                    | JSON_HEX_APOS
                                                    | JSON_HEX_QUOT
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>);"
                                        >
                                            <button type="submit">
                                                <?= htmlspecialchars(
                                                    $t(
                                                        'admin.pages.actions.delete'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php endif; ?>

</div>
