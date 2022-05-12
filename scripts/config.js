/**
 * Configuration file for some scripts.
 */
import path from 'path';

/**
 * The project root directory.
 *
 * Different packages treat 'current dir' differently. Someone relative from
 * script and some from package.json. To mitigate that problem we set root
 * relative to this file.
 */
const PROJECT_ROOT = path.resolve(__dirname + '/..');

/**
 * Settings for custom theme.
 */
const paths = {
  projectRoot: PROJECT_ROOT,
  theme: {
    css: PROJECT_ROOT + '/web/themes/custom/mechanical/assets/css',
  },
  modules: {
    custom: PROJECT_ROOT + '/web/modules/custom',
  }
}

export {
  paths,
}
