# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Environment

Local WordPress install via **Local by Flywheel**. No build tool (no npm, no webpack). All CSS and JS are plain files enqueued by WordPress — edit them directly, refresh the browser.

The repo root is the **child theme** at `wp-content/themes/prh68/`. The companion **plugin** lives at `wp-content/plugins/pluginPRH68/` and is a sibling directory outside this repo.

## Architecture

### Two codebases, one product

| Layer | Location | Responsibility |
|---|---|---|
| Child theme | `wp-content/themes/prh68/` | Page templates, CSS/JS per page, CPT registration, ACF field sync |
| Plugin `pluginPRH68` | `wp-content/plugins/pluginPRH68/` | Custom DB tables, AJAX handlers, admin UI, shortcodes |

They are separate git repos. Changes to the plugin must be made in its own directory.

### Theme structure

- `page-*.php` — one file per page template (home, outils-terrain, temoignages, formulaire-outil, formulaire-temoignage, mentions-légales, observatoire, politique-confidentialite, professionnels). Each is self-contained: PHP processing at the top, then `get_header()`, HTML, `get_footer()`.
- `css/style-*.css` — one stylesheet per page template, loaded conditionally via `inc/scripts.php`.
- `js/*.js` — one script per page template, same conditional loading.
- `inc/scripts.php` — all `wp_enqueue_*` calls. Template detection uses `is_page_template('page-foo.php')`.
- `inc/cpt.php` — registers CPTs `temoignage` and `outil_terrain` (both `public: false`, UI only).
- `inc/acf-fields.php` — ACF field group registration in PHP.
- `acf-json/` — ACF field groups synced as JSON (source of truth for ACF structure).

### Plugin structure

- `pluginPRH68.php` — main file: admin menu, shortcode registration, AJAX hooks, asset enqueueing.
- `includes/install.php` — `dbDelta()` schema. Runs on plugin activation.
- `includes/admin/crud-evenements.php` — `admin_post_*` handlers for event CRUD.
- `includes/front/controller/traitement_formulaire.php` — AJAX handler for mallette loan form (`traiter_location`).
- `includes/front/formulaireContact/traiter_formulaire_contact.php` — AJAX handler for contact form (`traiter_contact`).
- `templates/Front/*/` — shortcode output templates (rendered via `ob_start()`/`ob_get_clean()`).
- `templates/admin/*/` — admin page templates (`require`d from callback functions in main plugin file).

### Custom database tables (plugin)

All prefixed `wp_` (or `$wpdb->prefix`). Schema defined in `includes/install.php`:

| Table | Purpose |
|---|---|
| `wp_evenements` | Events: titre, description, date, pdf_id, complet, visible, places_disponibles |
| `wp_mallettes` | Pedagogical kits: nom, description, quantite, image_id, ordre |
| `wp_objets` | Items inside a mallette (FK: mallette_id) |
| `wp_locations` | Loan requests: statut ENUM('en attente','en cours','retourné'), dates, contact fields |
| `wp_mots_cles` / `wp_mallettes_mots_cles` | Keywords + pivot table |

Events and mallettes use **custom tables**, not WordPress CPTs.

### ACF fields on CPTs

`outil_terrain` CPT uses ACF fields: `ot_description`, `ot_story`, `ot_envs`, `ot_age_ranges`, `ot_contact_name`, `ot_contact_role`, `ot_contact_org`, `ot_category`, `ot_structure`. These are read in `page-outils-terrain.php` and `js/outils-terrain.js` (via `wp_localize_script`).

`temoignage` CPT uses: `temoig_category`, `temoig_type`, `temoig_featured`, and media fields.

## Key Patterns

### Page template detection
Always use `_wp_page_template` meta to find pages, not slug:
```php
get_posts(['meta_key' => '_wp_page_template', 'meta_value' => 'page-foo.php', ...])
```

### AJAX (front-end forms)
All front-end form submissions go to `/wp-admin/admin-ajax.php` via `fetch`. Handlers registered with both `wp_ajax_nopriv_*` and `wp_ajax_*`. Response always via `wp_send_json(['success' => bool, 'message' => '...'])`.

### Inline scripts in shortcode templates
**Do not use IIFE or `DOMContentLoaded`** inside shortcode template scripts. In the `ob_start()` shortcode context, `DOMContentLoaded` may not fire. Pattern used:
```js
var _prhUniqueFlag = false;
document.getElementById('myForm').addEventListener('submit', function(e) { ... });
```
Script runs directly after its form HTML. Global variable names must be unique to avoid collisions across shortcodes on the same page.

### Anti-double-submit
Button is disabled + text changed on first click. Flag reset only on error (stays locked on success). Example: `_prhContactSubmitting`, `_prhLocationSubmitting`.

### Asset versioning
Theme assets use `filemtime()` for cache busting. Plugin assets use either `filemtime()` or a hardcoded version string.

### Email sending
All emails use `wp_mail()`. HTML emails pass `['Content-Type: text/html; charset=UTF-8']` as headers. Admin email from `get_option('admin_email')`.
