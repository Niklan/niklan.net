<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Transformer\TransformerContext;
use Psr\Log\LoggerInterface;

final class HtmlTransformerContext implements TransformerContext {

  private ?HtmlNodeChildrenTransformer $htmlNodeChildrenTransformer = NULL;

  public function __construct(
    public string $rawHtmlContent,
    private readonly LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

  public function setHtmlNodeChildrenTransformer(HtmlNodeChildrenTransformer $transformer): void {
    $this->htmlNodeChildrenTransformer = $transformer;
  }

  public function getHtmNodeChildrenTransformer(): HtmlNodeChildrenTransformer {
    if (!$this->htmlNodeChildrenTransformer) {
      throw new \LogicException("The child HTML parser shouldn't be called before it is set");
    }

    return $this->htmlNodeChildrenTransformer;
  }

}
