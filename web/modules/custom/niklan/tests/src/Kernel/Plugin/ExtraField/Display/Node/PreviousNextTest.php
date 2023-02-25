<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Plugin\Block\ExtraField\Display\Node;

use Drupal\node\Entity\Node;
use Drupal\Tests\niklan\Kernel\Plugin\ExtraField\ExtraFieldTestBase;
use Drupal\Tests\niklan\Traits\BlogEntryTrait;

/**
 * Provides a test for previous/next extra field.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\ExtraField\Display\Node\PreviousNext
 */
final class PreviousNextTest extends ExtraFieldTestBase {

  use BlogEntryTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->setUpBlogEntry();
  }

  /**
   * Tests that field works as expected.
   */
  public function testView(): void {
    $this->createBlogEntry(['title' => 'Node A', 'created' => 1])->save();
    $this->createBlogEntry(['title' => 'Node B', 'created' => 2])->save();
    $this->createBlogEntry(['title' => 'Node C', 'created' => 3])->save();

    $plugin = $this->createExtraFieldDisplayInstance('previous_next');
    // Node B as a reference.
    $build = $plugin->view(Node::load(2));
    $this->render($build);

    self::assertRaw('Node A');
    self::assertRaw('Node C');
  }

}
