<div id="mv-doge-tip-modal" class="mv-modal" hidden>

	<div class="mv-modal-backdrop"></div>

	<div
		class="mv-modal-dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="mv-doge-tip-title"
	>

		<div class="mv-modal-header">

			<h3 id="mv-doge-tip-title">
				<?= htmlspecialchars(
					$t('crypto.tip_modal.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h3>

			<button
				type="button"
				class="mv-modal-close mv-doge-tip-close"
				aria-label="<?= htmlspecialchars(
					$t('crypto.tip_modal.close'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>
				&times;
			</button>

		</div>

		<div class="mv-modal-body">

			<p>
				<?= htmlspecialchars(
					$t('crypto.tip_modal.intro_before_recipient'),
					ENT_QUOTES,
					'UTF-8'
				) ?>

				<strong id="mv-doge-tip-recipient">
					<?= htmlspecialchars(
						$username,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</strong>.
			</p>

			<div
				id="mv-doge-tip-fallback"
				class="mv-doge-tip-fallback"
				hidden
			>

				<div class="mv-doge-tip-fallback-label">
					<?= htmlspecialchars(
						$t('crypto.tip_modal.fallback.address_label'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</div>

				<div class="mv-doge-tip-qr-wrap">

					<div
						id="mv-doge-tip-qr"
						class="mv-doge-tip-qr"
						data-doge-address="<?= htmlspecialchars(
							$dogeTipAddress,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						aria-label="<?= htmlspecialchars(
							$t('crypto.tip_modal.fallback.qr_label'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					></div>

				</div>

				<div class="mv-doge-tip-fallback-address">

					<code id="mv-doge-tip-fallback-address">
						<?= htmlspecialchars(
							$dogeTipAddress,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</code>

					<button
						type="button"
						class="mv-button mv-doge-tip-copy"
						id="mv-doge-tip-copy"
					>
						<?= htmlspecialchars(
							$t('crypto.tip_modal.fallback.copy'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

				<p class="mv-doge-tip-fallback-help">
					<?= htmlspecialchars(
						$t('crypto.tip_modal.fallback.help'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

			<div class="mv-form-group">

				<label for="mv-doge-tip-amount">
					<?= htmlspecialchars(
						$t('crypto.tip_modal.amount.label'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					id="mv-doge-tip-amount"
					type="number"
					min="0.00000001"
					step="0.00000001"
					inputmode="decimal"
					placeholder="10"
				>

			</div>

			<div
				id="mv-doge-tip-share-profile"
				class="mv-doge-tip-share-context"
			>

				<label class="mv-doge-tip-share">

					<input
						type="checkbox"
						id="mv-doge-tip-share"
						value="1"
					>

					<span>
						<strong>
							<?= htmlspecialchars(
								$t('crypto.tip_modal.share_profile.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<small>
							<?= htmlspecialchars(
								$t('crypto.tip_modal.share_profile.help'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</small>
					</span>

				</label>

			</div>

			<div
				id="mv-doge-tip-share-pong"
				class="mv-doge-tip-share-context"
				hidden
			>

				<label class="mv-doge-tip-share">

					<input
						type="checkbox"
						id="mv-doge-tip-pong"
						value="1"
					>

					<span>
						<strong>
							<?= htmlspecialchars(
								$t('crypto.tip_modal.share_pong.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<small>
							<?= htmlspecialchars(
								$t('crypto.tip_modal.share_pong.help'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</small>
					</span>

				</label>

				<div
					id="mv-doge-tip-pong-message-wrap"
					class="mv-form-group"
					hidden
				>

					<label for="mv-doge-tip-pong-message">
						<?= htmlspecialchars(
							$t('crypto.tip_modal.share_pong.message_label'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<textarea
						id="mv-doge-tip-pong-message"
						rows="3"
						maxlength="500"
						placeholder="<?= htmlspecialchars(
							$t('crypto.tip_modal.share_pong.message_placeholder'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					></textarea>

				</div>

			</div>

			<div
				id="mv-doge-tip-status"
				class="mv-doge-tip-status"
				aria-live="polite"
			></div>

		</div>

		<div class="mv-modal-footer">

			<button
				type="button"
				class="mv-button mv-doge-tip-close mv-doge-tip-footer-close"
			>
				<?= htmlspecialchars(
					$t('crypto.tip_modal.cancel'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</button>

			<button
				type="button"
				id="mv-doge-tip-send"
				class="mv-button mv-button-primary"
			>
				<?= htmlspecialchars(
					$t('crypto.tip_modal.send'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</button>

		</div>

	</div>

</div>