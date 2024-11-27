((Drupal, once) => {

  function processSearchBar(element) {
    const input = element.querySelector('[data-input]');
    const query = new URLSearchParams(window.location.search).get(input.name);
    input.value = query || '';
  }

  Drupal.behaviors.searchBar = {
    attach: () => {
      once('search-bar', '[data-selector="laszlo:search-bar"]').forEach((element) => {
        requestIdleCallback(() => {
          processSearchBar(element);
        });
      });
    },
  };

})(Drupal, once);