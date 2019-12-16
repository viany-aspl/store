<?php
namespace DB;
define('REPLICA_SET', false);
final 
class mongo {
    /**
     * mongo connection - if a MongoDB object already exists (from a previous script) then only DB operations use this
     * @var Mongo
     */
    protected $_db;

    /**
     * Name of last selected DB
     * @var string Defaults to admin as that is available in all Mongo instances
     */
    public static $dbName = 'admin';

    /**
     * MongoDB
     * @var MongoDB
     */
    public $mongo;

    /**
     * Returns a new Mongo connection
     * @return Mongo
     */
    public $hostname;	
		
    protected function _mongo() {
       // $connection = (!$this->hostname ? 'mongodb://localhost:7017' : $this->hostname);
        $connection = ('mongodb://'.$this->hostname  .':27017/'.self::$dbName);

        
        $Mongo = (class_exists('MongoClient') === true ? 'MongoClient' : 'Mongo');
        return (!REPLICA_SET ? new $Mongo($connection,array('connectTimeoutMS'=>30000)) : new $Mongo($connection, array('connectTimeoutMS'=>30000,'replicaSet' => true)));
    }

    /**
     * Connects to a Mongo database if the name of one is supplied as an argument
     * @param string $db
     */
    public function __construct($hostname, $username, $password, $database) {
	    self::$dbName =$database;
            $db = $database;
            if(!empty($username)&&!empty($password)){
        $this->hostname=$username.":".$password.'@'.$hostname;
            }
 else {
     $this->hostname=$hostname;
 }


        if ($db) {
            if (!extension_loaded('mongo')) {
                throw new mongoExtensionNotInstalled();
            }
            try {
                $this->_db = $this->_mongo();
                $this->mongo = $this->_db->selectDB($db);

            } catch (MongoConnectionException $e) {
                throw new cannotConnectToMongoServer();
            }
        }
    }

    /**
     * Executes a native JS MongoDB command
     * This method is not currently used for anything
     * @param string $cmd
     * @return mixed
     */
    protected function _exec($cmd) {
        $exec = $this->mongo->execute($cmd);
        return $exec['retval'];
    }

    /**
     * Change the DB connection
     * @param string $db
     */
    public function setDb($db) {
        if (self::$databaseWhitelist && !in_array($db, self::$databaseWhitelist)) {
            $db = current(self::$databaseWhitelist);
        }
        if (!isset($this->_db)) {
            $this->_db = $this->_mongo();
        }
        $this->mongo = $this->_db->selectDB($db);
        self::$dbName = $db;
    }

    /**
     * Total size of all the databases
     * @var int
     */
    public $totalDbSize = 0;

    /**
     * Adds ability to restrict databases-access to those on the whitelist
     * @var array
     */
    public static $databaseWhitelist = array();

    /**
     * Gets list of databases
     * @return array
     */
    public function listDbs() {
        $return = array();
        $restrictDbs = (bool) self::$databaseWhitelist;
        $dbs = $this->_db->selectDB('admin')->command(array('listDatabases' => 1));
        $this->totalDbSize = $dbs['totalSize'];
        foreach ($dbs['databases'] as $db) {
            if (!$restrictDbs || in_array($db['name'], self::$databaseWhitelist)) {
                $return[$db['name']] = $db['name'] . ' ('
                                     . (!$db['empty'] ? round($db['sizeOnDisk'] / 1000000) . 'mb' : 'empty') . ')';
            }
        }
        ksort($return);
        $dbCount = 0;
        foreach ($return as $key => $val) {
            $return[$key] = ++$dbCount . '. ' . $val;
        }
        return $return;
    }

