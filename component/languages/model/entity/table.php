<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Languages;

use Nooku\Library;

/**
 * Table Model Entity
 *
 * @author  Gergo Erdosi <http://github.com/gergoerdosi>
 * @package Nooku\Component\Languages
 */
class ModelEntityTable extends Library\ModelEntityRow
{
    public function save()
    {
        $modified = $this->isModified('enabled');
        $result   = parent::save();
        
        if($this->getStatus() == self::STATUS_UPDATED && $modified && $this->enabled)
        {
            $database  = $this->getTable()->getAdapter();
            $languages = $this->getObject('application.languages');
            $primary   = $languages->getPrimary();

            foreach($languages as $language)
            {
                if($language->id != $primary->id)
                {
                    $table = strtolower($language->iso_code).'_'.$this->name;
                    
                    // Create language specific table.
                    $query = 'CREATE TABLE '.$database->quoteIdentifier($table).
                        ' LIKE '.$database->quoteIdentifier($this->name);
                    $database->execute($query);
                    
                    // Copy content of original table into the language specific one.
                    $query = $this->getObject('lib:atabase.query.insert')
                        ->table($table)
                        ->values($this->getObject('lib:database.query.select')->table($this->name));
                    $database->execute($query);
                    
                    $status   = ModelEntityTranslation::STATUS_MISSING;
                    $original = 0;
                            
                }
                else
                {
                    $status   = ModelEntityTranslation::STATUS_COMPLETED;
                    $original = 1;
                }
                
                // Add items to the translations table.
                $select = $this->getObject('lib:database.query.select')
                    ->columns(array(
                        'iso_code'  => ':iso_code',
                        'table'     => ':table',
                        'row'       => $this->unique_column,
                        'status'    => ':status',
                        'original'  => ':original'
                    ))
                    ->table($this->name)
                    ->bind(array(
                        'iso_code'  => $language->iso_code,
                        'table'     => $this->name,
                        'status'    => $status,
                        'original'  => $original
                    ));
                
                $query = $this->getObject('lib:database.query.insert')
                    ->table('languages_translations')
                    ->columns(array('iso_code', 'table', 'row', 'status', 'original'))
                    ->values($select);
                
                $database->execute($query);
            }
        }
        
        return $result;
    }
}