# Ozpital WP WeTransfer

### Installation
- Get a WeTransfer API Key from https://developers.wetransfer.com/
- Copy the `/plugin/ozpital-wp-wetransfer/` directory to your WordPress `plugins` directory.
- Enable the `Ozpital WP WeTransfer` plugin in WordPress
- Visit the plugin settings page and enter your WeTransfer API Key.

### Usage
- Use the shortcode `[ozpital-wp-wetransfer]` to display
- Use the javascript event `ozpital-wp-wetransfer-complete` which fires on successful transfer:

```
document.addEventListener('ozpital-wp-wetransfer-complete', (event) => {
    console.log(event);
});
```
