<?php

declare(strict_types=1);

namespace Drupal\niklan\Node\Markdown;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * @ingroup markdown
 */
abstract class BlockDirective extends AbstractBlock {

  final public function __construct(
    public readonly string $type,
    public readonly ?string $inlineContentRaw = NULL,
    public readonly ?string $argument = NULL,
    public readonly array $attributes = [],
    public readonly DirectiveInlineContent $inlineContent = new DirectiveInlineContent(),
  ) {
    parent::__construct();
  }

}
