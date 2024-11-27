onmessage = (event) => {
  /* eslint-disable no-undef */
  importScripts(event.data.libraryPath);
  if (event.data.language) {
    /* eslint-disable no-restricted-globals */
    const result = self.hljs.highlight(
      event.data.code,
      { language: event.data.language},
    );
    postMessage(result.value);
  }
  else {
    /* eslint-disable no-restricted-globals */
    const result = self.hljs.highlightAuto(event.data.code);
    postMessage(result.value);
  }
}
