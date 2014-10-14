<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Attachments;

use Nooku\Library;

/**
 * Attachment Template Helper
 *
 * @author  Steven Rombauts <http://github.com/stevenrombauts>
 * @package Nooku\Component\Attachments
 */
class TemplateHelperAttachment extends Library\TemplateHelperAbstract
{
	/**
	 * Builds the file upload control and initializes it's related javascript classes.
	 * 
	 * To enable maximum compliance with the current state of the file upload's accept attribute, specify both any MIME
     * types and any corresponding extension.
	 * @see http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#attr-input-accept
	 * 
	 * @param mixed $config An optional Library\ObjectConfig object with configuration options
	 */
	public function upload($config = array())
	{
		$config = new Library\ObjectConfig($config);
		$config->append(array(
	        'container'	=> 'document.body'
		));

		if(!$config->allowed_extensions || !$config->allowed_mimetypes)
		{
			$container = $this->getObject('com:files.model.containers')
                ->slug('attachments-attachments')
                ->fetch();

			$config->append(array(
					'allowed_extensions'  => $container->getParameters()->allowed_extensions,
					'allowed_mimetypes'   => $container->getParameters()->allowed_mimetypes
			));
		}
		
		if($config->container != 'document.body') {
			$config->container = '\''.$config->container.'\'';
		}

        $extensions = json_encode($config->allowed_extensions->toArray());

		$html = <<<END
		<ktml:script src="assets://attachments/js/attachments.upload.js" />
		<script>
		window.addEvent('domready', function() {
			new Attachments.Upload({
				container: {$config->container},
			    extensions: {$extensions}
			});
		});
		</script>
END;
		
		$accept = array();
		foreach($config->allowed_extensions->toArray() as $val) {
			$accept[] = '.'.$val;
		}
		
		$accept = array_merge($accept, $config->allowed_mimetypes->toArray());
		
		$html .= '<input type="file" name="attachments[]" accept="'.implode(', ', $accept).'" />';
	
		return $html;
	}
}