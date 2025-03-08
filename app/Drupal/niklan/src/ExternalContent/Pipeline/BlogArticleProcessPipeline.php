<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Pipeline;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Pipeline;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\external_content\Pipeline\NullConfig;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\niklan\ExternalContent\Domain\BlogArticleProcessContext;
use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;

final readonly class BlogArticleProcessPipeline implements Pipeline {

  private Pipeline $pipeline;

  public function __construct() {
    $this->pipeline = new SequentialPipeline();
  }

  public function addStage(Stage $stage, Config $config = new NullConfig()): void {
    $this->pipeline->addStage($stage, $config);
  }

  public function run(Context $context): Context {
    if (!$context instanceof BlogSyncContext) {
      throw new \InvalidArgumentException('Invalid context');
    }

    foreach ($context->getArticles() as $article) {
      $article_process_context = new BlogArticleProcessContext($article, $context);
      $this->pipeline->run($article_process_context);
    }

    return $context;
  }

}
