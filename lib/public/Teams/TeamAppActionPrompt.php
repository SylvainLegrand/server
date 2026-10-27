<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCP\Teams;

/**
 * A text prompt displayed before executing an app action.
 *
 * @since 36.0.0
 */
final class TeamAppActionPrompt implements \JsonSerializable {
	/** @since 36.0.0 */
	public function __construct(
		public readonly string $title,
		public readonly ?string $value = null,
	) {
	}

	/**
	 * @return array{type: 'prompt', title: string, value?: string}
	 * @since 36.0.0
	 */
	#[\Override]
	public function jsonSerialize(): array {
		$result = ['type' => 'prompt', 'title' => $this->title];
		if ($this->value !== null) {
			$result['value'] = $this->value;
		}
		return $result;
	}
}
