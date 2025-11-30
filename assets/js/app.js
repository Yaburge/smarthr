import { initGlobalModalListener } from './ui.js';
import { navigate } from './router.js';

export const BASE_PATH = '/SmartHR';

initGlobalModalListener();
window.navigate = navigate;