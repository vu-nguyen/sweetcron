<?php

namespace App\Models;

use CodeIgniter\Model;

class FeedModel extends Model
{
    protected $table = 'feeds';
    protected $primaryKey = 'feed_id';
    protected $allowedFields = ['feed_title', 'feed_icon', 'feed_url', 'feed_data', 'feed_status', 'feed_domain'];
    protected $useTimestamps = false;

    public function active(): array
    {
        return $this->where('feed_status', 'active')->orderBy('feed_title')->findAll();
    }
}