    /**
     * Generate system info and stats
     * @return array
     */
    public function getStats() {
        $admin = $this->_db->selectDB('admin');
        $return = $admin->command(array('buildinfo' => 1));
        try {
            $return = array_merge($return, $admin->command(array('serverStatus' => 1)));
        } catch (MongoCursorException $e) {}
        $profile = $admin->command(array('profile' => -1));
        $return['profilingLevel'] = $profile['was'];
        $return['mongoDbTotalSize'] = round($this->totalDbSize / 1000000) . 'mb';
        $prevError = $admin->command(array('getpreverror' => 1));
        if (!$prevError['n']) {
            $return['previousDbErrors'] = 'None';
        } else {
            $return['previousDbErrors']['error'] = $prevError['err'];
            $return['previousDbErrors']['numberOfOperationsAgo'] = $prevError['nPrev'];
        }
        if (isset($return['globalLock']['totalTime'])) {
            $return['globalLock']['totalTime'] .= ' &#0181;Sec';
        }
        if (isset($return['uptime'])) {
            $return['uptime'] = round($return['uptime'] / 60) . ':' . str_pad($return['uptime'] % 60, 2, '0', STR_PAD_LEFT)
                              . ' minutes';
        }
        $unshift['mongo'] = $return['version'] . ' (' . $return['bits'] . '-bit)';
        $unshift['mongoPhpDriver'] = Mongo::VERSION;
        $unshift['MoAdmin'] = '1.1.4';
        $unshift['php'] = PHP_VERSION . ' (' . (PHP_INT_MAX > 2200000000 ? 64 : 32) . '-bit)';
        $unshift['gitVersion'] = $return['gitVersion'];
        unset($return['ok'], $return['version'], $return['gitVersion'], $return['bits']);
        $return = array_merge(array('version' => $unshift), $return);
        $iniIndex = array(-1 => 'Unlimited', 'Off', 'On');
        $phpIni = array('allow_persistent', 'auto_reconnect', 'chunk_size', 'cmd', 'default_host', 'default_port',
                        'max_connections', 'max_persistent');
        foreach ($phpIni as $ini) {
            $key = 'php_' . $ini;
            $return[$key] = ini_get('mongo.' . $ini);
            if (isset($iniIndex[$return[$key]])) {
                $return[$key] = $iniIndex[$return[$key]];
            }
        }
        return $return;
    }

    /**
     * Repairs a database
     * @return array Success status
     */
    public function repairDb() {
        return $this->mongo->repair();
    }

    /**
     * Drops a database
     */
    public function dropDb() {
        $this->mongo->drop();
        return;
        if (!isset($this->_db)) {
            $this->_db = $this->_mongo();
        }
        $this->_db->dropDB($this->mongo);
    }

    /**
     * Gets a list of database collections
     * @return array
     */
    public function listCollections() {
        $collections = array();
        $MongoCollectionObjects = $this->mongo->listCollections();
        foreach ($MongoCollectionObjects as $collection) {
            $collection = substr(strstr((string) $collection, '.'), 1);
            $collections[$collection] = $this->mongo->selectCollection($collection)->count();
        }
        ksort($collections);
        return $collections;
    }

    /**
     * Drops a collection
     * @param string $collection
     */
    public function dropCollection($collection) {
        $this->mongo->selectCollection($collection)->drop();
    }

    /**
     * Creates a collection
     * @param string $collection
     */
    public function createCollection($collection) {
        if ($collection) {
            $this->mongo->createCollection($collection);
        }
    }

    /**
     * Renames a collection
     *
     * @param string $from
     * @param string $to
     */
    public function renameCollection($from, $to) {
        $result = $this->_db->selectDB('admin')->command(array(
            'renameCollection' => self::$dbName . '.' . $from,
            'to' => self::$dbName . '.' . $to,
        ));
    }

    /**
     * Gets a list of the indexes on a collection
     *
     * @param string $collection
     * @return array
     */
    public function listIndexes($collection) {
        return $this->mongo->selectCollection($collection)->getIndexInfo();
    }

    /**
     * Ensures an index
     *
     * @param string $collection
     * @param array $indexes
     * @param array $unique
     */
    public function ensureIndex($collection, array $indexes, array $unique) {
        $unique = ($unique ? true : false); //signature requires a bool in both Mongo v. 1.0.1 and 1.2.0
        $this->mongo->selectCollection($collection)->ensureIndex($indexes, $unique);
    }

    /**
     * Removes an index
     *
     * @param string $collection
     * @param array $index Must match the array signature of the index
     */
    public function deleteIndex($collection, array $index) {
        $this->mongo->selectCollection($collection)->deleteIndex($index);
    }

    /**
     * Sort array - currently only used for collections
     * @var array
     */
    public $sort = array('_id' => 1);

