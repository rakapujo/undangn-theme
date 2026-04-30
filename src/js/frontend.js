/**
 * Core frontend bundle — di-load di setiap halaman undangan.
 *
 * Modul fitur (gallery, countdown, music, rsvp) ada di bundle terpisah
 * dan di-enqueue conditional via PHP (CLAUDE.md Bagian 6.2).
 */

import '../scss/frontend.scss';

const init = () => {
	document.documentElement.dataset.mguTheme = 'ready';
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
