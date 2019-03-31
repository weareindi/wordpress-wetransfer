// Directives
import {WeTransfer} from './directives/WeTransfer/WeTransfer';

// Run
for (var i = 0; i < document.querySelectorAll('.indi-wetransfer').length; i++) {
    new WeTransfer(document.querySelectorAll('.indi-wetransfer')[i]);
}
