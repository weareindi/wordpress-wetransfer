// Directives
import {WordPressWeTransfer} from './directives/WordPressWeTransfer/WordPressWeTransfer';

// Run
Array.forEach(document.querySelectorAll('[wordpress-wetransfer]'), (surface) => {
    new WordPressWeTransfer(surface);
});
