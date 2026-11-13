<?php

namespace App\Libraries;

use App\Models\FeedModel;
use App\Models\ItemModel;
use App\Models\OptionModel;
use App\Models\TagModel;
use Config\Services;

class FeedFetcher
{
    public function fetchAll(): int
    {
        $client = Services::curlrequest(['timeout' => 20, 'allow_redirects' => ['max' => 5]]);
        $feeds = new FeedModel();
        $items = new ItemModel();
        $plugins = new FeedPluginManager();
        $imported = 0;
        foreach ($feeds->active() as $feed) {
            try {
                $xml = @simplexml_load_string($client->get($feed['feed_url'])->getBody(), \SimpleXMLElement::class, LIBXML_NOCDATA | LIBXML_NONET);
                if (!$xml) continue;
                $entries = isset($xml->channel->item) ? $xml->channel->item : ($xml->entry ?? []);
                foreach ($entries as $entry) {
                    $title = trim((string) ($entry->title ?? ''));
                    $link = (string) ($entry->link['href'] ?? $entry->link ?? '');
                    $rawContent = (string) ($entry->description ?? $entry->content ?? $entry->summary ?? '');
                    $content = strip_tags($rawContent);
                    $date = strtotime((string) ($entry->pubDate ?? $entry->published ?? $entry->updated ?? '')) ?: time();
                    if ($title === '' || $link === '') continue;
                    $title = strip_tags($title);
                    $categories = [];
                    foreach ($entry->category ?? [] as $category) {
                        $categories[] = trim((string) ($category['term'] ?? $category));
                    }
                    $item = $plugins->beforeSave($feed, $entry, [
                        'item_title' => $title, 'item_content' => $content, 'item_permalink' => $link,
                        'item_status' => 'publish', 'item_date' => $date,
                        'item_data' => ['permalink' => $link] + $this->extractMedia($entry, $rawContent), 'tags' => $categories,
                    ]);
                    $title = $item['item_title'];
                    if ($title === '') continue;
                    $exists = $items->where(['item_feed_id' => $feed['feed_id'], 'item_permalink' => $link])->first();
                    if ($exists) continue;
                    $items->insert([
                        'item_date' => $item['item_date'], 'item_content' => $item['item_content'], 'item_title' => $title,
                        'item_permalink' => $link, 'item_status' => $item['item_status'], 'item_name' => url_title($title, '-', true),
                        'item_parent' => 0, 'item_data' => serialize($item['item_data']), 'item_feed_id' => $feed['feed_id'],
                    ]);
                    $itemId = (int) $items->getInsertID();
                    if ($itemId && $item['tags'] !== []) (new TagModel())->attachToItem($item['tags'], $itemId);
                    $imported++;
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Feed fetch failed for {url}: {message}', ['url' => $feed['feed_url'], 'message' => $e->getMessage()]);
            }
        }
        (new OptionModel())->setValue('last_fetch', (string) time());
        return $imported;
    }

    private function extractMedia(\SimpleXMLElement $entry, string $content): array
    {
        $media = [];
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $match)) {
            $media['image'] = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $enclosure = (string) ($entry->enclosure['url'] ?? '');
        if ($enclosure !== '' && preg_match('/\.(jpg|jpeg|gif|png|webp)(?:$|\?)/i', $enclosure)) $media['image'] = $enclosure;
        return $media;
    }
}
