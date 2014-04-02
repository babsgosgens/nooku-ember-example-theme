<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Grid Template Helper
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Template
 * @see     http://ajaxpatterns.org/Data_Grid
 */
class TemplateHelperGrid extends TemplateHelperAbstract
{
	/**
	 * Render a checkbox field
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function checkbox($config = array())
	{
		$config = new ObjectConfigJson($config);
		$config->append(array(
			'entity'    => null,
	    ))->append(array( 
        	'column' => $config->entity->getIdentityKey()
        )); 
		
		if($config->entity->isLockable() && $config->entity->isLocked())
		{
		    $html = '<i class="icon-lock"></i>';
		}
		else
		{
		    $column = $config->column;
		    $value  = $config->entity->{$column};

		    $html = '<input type="checkbox" class="-koowa-grid-checkbox" name="'.$column.'[]" value="'.$value.'" />';
		}

		return $html;
	}

	/**
	 * Render an search header
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function search($config = array())
	{
	    $config = new ObjectConfigJson($config);
		$config->append(array(
			'search'      => null,
			'results'     => 5,
			'placeholder' => 'Title'
		));

	    $html = '<input type="search" results="'.$config->results.'" name="search" id="search" placeholder="'.$config->placeholder.'" value="'.$this->escape($config->search).'" />';
        $html .= '<button class="button">'.$this->translate('Go').'</button>';
		$html .= '<button class="button" onclick="document.getElementById(\'search\').value=\'\';this.form.submit();">'.$this->translate('Reset').'</button>';

	    return $html;
	}

	/**
	 * Render a checkall header
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function checkall($config = array())
	{
		$config = new ObjectConfigJson($config);

		$html = '<input type="checkbox" class="-koowa-grid-checkall" />';
		return $html;
	}

	/**
	 * Render a sorting header
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function sort( $config = array())
	{
		$config = new ObjectConfigJson($config);
		$config->append(array(
			'title'   	    => '',
			'column'  	    => '',
			'direction'     => 'asc',
			'sort'          => '',
		));

		//Set the title
		if(empty($config->title)) {
			$config->title = ucfirst($config->column);
		}

		//Set the direction
		$direction	= strtolower($config->direction);
		$direction 	= in_array($direction, array('asc', 'desc')) ? $direction : 'asc';
        $toggle     = $direction == 'desc' ? 'asc' : 'desc';

        //Set the route
        $route = 'direction='.$toggle;
        if($config->column != $config->sort) {
            $route = 'sort='.$config->column;
        }

		//Set the class
		$class = '';
		if($config->column == $config->sort) {
			$class = 'class="-koowa-'.$direction.'"';
		}

		$route = $this->getTemplate()->getView()->getRoute($route);
		$html  = '<a href="'.$route.'" title="'.$this->translate('Click to sort by this column').'"  '.$class.'>';
		$html .= $this->translate($config->title);
		$html .= '</a>';

		return $html;
	}

	/**
	 * Render an enable field
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function enable($config = array())
	{
		$config = new ObjectConfigJson($config);
		$config->append(array(
			'entity'  	=> null,
		    'field'	=> 'enabled'
		))->append(array(
		    'data'	=> array($config->field => $config->entity->{$config->field})
		));

		$img    = $config->entity->{$config->field} ? 'icon-ok' : 'icon-remove';
		$alt 	= $config->entity->{$config->field} ? $this->translate( 'Enabled' ) : $this->translate( 'Disabled' );
		$text 	= $config->entity->{$config->field} ? $this->translate( 'Disable Item' ) : $this->translate( 'Enable Item' );

	    $config->data->{$config->field} = $config->entity->{$config->field} ? 0 : 1;
	    $data = str_replace('"', '&quot;', $config->data);

		$html = '<i class="'. $img .'" data-action="edit" data-data="'.$data.'"></i>';

		return $html;
	}

	/**
	 * Render an order field
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function order($config = array())
	{
		$config = new ObjectConfigJson($config);
		$config->append(array(
			'entity'   => null,
		    'total'	=> null,
		    'field'	=> 'ordering',
		    'data'	=> array('order' => 0)
		));

		$config->data->order = -1;
		$updata   = str_replace('"', '&quot;', $config->data);

		$config->data->order = +1;
		$downdata = str_replace('"', '&quot;', $config->data);

		$html = '';

		if ($config->entity->{$config->field} > 1) {
            $html .= '<i class="icon-chevron-up" data-action="edit" data-data="'.$updata.'"></i>';
        }

        $html .= '<span class="data-order">'.$config->entity->{$config->field}.'</span>';

        if($config->entity->{$config->field} != $config->total) {
            $html .= '<i class="icon-chevron-down" data-action="edit" data-data="'.$downdata.'"></i>';
	    }

		return $html;
	}

	/**
	 * Render an access field
	 *
	 * @param 	array 	$config An optional array with configuration options
	 * @return	string	Html
	 */
	public function access($config = array())
	{
		$config = new ObjectConfigJson($config);
		$config->append(array(
			'entity'  		=> null,
		    'field'		=> 'access'
		))->append(array(
		    'data'		=> array($config->field => $config->entity->{$config->field})
		));

		switch($config->entity->{$config->field})
		{
			case 0 :
			{
				$color   = 'green';
				$group   = $this->translate('Public');
				$access  = 1;
			} break;

			case 1 :
			{
				$color   = 'red';
				$group   = $this->translate('Registered');
				$access  = 2;
			} break;
		}

		$config->data->{$config->field} = $access;
	    $data = str_replace('"', '&quot;', $config->data);

		$html = '<span style="color:'.$color.'" data-action="edit" data-data="'.$data.'">'.$group.'</span>';

		return $html;
	}
}