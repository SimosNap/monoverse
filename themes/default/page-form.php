<?php
declare(strict_types=1);

$page = is_array($page ?? null)
    ? $page
    : [];

$errors = is_array($errors ?? null)
    ? $errors
    : [];

$formAction = trim(
    (string) ($formAction ?? '/admin/pages')
);

$blockPageKey = trim(
    (string) ($blockPageKey ?? '')
);

$pageId = (int) ($page['id'] ?? 0);

$isEdit = $pageId > 0;

$pageTitle = trim(
    (string) ($page['title'] ?? '')
);

$pageSlug = trim(
    (string) ($page['slug'] ?? '')
);

$pageStatus = trim(
    (string) ($page['status'] ?? 'draft')
);

if (!in_array(
    $pageStatus,
    [
        'draft',
        'published',
        'private',
    ],
    true
)) {
    $pageStatus = 'draft';
}

$showInNavigation = array_key_exists(
    'show_in_navigation',
    $page
)
    ? (bool) $page['show_in_navigation']
    : true;

$menuLabel = trim(
    (string) ($page['menu_label'] ?? '')
);

$navigationGroup = trim(
    (string) ($page['navigation_group'] ?? 'default')
);

if ($navigationGroup === '') {
    $navigationGroup = 'default';
}

$sortOrder = max(
    0,
    (int) ($page['sort_order'] ?? 0)
);

$metaTitle = trim(
    (string) ($page['meta_title'] ?? '')
);

$metaDescription = trim(
    (string) ($page['meta_description'] ?? '')
);

$availableLocales = is_array(
    $availableLocales ?? null
)
    ? $availableLocales
    : [];

$defaultLocale = trim(
    (string) ($defaultLocale ?? 'it')
);

$titleTranslations = is_array(
    $titleTranslations ?? null
)
    ? $titleTranslations
    : [];

$menuLabelTranslations = is_array(
    $menuLabelTranslations ?? null
)
    ? $menuLabelTranslations
    : [];

$metaTitleTranslations = is_array(
    $metaTitleTranslations ?? null
)
    ? $metaTitleTranslations
    : [];

$metaDescriptionTranslations = is_array(
    $metaDescriptionTranslations ?? null
)
    ? $metaDescriptionTranslations
    : [];
?>

