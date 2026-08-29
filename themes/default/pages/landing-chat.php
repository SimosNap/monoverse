<?php
declare(strict_types=1);

$user = is_array($user ?? null) ? $user : [];
$profile = is_array($profile ?? null) ? $profile : [];
$settings = is_array($settings ?? null) ? $settings : [];

$isLogged = !empty($user['sub']);
$hasDatabaseProfile = !empty($profile);

$aliases = is_array($user['aliases'] ?? null)
	? array_values(array_filter($user['aliases'], 'is_string'))
	: [];

$siteName = (string) ($settings['site_name'] ?? 'Monoverse Community');
$siteTagline = (string) ($settings['site_tagline'] ?? 'Community IRC');

$selectedNickname = (string) (
	$profile['nickname']
	?? $user['nickname']
	?? ''
);

$selectedAge = (string) ($profile['age'] ?? '');
$selectedCity = (string) ($profile['city'] ?? '');
$selectedSex = (string) ($profile['sex'] ?? 'U');
$avatarUrl = (string) ($user['avatar_url'] ?? '');

$hasCompleteChatProfile =
	$isLogged
	&& trim($selectedAge) !== ''
	&& trim($selectedCity) !== ''
	&& in_array($selectedSex, ['M', 'F', 'O'], true);

$sexLabels = [
	'M' => $t('landing_chat.profile.male'),
	'F' => $t('landing_chat.profile.female'),
	'O' => $t('landing_chat.profile.other'),
];


