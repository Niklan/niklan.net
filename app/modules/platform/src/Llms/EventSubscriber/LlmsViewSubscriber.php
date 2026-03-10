<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms\EventSubscriber;

use Drupal\app_platform\Llms\LlmsRenderer;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class LlmsViewSubscriber implements EventSubscriberInterface {

  public function __construct(
    private LlmsRenderer $llmsRenderer,
    private RouteMatchInterface $routeMatch,
  ) {}

  public function onView(ViewEvent $event): void {
    $request = $event->getRequest();
    $result = $event->getControllerResult();

    if (!\is_array($result) || $request->getRequestFormat() !== 'llms') {
      return;
    }

    $response = $this->llmsRenderer->renderResponse($result, $request, $this->routeMatch);
    $event->setResponse($response);
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [KernelEvents::VIEW => ['onView', 100]];
  }

}
