<?php

namespace App\Controllers\Admin;

use App\Models\OptionModel;
use CodeIgniter\Controller;

class Install extends Controller
{
    public function index()
    {
        if ((int) db_connect()->table('users')->countAllResults() > 0) return redirect()->to('/admin/login');
        return view('admin/install');
    }

    public function create()
    {
        $db = db_connect();
        if ((int) $db->table('users')->countAllResults() > 0) return redirect()->to('/admin/login');
        $rules = ['username' => 'required|min_length[5]|max_length[60]', 'email' => 'required|valid_email|max_length[100]', 'password' => 'required|min_length[12]', 'title' => 'required|max_length[255]'];
        if (! $this->validate($rules)) return view('admin/install', ['errors' => $this->validator->getErrors()]);
        $username = trim((string) $this->request->getPost('username'));
        if ($db->table('users')->where('user_login', $username)->countAllResults() > 0) {
            return view('admin/install', ['errors' => ['That username is already in use.']]);
        }
        $db->transStart();
        $db->table('users')->insert(['user_login' => $username, 'user_pass' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT), 'user_email' => trim((string) $this->request->getPost('email')), 'user_activation_key' => '']);
        $options = new OptionModel();
        $options->setValue('lifestream_title', trim((string) $this->request->getPost('title')));
        $options->setValue('per_page', '20');
        $options->setValue('cron_type', 'true');
        $options->setValue('cron_key', bin2hex(random_bytes(24)));
        $db->transComplete();
        return redirect()->to('/admin/login')->with('message', 'Setup is complete. Sign in with the administrator account you created.');
    }
}
