<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= esc($title) ?></title><link rel="stylesheet" href="<?= base_url('css/reset.css') ?>"><link rel="stylesheet" href="<?= base_url('css/admin.css') ?>"></head>
<body><main class="container"><header><h1><?= esc($title) ?></h1><nav><a href="<?= site_url('/') ?>">Home</a> · <a href="<?= site_url('feed') ?>">RSS</a> · <a href="<?= site_url('admin') ?>">Admin</a></nav><form action="<?= site_url('items/search') ?>" method="post"><?= csrf_field() ?><input name="query" aria-label="Search"><button>Search</button></form></header>
<?php foreach ($items as $item): ?><?php $external = safe_external_url($item['item_permalink'] ?? null); $image = safe_external_url($item['item_data']['image'] ?? null); $video = safe_external_url($item['item_data']['video_url'] ?? null); ?>
<article><small><?= esc($item['feed_domain'] ?? ($item['publisher'] ?? 'This site')) ?> · <?= esc(date('Y-m-d H:i', (int) $item['item_date'])) ?></small><h2><a href="<?= esc($external ?: site_url('items/view/' . $item['ID'])) ?>"><?= esc($item['item_title']) ?></a></h2>
<?php if ($item['item_content'] !== ''): ?><p><?= nl2br(esc($item['item_content'])) ?></p><?php endif; ?>
<?php if ($image !== ''): ?><p><a href="<?= esc($external ?: $image) ?>"><img src="<?= esc($image) ?>" alt="" loading="lazy" style="max-width:100%;height:auto"></a></p><?php endif; ?>
<?php if ($video !== ''): ?><p><a href="<?= esc($video) ?>" rel="noopener noreferrer">View video or presentation</a></p><?php endif; ?>
<?php if (! empty($item['tags'])): ?><p>Tags: <?php foreach ($item['tags'] as $index => $tag): ?><?= $index ? ', ' : '' ?><a href="<?= site_url('items/tag/' . rawurlencode($tag['slug'])) ?>"><?= esc($tag['name']) ?></a><?php endforeach; ?></p><?php endif; ?></article><?php endforeach; ?>
</main></body></html>