$communityUpdates = [
	'Nuovo bridge Telegram disponibile',
	'Radio Contatto è online',
	'Aperto il canale #linux',
];
?>

	<section class="mv-town">
		<?php if (!empty($settings['landing_show_hero'])): ?>

			<?php
			$channelName = (string) (
				$channelInfo['name']
					?? $defaultChannel
					?? '#community'
			);

			if (
				$channelName !== ''
				&& $channelName[0] !== '#'
			) {
				$channelName = '#' . $channelName;
			}
			?>

			<header class="mv-town-hero">

				<div class="mv-town-hero-identity">

					<span class="mv-town-hero-label">
						<?= htmlspecialchars(
							$t('landing_chat.hero.community'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<h1>
						<?= htmlspecialchars(
							$channelName,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h1>

					<p>
						<?= htmlspecialchars(
							$t('landing_chat.hero.identity'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-town-hero-visual">

					<img
						src="/themes/default/assets/images/landing-chat/community-hero-tight.webp"
						alt=""
						loading="eager"
						decoding="async"
					>

				</div>

			</header>

		<?php endif; ?>

	<main class="mv-town-main">

	<?php if (!empty($blocksBeforeEntry)): ?>

		<section class="mv-block-area mv-block-area-before-entry">
			<?= $blocksBeforeEntry ?>
		</section>

	<?php endif; ?>

		<section class="mv-town-entry" id="entra">

			<div class="mv-town-entry-copy">

				<?php if (!empty($blocksEntryLeftBefore)): ?>

					<section class="mv-block-area mv-block-area-entry-left-before">
						<?= $blocksEntryLeftBefore ?>
					</section>

				<?php endif; ?>

				<?php if (!empty($settings['landing_show_channel_card'])): ?>

					<?= $component('landing-channel-card', [
						'channelInfo' => $channelInfo ?? [],
						'channelFeatures' => $channelFeatures ?? [],
						'isLogged' => $isLogged,
					]) ?>

				<?php endif; ?>

				<?php if (!empty($blocksEntryLeftAfter)): ?>

					<section class="mv-block-area mv-block-area-entry-left-after">
						<?= $blocksEntryLeftAfter ?>
					</section>

				<?php endif; ?>
			</div>

			<article class="mv-town-form-card">

			<?php if (
				!empty($registeredOnly)
				&& !$isLogged
			): ?>

				<div class="mv-town-registered-only">

					<div class="mv-town-registered-only-head">

						<div
							class="mv-town-registered-only-icon"
							aria-hidden="true"
						>
							<i class="fa-solid fa-user-lock"></i>
						</div>

						<div class="mv-town-registered-only-copy">

							<h3>
								<?= htmlspecialchars(
									$t('landing_chat.registered_only.title'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</h3>

							<p>
								<?= htmlspecialchars(
									$t('landing_chat.registered_only.description'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

							<p>
								<?= htmlspecialchars(
									$t('landing_chat.registered_only.security'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						</div>

					</div>

					<div class="mv-town-registered-only-actions">

						<a
							href="/oauth/login"
							class="mv-town-registered-only-login"
						>
							<i
								class="fa-solid fa-right-to-bracket"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t('landing_chat.registered_only.login'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</a>

						<a
							href="/register"
							class="mv-town-registered-only-register"
						>
							<span>
								<?= htmlspecialchars(
									$t('landing_chat.registered_only.register'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<i
								class="fa-solid fa-arrow-right"
								aria-hidden="true"
							></i>
						</a>

					</div>

				</div>

			<?php else: ?>

			<div class="mv-town-form-header">

					<div class="mv-town-form-header-icon" aria-hidden="true">
						<i class="fa-solid fa-right-to-bracket"></i>
					</div>

					<div>
						<h3>
							<?= htmlspecialchars(
								$t('landing_chat.entry.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h3>

						<p>
							<?= htmlspecialchars(
								$t('landing_chat.entry.subtitle'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>
					</div>

				</div>

				<form
					id="landing-chat-form"
					action="<?= htmlspecialchars(
						(string) ($kiwiUrl ?? 'https://kiwiirc.simosnap.com/login.php'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					method="post"
					target="chat"
					name="kiwiircform"
					class="mv-town-form"
					data-profile-source="<?= $hasDatabaseProfile ? 'database' : 'local' ?>"
					data-registered-only="<?= !empty($registeredOnly) ? 'true' : 'false' ?>"
					data-is-logged="<?= $isLogged ? 'true' : 'false' ?>">

					<div
						id="landing-chat-step-entry"
						data-chat-step="entry"
					>

					<div class="mv-town-field mv-town-field-main">

						<label for="nickinput">
							<?= htmlspecialchars(
								$t('landing_chat.nickname.label'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<?php if ($isLogged && $aliases !== []): ?>

						<input
							id="nickinput"
							type="hidden"
							name="nick"
							value="<?= htmlspecialchars(
								$selectedNickname,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

						<div
							class="mv-town-identity"
							data-chat-identity
						>

							<div class="mv-town-identity-avatar">

								<?php if ($avatarUrl !== ''): ?>

									<img
										src="<?= htmlspecialchars(
											$avatarUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										alt=""
									>

								<?php else: ?>

									<span>
										<?= htmlspecialchars(
											mb_strtoupper(
												mb_substr(
													$selectedNickname !== ''
														? $selectedNickname
														: 'U',
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

							<div class="mv-town-identity-main">

								<span class="mv-town-identity-label">
									<?= htmlspecialchars(
										$t('landing_chat.nickname.join_as'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<strong data-chat-identity-nickname>
									<?= htmlspecialchars(
										$selectedNickname,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							</div>

							<?php if (count($aliases) > 1): ?>

								<button
									type="button"
									class="mv-town-identity-toggle"
									data-chat-identity-toggle
									aria-label="<?= htmlspecialchars(
										$t('landing_chat.nickname.change'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									aria-expanded="false"
								>
									<i
										class="fa-solid fa-chevron-down"
										aria-hidden="true"
									></i>
								</button>

								<div
									class="mv-town-identity-menu"
									data-chat-identity-menu
									hidden
								>

									<span class="mv-town-identity-menu-title">
										<?= htmlspecialchars(
											$t('landing_chat.nickname.choose'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

									<?php foreach ($aliases as $alias): ?>

										<button
											type="button"
											class="mv-town-identity-option <?= $alias === $selectedNickname
												? 'is-active'
												: '' ?>"
											data-chat-identity-option
											data-nickname="<?= htmlspecialchars(
												$alias,
												ENT_QUOTES,
												'UTF-8'
											) ?>"
										>
											<span>
												<?= htmlspecialchars(
													$alias,
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</span>

											<?php if ($alias === $selectedNickname): ?>

												<i
													class="fa-solid fa-check"
													aria-hidden="true"
												></i>

											<?php endif; ?>

										</button>

									<?php endforeach; ?>

								</div>

							<?php endif; ?>

						</div>

						<?php else: ?>

							<input
								id="nickinput"
								type="text"
								name="nick"
								value="<?= htmlspecialchars(
									$selectedNickname,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								placeholder="<?= htmlspecialchars(
									$t('landing_chat.nickname.placeholder'),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								autocomplete="nickname"
								autocapitalize="none"
								spellcheck="false"
								required>

						<?php endif; ?>

						</div>

						<div
							id="nsnotify"
							class="mv-town-alert"
							hidden>

							<i class="fa-solid fa-user-lock" aria-hidden="true"></i>

							<div class="mv-town-alert-content">
								<?= htmlspecialchars(
									$t('landing_chat.nickname.registered'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</div>

						</div>

						<div
							id="nsok"
							class="mv-town-alert mv-town-alert-success"
							hidden>

							<i class="fa-solid fa-circle-check" aria-hidden="true"></i>

							<div class="mv-town-alert-content">
								<?= htmlspecialchars(
									$t('landing_chat.nickname.available'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</div>

						</div>

						<div
							id="nsloading"
							class="mv-town-alert mv-town-alert-loading"
							hidden>

							<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>

							<div class="mv-town-alert-content">
								<?= htmlspecialchars(
									$t('landing_chat.nickname.checking'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</div>

						</div>

						<div
							id="nsregisteredonly"
							class="mv-town-nick-restricted"
							hidden
						>

							<i
								class="fa-solid fa-circle-exclamation"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t('landing_chat.nickname.registered_only'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						</div>


					<?php if (!$hasCompleteChatProfile): ?>

						<div class="mv-town-fields-row">

							<div class="mv-town-field">

								<label for="age">
									<?= htmlspecialchars(
										$t('landing_chat.profile.age'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</label>

								<input
									id="age"
									type="text"
									name="age"
									value="<?= htmlspecialchars(
										$selectedAge,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									placeholder="<?= htmlspecialchars(
										$t('landing_chat.profile.optional'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									inputmode="numeric">

							</div>

							<div class="mv-town-field">

								<label for="sex-select">
									<?= htmlspecialchars(
										$t('landing_chat.profile.type'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</label>

								<select id="sex-select" name="sex">

									<option
										value="U"
										<?= $selectedSex === 'U' ? 'selected' : '' ?>>
										<?= htmlspecialchars(
											$t('landing_chat.profile.not_specified'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</option>

									<?php foreach ($sexLabels as $sex => $label): ?>

										<option
											value="<?= htmlspecialchars(
												$sex,
												ENT_QUOTES,
												'UTF-8'
											) ?>"
											<?= $selectedSex === $sex ? 'selected' : '' ?>>
											<?= htmlspecialchars(
												$label,
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</option>

									<?php endforeach; ?>

								</select>

							</div>

						</div>

						<div class="mv-town-field">

							<label for="location">
								<?= htmlspecialchars(
									$t('landing_chat.profile.city'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</label>

							<input
								id="location"
								type="text"
								name="location"
								value="<?= htmlspecialchars(
									$selectedCity,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								placeholder="<?= htmlspecialchars(
									$t('landing_chat.profile.city_placeholder'),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								autocomplete="address-level2">

						</div>

					<?php else: ?>

						<details class="mv-town-profile-summary">

							<summary class="mv-town-profile-summary-toggle">

								<span class="mv-town-profile-summary-icon" aria-hidden="true">
									<i class="fa-solid fa-user-check"></i>
								</span>

								<span class="mv-town-profile-summary-title">
									<?= htmlspecialchars(
										$t('landing_chat.profile.loaded'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<span
									class="mv-town-profile-summary-chevron"
									aria-hidden="true"
								>
									<i class="fa-solid fa-chevron-down"></i>
								</span>

							</summary>

							<div class="mv-town-profile-summary-content">

								<div class="mv-town-profile-summary-item">

									<i
										class="fa-solid fa-cake-candles"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$selectedAge,
											ENT_QUOTES,
											'UTF-8'
										) ?>

										<?= htmlspecialchars(
											$t('landing_chat.profile.years'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</div>

								<div class="mv-town-profile-summary-item">

									<i
										class="fa-solid fa-location-dot"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$selectedCity,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</div>

								<div class="mv-town-profile-summary-item">

									<i
										class="fa-solid fa-user"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$sexLabels[$selectedSex]
												?? $t('landing_chat.profile.not_specified'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</div>

								<a
									href="/account/profile"
									class="mv-town-profile-summary-edit"
								>
									<i
										class="fa-solid fa-pen"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$t('landing_chat.profile.edit'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>
								</a>

							</div>

						</details>

					<?php endif; ?>

					<?php if (!$isLogged): ?>

						<label class="mv-town-confirm">

							<input
								type="checkbox"
								name="ageverify"
								value="true"
								required
								aria-label="<?= htmlspecialchars(
									$t('landing_chat.age_verification'),
									ENT_QUOTES,
									'UTF-8'
								) ?>">

							<span>
								<?= htmlspecialchars(
									$t('landing_chat.age_verification'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						</label>

					<?php endif; ?>

					<div class="mv-town-form-footer">

						<details class="mv-town-options">

							<summary>
								<?= htmlspecialchars(
									$t('landing_chat.preferences.title'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</summary>

							<label>

								<input
									type="checkbox"
									name="show_joinparts"
									value="true"
									checked>

								<span>
									<?= htmlspecialchars(
										$t('landing_chat.preferences.hide_join_parts'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							</label>

						</details>

						<button class="mv-town-submit" type="submit">

							<span>
								<?= htmlspecialchars(
									$t('landing_chat.actions.join'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<span
								class="mv-town-submit-icon"
								aria-hidden="true">
								→
							</span>

						</button>

					</div>

					<?php if (!$isLogged): ?>

						<p class="mv-town-login-note">
							<a href="/oauth/login">
								<?= htmlspecialchars(
									$t('landing_chat.actions.login_sync'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>
						</p>

					<?php endif; ?>

					</div>

					<div
						id="landing-chat-step-auth"
						class="mv-town-auth-step"
						data-chat-step="auth"
						hidden
					>

						<div class="mv-town-auth-step-icon" aria-hidden="true">
							<i class="fa-solid fa-user-lock"></i>
						</div>

						<div class="mv-town-auth-step-copy">

							<h4>
								<?= htmlspecialchars(
									$t('landing_chat.auth.title'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</h4>

							<p>
								<?= htmlspecialchars(
									$t('landing_chat.auth.description_before_nickname'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

								<strong data-auth-nickname></strong>

								<?= htmlspecialchars(
									$t('landing_chat.auth.description_after_nickname'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						</div>

						<div class="mv-town-field mv-town-auth-password">

							<label for="nspwd">
								<?= htmlspecialchars(
									$t('landing_chat.auth.password_label'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</label>

							<div class="mv-town-auth-password-input">

								<i
									class="fa-solid fa-key"
									aria-hidden="true"
								></i>

								<input
									id="nspwd"
									type="password"
									name="password"
									placeholder="<?= htmlspecialchars(
										$t('landing_chat.auth.password_placeholder'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									autocomplete="current-password"
								>

							</div>

						</div>

						<div class="mv-town-auth-actions">

							<button
								type="button"
								class="mv-town-auth-back"
								data-auth-back
							>
								<i
									class="fa-solid fa-arrow-left"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t('landing_chat.actions.back'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>
							</button>

							<button
								class="mv-town-submit"
								type="submit"
							>
								<span>
									<?= htmlspecialchars(
										$t('landing_chat.actions.authenticate_join'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<span
									class="mv-town-submit-icon"
									aria-hidden="true"
								>
									→
								</span>
							</button>

						</div>

					</div>

					<input
						type="hidden"
						name="title"
						value="<?= htmlspecialchars(
							(string) $chatTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>">

					<input
						type="hidden"
						name="channel"
						value="<?= htmlspecialchars(
							(string) $defaultChannel,
							ENT_QUOTES,
							'UTF-8'
						) ?>">

					<input
						type="hidden"
						name="theme"
						value="<?= htmlspecialchars(
							(string) $chatTheme,
							ENT_QUOTES,
							'UTF-8'
						) ?>">

					<input type="hidden" name="layout" value="">
					<input type="hidden" name="conference" value="true">
					<input type="hidden" name="fileuploader" value="true">

					<input
						type="hidden"
						name="stateKey"
						value="<?= htmlspecialchars(
							(string) $stateKey,
							ENT_QUOTES,
							'UTF-8'
						) ?>">

					<input type="hidden" name="radio" value="off">
					<input type="hidden" name="streaming" value="">
					<input type="hidden" name="radioname" value="">
					<input type="hidden" name="radioweb" value="">

				</form>

			<?php endif; ?>

			</article>

		</section>
	<?php if (!empty($blocksAfterEntry)): ?>

		<section class="mv-block-area mv-block-area-after-entry">
			<?= $blocksAfterEntry ?>
		</section>

	<?php endif; ?>
	</main>
	<?php if (!empty($blocksBeforeFooter)): ?>

		<section class="mv-block-area mv-block-area-before-footer">
			<?= $blocksBeforeFooter ?>
		</section>

	<?php endif; ?>
	<footer class="mv-town-footer">

		<span>
			<?= htmlspecialchars(
				$t('landing_chat.footer.powered_by'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</span>

		<?php if ($isLogged): ?>

			<a href="/account">
				<?= htmlspecialchars(
					$t('landing_chat.footer.account'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		<?php else: ?>

			<a href="/oauth/login">
				<?= htmlspecialchars(
					$t('landing_chat.footer.login'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		<?php endif; ?>

	</footer>

</section>

<script src="/themes/default/assets/js/account.js"></script>
<script src="/themes/default/assets/js/landing-chat.js"></script>