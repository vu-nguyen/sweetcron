<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\FeedPluginManager;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text', 'sweetcron'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function renderItems(array $items, string $title = 'Lifestream')
    {
        return view('stream', ['items' => (new FeedPluginManager())->forDisplay($items), 'title' => $title]);
    }
}
