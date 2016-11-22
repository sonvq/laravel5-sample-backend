<?php

namespace App\Repositories\Api\V1;

use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Events\Backend\CorporateDeck\CorporateDeckCreated;
use App\Events\Backend\CorporateDeck\CorporateDeckDeleted;
use App\Events\Backend\CorporateDeck\CorporateDeckUpdated;

/**
 * Class EloquentBaseRepository
 * @package App\Repositories\Api\V1;
 */
class EloquentBaseRepository
{
    protected $table;    
    protected $model;
    protected $connection;
    protected $primaryKey = 'id';

    
	public function getAll (array $where = array(), array $sort = array(), $limit = 100, $offset = 0, $fields = array()) {        
        $dbConnection = null;
        if (!empty($this->connection)) {
            $dbConnection = DB::connection($this->connection); 
            $query = $dbConnection->table($this->table . ' as r');
        } else {
            $query = DB::table($this->table . ' as r');               
        }          
        
        if (count($fields) > 0) {
            $fieldsAddedAlias = array();
        
            foreach ($fields as $singleField) {
                $fieldsAddedAlias[] = 'r.' . $singleField;
            }

            $query->select($fieldsAddedAlias);

            if (isset($this->primaryKey) && !empty($this->primaryKey)) {
                if (!in_array($this->primaryKey, $fields)) {
                    $query->addSelect('r.' . $this->primaryKey);    
                }
            }                        
        } else {
            $query->select('r.*');
        }
        
        $this->onPreQuery($query, $where);
        
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query->whereIn('r.' . $key, $value);    
            } else {
                $query->where('r.' . $key, $value);
            }
        }
        
        foreach ($sort as $key => $value) {
            $query->orderBy('r.' . $key, $value);    
        }
        
        if ($limit) {
            $query->skip($offset);
            $query->take($limit);
        }
        
        $model = $this->model;
        
        $objectList = $model::hydrate($query->get());
        
        foreach ($objectList as $key=>$singleObject) {            
            $singleObject = $singleObject->postProcessModel();            
            $objectList[$key] = $singleObject;
        }

        return $objectList;        
    }
    
    protected function onPreQuery(\Illuminate\Database\Query\Builder  $query, &$where = null)
    {
        
    }

}