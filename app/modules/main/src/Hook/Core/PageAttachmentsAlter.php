<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Core;

use Drupal\app_platform\Http\ErrorPageDetector;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Hook\Order\Order;
use Drupal\Core\Render\RendererInterface;
use Drupal\metatag\MetatagTagPluginManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Strips metatags computed for a page that turned out to 404.
 *
 * When e.g. PagerNotFound rejects an out-of-range "?page=" after the
 * original page has already fully rendered, metatag_page_attachments()
 * (and metatag_preprocess_html()) has already memoized its output in a
 * plain drupal_static() — a cache unaware of cache contexts, routes or the
 * exception that follows. Drupal's exception handling then builds the
 * actual error page via an internal subrequest, but that static memo is
 * still served as-is, leaking the discarded page's canonical URL, meta
 * title/description and <title> tag into the 404 response's <head>. A 404
 * page does not need SEO metatags, so the simplest fix is to drop them
 * rather than fight the memoization.
 *
 * @ingroup seo_pager
 */
final readonly class PageAttachmentsAlter {

  public function __construct(
    private ErrorPageDetector $errorPageDetector,
    #[Autowire(service: 'plugin.manager.metatag.tag')]
    private MetatagTagPluginManager $metatagTagPluginManager,
    private RendererInterface $renderer,
    private ConfigFactoryInterface $configFactory,
  ) {}

  #[Hook('page_attachments_alter')]
  public function alterAttachments(array &$attachments): void {
    if (!$this->errorPageDetector->isErrorPageSubrequest() || !($attachments['#attached']['html_head'] ?? NULL)) {
      return;
    }

    $metatag_ids = \array_keys($this->metatagTagPluginManager->getDefinitions());
    $attachments['#attached']['html_head'] = \array_filter(
      $attachments['#attached']['html_head'],
      static fn (array $item): bool => !\in_array($item[1] ?? NULL, $metatag_ids, TRUE),
    );
  }

  /**
   * Restores the <title> tag metatag_preprocess_html() overwrote.
   *
   * Runs after metatag's own hook_preprocess_html() (which has no module
   * weight guarantee otherwise) so it can undo the stale title it set.
   */
  #[Hook('preprocess_html', order: Order::Last)]
  public function restoreHeadTitle(array &$variables): void {
    if (!$this->errorPageDetector->isErrorPageSubrequest() || !($variables['page']['#title'] ?? NULL)) {
      return;
    }

    $title = $variables['page']['#title'];

    if (\is_array($title)) {
      $title = (string) $this->renderer->renderInIsolation($title);
    }

    $variables['head_title'] = [
      'title' => \trim(\strip_tags((string) $title)),
      'name' => $this->configFactory->get('system.site')->get('name'),
    ];
  }

}
