<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms;

use League\HTMLToMarkdown\HtmlConverter;

final readonly class HtmlToMarkdownConverter {

  public function convert(string $html): string {
    $converter = new HtmlConverter([
      'strip_tags' => TRUE,
      'header_style' => 'atx',
      'remove_nodes' => 'script style nav form iframe',
    ]);

    return $converter->convert($html);
  }

}
