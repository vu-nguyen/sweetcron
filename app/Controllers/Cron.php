<?php

namespace App\Controllers;

use App\Libraries\FeedFetcher;
use App\Models\OptionModel;

class Cron extends BaseController
{
    public function fetchItems(string $key)
    {
        if (!hash_equals((string) (new OptionModel())->value('cron_key'), $key)) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }
        $count = (new FeedFetcher())->fetchAll();
        return $this->response->setBody("Fetched feeds; imported {$count} items.");
    }
}
