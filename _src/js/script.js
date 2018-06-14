// Directives
import {WPWeTransfer} from './directives/WPWeTransfer/WPWeTransfer';

// Run
Array.forEach(document.querySelectorAll('[ozpital-wpwetransfer]'), (surface) => {
    new WPWeTransfer(surface);
});
