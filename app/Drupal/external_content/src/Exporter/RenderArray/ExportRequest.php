<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Exporter\ContentExporterContext;
use Drupal\external_content\Contract\Exporter\ContentExportRequest;
use Drupal\external_content\Nodes\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Exporter\ContentExportRequest<\Drupal\external_content\Exporter\Array\ExporterContext>
 */
final class ExportRequest implements ContentExportRequest {

  public function __construct(
    private RootNode $content,
    private ContentExporterContext $context,
    private Builder $builder,
  ) {}

  public function getContent(): RootNode {
    return $this->content;
  }

  /**
   * @return \Drupal\external_content\Exporter\Array\ExporterContext
   */
  public function getContext(): ContentExporterContext {
    return $this->context;
  }

  public function getArrayStructureBuilder(): Builder {
    return $this->builder;
  }

}
