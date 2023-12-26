<?php declare(strict_types = 1);

namespace Drupal\niklan\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Provides a 'niklanHistoryReplaceState' command for updating URL.
 */
final class HistoryReplaceStateCommand implements CommandInterface {

  /**
   * Constructs a new HistoryReplaceStateCommand instance.
   *
   * @param string $url
   *   The URL to replace by.
   * @param array|null $stateObj
   *   The state object.
   */
  public function __construct(
    protected string $url,
    protected ?array $stateObj = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    return [
      'command' => 'niklanHistoryReplaceState',
      'stateObj' => $this->stateObj,
      'url' => $this->url,
    ];
  }

}
