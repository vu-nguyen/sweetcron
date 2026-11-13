<?php

namespace App\Controllers\Admin;

use App\Models\OptionModel;
use CodeIgniter\Controller;

class Login extends Controller
{
    public function index()
    {
        if ((int) db_connect()->table('users')->countAllResults() === 0) return redirect()->to('/admin/install');
        if ($this->request->getMethod() === 'post') {
            $db = db_connect();
            $user = $db->table('users')->where('user_login', trim((string) $this->request->getPost('username')))->get()->getRowArray();
            $password = (string) $this->request->getPost('password');
            if ($user && (password_verify($password, $user['user_pass']) || hash_equals($user['user_pass'], md5($password)))) {
                if (hash_equals($user['user_pass'], md5($password))) {
                    $db->table('users')->where('ID', $user['ID'])->update(['user_pass' => password_hash($password, PASSWORD_DEFAULT)]);
                }
                session()->regenerate();
                session()->set('admin_id', $user['ID']);
                return redirect()->to('/admin');
            }
            return view('admin/login', ['error' => 'Username or password is incorrect.']);
        }
        return view('admin/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
