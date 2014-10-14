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
 * Modal Helper
 *
 * @author  Ercan Ozkaya <http://github.com/ercanozkaya>
 * @package Nooku\Component\Files
 */
class TemplateHelperModal extends Library\TemplateHelperAbstract
{
	public function select($config = array())
	{
		$config = new Library\ObjectConfig($config);
		$config->append(array(
			'name'      => '',
			'visible'   => true,
			'link'      => '',
			'link_text' => $this->getObject('translator')->translate('Select'),
			'link_selector' => 'modal'
		))->append(array(
			'value' => $config->name
		));

		$input = '<input name="%1$s" id="%1$s" value="%2$s" %3$s size="40" />';

		$link = '<a class="%s"
					rel="{\'ajaxOptions\': {\'method\': \'get\'}, \'handler\': \'iframe\', \'size\': {\'x\': 700}}"
					href="%s">%s</a>';

		$html  = sprintf($input, $config->name, $config->value, $config->visible ? 'type="text" readonly' : 'type="hidden"');
		$html .= sprintf($link, $config->link_selector, $config->link, $config->link_text);

		return $html;
	}
}