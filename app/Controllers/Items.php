<?php

namespace App\Controllers;

use App\Models\ItemModel;

class Items extends BaseController
{
    public function doSearch()
    {
        $query = trim((string) $this->request->getPost('query'));
        return $query === '' ? redirect()->to('/') : redirect()->to('/items/search/' . rawurlencode($query));
    }

    public function search(string $query, int $page = 1)
    {
        return $this->renderItems((new ItemModel())->search($query, max(0, $page - 1) * 20), 'Search: ' . $query);
    }

    public function tag(string $tag, int $page = 1)
    {
        return $this->renderItems((new ItemModel())->byTag($tag, max(0, $page - 1) * 20), 'Tag: ' . $tag);
    }

    public function site(string $site, int $page = 1)
    {
        return $this->renderItems((new ItemModel())->bySite($site, max(0, $page - 1) * 20), 'Source: ' . $site);
    }

    public function view(int $id)
    {
        $item = (new ItemModel())->publishedItem($id);
        return $item ? $this->renderItems([$item], $item['item_title']) : $this->response->setStatusCode(404);
    }
}
