=== Image Captcha for Elementor ===
Contributors: creativehassan
Tags: elementor, captcha, image captcha, spam protection, forms
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.3.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, privacy-friendly image captcha field for Elementor Pro forms with honeypot spam protection. No API keys required.

== Description ==

**Image Captcha for Elementor** adds a simple image-based challenge to your Elementor Pro forms. Instead of relying on third-party services like reCAPTCHA, it presents users with a set of recognizable SVG icons and asks them to select the correct one.

= Key Features =

* **Image-based challenge** — random SVG icons with a simple "select the correct icon" prompt.
* **Server-side validation** — one-time tokens stored as WordPress transients prevent client-side bypass and replay attacks.
* **Built-in honeypot** — an invisible hidden field catches bots automatically.
* **Elementor global style integration** — color controls inherit from your site's Global Colors (Primary, Accent, Text).
* **Full style controls** — alignment, colors, icon size, gaps, padding, borders, and selected-state styling from the Elementor editor.
* **Hide until interaction** — optionally reveal the captcha only when the user starts typing.
* **Auto-regeneration** — captcha refreshes via AJAX after form submission errors or success.
* **Zero external dependencies** — no third-party requests, no cookies, no API keys.
* **Translation-ready** — fully internationalized.

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* Elementor 3.5 or higher
* Elementor Pro 3.5 or higher

== Installation ==

1. Upload the `image-captcha-elementor` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Edit any page with Elementor, add or edit a Form widget, and add a field of type **Image Captcha**.

== Frequently Asked Questions ==

= Does this plugin require Elementor Pro? =

Yes. The Image Captcha field type integrates with Elementor Pro's Form widget. Elementor Pro is required for the form fields API.

= Does it make any external requests? =

No. Everything runs entirely on your server. There are no third-party API calls, no cookies set, and no external scripts loaded.

= How many icons are available? =

The plugin ships with 11 built-in SVG icons: Heart, House, Star, Camera, Cup, Flag, Key, Truck, Tree, Plane, and Lock. You can configure how many appear per challenge (3–10).

= Can I style the captcha to match my theme? =

Yes. The Style tab in the Elementor editor provides full control over colors, sizes, spacing, borders, and alignment. Color controls automatically inherit from your Elementor Global Colors.

= How does the validation work? =

When the captcha loads, the server generates a unique token and stores the correct answer in a WordPress transient (valid for 5 minutes). On submission, the answer is verified server-side using `hash_equals()` and the transient is immediately deleted to prevent reuse.

== Screenshots ==

1. Image Captcha field in the Elementor form editor.
2. Captcha challenge on the frontend.
3. Style controls in the Elementor editor.

== Changelog ==

= 1.3.0 =
* Rebranded to "Image Captcha for Elementor" by Hassan Ali | Coresol Studio.
* Added Elementor Global Color integration for style controls.
* Added CSS variable fallbacks for automatic theme color inheritance.
* Updated text domain to `image-captcha-elementor`.

= 1.2.0 =
* Added full style controls (container, text, icons, selected state).
* Added "Hide Until Interaction" toggle.
* Added responsive alignment control.
* Implemented server-side token validation with transients.
* Added honeypot spam detection.
* Added AJAX captcha regeneration.
* Security hardening: input sanitization, output escaping, nonce verification.

= 1.1.0 =
* Initial public release.

== Upgrade Notice ==

= 1.3.0 =
Rebranded release with Elementor Global Color support and updated text domain.