<div class="admin-page admin-page-editor">

    <header class="admin-page-header">

        <div>

            <h1>
                <?= htmlspecialchars(
                    $isEdit
                        ? $t('admin.page_form.title.edit')
                        : $t('admin.page_form.title.create'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars(
                    $t('admin.page_form.description'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

        <a
            class="mv-admin-button"
            href="/admin/pages"
        >
            <i
                class="fa-solid fa-arrow-left"
                aria-hidden="true"
            ></i>

            <?= htmlspecialchars(
                $t('admin.page_form.back'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>

    </header>

    <?php if (!empty($errors['general'])): ?>

        <div class="admin-alert admin-alert-error">

            <i
                class="fa-solid fa-circle-exclamation"
                aria-hidden="true"
            ></i>

            <span>
                <?= htmlspecialchars(
                    (string) $errors['general'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

        </div>

    <?php endif; ?>

    <div class="admin-form-card">

        <form
            class="admin-form"
            action="<?= htmlspecialchars(
                $formAction,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            method="post"
        >

            <div class="form-group">

                <label for="page-title">
                    <?= htmlspecialchars(
                        $t('admin.page_form.fields.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                    (<?= htmlspecialchars(
                        strtoupper($defaultLocale),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>)
                </label>

                <input
                    id="page-title"
                    name="title"
                    type="text"
                    value="<?= htmlspecialchars(
                        $pageTitle,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    maxlength="190"
                    required
                    autofocus
                >

                <?php if (!empty($errors['title'])): ?>

                    <p class="form-error">
                        <?= htmlspecialchars(
                            (string) $errors['title'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

            <?php foreach ($availableLocales as $locale): ?>

                <?php
                $locale = trim((string) $locale);

                if (
                    $locale === ''
                    || $locale === $defaultLocale
                ) {
                    continue;
                }

                $translatedTitle = trim(
                    (string) (
                        $titleTranslations[$locale]
                        ?? ''
                    )
                );
                ?>

                <div class="form-group">

                    <label for="page-title-<?= htmlspecialchars(
                        $locale,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
                        <?= htmlspecialchars(
                            $t('admin.page_form.fields.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        (<?= htmlspecialchars(
                            strtoupper($locale),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>)
                    </label>

                    <input
                        id="page-title-<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        name="translations[<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>][title]"
                        type="text"
                        value="<?= htmlspecialchars(
                            $translatedTitle,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="190"
                    >

                </div>

            <?php endforeach; ?>

            <div class="form-group">

                <label for="page-slug">
                    <?= htmlspecialchars(
                        $t('admin.page_form.fields.slug.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <div class="admin-input-prefix">

                    <span aria-hidden="true">/</span>

                    <input
                        id="page-slug"
                        name="slug"
                        type="text"
                        value="<?= htmlspecialchars(
                            $pageSlug,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="190"
                        pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                        placeholder="radio"
                        required
                    >

                </div>

                <p class="form-help">
                    <?= htmlspecialchars(
                        $t(
                            'admin.page_form.fields.slug.help',
                            [
                                'example' => '/radio',
                            ]
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <?php if (!empty($errors['slug'])): ?>

                    <p class="form-error">
                        <?= htmlspecialchars(
                            (string) $errors['slug'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

            <div class="form-group">

                <label for="page-status">
                    <?= htmlspecialchars(
                        $t('admin.page_form.fields.status.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <select
                    id="page-status"
                    name="status"
                >

                    <option
                        value="draft"
                        <?= $pageStatus === 'draft'
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $t('admin.page_form.status.draft'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                    <option
                        value="published"
                        <?= $pageStatus === 'published'
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $t('admin.page_form.status.published'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                    <option
                        value="private"
                        <?= $pageStatus === 'private'
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            $t('admin.page_form.status.private'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                </select>

                <p class="form-help">
                    <?= htmlspecialchars(
                        $t('admin.page_form.fields.status.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

            <div class="admin-form-section">

                <div class="admin-form-section-header">

                    <h2>
                        <i
                            class="fa-solid fa-bars"
                            aria-hidden="true"
                        ></i>

                        <?= htmlspecialchars(
                            $t('admin.page_form.navigation.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.page_form.navigation.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="form-group">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="show_in_navigation"
                            value="1"
                            <?= $showInNavigation
                                ? 'checked'
                                : ''
                            ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.page_form.navigation.show'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <p class="form-help">
                        <?= htmlspecialchars(
                            $t('admin.page_form.navigation.show_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="form-group">

                    <label for="page-menu-label">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.menu_label.label'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        (<?= htmlspecialchars(
                            strtoupper($defaultLocale),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>)
                    </label>

                    <input
                        id="page-menu-label"
                        name="menu_label"
                        type="text"
                        value="<?= htmlspecialchars(
                            $menuLabel,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="100"
                        placeholder="<?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.menu_label.placeholder'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <p class="form-help">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.menu_label.help'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <?php foreach ($availableLocales as $locale): ?>

                    <?php
                    $locale = trim((string) $locale);

                    if (
                        $locale === ''
                        || $locale === $defaultLocale
                    ) {
                        continue;
                    }

                    $translatedMenuLabel = trim(
                        (string) (
                            $menuLabelTranslations[$locale]
                            ?? ''
                        )
                    );
                    ?>

                    <div class="form-group">

                        <label for="page-menu-label-<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">
                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.fields.menu_label.label'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                            (<?= htmlspecialchars(
                                strtoupper($locale),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>)
                        </label>

                        <input
                            id="page-menu-label-<?= htmlspecialchars(
                                $locale,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            name="translations[<?= htmlspecialchars(
                                $locale,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>][menu_label]"
                            type="text"
                            value="<?= htmlspecialchars(
                                $translatedMenuLabel,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            placeholder="<?= htmlspecialchars(
                                $t(
                                    'admin.page_form.fields.menu_label.placeholder'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                    </div>

                <?php endforeach; ?>

                <div class="form-group">

                    <label for="page-navigation-group">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.navigation_group.label'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        id="page-navigation-group"
                        name="navigation_group"
                        type="text"
                        value="<?= htmlspecialchars(
                            $navigationGroup,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="50"
                        placeholder="default"
                    >

                    <p class="form-help">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.navigation_group.help'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="form-group">

                    <label for="page-sort-order">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.sort_order.label'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        id="page-sort-order"
                        name="sort_order"
                        type="number"
                        value="<?= $sortOrder ?>"
                        min="0"
                        step="1"
                    >

                    <p class="form-help">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.sort_order.help'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            </div>

            <div class="form-group">

                <label for="page-meta-title">
                    <?= htmlspecialchars(
                        $t('admin.page_form.fields.meta_title.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                    (<?= htmlspecialchars(
                        strtoupper($defaultLocale),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>)
                </label>

                <input
                    id="page-meta-title"
                    name="meta_title"
                    type="text"
                    value="<?= htmlspecialchars(
                        $metaTitle,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    maxlength="190"
                >

                <p class="form-help">
                    <?= htmlspecialchars(
                        $t('admin.page_form.fields.meta_title.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <?php if (!empty($errors['meta_title'])): ?>

                    <p class="form-error">
                        <?= htmlspecialchars(
                            (string) $errors['meta_title'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

            <?php foreach ($availableLocales as $locale): ?>

                <?php
                $locale = trim((string) $locale);

                if (
                    $locale === ''
                    || $locale === $defaultLocale
                ) {
                    continue;
                }

                $translatedMetaTitle = trim(
                    (string) (
                        $metaTitleTranslations[$locale]
                        ?? ''
                    )
                );
                ?>

                <div class="form-group">

                    <label for="page-meta-title-<?= htmlspecialchars(
                        $locale,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.meta_title.label'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        (<?= htmlspecialchars(
                            strtoupper($locale),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>)
                    </label>

                    <input
                        id="page-meta-title-<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        name="translations[<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>][meta_title]"
                        type="text"
                        value="<?= htmlspecialchars(
                            $translatedMetaTitle,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="190"
                    >

                </div>

            <?php endforeach; ?>

            <div class="form-group">

                <label for="page-meta-description">
                    <?= htmlspecialchars(
                        $t(
                            'admin.page_form.fields.meta_description.label'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                    (<?= htmlspecialchars(
                        strtoupper($defaultLocale),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>)
                </label>

                <textarea
                    id="page-meta-description"
                    name="meta_description"
                    rows="4"
                ><?= htmlspecialchars(
                    $metaDescription,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

                <p class="form-help">
                    <?= htmlspecialchars(
                        $t(
                            'admin.page_form.fields.meta_description.help'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

            <?php foreach ($availableLocales as $locale): ?>

                <?php
                $locale = trim((string) $locale);

                if (
                    $locale === ''
                    || $locale === $defaultLocale
                ) {
                    continue;
                }

                $translatedMetaDescription = trim(
                    (string) (
                        $metaDescriptionTranslations[$locale]
                        ?? ''
                    )
                );
                ?>

                <div class="form-group">

                    <label for="page-meta-description-<?= htmlspecialchars(
                        $locale,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.fields.meta_description.label'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        (<?= htmlspecialchars(
                            strtoupper($locale),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>)
                    </label>

                    <textarea
                        id="page-meta-description-<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        name="translations[<?= htmlspecialchars(
                            $locale,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>][meta_description]"
                        rows="4"
                    ><?= htmlspecialchars(
                        $translatedMetaDescription,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>

            <?php endforeach; ?>

            <div class="admin-form-actions">

                <a
                    class="mv-admin-button"
                    href="/admin/pages"
                >
                    <?= htmlspecialchars(
                        $t('admin.page_form.actions.cancel'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <button
                    class="mv-admin-button"
                    type="submit"
                >
                    <i
                        class="fa-solid fa-floppy-disk"
                        aria-hidden="true"
                    ></i>

                    <?= htmlspecialchars(
                        $isEdit
                            ? $t('admin.page_form.actions.save')
                            : $t('admin.page_form.actions.create'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </button>

            </div>

        </form>

    </div>

    <?php if (
        $isEdit
        && $blockPageKey !== ''
    ): ?>

        <section class="admin-page-blocks">

            <header class="admin-section-header">

                <div>

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.page_form.composition.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t(
                                'admin.page_form.composition.description'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            </header>

            <div class="admin-page-blocks-grid">

                <article class="admin-page-block-area">

                    <span class="admin-page-block-icon">

                        <i
                            class="fa-solid fa-table-columns"
                            aria-hidden="true"
                        ></i>

                    </span>

                    <div class="admin-page-block-body">

                        <h3>
                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.composition.content.title'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.composition.content.description'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <a
                            class="mv-admin-button"
                            href="/admin/blocks/area?page=<?= rawurlencode(
                                $blockPageKey
                            ) ?>&amp;area=content"
                        >
                            <i
                                class="fa-solid fa-puzzle-piece"
                                aria-hidden="true"
                            ></i>

                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.actions.manage_widgets'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>

                    </div>

                </article>

                <article class="admin-page-block-area">

                    <span class="admin-page-block-icon">

                        <i
                            class="fa-solid fa-table-columns"
                            aria-hidden="true"
                        ></i>

                    </span>

                    <div class="admin-page-block-body">

                        <h3>
                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.composition.sidebar.title'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.composition.sidebar.description'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <a
                            class="mv-admin-button"
                            href="/admin/blocks/area?page=<?= rawurlencode(
                                $blockPageKey
                            ) ?>&amp;area=sidebar"
                        >
                            <i
                                class="fa-solid fa-puzzle-piece"
                                aria-hidden="true"
                            ></i>

                            <?= htmlspecialchars(
                                $t(
                                    'admin.page_form.actions.manage_widgets'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>

                    </div>

                </article>

            </div>

            <div class="admin-page-public-actions">

                <a
                    class="mv-admin-button"
                    href="/<?= rawurlencode($pageSlug) ?>"
                    target="_blank"
                    rel="noopener"
                >
                    <i
                        class="fa-solid fa-arrow-up-right-from-square"
                        aria-hidden="true"
                    ></i>

                    <?= htmlspecialchars(
                        $t('admin.page_form.actions.open'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <form
                    action="/admin/pages/<?= $pageId ?>/delete"
                    method="post"
                    onsubmit="return confirm(<?= htmlspecialchars(
                        json_encode(
                            $t('admin.page_form.confirm.delete'),
                            JSON_HEX_TAG
                            | JSON_HEX_AMP
                            | JSON_HEX_APOS
                            | JSON_HEX_QUOT
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>);"
                >
                    <button
                        class="mv-admin-button is-danger"
                        type="submit"
                    >
                        <i
                            class="fa-solid fa-trash"
                            aria-hidden="true"
                        ></i>

                        <?= htmlspecialchars(
                            $t('admin.page_form.actions.delete'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </button>

                </form>

            </div>

        </section>

    <?php else: ?>

        <div class="admin-page-blocks-placeholder">

            <i
                class="fa-solid fa-puzzle-piece"
                aria-hidden="true"
            ></i>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $t('admin.page_form.unsaved.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <p>
                    <?= htmlspecialchars(
                        $t('admin.page_form.unsaved.description'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>
