<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Test\Teams;

use OCP\Teams\TeamAppAction;
use OCP\Teams\TeamAppActionConfirmation;
use OCP\Teams\TeamAppActionPrompt;
use OCP\Teams\TeamAppActionResult;
use OCP\Teams\TeamAppEntry;
use OCP\Teams\TeamAppNavigation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TeamAppDataTest extends TestCase {
	public function testSerializesNestedNavigation(): void {
		$icon = 'M3 3H21V21H3Z';
		$prompt = new TeamAppActionPrompt('Board name', 'Sketch');
		$action = new TeamAppAction('rename', 'Rename', $icon, $prompt);
		$entry = new TeamAppEntry('42', 'Board', $icon, '/board/42', [$action], 'https://cloud.example/f/42');
		$navigation = new TeamAppNavigation([$entry], [$action]);
		$expectedAction = [
			'id' => 'rename', 'label' => 'Rename', 'icon' => $icon,
			'dialog' => ['type' => 'prompt', 'title' => 'Board name', 'value' => 'Sketch'],
		];

		$this->assertSame([$entry], $navigation->entries);
		$this->assertSame([$action], $entry->actions);
		$this->assertSame($prompt, $action->dialog);
		$this->assertSame(['entries' => [[
			'id' => '42', 'label' => 'Board', 'icon' => $icon,
			'href' => '/board/42', 'actions' => [$expectedAction],
			'referenceUrl' => 'https://cloud.example/f/42',
		]], 'actions' => [$expectedAction]], $this->decode($navigation));
	}

	#[DataProvider('actionDialogs')]
	public function testSerializesOneActionDialog(TeamAppActionPrompt|TeamAppActionConfirmation $dialog, array $expected): void {
		$action = new TeamAppAction('example', 'Example', dialog: $dialog);
		$this->assertSame($dialog, $action->dialog);
		$this->assertSame(['id' => 'example', 'label' => 'Example', 'dialog' => $expected], $this->decode($action));
	}

	public static function actionDialogs(): array {
		return [
			'prompt' => [new TeamAppActionPrompt('Name'), ['type' => 'prompt', 'title' => 'Name']],
			'prefilled prompt' => [new TeamAppActionPrompt('Name', 'Guide'), ['type' => 'prompt', 'title' => 'Name', 'value' => 'Guide']],
			'empty prompt value' => [new TeamAppActionPrompt('Name', ''), ['type' => 'prompt', 'title' => 'Name', 'value' => '']],
			'confirmation' => [new TeamAppActionConfirmation('Delete', 'Delete this item?'), ['type' => 'confirm', 'title' => 'Delete', 'message' => 'Delete this item?']],
		];
	}

	public function testPreservesPathAndEmojiIcons(): void {
		foreach (['M3 3H21V21H3Z', "\u{1F4DA}", "\u{1F469}\u{200D}\u{1F4BB}"] as $icon) {
			$this->assertSame(['id' => '42', 'label' => 'Example', 'icon' => $icon], $this->decode(new TeamAppEntry('42', 'Example', icon: $icon)));
			$this->assertSame(['id' => 'create', 'label' => 'Example', 'icon' => $icon], $this->decode(new TeamAppAction('create', 'Example', icon: $icon)));
		}
	}

	public function testOmitsAbsentOptionalFields(): void {
		$this->assertSame(['entries' => []], $this->decode(new TeamAppNavigation([])));
		$this->assertSame(['id' => '42', 'label' => 'Board'], $this->decode(new TeamAppEntry('42', 'Board')));
		$this->assertSame(['id' => 'rename', 'label' => 'Rename'], $this->decode(new TeamAppAction('rename', 'Rename')));
		$this->assertSame(['type' => 'prompt', 'title' => 'Name'], $this->decode(new TeamAppActionPrompt('Name')));
		$this->assertSame([], $this->decode(new TeamAppActionResult()));
	}

	public function testPreservesExplicitEmptyLists(): void {
		$this->assertSame(['entries' => [], 'actions' => []], $this->decode(new TeamAppNavigation([], [])));
		$this->assertSame(['id' => '42', 'label' => 'Board', 'actions' => []], $this->decode(new TeamAppEntry('42', 'Board', actions: [])));
		$this->assertSame(['navigationEntryIdsToAdd' => []], $this->decode(new TeamAppActionResult(navigationEntryIdsToAdd: [])));
		$this->assertSame(['navigationEntryIdsToRemove' => []], $this->decode(new TeamAppActionResult(navigationEntryIdsToRemove: [])));
	}

	public function testSerializesActionResult(): void {
		$result = new TeamAppActionResult(['42'], ['99'], '42');

		$this->assertSame(['42'], $result->navigationEntryIdsToAdd);
		$this->assertSame(['99'], $result->navigationEntryIdsToRemove);
		$this->assertSame('42', $result->open);
		$this->assertSame(['navigationEntryIdsToAdd' => ['42'], 'navigationEntryIdsToRemove' => ['99'], 'open' => '42'], $this->decode($result));
		$this->assertSame(['navigationEntryIdsToAdd' => ['42']], $this->decode(new TeamAppActionResult(navigationEntryIdsToAdd: ['42'])));
		$this->assertSame(['open' => '42'], $this->decode(new TeamAppActionResult(open: '42')));
		$this->assertSame(['open' => ''], $this->decode(new TeamAppActionResult(open: '')));
	}

	private function decode(\JsonSerializable $value): array {
		return json_decode(json_encode($value, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
	}
}
