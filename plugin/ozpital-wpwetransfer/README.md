# Ozpital WPWeTransfer

### Installation
- Get a WeTransfer API Key from https://developers.wetransfer.com/
- Copy the `/plugin/ozpital-wpwetransfer/` directory to your WordPress `plugins` directory.
- Enable the `Ozpital WPWeTransfer` plugin in WordPress
- Visit the plugin settings page and enter your WeTransfer API Key.

### Usage
- Use the shortcode `[ozpital-wpwetransfer]` to display
- Use the javascript event `ozpital-wpwetransfer-success` which fires on successful transfer:

```
document.addEventListener('ozpital-wpwetransfer-success', (event) => {
    console.log(event);
});
```
