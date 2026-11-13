<?php

namespace App\Libraries;

use App\Libraries\FeedPlugins\XComPlugin;

/** Applies the provider-specific cleanup formerly performed by system/application/plugins. */
class FeedPluginManager
{
    public function beforeSave(array $feed, \SimpleXMLElement $entry, array $item): array
    {
        $domain = strtolower((string) ($feed['feed_domain'] ?? ''));
        $data = $item['item_data'];
        $categories = $item['tags'];
        $namespaces = $entry->getNamespaces(true);
        $media = isset($namespaces['media']) ? $entry->children($namespaces['media']) : null;
        $dc = isset($namespaces['dc']) ? $entry->children($namespaces['dc']) : null;
        $author = trim((string) ($entry->author->name ?? $entry->author ?? ''));

        if ($media) {
            if (isset($media->thumbnail)) $data['image'] = (string) ($media->thumbnail[0]['url'] ?? '');
            if (isset($media->group->thumbnail)) $data['image'] = (string) ($media->group->thumbnail[0]['url'] ?? $data['image'] ?? '');
            if (isset($media->group->content)) $data['media_url'] = (string) ($media->group->content[0]['url'] ?? '');
            if (isset($media->content)) $data['media_url'] = (string) ($media->content[0]['url'] ?? '');
        }

        if ($this->isDomain($domain, 'blogsearch.google.com') && $dc && isset($dc->publisher)) {
            $data['publisher'] = trim((string) $dc->publisher);
        }

        if ($this->isDomain($domain, 'flickr.com')) {
            if (preg_match('/.*\(([^()]*)\).*$/', $author, $match)) $author = trim($match[1]);
            if ($author !== '') $item['item_content'] = trim(str_replace($author . ' posted a photo:', '', $item['item_content']));
            if ($categories !== []) $data['tags'] = $categories;
            if (! empty($data['image'])) {
                $image = (string) $data['image'];
                if (str_ends_with($image, '_m.jpg')) $data['flickr_images'] = ['medium' => substr($image, 0, -6) . '.jpg', 'large' => substr($image, 0, -5) . 'b.jpg', 'original' => substr($image, 0, -5) . 'o.jpg'];
            }
        }

        if ($this->isDomain($domain, 'friendfeed.com')) {
            $enclosure = (string) ($entry->enclosure['url'] ?? '');
            if ($this->isImageUrl($enclosure)) $data['image'] = $enclosure;
            if (str_starts_with((string) ($data['image'] ?? ''), 'http://friendfeed.com/static/images/icons/')) unset($data['image']);
        }

        if ($this->isDomain($domain, 'youtube.com')) {
            $item['item_content'] = trim(explode('Author:', $item['item_content'], 2)[0]);
            $data['video_url'] = $item['item_permalink'];
        }
        if ($this->isDomain($domain, 'vimeo.com') || $this->isDomain($domain, 'qik.com') || $this->isDomain($domain, 'seesmic.com') || $this->isDomain($domain, 'slideshare.net')) {
            $data['video_url'] = $item['item_permalink'];
        }
        if ($this->isDomain($domain, 'qik.com') || $this->isDomain($domain, 'seesmic.com')) {
            $enclosure = (string) ($entry->enclosure['url'] ?? '');
            if ($enclosure !== '') $data['media_url'] = $enclosure;
        }
        if ($this->isDomain($domain, 'seesmic.com') && ! empty($media->group->thumbnail)) {
            $data['image'] = (string) $media->group->thumbnail[0]['url'];
        }
        if ($this->isDomain($domain, 'tumblr.com')) {
            $tumblr = isset($namespaces['tumblr']) ? $entry->children($namespaces['tumblr']) : null;
            if ($tumblr && isset($tumblr->{'post-type'})) $data['type'] = (string) $tumblr->{'post-type'};
        }
        if ($this->isDomain($domain, 'slideshare.net')) {
            $data['publisher'] = 'SlideShare';
        }
        if ($this->isDomain($domain, 'x.com')) {
            $normalized = (new XComPlugin())->process($item['item_title'], $item['item_content'], $item['item_permalink'], (string) $feed['feed_url'], $item['item_status']);
            $item['item_title'] = $normalized['title'];
            $item['item_content'] = $normalized['content'];
            $item['item_status'] = $normalized['status'];
            if ($author !== '') $data['publisher'] = $author;
            if (isset($entry->author->uri)) $data['publisher_link'] = (string) $entry->author->uri;
        }

        $item['tags'] = array_values(array_unique(array_filter(array_map(static fn ($tag) => trim((string) $tag), $data['tags'] ?? $categories))));
        $item['item_data'] = $data;
        return $item;
    }

    public function forDisplay(array $items): array
    {
        $db = db_connect();
        foreach ($items as &$item) {
            $decoded = @unserialize((string) ($item['item_data'] ?? ''), ['allowed_classes' => false]);
            $item['item_data'] = is_array($decoded) ? $decoded : [];
            $item['tags'] = $db->table('tags')->select('tags.name, tags.slug')
                ->join('tag_relationships', 'tag_relationships.tag_id = tags.tag_id')
                ->where('tag_relationships.item_id', $item['ID'])->get()->getResultArray();
            if (empty($item['feed_domain']) && isset($item['item_data']['publisher'])) {
                $item['publisher'] = $item['item_data']['publisher'];
            }
        }
        unset($item);
        return $items;
    }

    private function isDomain(string $domain, string $provider): bool
    {
        return $domain === $provider || str_ends_with($domain, '.' . $provider);
    }

    private function isImageUrl(string $url): bool
    {
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        return (bool) preg_match('/\.(jpg|jpeg|gif|png|webp)$/', $path);
    }
}
