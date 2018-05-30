# WordPress WeTransfer

### To Do
- Comments
- Refactor
- Improve Styling

### Installation
- Get a WeTransfer API Key from https://developers.wetransfer.com/
- Copy the `/plugin/wordpress-wetransfer/` directory to your WordPress `plugins` directory.
- Enable the `WordPress WeTransfer` plugin in WordPress
- Visit the plugin settings page and enter your WeTransfer API Key.

### Usage
- Use the shortcode `[wordpress-wetransfer]` to display
- Use the javascript event `wordpress-wetransfer-complete` which fires on successful transfer:

```
document.addEventListener('wordpress-wetransfer-complete', (event) => {
    console.log(event);
});
```
