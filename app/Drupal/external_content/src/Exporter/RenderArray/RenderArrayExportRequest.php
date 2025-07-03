<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Exporter\ExporterContext;
use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\Nodes\Document;

/**
 * @implements \Drupal\external_content\Contract\Exporter\ExportRequest<\Drupal\external_content\Exporter\RenderArray\RenderArrayExportRequest>
 */
final class RenderArrayExportRequest implements ExportRequest {

  public function __construct(
    private Document $content,
    private RenderArrayExporterContext $context,
    private RenderArrayBuilder $builder,
  ) {}

  public function getContent(): Document {
    return $this->content;
  }

  /**
   * @return \Drupal\external_content\Exporter\RenderArray\RenderArrayExporterContext
   */
  public function getContext(): ExporterContext {
    return $this->context;
  }

  public function getRenderArrayBuilder(): RenderArrayBuilder {
    return $this->builder;
  }

}
