<?php declare(strict_types = 1);

namespace Drupal\niklan\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * {@selfdoc}
 */
final class NiklanStyle extends SymfonyStyle {

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function title(string $message): void {
    $this->writeln("<options=bold><comment>::</comment> {$message}</>");
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function info(array|string $message, string $prefix = '<fg=yellow;options=bold> -> </>'): void {
    if (!\is_iterable($message)) {
      $message = [$message];
    }

    foreach ($message as $item) {
      $this->writeln("{$prefix} {$item}");
    }
  }

  /**
   * {@selfdoc}
   */
  public function advancePseudoProgress(int $current, int $max, string $message): void {
    $max_char = \strlen((string) $max);
    $current = \str_pad((string) $current, $max_char, ' ', \STR_PAD_LEFT);

    $this->writeln("($current/$max) $message");
  }

}
