# Sweetcron on CodeIgniter 4

Sweetcron collects RSS and Atom activity into a self-hosted chronological stream. This repository now boots on CodeIgniter 4.7, managed by Composer. CodeIgniter 4.7 requires PHP 8.2 or newer.

## Install

1. Use PHP 8.2+ with CodeIgniter's required extensions, plus `mysqli`, `curl`, and `simplexml` for the MySQL database and feed importer. Point the web server document root at `public/` when possible. Apache rewrite support is needed for clean URLs.
2. Install dependencies with `composer install`.
3. Copy `.env.example` to `.env` and set the base URL and MySQL credentials.
4. Run `php spark migrate` to create the tables. Existing Sweetcron databases are retained: the migration creates only missing tables.
5. Visit `/admin/install` to create the first administrator. Setup is disabled after the first account is created. Existing MD5-password accounts are accepted once and upgraded after successful login.
6. Configure a scheduler to request `/cron/fetch-items/{cron_key}`. The key is read from the `options` row named `cron_key`.

The public routes provide the stream, item pages, source/tag/search filters, and RSS. `/admin` provides feed management, post writing, item moderation, and site title settings. CI4 now applies migrated provider cleanup for Google Blog Search, Flickr, FriendFeed, Qik, Seesmic, SlideShare, Tumblr, Vimeo, YouTube, and X.com. It saves provider metadata, image/video links, and feed tags with imported items. Old Flash embeds are represented as links in the modern stream. The legacy source files remain in `system/application/` for reference, except the superseded Twitter plugins; active plugin handling lives in `app/Libraries/FeedPluginManager.php`. Existing database tables are retained, so pre-existing feed imports and posts are not discarded by setup.
