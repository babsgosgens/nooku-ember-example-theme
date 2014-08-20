<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * Filesize Helper
 *
 * @author  Ercan Ozkaya <http://github.com/ercanozkaya>
 * @package Nooku\Component\Files
 */
class TemplateHelperFilesize extends Library\TemplateHelperAbstract
{
	public function humanize($config = array())
	{
		$config = new Library\ObjectConfig($config);
		$config->append(array(
			'sizes' => array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB')
		));
		$bytes = $config->size;
		$result = '';
		$format = (($bytes > 1024*1024 && $bytes % 1024 !== 0) ? '%.2f' : '%d').' %s';

		foreach ($config->sizes as $s)
		{
			$size = $s;
			if ($bytes < 1024) {
				$result = $bytes;
				break;
			}
			$bytes /= 1024;
		}

		if ($result == 1) {
			$size = Library\StringInflector::singularize($size);
		}

		return sprintf($format, $result, $this->getObject('translator')->translate($size));
	}
}