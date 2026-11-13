<?php

namespace App\Controllers\Admin;

use App\Models\FeedModel;
use App\Models\ItemModel;

class Dashboard extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('admin/dashboard', ['feeds' => count((new FeedModel())->active()), 'items' => (new ItemModel())->countAllResults()]);
    }
}
