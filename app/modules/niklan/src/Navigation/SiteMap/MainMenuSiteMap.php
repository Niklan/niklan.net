<?php

declare(strict_types=1);

namespace Drupal\niklan\Navigation\SiteMap;

use Drupal\Core\Link;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\SiteMap\Structure\Category;
use Drupal\niklan\SiteMap\Structure\Section;
use Drupal\niklan\SiteMap\Structure\SiteMap;
use Drupal\niklan\SiteMap\Structure\SiteMapBuilderInterface;

final readonly class MainMenuSiteMap implements SiteMapBuilderInterface {

  public function __construct(
    private MenuLinkTreeInterface $menuLinkTree,
  ) {}

  #[\Override]
  public function build(): SiteMap {
    $sitemap = new SiteMap();
    $category = new Category(new TranslatableMarkup('General', options: ['context' => 'site map category']));
    $section = new Section(new TranslatableMarkup('Pages'));

    $tree = $this->menuLinkTree->load('main', new MenuTreeParameters());
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    foreach ($this->menuLinkTree->transform($tree, $manipulators) as $element) {
      $element->link->getUrlObject();
      $section->add(Link::fromTextAndUrl($element->link->getTitle(), $element->link->getUrlObject()));
    }

    $category->add($section);
    $sitemap->add($category);

    return $sitemap;
  }

}
