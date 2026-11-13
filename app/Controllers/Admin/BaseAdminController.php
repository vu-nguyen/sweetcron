<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

abstract class BaseAdminController extends BaseController
{
    protected function requireLogin()
    {
        if (! session()->get('admin_id')) return redirect()->to('/admin/login');
        return null;
    }
}
