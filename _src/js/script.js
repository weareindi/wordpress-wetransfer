// Directives
import {WPWeTransfer} from './directives/WPWeTransfer/WPWeTransfer';

// Run
for (var i = 0; i < document.querySelectorAll('[ozpital-wpwetransfer]').length; i++) {
    new WPWeTransfer(document.querySelectorAll('[ozpital-wpwetransfer]')[i]);
}
