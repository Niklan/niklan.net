<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\Html;

/**
 * {@selfdoc}
 */
interface HtmlParserManagerInterface {

  /**
   * {@selfdoc}
   */
  public function parse(Html $html, EnvironmentInterface $environment): Content;

  /**
   * {@selfdoc}
   */
  public function get(string $parser_id): HtmlParserInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $parser_id): bool;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *   id: string,
   *   }
   */
  public function list(): array;

}
