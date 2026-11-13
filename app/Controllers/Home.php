<?php

namespace App\Controllers;

use App\Models\ItemModel;

class Home extends BaseController
{
    public function index(int $page = 1)
    {
        $model = new ItemModel();
        return $this->renderItems($model->published(max(0, $page - 1) * 20), 'Lifestream');
    }
}
