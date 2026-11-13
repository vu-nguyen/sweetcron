<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class X_com {

	function pre_db($item, $original)
	{
		$publisher = $original->get_permalink();
		$path = trim((string) parse_url($publisher, PHP_URL_PATH), '/');
		$handle = $path === '' ? '' : explode('/', $path)[0];

		// X feed bridges commonly prefix each post with its account handle.
		if ($handle !== '') {
			$item->item_title = trim(preg_replace('/^@?' . preg_quote($handle, '/') . ':\\s*/i', '', $item->item_title));
		}

		// Keep replies out of the public stream, matching the former Twitter plugin.
		if (substr($item->item_title, 0, 1) === '@') {
			$item->item_status = 'draft';
		}

		$item->item_content = '';
		return $item;
	}

	function pre_display($item)
	{
		return $item;
	}
}
