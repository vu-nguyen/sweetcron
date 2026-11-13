<?php

namespace App\Controllers\Admin;

use App\Models\ItemModel;

class Items extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('admin/items', ['items' => (new ItemModel())->adminItems()]);
    }

    public function status(int $id)
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        (new ItemModel())->update($id, ['item_status' => $this->request->getPost('status') === 'draft' ? 'draft' : 'publish']);
        return redirect()->to('/admin/items');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        (new ItemModel())->update($id, ['item_status' => 'deleted']);
        return redirect()->to('/admin/items');
    }
}
