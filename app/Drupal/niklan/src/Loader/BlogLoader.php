<?php declare(strict_types = 1);

namespace Drupal\niklan\Loader;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\ContentVariation;
use Drupal\external_content\Data\LoaderResult;
use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\node\NodeStorageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class BlogLoader implements LoaderInterface, ContainerAwareInterface {

  /**
   * {@selfdoc}
   */
  private ContainerInterface $container;

  /**
   * {@inheritdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface {
    $blog_entry = $this->findBlogEntry($bundle->id);

    // If it's not found, stop processing. The new content is not created during
    // sync at this point, and maybe never will be.
    if (!$blog_entry instanceof BlogEntryInterface) {
      return LoaderResult::ignore();
    }

    // Loop over variations with a different languages.
    foreach ($bundle->getByAttribute('language') as $content_variation) {
      \assert($content_variation instanceof ContentVariation);
      $this->processBlogEntryVariation($blog_entry, $content_variation);
    }

    // @todo Implement the logic to avoid unnecessary save.
    $blog_entry->save();

    return LoaderResult::entity(
      entity_type_id: $blog_entry->getEntityTypeId(),
      entity_id: $blog_entry->id(),
    );
  }

  /**
   * {@selfdoc}
   */
  private function findBlogEntry(string $external_id): ?BlogEntry {
    $node_storage = $this->getEntityTypeManager()->getStorage('node');
    \assert($node_storage instanceof NodeStorageInterface);

    $ids = $node_storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();

    if (!$ids) {
      return NULL;
    }

    return $node_storage->load(\reset($ids));
  }

  /**
   * {@selfdoc}
   */
  private function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->container->get('entity_type.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function setContainer(?ContainerInterface $container): void {
    $this->container = $container;
  }

  /**
   * {@selfdoc}
   */
  private function processBlogEntryVariation(BlogEntryInterface $blog_entry, ContentVariation $content_variation): void {
    // Switch the content language to be the same as variation.
    $langcode = $content_variation->attributes->getAttribute('language');
    $blog_entry = $blog_entry->getTranslation($langcode);

    // @todo Loop over content and replace asset nodes with a new one that
    //   uses Drupal internal Media IDs/URIs.
    // @todo Compare an updated value with existing one and update if needed.
  }

}
