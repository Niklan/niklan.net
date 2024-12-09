<?php

declare(strict_types=1);

namespace Drupal\niklan\Tag\SiteMap;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\niklan\SiteMap\Structure\Category;
use Drupal\niklan\SiteMap\Structure\Section;
use Drupal\niklan\SiteMap\Structure\SiteMap;
use Drupal\niklan\SiteMap\Structure\SiteMapBuilderInterface;
use Drupal\taxonomy\TermInterface;

final readonly class TagSiteMap implements SiteMapBuilderInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function build(): SiteMap {
    $sitemap = new SiteMap();
    $category = new Category(new TranslatableMarkup('Blog'));
    $section = new Section(new TranslatableMarkup('Tags'));

    foreach ($this->tags() as $tag) {
      \assert($tag instanceof TermInterface);
      $section->add($tag->toLink());
    }

    $category->add($section);
    $sitemap->add($category);
    $sitemap->addCacheTags(['taxonomy_term_list:tags']);

    return $sitemap;
  }

  private function tags(): \Generator {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $ids = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('vid', 'tags')
      ->sort('tid', 'DESC')
      ->execute();

    yield from $storage->loadMultiple($ids);
  }

}
