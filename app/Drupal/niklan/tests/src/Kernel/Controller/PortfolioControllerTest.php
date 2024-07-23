<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\niklan\Controller\PortfolioController;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides test for portfolio controller.
 *
 * @coversDefaultClass \Drupal\niklan\Controller\PortfolioController
 */
final class PortfolioControllerTest extends NiklanTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['datetime'];

  /**
   * Tests that controller properly handles empty results.
   */
  public function testWithoutPortfolioEntities(): void {
    $controller = PortfolioController::create($this->container);

    $build = $controller->list();
    self::assertEmpty($build['#items']);

    self::render($build);

    $elements = $this->cssSelect('.portfolio-list');
    self::assertCount(1, $elements);

    $elements = $this->cssSelect('.node');
    self::assertEmpty($elements);
  }

  /**
   * Tests that controller properly display results.
   */
  public function testWithPortfolioEntities(): void {
    Node::create([
      'type' => 'portfolio',
      'title' => 'Portfolio 1',
    ])->save();

    Node::create([
      'type' => 'portfolio',
      'title' => 'Portfolio 2',
    ])->save();

    Node::create([
      'type' => 'portfolio',
      'title' => 'Portfolio 3',
    ])->save();

    $controller = PortfolioController::create($this->container);
    $build = $controller->list();
    self::assertCount(3, $build['#items']);

    self::render($build);

    $elements = $this->cssSelect('.portfolio-list');
    self::assertCount(1, $elements);

    $elements = $this->cssSelect('.portfolio-list article');
    self::assertCount(3, $elements);

    self::assertRaw('Portfolio 1');
    self::assertRaw('Portfolio 2');
    self::assertRaw('Portfolio 3');
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('field_config');
    $this->installEntitySchema('date_format');

    NodeType::create(['type' => 'portfolio'])->save();

    $field_date_storage = FieldStorageConfig::create([
      'field_name' => 'field_date',
      'entity_type' => 'node',
      'type' => 'datetime',
      'settings' => [
        'datetime_type' => 'date',
      ],
    ]);
    $field_date_storage->save();

    FieldConfig::create([
      'field_storage' => $field_date_storage,
      'bundle' => 'portfolio',
      'label' => 'Date',
    ])->save();

    DateFormat::create([
      'id' => 'fallback',
      'pattern' => 'D, m/d/Y - H:i',
    ])->save();
  }

}
