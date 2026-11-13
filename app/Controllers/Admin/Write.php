<?php

namespace App\Controllers\Admin;

use App\Models\ItemModel;

class Write extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('admin/write');
    }

    public function create()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        $rules = ['title' => 'required|max_length[255]', 'content' => 'permit_empty'];
        if (! $this->validate($rules)) return view('admin/write', ['errors' => $this->validator->getErrors()]);
        (new ItemModel())->insertManual([
            'item_title' => trim((string) $this->request->getPost('title')),
            'item_content' => trim((string) $this->request->getPost('content')),
            'item_date' => time(), 'tags' => [],
            'item_status' => $this->request->getPost('draft') ? 'draft' : 'publish',
        ]);
        return redirect()->to('/admin/items');
    }
}