    /**
     * Number of rows in the entire resultset (before limit-clause is applied)
     * @var int
     */
    public $count;

    /**
     * Array keys in the first and last object in a collection merged together (used to build sort-by options)
     * @var array
     */
    public $colKeys = array();

    
  public function lookup($collection,$lookupin,$unwind,$match,$limit='',$start='',$sort_array='',$columns='',$lookupwithunwind='',$groupby='',$matchcount='') {

  $col = $this->mongo->selectCollection($collection);
  $paas_array=array();
  
  if(!empty($lookupin))
  {
      
      if(count(@$lookupin[0])>1)
      {
          foreach($lookupin as $lookupin2)
          {
               $paas_array[]=array('$lookup' => $lookupin2);
          }
      }
      else 
      {
          $paas_array[]=array('$lookup' => $lookupin);
      }
      
  }
  if(!empty($unwind))
  {
      //$paas_array[]=array('$unwind' => $unwind);
      if(is_array($unwind))
      {
          foreach($unwind as $unwind2)
          {
               $paas_array[]=array('$unwind' => $unwind2);
          }
      }
      else 
      {
          $paas_array[]=array('$unwind' => $unwind);
      }
  }
  if(!empty($lookupwithunwind))
  {
      foreach($lookupwithunwind as $lookupwithunwind2)
      {
        $paas_array[]=array('$lookup' => $lookupwithunwind2['lookup']);
        $paas_array[]=array('$unwind' => $lookupwithunwind2['unwind']);
      }
  }
  if(!empty($match))
  {
      $paas_array[]=array('$match' => $match);
  }
  if(!empty($groupby))
  {
      foreach($groupby as $grp)
      {
        $paas_array[]=array('$group' => $grp);
        
      }
  }
  
   if(!empty($sort_array))
  {
      $paas_array[]=array('$sort' => $sort_array);
  }
  if(!empty($start))
  {
      $paas_array[]=array('$skip' => $start);
  }
  if(!empty($limit))
  {
      $paas_array[]=array('$limit' => $limit);
  }
 
  if(!empty($columns))
  {
      $paas_array[]=array('$project' => $columns);
  }
  
  /////
  $count_array=array();
  if(!empty($lookupin))
  {
      //$count_array[]=array('$lookup' => $lookupin);
  }
  if(!empty($unwind))
  {
      //$count_array[]=array('$unwind' => $unwind);
  }
  if(!empty($matchcount))
  {
      $count_array=$matchcount;//array('$match' => $match);
  }
  
  if(!empty($sort_array))
  {
      //$count_array[]=array('$sort' => $sort_array);
  }
  if(!empty($groupby))
  {
      foreach($groupby as $grp)
      {
        //$count_array[]=array('$group' => $grp);
        
      }
  }
  $log=new \Log( "mongo-".date('Y-m-d').".log");
	//$log->write();
	$log->write('pass_array');
    $log->write(json_encode($paas_array));
	$log->write('count_array');
    $log->write(json_encode($count_array));
	
 
    $data= iterator_to_array($col->aggregatecursor($paas_array));        
    
   
	if(!empty($groupby))
	{
		$paas_array=array_diff($paas_array,array('$skip','$limit'));
		foreach($paas_array as $key=>&$value)
		{
  
			//if($value==array('$limit' => 20))
			if (in_array("$limit", $value))	
			{
				unset($paas_array[$key]);
			}
			
			if (in_array("$skip", $value))
			{
				unset($paas_array[$key]);
			}
			
		}
		$log->write(json_encode($paas_array));
		$data['rowcount']=sizeof(iterator_to_array($col->aggregatecursor($paas_array)));
	}
	else
	{
		$data['rowcount']=$col->count($count_array);
	}
    return $data;
  }  
  
