<?php

class MONGORILLA_DB extends MONGORILLA {

    public $dbh;
    private $mongo;
    public $error;
    private $is_connected = false;

    function __construct($force = false) {
        parent::__construct();
        if ($force)
            $this->is_connected = $this->connect();
        // make sure we have a default collection specified for the base mongorilla col
        if (!defined('DEFAULT_COLLECTION')) {
            define('DEFAULT_COLLECTION', 'mongorilla');
        }
    }

    private function connect() {

        if (is_object($this->dbh))
            return $this->dbh;
        $options = $this->options();

        try {

            if ($options['db_replicas'] === true && $options['db_user'] !== '') {
                //replica and database need authentication
                $m = new Mongo("mongodb://{$options['db_user']}:{$options['db_pass']}@{$options['db_host']}:{$options['db_port']}", array('replicaSet' => true));
            } elseif ($options['db_replicas'] === true) {
                //replica set and no auth.
                $m = new Mongo("mongodb://{$options['db_host']}:{$options['db_port']}", array('replicaSet' => true));
            } elseif ($options['db_user'] != false) {
                ////database need authentication
                $m = new Mongo("mongodb://{$options['db_user']}:{$options['db_pass']}@{$options['db_host']}:{$options['db_port']}");
            } else {
                //default without auth and replica
                $m = new Mongo("mongodb://{$options['db_host']}:{$options['db_port']}", array("persist" => "mongorilla"));
            }

            $this->mongo = $m;
            $this->dbh = $m->$options['db_name'];

            return true;
        } catch (MongoConnectionException $e) {
            $this->error = $this->__('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            $this->error = $this->__('Error: MongoDB error') . $e->getMessage();
        } catch (MongoCursorException $e) {
            $this->error = $this->__('Error: probably username password in config') . $e->getMessage();
        } catch (Exception $e) {
            $this->error = $this->__('Error: ') . $e->getMessage();
        }
        return false;
    }

    private function arrayed($these_objs) {
        /* takes mongo_db objects and returns them as arrays for php */
        if (is_object($these_objs)) {
            $objects = array();
            foreach ($these_objs as $this_obj) {
                $this_object = array();
                foreach ($this_obj as $key => $value) {
                    $this_object[$key] = $value;
                } $objects[] = $this_object;
            }
            if (is_array($objects)) {
                if (!empty($objects)) {
                    return $objects;
                }
            }
        }
    }

    public function count($args = false) {

        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = DEFAULT_COLLECTION;
        }

        $defaults = array(
            'col' => $col,
            'id' => false,
            'type' => false,
            'where' => array()
        );
        $settings = $this->settings($args, $defaults);


        if ($settings['type']) {
            $doc_array = array('docType' => $settings['type']);
            $settings['where'] = $settings['where'] + $doc_array;
        }

        if ($settings['id']) {
            $mongo_id = new MongoID($settings['id']);
            $id_array = array("_id" => $mongo_id);
            $settings['where'] = $settings['where'] + $id_array;
        }

        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;
        try {

            $collection = $dbh->$settings['col'];
            $results = $collection->find($settings['where'])->count();
            return $results;
        } catch (Exception $e) {
            // should be able to do this class wide on the base object
            return $this->__('Error: ') . $e->getMessage();
        }
    }

    public function _id($id) {
        $mongo_id = '';
        if (isset($id)) {
            if (is_object($id)) {
                foreach ($id as $key => $value) {
                    if ($key == '$id') {
                        $mongo_id = $value;
                    }
                }
            }
            return (string) $mongo_id;
        } else {
            return (string) $id;
        }
    }

    public function options() {

        if (isset($this->options) && !empty($this->options))
            return $this->options;

        /* ELSE - GATHER CONFIG SETTINGS */
        if (!defined('MONGODB_NAME'))
            define('MONGODB_NAME', 'medbase');
        $this->register_configuration_setting('db_name', 'MONGODB_NAME', MONGODB_NAME);

        if (!defined('MONGODB_HOST'))
            define('MONGODB_HOST', 'localhost');
        $this->register_configuration_setting('db_host', 'MONGODB_HOST', MONGODB_HOST);

        if (!defined('MONGODB_USERNAME'))
            define('MONGODB_USERNAME', false);
        $this->register_configuration_setting('db_user', 'MONGODB_USERNAME', MONGODB_USERNAME);

        if (!defined('MONGODB_PASSWORD'))
            define('MONGODB_PASSWORD', false);
        $this->register_configuration_setting('db_pass', 'MONGODB_PASSWORD', MONGODB_PASSWORD);

        if (!defined('MONGODB_PORT'))
            define('MONGODB_PORT', '27017');
        $this->register_configuration_setting('db_port', 'MONGODB_PORT', MONGODB_PORT);

        if (!defined('MONGODB_REPLICAS'))
            define('MONGODB_REPLICAS', false);
        $this->register_configuration_setting('db_replicas', 'MONGODB_REPLICAS', MONGODB_REPLICAS);

        return $this->options;
    }

