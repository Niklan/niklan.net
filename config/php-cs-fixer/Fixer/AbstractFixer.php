<?php

declare(strict_types=1);

namespace Niklan\PhpCsFixer\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

abstract class AbstractFixer implements FixerInterface {

  public function supports(SplFileInfo $file): bool {
    return true;
  }

  public function getPriority(): int {
    return 0;
  }

  public function isRisky(): bool {
    return false;
  }

  public function fix(SplFileInfo $file, Tokens $tokens): void {
    if (!$this->isCandidate($tokens)) {
      return;
    }

    $this->applyFix($file, $tokens);
  }

  abstract protected function applyFix(SplFileInfo $file, Tokens $tokens): void;

}
