<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\app_portfolio\Node\PortfolioBundle;

final readonly class PreprocessNodePortfolio {

  public function __invoke(array &$variables): void {
    $portfolio = $variables['node'];
    \assert($portfolio instanceof PortfolioBundle);
    $variables['year_of_completion'] = $portfolio->getYearOfCompletion();
    $variables['category_names'] = \array_map(static fn ($category) => $category->label(), $portfolio->getCategories());
    $variables['project_url'] = $portfolio->getProjectUrl();
  }

}
