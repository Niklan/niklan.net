((Drupal, once) => {

  function initActiveTrail(settings) {
    once('navigation-active-trail', '[data-navigation-active-trail-pattern]')
      .forEach(itemElement => {
        const pattern = itemElement.dataset.navigationActiveTrailPattern;
        const activeTrailClass = itemElement.dataset.navigationActiveTrailClass;

        if (settings.path.isFront && pattern === '<front>') {
          itemElement.classList.add(activeTrailClass);
          return;
        }

        const regex = new RegExp(pattern);

        if (regex.exec(window.location.pathname) !== null) {
          itemElement.classList.add(activeTrailClass);
        }
      });
  }

  Drupal.behaviors.mainNavigationItem = {
    attach(context, settings) {
      initActiveTrail(settings);
    },
  };

})(Drupal, once);