 public function gettotalcount($collection,$groupbyarray,$match,$sort_array=array(),$limit='',$unwind='') {

  $col = $this->mongo->selectCollection($collection);
  
  $paas_array=array();
  if(!empty($unwind))
  {
      $paas_array[]=array('$unwind' => $unwind);
  }
  
  if(!empty($match))
  {
      $paas_array[]=array('$match' => $match);
  }
  if(!empty($groupbyarray))
  {
      $paas_array[]=array('$group' => $groupbyarray);
  }
  if(!empty($sort_array))
  {
      $paas_array[]=array('$sort' => $sort_array);
  }
  if(!empty($limit))
  {
      $paas_array[]=array('$limit' => $limit);
  }
    //print_r(json_encode($paas_array)); 
	
    return  $col->aggregate($paas_array);
 

  }
  public function gettotalsum($collection,$groupbyarray,$match,$sort_array=array(),$limit='') {

  $col = $this->mongo->selectCollection($collection);
  $paas_array=array();
  if(!empty($match))
  {
      $paas_array[]=array('$match' => $match);
  }
  if(!empty($groupbyarray))
  {
      $paas_array[]=array('$group' => $groupbyarray);
  }
  if(!empty($sort_array))
  {
      $paas_array[]=array('$sort' => $sort_array);
  }
  if(!empty($limit))
  {
      $paas_array[]=array('$limit' => $limit);
  }


    return  $col->aggregate($paas_array);
 

  }
 
public function getcount($collection,$where) 
{

	$log=new \Log( "mongo-".date('Y-m-d').".log");
	$log->write();
	
	$log->write('count_array');
    $log->write(json_encode($where));
	$log->write();
	$col = $this->mongo->selectCollection($collection);
	return  $col->count($where);

}


public function search($collection,$where) {

//{ $text: { $search: "Moss Carrie-Anne" } }
  $col = $this->mongo->selectCollection($collection);
 return  $col->find($where);

}

 public function update($collection,$where,$data) 
 { 
   
    $log=new \Log( "mongo-update".date('Y-m-d').".log");
    $log->write('call update for : ');
    $log->write($collection);
    $log->write($data);
    $col = $this->mongo->selectCollection($collection);
    $update = array('$set' => ($data));
    $options = array('multi' => true);   
    return  $col->update($where, $update,$options);
    

}
public function upsert($collection,$where,$data) 
 {
    $col = $this->mongo->selectCollection($collection);
    $update = array('$set' => ($data));
    $options = array('multi' => true,'upsert'=>true);      
    return  $col->update($where, $update,$options);

}

public function incModify($collection,$where,$data)
{ 
   // $update=array(array('$inc'=> ($data))); 
    //$options = array('upsert'=>true);
   
    $col = $this->mongo->selectCollection($collection);    
    $update = array('$inc' => ($data));
    $options = array('multi' => true,'upsert'=>true);     
    return  $col->update($where, $update,$options);           

}

public function deleterow($collection,$where) {

    $col = $this->mongo->selectCollection($collection);
    return  $col->remove($where);

}
    /**
     * Get the records in a collection
     *
     * @param string $collection
     * @return array
     */
    public function listRows($collection,$search='',$searchField='',$find='',$where='',$or='',$limit='',$cols='',$start,$sort) {

        foreach ($this->sort as $key => $val) { //cast vals to int
            $sort[$key] = (int) $val;
        }
        $col = $this->mongo->selectCollection($collection);
      
        $find=array();
        if (!empty($find)) {                                    
            if (strpos($find, 'array') === 0) {              
                eval('$find = ' . $find . ';');
            } else if (is_string($find)) {
                if ($findArr = json_decode($find, true)) {
                    $find = $findArr;
                }
            }
            
        }
        
        
  if (!empty($where) ) {
 
                            $find = array('$and' => array($where));
}
  if (!empty($or) ) {
 
                            $find = array('$or' => array($or));
}

        if (!empty($searchField) ) {

            switch (substr(trim($search), 0, 1)) { //first character
                case '/': //regex
                    $find[$searchField] = new mongoRegex($search);
                    break;
                case '{': //JSON
                    if ($search = json_decode($search, true)) {
                        $find[$searchField] = $search;
                    }
                    break;
                case '(':
                    $types = array('bool', 'boolean', 'int', 'integer', 'float', 'double', 'string', 'array', 'object',
                                   'null', 'mongoid');
                    $closeParentheses = strpos($search, ')');
                    if ($closeParentheses) {
                        $cast = strtolower(substr($search, 1, ($closeParentheses - 1)));
                        if (in_array($cast, $types)) {
                            $search = trim(substr($search, ($closeParentheses + 1)));
                            if ($cast == 'mongoid') {
                                $search = new MongoID($search);
                            } else {
                                settype($search, $cast);
                            }
                            $find[$searchField] = $search;
                            break;
                        }
                    } //else no-break
                default: //text-search
                    if (strpos($searchField, '*') === false) {
                        if (!is_numeric($search)) {
                            $find[$searchField] = $search;
                        } else { //$_GET is always a string-type
                            $in = array((string) $search, (int) $search, (float) $search);
                            $find[$searchField] = array('$in' => $in);
                        }
                    } else { //text with wildcards
                        $regex = '/' . str_replace('\*', '.*', preg_quote($search)) . '/i';
                        $find[$searchField] = new mongoRegex($regex);
                    }
                    break;
            }
        }
        if(!empty($cols))
        {$cols =array_fill_keys($cols,true);}
        else{$cols =array();} 
       
        $cur = $col->find($find, $cols)->sort($sort);
		$log=new \Log( "mongo-".date('Y-m-d').".log");
		$log->write('');
		$log->write('select call');
		$log->write($find);
        $this->count = $cur->count();

        //get keys of first object
        if ( $limit && $this->count > $limit //more results than per-page limit
            && (!isset($_GET['export']) || $_GET['export'] != 'nolimit')) {
            if ($this->count > 1) {
                $this->colKeys = self::getArrayKeys($col->findOne());
            }
            $cur->limit($limit);
            if (isset($start)) {
                if ($this->count <= $start) {
                    $start = ($this->count - $limit);
                }
                $cur->skip($start);
            }
        } else if ($this->count) { // results exist but are fewer than per-page limit

            $this->colKeys = self::getArrayKeys($cur->getNext());
        } else if ($find && $col->count()) { //query is not returning anything, get cols from first obj in collection
            $this->colKeys = self::getArrayKeys($col->findOne());
        }

        //get keys of last or much-later object
        if ($this->count > 1) {
            $curLast = $col->find()->sort($sort);
            if ($this->count > 2) {
                $curLast->skip(min($this->count, 100) - 1);
            }
            $this->colKeys = array_merge($this->colKeys, self::getArrayKeys($curLast->getNext()));
            ksort($this->colKeys);
        }
        return $cur;
    }

