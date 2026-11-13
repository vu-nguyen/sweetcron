<?php

namespace App\Libraries\FeedPlugins;

class XComPlugin
{
    /**
     * Normalize the feed-specific wrapper used by X RSS bridges.
     * Replies are kept in the archive as drafts, as they were by the old Twitter plugin.
     *
     * @return array{title:string,content:string,status:string}
     */
    public function process(string $title, string $content, string $permalink, string $feedUrl, string $status = 'publish'): array
    {
        $path = trim((string) parse_url($feedUrl, PHP_URL_PATH), '/');
        $handle = $path === '' ? '' : explode('/', $path)[0];
        if ($handle !== '') {
            $title = preg_replace('/^@?' . preg_quote($handle, '/') . ':\s*/i', '', $title) ?? $title;
        }

        if (str_starts_with(ltrim($title), '@')) {
            $status = 'draft';
        }

        return [
            'title' => trim($title),
            'content' => '',
            'status' => $status,
        ];
    }
}
