<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Markdown\Parser;

use Drupal\niklan\Helper\CommonMarkDirectiveHelper;
use Drupal\niklan\Markup\Markdown\Node\BlockDirective;
use Drupal\niklan\Markup\Markdown\Node\LeafBlockDirective;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockContinueParserWithInlinesInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserEngineInterface;

/**
 * @ingroup markdown
 */
final class LeafBlockDirectiveParser extends AbstractBlockContinueParser implements BlockContinueParserWithInlinesInterface {

  private readonly BlockDirective $block;

  public function __construct(
    public readonly string $directiveInfo,
  ) {
    $info = CommonMarkDirectiveHelper::parseInfoString($this->directiveInfo);
    $attributes = CommonMarkDirectiveHelper::parseExtraAttributes($info['attributes'] ?? '');
    $attributes = CommonMarkDirectiveHelper::flattenExtraAttributes($attributes);

    $this->block = new LeafBlockDirective(
      type: $info['type'],
      inlineContentRaw: $info['inline-content'],
      argument: $info['argument'],
      attributes: $attributes,
    );
  }

  #[\Override]
  public function getBlock(): AbstractBlock {
    return $this->block;
  }

  #[\Override]
  public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue {
    // Since this directive is not a container and the cursor has already been
    // moved to the end of the line in the initial parser, the continuation is
    // immediately interrupted.
    return BlockContinue::none();
  }

  #[\Override]
  public function isContainer(): bool {
    return FALSE;
  }

  #[\Override]
  public function parseInlines(InlineParserEngineInterface $inlineParser): void {
    if (!$this->block->inlineContentRaw) {
      return;
    }

    $inlineParser->parse(
      contents: $this->block->inlineContentRaw,
      block: $this->block->inlineContent,
    );
  }

}
