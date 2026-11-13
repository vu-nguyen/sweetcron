<?php

namespace App\Controllers;

use App\Models\ItemModel;
use App\Libraries\FeedPluginManager;

class Feed extends BaseController
{
    public function index()
    {
        return $this->rss((new ItemModel())->published());
    }

    public function search(string $query) { return $this->rss((new ItemModel())->search($query)); }
    public function tag(string $tag) { return $this->rss((new ItemModel())->byTag($tag)); }
    public function site(string $site) { return $this->rss((new ItemModel())->bySite($site)); }

    private function rss(array $items)
    {
        return $this->response->setContentType('application/rss+xml')->setBody(view('rss', ['items' => (new FeedPluginManager())->forDisplay($items)]));
    }
}
