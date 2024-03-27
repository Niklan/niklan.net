<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Repository;

use Drupal\niklan\Repository\ContentSyncSettingsRepositoryInterface;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides test for content sync settings repository.
 *
 * @covers \Drupal\niklan\Repository\ContentSyncSettingsRepository
 */
final class ContentSyncSettingsRepositoryTest extends NiklanTestBase {

  /**
   * Tests that repository works as expected.
   */
  public function testRepository(): void {
    $repository = $this
      ->container
      ->get('niklan.repository.content_sync_settings');
    \assert($repository instanceof ContentSyncSettingsRepositoryInterface);

    self::assertNull($repository->getWorkingDir());

    $working_dir = 'public://foo-bar';
    $repository->setWorkingDir($working_dir);
    self::assertEquals($working_dir, $repository->getWorkingDir());

    $repository->setWorkingDir(NULL);
    self::assertNull($repository->getWorkingDir());
  }

}