    /**
     * Returns a serialized element back to its native PHP form
     *
     * @param string $_id
     * @param string $idtype
     * @return mixed
     */
    protected function _unserialize($_id, $idtype) {
        if ($idtype == 'object' || $idtype == 'array') {
            $errLevel = error_reporting();
            error_reporting(0); //unserializing an object that is not serialized throws a warning
            $_idObj = unserialize($_id);
            error_reporting($errLevel);
            if ($_idObj !== false) {
                $_id = $_idObj;
            }
        } else if (gettype($_id) != $idtype) {
            settype($_id, $idtype);
        }
        return $_id;
    }

    /**
     * Removes an object from a collection
     *
     * @param string $collection
     * @param string $_id
     * @param string $idtype
     */
    public function removeObject($collection, $_id, $idtype) {
        $this->mongo->selectCollection($collection)->remove(array('_id' => $this->_unserialize($_id, $idtype)));
    }

    /**
     * Retieves an object for editing
     *
     * @param string $collection
     * @param string $_id
     * @param string $idtype
     * @return array
     */
    public function editObject($collection, $_id, $idtype) {
        return $this->mongo->selectCollection($collection)->findOne(array('_id' => $this->_unserialize($_id, $idtype)));
    }

    /**
     * Saves an object
     *
     * @param string $collection
     * @param string $obj
     * @return array
     */
    public function saveObject($collection, $obj) {
        //eval('$obj=' . $obj . ';'); //cast from string to array
        
        return $this->mongo->selectCollection($collection)->save($obj);
    }

