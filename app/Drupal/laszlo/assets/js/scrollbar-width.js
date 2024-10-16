(() => {

  function updateScrollbarWidth() {
    document.documentElement.style.setProperty(
      '--scrollbar-width',
      window.innerWidth - document.documentElement.clientWidth,
    );
  }

  updateScrollbarWidth();
  window.addEventListener("resize", updateScrollbarWidth, {passive: true});

})();
