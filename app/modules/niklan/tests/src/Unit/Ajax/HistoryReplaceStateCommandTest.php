<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Ajax;

use Drupal\niklan\Http\Ajax\HistoryReplaceStateCommand;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HistoryReplaceStateCommand::class)]
final class HistoryReplaceStateCommandTest extends UnitTestCase {

  /**
   * Tests command with URL only.
   */
  public function testOnlyUrl(): void {
    $command = new HistoryReplaceStateCommand('https://example.com');
    $expected = [
      'command' => 'niklanHistoryReplaceState',
      'stateObj' => NULL,
      'url' => 'https://example.com',
    ];

    self::assertEquals($expected, $command->render());
  }

  /**
   * Tests command with state object.
   */
  public function testWithStateObj(): void {
    $state_obj = ['foo' => 'bar', 'baz' => 'bazz'];
    $command = new HistoryReplaceStateCommand('https://example.com', $state_obj);
    $expected = [
      'command' => 'niklanHistoryReplaceState',
      'stateObj' => $state_obj,
      'url' => 'https://example.com',
    ];

    self::assertEquals($expected, $command->render());
  }

}
