<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ConverterResult;
use Drupal\external_content\Source\Html;

/**
 * {@selfdoc}
 */
final class HtmlConverter implements ConverterInterface, EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function convert(SourceInterface $input): ConverterResult {
    $expected_html_types = $this
      ->environment
      ->getConfiguration()
      ->get('html_converter.expected_types');

    if (!\in_array($input->type(), $expected_html_types)) {
      return ConverterResult::pass();
    }

    $html = new Html(
      contents: $input->contents(),
      data: $input->data(),
    );

    return ConverterResult::withHtml($html);
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
