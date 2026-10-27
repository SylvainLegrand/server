<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCP\Teams;

/**
 * A navigation entry linking to a resource or an embedded app view.
 *
 * @since 36.0.0
 */
final class TeamAppEntry implements \JsonSerializable {
	/**
	 * @param string $id App-local navigation entry ID.
	 * @param string $label Display name of the navigation entry.
	 * @param string|null $icon Emoji or SVG path data for a 24x24 view box, starting with an M/m move command.
	 *                          Full SVG markup and image URLs are not supported.
	 * @param string|null $href URL to open the resource outside the embedded view.
	 * @param list<TeamAppAction>|null $actions Commands available for this entry.
	 * @param string|null $referenceUrl Canonical URL resolved by the Smart Picker for an embedded preview.
	 * @since 36.0.0
	 */
	public function __construct(
		public readonly string $id,
		public readonly string $label,
		public readonly ?string $icon = null,
		public readonly ?string $href = null,
		public readonly ?array $actions = null,
		public readonly ?string $referenceUrl = null,
	) {
	}

	/**
	 * @return array<string, mixed>
	 * @since 36.0.0
	 */
	#[\Override]
	public function jsonSerialize(): array {
		return array_filter(get_object_vars($this), static fn ($value): bool => $value !== null);
	}
}