    /**
     * Imports data into the current collection
     *
     * @param string $collection
     * @param array $data
     * @param string $importMethod Valid options are batchInsert, save, insert, update
     */
    public function import($collection, array $data, $importMethod) {
        $coll = $this->mongo->selectCollection($collection);
        switch ($importMethod) {
            case 'batchInsert':
                foreach ($data as &$obj) {
                    $obj = unserialize($obj);
                }
                $coll->$importMethod($data);
                break;
            case 'update':
                foreach ($data as $obj) {
                    $obj = unserialize($obj);
                    if (is_object($obj) && property_exists($obj, '_id')) {
                        $_id = $obj->_id;
                    } else if (is_array($obj) && isset($obj['_id'])) {
                        $_id = $obj['_id'];
                    } else {
                        continue;
                    }
                    $coll->$importMethod(array('_id' => $_id), $obj);
                }
                break;
            default: //insert & save
                foreach ($data as $obj) {
                    $coll->$importMethod(unserialize($obj));
                }
            break;
        }
    }

 /**
     * Sets the depth limit for phpMoAdmin::getArrayKeys (and prevents an endless loop with self-referencing objects)
     */
    const DRILL_DOWN_DEPTH_LIMIT = 8;

    /**
     * Retrieves all the keys & subkeys of an array recursively drilling down
     *
     * @param array $array
     * @param string $path
     * @param int $drillDownDepthCount
     * @return array
     */
    public static function getArrayKeys(array $array, $path = '', $drillDownDepthCount = 0) {
        $return = array();
        if ($drillDownDepthCount) {
            $path .= '.';
        }
        if (++$drillDownDepthCount < self::DRILL_DOWN_DEPTH_LIMIT) {
            foreach ($array as $key => $val) {
                $return[$id] = $id = $path . $key;
                if (is_array($val)) {
                    $return = array_merge($return, self::getArrayKeys($val, $id, $drillDownDepthCount));
                }
            }
        }
        return $return;
    }

    /**
     * Strip slashes recursively - used only when magic quotes is enabled (this reverses magic quotes)
     *
     * @param mixed $val
     * @return mixed
     */
    public static function stripslashes($val) {
        return (is_array($val) ? array_map(array('self', 'stripslashes'), $val) : stripslashes($val));
    }
public function escape($value) {
		$search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
		$replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
		return str_replace($search, $replace, $value);
	}
public function countAffected() {
		return $this->count;
	}

public function dumptocsv($collection)
 {
            $time = microtime(true); // time in Microseconds         
            $cnt=  $this->mongo->selectCollection($collection)->count();
            $limit=10000;
            $page=round($cnt/$limit);
            $estimatedtime=($page/2)*30;
            echo $this->formatseconds($estimatedtime);              
            $file = $collection.'.csv';
            if(file_exists($file))
            {unlink($file);}
            $fileIO = fopen($file, 'w+');
            for($icount=0;$icount<$page;$icount++)
            {
            $col = $this->mongo->selectCollection($collection)->find()->skip($icount*$limit)->limit($limit);
            $col=iterator_to_array($col);                           
            foreach(array_keys($col[array_keys($col)[0]]) as $key){
		    $keys[0][$key] = $key;
		}
            if($icount==0){
             $col = array_merge($keys,  $col);}                
		foreach ($col as $fields) {
		    fputcsv($fileIO, $fields);
		}
            }
            fclose($fileIO);            
            echo ' elapsed';
            echo $this->formatseconds((microtime(true) - $time));
 }
 public function  formatseconds($sec)
 {
    $hours = floor($sec / 3600);
    $minutes = floor(($sec / 60) % 60);
    $seconds = $sec % 60;
    return "$hours:$minutes:$seconds";
 }

public function getNextSequenceValue($collection)
{
    
    $col = $this->mongo->selectCollection('counters');
    
    $sequenceDocument = $col->findAndModify( array('collection' => $collection),
     array('$inc' => array("sequence_value" => 1)),
     null,
     array(
        "new" => true,
    ));
	
   return $sequenceDocument['sequence_value'];
}
public function getNextInvoiceValue($store_id)
{
    
    $col = $this->mongo->selectCollection('oc_order_invoice');
    
    $sequenceDocument = $col->findAndModify( array('store_id' => $store_id),
     array('$inc' => array("sequence_value" => 1)),
     null,
     array(
        "new" => true,
		'upsert'=>true
    ));
	
   return $sequenceDocument['sequence_value'];
}
}
?>
