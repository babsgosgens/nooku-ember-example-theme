<?php
/**
 * @version     $Id$
 * @package     Nooku_Server
 * @subpackage  Languages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Component Database Row Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Languages
 */

class ComLanguagesDatabaseRowComponent extends KDatabaseRowDefault
{
    public function save()
    {
        $modified = $this->isModified('enabled');
        $result   = parent::save();
        
        if($this->getStatus() == KDatabase::STATUS_UPDATED && $modified && $this->enabled)
        {
            // If there aren't any tables for the component yet, add them from the manifest file.
            $tables = $this->getService('com://admin/languages.database.table.tables');
            if(!$tables->count(array('components_component_id' => $this->id)))
            {
                $component = $this->getService('com://admin/extensions.database.table.components')
                    ->select($this->id, KDatabase::FETCH_ROW);
                
                $file = $this->getIdentifier()->getApplication('admin').'/components/'.$component->option.'/databases/behaviors/translatable.xml';
                if(file_exists($file) && $xml = simplexml_load_file($file))
                {
                    $data = array();
                    foreach($xml as $table)
                    {
                        $data[] = array(
                            'components_component_id' => $this->id,
                            'name' => (string) $table->name,
                            'unique_column' => (string) $table->columns->unique,
                            'title_column' => (string) $table->columns->title,
                            'table_column' => (string) $table->columns->table,
                            'table_value' => (string) $table->columns->table['value']
                        );
                    }
                    
                    if($data) {
                        $tables->getRowset()->addData($data)->save();
                    }
                }
            }
        }
        
        return $result;
    }
}