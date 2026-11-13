<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'tag_id';
    protected $allowedFields = ['name', 'slug', 'count'];
    protected $useTimestamps = false;

    public function attachToItem(array $names, int $itemId): void
    {
        $db = db_connect();
        foreach (array_unique(array_filter(array_map('trim', $names))) as $name) {
            $slug = url_title($name, '-', true);
            if ($slug === '') continue;
            $tag = $this->where('slug', $slug)->first();
            if (! $tag) {
                $this->insert(['name' => $name, 'slug' => $slug, 'count' => 1]);
                $tagId = (int) $this->getInsertID();
            } else {
                $tagId = (int) $tag['tag_id'];
            }
            $relation = $db->table('tag_relationships')->where(['item_id' => $itemId, 'tag_id' => $tagId])->get()->getRowArray();
            if (! $relation) {
                $db->table('tag_relationships')->insert(['item_id' => $itemId, 'tag_id' => $tagId]);
                if ($tag) $this->update($tagId, ['count' => ((int) $tag['count']) + 1]);
            }
        }
    }
}
