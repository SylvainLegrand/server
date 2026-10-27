<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCP\Teams;

/**
 * Navigation entries and actions for a team.
 *
 * @since 36.0.0
 */
final class TeamAppNavigation implements \JsonSerializable {
	/**
	 * @param list<TeamAppEntry> $entries App-owned navigation entries for the team.
	 * @param list<TeamAppAction>|null $actions Actions available for the team.
	 * @since 36.0.0
	 */
	public function __construct(
		public readonly array $entries,
		public readonly ?array $actions = null,
	) {
	}

	/**
	 * @return array{entries: list<TeamAppEntry>, actions?: list<TeamAppAction>}
	 * @since 36.0.0
	 */
	#[\Override]
	public function jsonSerialize(): array {
		$result = ['entries' => $this->entries];
		if ($this->actions !== null) {
			$result['actions'] = $this->actions;
		}
		return $result;
	}
}
