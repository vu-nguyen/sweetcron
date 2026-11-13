<?php

namespace App\Controllers\Admin;

use App\Models\FeedModel;

class Feeds extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('admin/feeds', ['feeds' => (new FeedModel())->findAll()]);
    }

    public function create()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        $url = trim((string) $this->request->getPost('url'));
        if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            return redirect()->back()->with('error', 'Enter a valid HTTP or HTTPS feed URL.');
        }
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        (new FeedModel())->insert(['feed_title' => $host, 'feed_icon' => 'https://' . $host . '/favicon.ico', 'feed_url' => $url, 'feed_data' => '', 'feed_status' => 'active', 'feed_domain' => preg_replace('/^www\\./', '', $host)]);
        return redirect()->to('/admin/feeds');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        (new FeedModel())->update($id, ['feed_status' => 'deleted']);
        return redirect()->to('/admin/feeds');
    }
}
