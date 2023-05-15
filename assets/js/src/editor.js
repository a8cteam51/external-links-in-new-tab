import { createHooks } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';

window.wpcomsp_elint = window.wpcomsp_elint || {};
window.wpcomsp_elint.hooks = createHooks();

domReady( () => {
	window.wpcomsp_elint.hooks.doAction( 'editor.ready' );
} );
