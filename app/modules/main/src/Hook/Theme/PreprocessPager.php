<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Theme;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

/**
 * @ingroup seo_pager
 */
#[Hook('preprocess_pager')]
final class PreprocessPager {

  /**
   * Cleans pager URL by force them to be processed by outbound processor.
   */
  protected function cleanUrl(string $url): string {
    // We do nothing if no query parameters is presented in href.
    if (\stristr($url, '?') === FALSE) {
      return $url;
    }

    return Url::fromRoute('<current>', [], [
      'query' => UrlHelper::parse($url)['query'],
    ])->toString();
  }

  private function processPagesItems(array &$items): void {
    foreach ($items as &$item) {
      if (!isset($item['href'])) {
        continue;
      }

      $item['href'] = $this->cleanUrl($item['href']);
    }
  }

  private function processOtherLink(array &$item): void {
    if (!isset($item['href'])) {
      return;
    }

    $item['href'] = $this->cleanUrl($item['href']);
  }

  private function addMetaPrevNext(array &$variables): void {
    $items = $variables['items'];

    if (isset($items['previous']['href'])) {
      $variables['#attached']['html_head'][] = [
        [
          '#tag' => 'link',
          '#attributes' => [
            'rel' => 'prev',
            // The page numbers already adjusted in href by outbound processor,
            // which means this URL should avoid it.
            'href' => Url::fromUserInput($items['previous']['href'], ['path_processing' => FALSE])
              ->setAbsolute()
              ->toString(),
          ],
        ],
        'app_main_pager_prev_link',
      ];
    }

    if (!isset($items['next']['href'])) {
      return;
    }

    $variables['#attached']['html_head'][] = [
    [
      '#tag' => 'link',
      '#attributes' => [
        'rel' => 'next',
        'href' => Url::fromUserInput($items['next']['href'], ['path_processing' => FALSE])
          ->setAbsolute()
          ->toString(),
      ],
    ],
      'app_main_pager_next_link',
    ];
  }

  public function __invoke(array &$variables): void {
    // Sometimes on views pages can show warning about invalid argument.
    if (!isset($variables['items'])) {
      return;
    }

    foreach ($variables['items'] as $type => &$items) {
      match ($type) {
        default => $this->processOtherLink($items),
        'pages' => $this->processPagesItems($items),
      };
    }

    // This method should be called after links are processed.
    $this->addMetaPrevNext($variables);
  }

}
