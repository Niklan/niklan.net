<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Parser\ArticleXmlParser;
use Drupal\niklan\ExternalContent\Stages\ArticleFinder;
use Drupal\niklan\ExternalContent\Stages\ArticleProcessor;
use Drupal\niklan\ExternalContent\Validation\XmlValidator;

final readonly class ArticleSyncPipeline implements Pipeline {

  private Pipeline $pipeline;

  public function __construct() {
    $article_xml_validator = new XmlValidator();
    $article_xml_parser = new ArticleXmlParser($article_xml_validator);

    $this->pipeline = new SequentialPipeline();
    $this->pipeline->addStage(new ArticleFinder($article_xml_parser));
    $this->pipeline->addStage(new ArticleProcessor());
  }

  public function addStage(PipelineStage $stage, int $priority = 0): void {
    $this->pipeline->addStage($stage, $priority);
  }

  /**
   * @param \Drupal\niklan\ExternalContent\Domain\SyncContext $context
   */
  public function run(PipelineContext $context): void {
    if (!$context instanceof SyncContext) {
      throw new \InvalidArgumentException('Invalid context');
    }

    $this->pipeline->run($context);
  }

}
