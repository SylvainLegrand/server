<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCP\Teams;

/**
 * Optional navigation entry changes and the entry to open after an action.
 * Null fields leave the current navigation and open entry unchanged.
 *
 * @since 36.0.0
 */
final class TeamAppActionResult implements \JsonSerializable {
	/**
	 * @param list<string>|null $navigationEntryIdsToAdd
	 * @param list<string>|null $navigationEntryIdsToRemove
	 * @since 36.0.0
	 */
	public function __construct(
		public readonly ?array $navigationEntryIdsToAdd = null,
		public readonly ?array $navigationEntryIdsToRemove = null,
		public readonly ?string $open = null,
	) {
	}

	/**
	 * @return array{navigationEntryIdsToAdd?: list<string>, navigationEntryIdsToRemove?: list<string>, open?: string}
	 * @since 36.0.0
	 */
	#[\Override]
	public function jsonSerialize(): array {
		$result = [];
		if ($this->navigationEntryIdsToAdd !== null) {
			$result['navigationEntryIdsToAdd'] = $this->navigationEntryIdsToAdd;
		}
		if ($this->navigationEntryIdsToRemove !== null) {
			$result['navigationEntryIdsToRemove'] = $this->navigationEntryIdsToRemove;
		}
		if ($this->open !== null) {
			$result['open'] = $this->open;
		}
		return $result;
	}
}
