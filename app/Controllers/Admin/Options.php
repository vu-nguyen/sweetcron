<?php

namespace App\Controllers\Admin;

use App\Models\OptionModel;

class Options extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        $options = new OptionModel();
        return view('admin/options', ['title' => $options->value('lifestream_title', 'My Lifestream'), 'perPage' => $options->value('per_page', '20')]);
    }

    public function save()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        $this->validate(['title' => 'required|max_length[255]', 'per_page' => 'required|is_natural_no_zero|less_than_equal_to[100]']);
        if ($this->validator->hasError('title') || $this->validator->hasError('per_page')) return redirect()->back()->withInput()->with('error', 'Check the title and items per page values.');
        $options = new OptionModel();
        $options->setValue('lifestream_title', trim((string) $this->request->getPost('title')));
        $options->setValue('per_page', (string) $this->request->getPost('per_page'));
        return redirect()->to('/admin/options');
    }
}
