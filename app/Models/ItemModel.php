<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'ID';
    protected $allowedFields = ['item_date', 'item_content', 'item_title', 'item_permalink', 'item_status', 'item_name', 'item_parent', 'item_data', 'item_feed_id'];
    protected $useTimestamps = false;

    public function published(int $offset = 0): array
    {
        return $this->select('items.*, feeds.feed_domain, feeds.feed_icon')
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->where('item_status', 'publish')->orderBy('item_date', 'DESC')->findAll(20, $offset);
    }

    public function publishedItem(int $id): ?array
    {
        return $this->select('items.*, feeds.feed_domain, feeds.feed_icon')
            ->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->where(['items.ID' => $id, 'item_status' => 'publish'])->first();
    }

    public function search(string $query, int $offset = 0): array
    {
        return $this->select('items.*, feeds.feed_domain, feeds.feed_icon')->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->where('item_status', 'publish')->groupStart()->like('item_title', $query)->orLike('item_content', $query)->groupEnd()
            ->orderBy('item_date', 'DESC')->findAll(20, $offset);
    }

    public function bySite(string $site, int $offset = 0): array
    {
        return $this->select('items.*, feeds.feed_domain, feeds.feed_icon')->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->where('item_status', 'publish')->where('feed_domain', $site)->orderBy('item_date', 'DESC')->findAll(20, $offset);
    }

    public function byTag(string $tag, int $offset = 0): array
    {
        return $this->select('items.*, feeds.feed_domain, feeds.feed_icon')->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')
            ->join('tag_relationships', 'tag_relationships.item_id = items.ID')->join('tags', 'tags.tag_id = tag_relationships.tag_id')
            ->where(['item_status' => 'publish', 'tags.slug' => $tag])->orderBy('item_date', 'DESC')->findAll(20, $offset);
    }

    public function insertManual(array $data): bool
    {
        $data['item_feed_id'] = 0;
        $data['item_parent'] = 0;
        $data['item_status'] ??= 'publish';
        $data['item_permalink'] = '';
        $data['item_name'] = url_title($data['item_title'], '-', true);
        $data['item_data'] = serialize(['tags' => $data['tags'] ?? []]);
        unset($data['tags']);
        return (bool) $this->insert($data);
    }

    public function adminItems(): array
    {
        return $this->select('items.*, feeds.feed_domain')->join('feeds', 'feeds.feed_id = items.item_feed_id', 'left')->where('item_status !=', 'deleted')->orderBy('item_date', 'DESC')->findAll(100);
    }
}
