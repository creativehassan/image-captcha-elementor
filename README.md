# Image Captcha for Elementor

A lightweight, privacy-friendly image captcha field for **Elementor Pro** forms. Users prove they're human by selecting the correct icon — no third-party services, no cookies, no API keys.

![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue?logo=wordpress)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-GPLv2-green)

## Features

- **Image-based challenge** — presents a random set of SVG icons and asks the user to select the correct one.
- **Server-side validation** — answers are verified using one-time tokens stored as WordPress transients. Nothing is exposed to the client.
- **Honeypot field** — an invisible hidden field catches automated bots before the captcha is even evaluated.
- **Elementor global styles** — color controls inherit from your Elementor Kit palette (Primary, Accent, Text) out of the box.
- **Full style controls** — alignment, colors, icon size, gaps, padding, border radius, selected-state styling — all configurable from the Elementor editor.
- **Hide until interaction** — optionally keep the captcha hidden until the user starts typing, reducing visual clutter.
- **Auto-regeneration** — the captcha refreshes automatically after form submission errors or success via AJAX.
- **Zero external dependencies** — no Google reCAPTCHA, no hCaptcha, no third-party requests. Everything runs on your server.
- **Translation-ready** — fully internationalized with the `image-captcha-elementor` text domain.

## Requirements

| Requirement     | Minimum |
|-----------------|---------|
| WordPress       | 5.8+    |
| PHP             | 8.0+    |
| Elementor       | 3.5+    |
| Elementor Pro   | 3.5+    |

## Installation

1. Download or clone this repository into your `wp-content/plugins/` directory:

   ```bash
   cd wp-content/plugins/
   git clone https://github.com:creativehassan/image-captcha-elementor.git
   ```

2. Activate the plugin from **Plugins → Installed Plugins** in your WordPress admin.

3. Edit any page with **Elementor**, add or edit a **Form** widget, and add a new field of type **Image Captcha**.

## Usage

### Adding the captcha to a form

1. Open the Elementor editor on any page with a Form widget.
2. In the form's **Fields** repeater, click **Add Item**.
3. Set the field type to **Image Captcha**.
4. Configure:
   - **Total Captcha Images** — number of icons shown (3–10).
   - **Hide Until Interaction** — toggle to keep the captcha hidden until the user starts filling in the form.

### Styling

Switch to the **Style** tab on the Form widget. You'll find an **Image Captcha** section with controls for:

| Section        | Controls                                                    |
|----------------|-------------------------------------------------------------|
| Container      | Alignment, background color, border style/color/width, border radius, padding |
| Question Text  | Text color, highlight (icon name) color, font size          |
| Icons          | Size, gap, padding, fill color, hover color                 |
| Selected State | Border color, border width, border radius                   |

Color controls default to your **Elementor Global Colors** (Primary, Accent, Text), so the widget matches your site theme automatically.

## How It Works

1. **On page load**, the plugin picks a random subset of SVG icons, selects one as the "correct" answer, generates a unique token, and stores the expected answer in a WordPress transient (expires after 5 minutes).
2. **The user** sees a question like *"Please prove you are human by selecting the Heart"* alongside the icon options.
3. **On submit**, the server compares the selected value against the stored transient using `hash_equals()`, then deletes the transient to prevent replay attacks.
4. **A hidden honeypot field** is also checked — if it contains any value, the submission is rejected as spam.

## Built-in Icons

The plugin ships with 11 recognizable SVG icons: Heart, House, Star, Camera, Cup, Flag, Key, Truck, Tree, Plane, and Lock.

## Contributing

Contributions, issues, and feature requests are welcome. Feel free to open an issue or submit a pull request.

## License

This project is licensed under the [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

**Hassan Ali** — [Coresol Studio](https://coresolstudio.com)
