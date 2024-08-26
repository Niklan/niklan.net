((document, window) => {

  function register() {
    window.Alpine.data('ToCDialog', () => ({
      query: '',

      isVisible(element) {
        const regex = new RegExp(`${this.query}`, 'i');
        console.log(regex, this.query)

        if (element.textContent.search(regex) === -1) {
          return false;
        }

        return true;
      }
    }));
  }

  if (window.Alpine) {
    register();
  }
  else {
    document.addEventListener('alpine:initializing', register);
  }

})(document, window);