    public function upsert($args = false) {

        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = DEFAULT_COLLECTION;
        }

        $defaults = array(
            'col' => $col,
            'obj' => false,
            'type' => false,
            'partial' => true,
            'multi' => false,
            'id' => false
        );
        $settings = $this->settings($args, $defaults);

        if ($settings['type']) {
            $doc_array = array('docType' => $settings['type']);
            $settings['obj'] = $settings['obj'] + $doc_array;
        }

        if ($settings['partial'] && $settings['id']) {
            $old_doc = array(
                'col' => $settings['col'],
                'id' => $settings['id']
            );
            $old_data = $this->find($old_doc);
            foreach ($old_data as $old) {
                $old_data = $old;
            }
            $settings['obj'] = $settings['obj'] + $old_data;
        }

        if (isset($settings['multi']) && $settings['multi'] == true) {
            $update = 'multi';
        } else {
            $update = 'upsert';
        }


        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        try {

            $collection = $dbh->$settings['col'];
            $mongo_id = new MongoID($settings['id']);
            $key = array("_id" => $mongo_id);
            $data = $settings['obj'];
            $results = $dbh->command(array(
                'findAndModify' => $settings['col'],
                'query' => $key,
                'update' => $data,
                'new' => true,
                $update => true,
                'fields' => array('_id' => 1) // mongoDB returns these values
                    ));
            return $results['value']['_id'];
        } catch (Exception $e) {
            // should be able to do this class wide on the base object
            return $this->__('Error: ') . $e->getMessage();
        }
    }

    public function mbsert($args = false) {

        /* mbsert() allow for intelligent inserting and (or) updating */

        $defaults = array(
            'col' => 'mbsert',
            'obj' => false,
            'id' => false
        );
        $settings = $this->settings($args, $defaults);

        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        try {

            $collection = $dbh->$settings['col'];
            $mongo_id = new MongoID($settings['id']);
            $key = array("_id" => $mongo_id);
            $data = $settings['obj'];
            $results = $dbh->command(array(
                'findAndModify' => $settings['col'],
                'query' => $key,
                'update' => $data,
                'new' => true,
                'upsert' => true,
                'fields' => array('_id' => 1) // mongoDB returns these values
                    ));
            return $results['value']['_id'];
        } catch (Exception $e) {
            // should be able to do this class wide on the base object
            return $this->__('Error: ') . $e->getMessage();
        }
    }

    public function rawFind($col, $search, $sort) {

        $combined_array = false;
        $i = 0;
        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        foreach ($col as $this_collection) {
            $collection = $dbh->$this_collection;
            $results = $this->arrayed($dbh->$collection->find($search)->sort($sort));

            if (isset($results)) {
                foreach ($results as $result) {
                    $combined_array[$i] = $result;
                    $i++;
                }
            }
        }
        return $combined_array;
    }

    public function find($args = false) {
        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = array(DEFAULT_COLLECTION);
        }

        $defaults = array(
            'col' => $col,
            'where' => array(),
            'distinct' => false,
            'type' => false,
            'regex' => false,
            'limit' => false,
            'keys' => false,
            'offset' => false,
            'order_by' => false,
            'order' => false,
            'id' => false,
            'map' => false,
            'reduce' => false,
            'out' => false,
            'near' => false,
            'distance' => 100
        );
        $settings = $this->settings($args, $defaults);

        if ($settings['order_by']) {
            if ($settings['order'] == 'asc') {
                $order_value = 1;
            } else {
                $order_value = -1;
            }
            if ($settings['order_by'] == "natural") {
                $order_by = '$natural';
            } else {
                $order_by = $settings['order_by'];
            }
            $sort_clause = array($order_by => $order_value);
        } else {
            $sort_clause = array();
        }

        if ($this->is_set($settings['col']) == false) {
            $settings['col'] = array($settings['col']);
        }

        if ($settings['map']) {
            $settings['map'] = new MongoCode($settings['map']);
        }

        if ($settings['reduce']) {
            $settings['reduce'] = new MongoCode($settings['reduce']);
        }

        if ($settings['type']) {
            $doc_array = array('docType' => $settings['type']);
            $settings['where'] = $settings['where'] + $doc_array;
        }

        if ($settings['id']) {
            $mongo_id = new MongoID($settings['id']);
            $id_array = array("_id" => $mongo_id);
            $settings['where'] = $settings['where'] + $id_array;
        }

        /* if($this->is_set($settings['keys'])){
          $temp = array();
          foreach($settings['keys'] as $key)
          {
          $temp[$key] = true;
          }
          $settings['keys'] = $temp;
          }
          elseif($settings['keys']){
          $temp = array($settings['keys'] => true);
          $settings['keys'] = $temp;
          } */

        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        if ($settings['regex'] !== false) {
            $regex_object = new MongoRegex($settings['regex']);
            $where = $settings['where'];
            $settings['where'] = array(
                $where => $regex_object
            );
        }
        if ($settings['keys']) {
            
        } else {
            $settings['keys'] = array();
        }

        try {

            if ($this->is_set($settings['near'])) {

                $geo_near_query = array('geoNear' => $settings['col'], 'near' => $settings['near'], '$spherical' => true, '$maxDistance' => $settings['distance'] / 6378, 'num' => $settings['limit']);
                $geo_results = $dbh->command($geo_near_query);
                if (isset($geo_results['results'])) {
                    foreach ($geo_results['results'] as $result) {
                        if (is_array($result['obj'])) {
                            $temp_geo_results[] = $result['obj'];
                        }
                    } $results = $temp_geo_results;
                    return $results;
                } else {
                    return false;
                }
            } elseif (isset($settings['distinct']) && $settings['distinct'] != false) {

                $combined_array = false;
                $i = 0;

                foreach ($settings['col'] as $this_collection) {
                    $distinct_query = array("distinct" => '' . $this_collection . '', 'key' => $settings['distinct'], 'query' => $settings['where']);
                    $results = $dbh->command($distinct_query);
                    if (isset($results)) {
                        foreach ($results['values'] as $result) {
                            $combined_array[$i] = $result;
                            $i++;
                        }
                    }
                }

                return $combined_array;
            } elseif ($this->is_set($settings['col'])) {

                $combined_array = false;
                $i = 0;

                foreach ($settings['col'] as $this_collection) {
                    $collection = $dbh->$this_collection;

                    if ($settings['map']) {
                        $map_query = array(
                            "mapreduce" => $this_collection,
                            "map" => $settings['map'],
                            "reduce" => $settings['reduce'],
                            "query" => $settings['where'], //,$settings['keys']
                            "out" => $settings['out']);
                        $results = $this->arrayed($dbh->command($map_query));
                    } else {
                        $cursor = $collection->find($settings['where'], $settings['keys']);

                        $cursor = $cursor->sort($sort_clause)->skip($settings['offset'])->limit($settings['limit']);

                        $results = $this->arrayed($cursor);
                    }
                    if (isset($results)) {
                        foreach ($results as $result) {
                            $combined_array[$i] = $result;
                            $i++;
                        }
                    }
                }
                return $combined_array;
            } else {

                $collection = $dbh->$settings['col'];
                $results = $this->arrayed($collection->find($settings['where'], $settings['keys'])->sort($sort_clause)->skip($settings['offset'])->limit($settings['limit']));
                return $results;
            }
        } catch (Exception $e) {
            return $this->__('Error: ') . $e->getMessage();
        }
    }

    public function group($args = false) {

        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = array(DEFAULT_COLLECTION);
        }

        $defaults = array(
            'col' => $col,
            'keys' => array(),
            'initial' => false,
            'cond' => false,
            'reduce' => false,
            'finalize' => false,
            'options' => false,
        );

        $settings = $this->settings($args, $defaults);

        if ($this->is_set($settings['col']) == false) {
            $settings['col'] = array($settings['col']);
        }
        if ($this->is_set($settings['cond'])) {
            $settings['options'] = $settings['cond'];
        }

        if ($this->is_set($settings['finalize'])) {
            $settings['options'] = $settings['options'] + $settings['finalize'];
        }

        $combined_array = false;
        $i = 0;
        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        try {
            foreach ($settings['col'] as $this_collection) {
                $collection = $dbh->$this_collection;
                $results = $collection->group($settings['keys'], $settings['initial'], $settings['reduce'], $settings['cond']);
                $results = $results['retval'];
                if (isset($results)) {
                    foreach ($results as $result) {
                        $combined_array[$i] = $result;
                        $i++;
                    }
                }
            }
            return $combined_array;
        } catch (Exception $e) {
            return $this->__('Error: ') . $e->getMessage();
        }
    }

    public function delete($args = false) {

        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = DEFAULT_COLLECTION;
        }

        $defaults = array(
            'col' => $col,
            'id' => false,
            'where' => false,
        );
        $settings = $this->settings($args, $defaults);
        if ($settings['type']) {
            $doc_array = array('docType' => $settings['type']);
            $settings['where'] = $settings['where'] + $doc_array;
        }

        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        try {

            $collection = $dbh->$settings['col'];
            if ($settings['id']) {
                $criteria = array(
                    '_id' => new MongoId($settings['id']),
                );
            } elseif ($this->is_set($settings['where'])) {
                $criteria = $settings['where'];
            }
            $progress = $collection->remove($criteria, array('safe' => true));
            return $progress['n'];
        } catch (Exception $e) {
            return $this->__('Error: ') . get_class($e) . ' : ' . $e->getMessage();
        }
    }

    public function list_collections_names() {
        try {
            if (!$this->is_connected)
                $this->connect();
            $dbh = $this->dbh;
            $list = $dbh->listCollections();
            foreach ($list as $collection) {
                $collections[] = $collection->getName();
            }
            return $collections;
        } catch (Exception $e) {
            return $this->__('Error: ') . get_class($e) . ' : ' . $e->getMessage();
        }
    }

    public function list_collections() {
        try {
            if (!$this->is_connected)
                $this->connect();
            $dbh = $this->dbh;
            $list = $dbh->listCollections();
            return $list;
        } catch (Exception $e) {
            return $this->__('Error: ') . get_class($e) . ' : ' . $e->getMessage();
        }
    }

    public function createIndex($args = false) {
        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = DEFAULT_COLLECTION;
        }

        $defaults = array(
            'col' => $col,
            'key' => false,
            'unique' => false,
            'sparse' => false,
            'background' => false,
            'dropDups' => false
        );

        $settings = $this->settings($args, $defaults);
        $indexes = array();
        if ($this->is_set($settings['key'])) {
            foreach ($settings['key'] as $key => $value) {
                if (strtolower($value) == 'asc') {
                    $value = 1;
                } else {
                    $value = -1;
                }
                $indexes[$key] = $value;
            }
        }
        $options = array();
        if ($this->is_set($settings['unique'])) {
            $options['unique'] = $settings['unique'];
        }
        if ($this->is_set($settings['sparse'])) {
            $options['sparse'] = $settings['sparse'];
        }
        if ($this->is_set($settings['background'])) {
            $options['background'] = $settings['background'];
        }
        if ($this->is_set($settings['dropDups'])) {
            $options['dropDups'] = $settings['dropDups'];
        }

        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        try {
            $collection = $dbh->$settings['col'];
            $result = $collection->ensureIndex($indexes, $options);
            return $result;
        } catch (Exception $e) {
            return $this->__('Error: ') . get_class($e) . ' : ' . $e->getMessage();
        }
    }

    public function deleteIndex($args = false) {
        //See what collection we're using
        if (isset($args['col'])) {
            $col = $args['col'];
            //remove it so we add it cleanly
            unset($args['col']);
        } else {
            $col = DEFAULT_COLLECTION;
        }

        $defaults = array(
            'col' => $col,
            'key' => false
        );

        $settings = $this->settings($args, $defaults);

        if (!$this->is_connected)
            $this->connect();
        $dbh = $this->dbh;

        try {
            $collection = $dbh->$settings['col'];
            $result = $collection->deleteIndex($settings['key']);
            return $result;
        } catch (Exception $e) {
            return $this->__('Error: ') . get_class($e) . ' : ' . $e->getMessage();
        }
    }

}