<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Level_model extends MY_Model
{
    public function getTotalLevels($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->count("playbasis_exp_table");

        return $results;
    }

    public function getLevel($level_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($level_id));
        $results = $this->mongo_db->get("playbasis_exp_table");

        return $results ? $results[0] : null;
    }

    public function getLevelSite($level_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($level_id));
        $results = $this->mongo_db->get("playbasis_client_exp_table");

        return $results ? $results[0] : null;
    }

    public function getLevels($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['order'])) {
            if (strtolower($data['order']) == 'desc') {
                $order = -1;
            } else {
                $order = 1;
            }
        } else {
            $order = 1;
        }

        $sort_data = array(
            'level_title',
            'level',
            'status',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('_id' => $order));
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $level_data = $this->mongo_db->get('playbasis_exp_table');

//        foreach ($results as $result) {
//            $level_data[] = array(
//                'level_id' => $result['_id'],
//                'level' => $result['level'],
//                'title' => $result['level_title'],
//                'exp' => number_format($result['exp'], 0),
//                'status' => $result['status'],
//                'sort_order' => $result['sort_order']
//            );
//        }

        return $level_data;
    }

    public function getLevelConditions()
    {
        return $this->mongo_db->get('playbasis_level_to_condition');
    }

    public function getTotalLevelsSite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        $results = $this->mongo_db->count("playbasis_client_exp_table");

        return $results;
    }

    public function getLevelsSite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $this->mongo_db->where('status', (bool)$data['filter_status']);
        }

        if (isset($data['order'])) {
            if (strtolower($data['order']) == 'desc') {
                $order = -1;
            } else {
                $order = 1;
            }
        } else {
            $order = 1;

        }

        $sort_data = array(
            'level_title',
            'level',
            'status',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));

        } else {
            $this->mongo_db->order_by(array('level' => $order));
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $level_data = $this->mongo_db->get('playbasis_client_exp_table');

//        foreach ($results as $result) {
//            $level_data[] = array(
//                'level_id' => $result['_id'],
//                'level' => $result['level'],
//                'title' => $result['level_title'],
//                'exp' => number_format($result['exp'], 0),
//                'status' => $result['status'],
//                'sort_order' => $result['sort_order']
//            );
//        }

        return $level_data;
    }

    public function addLevel($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $data_insert = array(
            'level_title' => $data['level_title'] | '',
            'level' => (int)$data['level'] | 0,
            'exp' => (int)$data['exp'] | 0,
            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
            // 'tags' => $data['tags']|'' ,
            'tags' => (isset($data['tags'])) ? $data['tags'] : 0,
            'status' => (bool)$data['status'],
            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
        );

        $exp_id = $this->mongo_db->insert('playbasis_exp_table', $data_insert);
        return $exp_id;
    }

    public function addLevelSite($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['level'])) {
            $toInsertLevel = $data['level'];

            $exists = $this->checkLevelExists($data);

            if (!$exists) {
                $this->mongo_db->where('client_id', new MongoID($data['client_id']));
                $this->mongo_db->where('site_id', new MongoID($data['site_id']));
                $this->mongo_db->where_lt('level', floatval($toInsertLevel));
                $this->mongo_db->order_by(array('level' => 'DESC'));
                $lowerLevels = $this->mongo_db->get('playbasis_client_exp_table');
                $nextLowerLevel = isset($lowerLevels[0]) ? $lowerLevels[0] : null;

                $this->mongo_db->where('client_id', new MongoID($data['client_id']));
                $this->mongo_db->where('site_id', new MongoID($data['site_id']));
                $this->mongo_db->where_gt('level', floatval($toInsertLevel));
                $this->mongo_db->order_by(array('level' => 'ASC'));
                $higerLevels = $this->mongo_db->get('playbasis_client_exp_table');
                $nextHigherLevel = isset($higerLevels[0]) ? $higerLevels[0] : null;

                if (!$nextLowerLevel && $nextHigherLevel) {
                    if ($data['exp'] < $nextHigherLevel['exp']) {
                        $data_insert = array(
                            'client_id' => new MongoID($data['client_id']),
                            'site_id' => new MongoID($data['site_id']),
                            'level_title' => $data['level_title'] | '',
                            'level' => (int)$data['level'],
                            'exp' => (int)$data['exp'] | 0,
                            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES,
                                'UTF-8') : '',
                            // 'tags' => $data['tags']|'' ,
                            'tags' => (isset($data['tags'])) ? $data['tags'] : 0,
                            'status' => (bool)$data['status'],
                            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                        );
                        $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                        return $exp_id;
                    } else {
                        return false;
                    }
                }

                if ($nextLowerLevel && !$nextHigherLevel) {
                    if ($data['exp'] > $nextLowerLevel['exp']) {
                        $data_insert = array(
                            'client_id' => new MongoID($data['client_id']),
                            'site_id' => new MongoID($data['site_id']),
                            'level_title' => $data['level_title'] | '',
                            'level' => (int)$data['level'],
                            'exp' => (int)$data['exp'] | 0,
                            'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES,
                                'UTF-8') : '',
                            // 'tags' => $data['tags']|'' ,
                            'tags' => (isset($data['tags'])) ? $data['tags'] : 0,
                            'status' => (bool)$data['status'],
                            'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                            'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                        );
                        $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                        return $exp_id;
                    } else {
                        return false;
                    }
                }

                if ($data['exp'] > $nextLowerLevel['exp'] && $data['exp'] < $nextHigherLevel['exp']) {
                    $data_insert = array(
                        'client_id' => new MongoID($data['client_id']),
                        'site_id' => new MongoID($data['site_id']),
                        'level_title' => $data['level_title'] | '',
                        'level' => (int)$data['level'],
                        'exp' => (int)$data['exp'] | 0,
                        'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
                        // 'tags' => $data['tags']|'' ,
                        'tags' => (isset($data['tags'])) ? $data['tags'] : 0,
                        'status' => (bool)$data['status'],
                        'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                        'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                    );
                    $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                    return $exp_id;
                } elseif ($nextLowerLevel['exp'] == null && $nextHigherLevel['exp'] == null) {
                    $data_insert = array(
                        'client_id' => new MongoID($data['client_id']),
                        'site_id' => new MongoID($data['site_id']),
                        'level_title' => $data['level_title'] | '',
                        'level' => (int)$data['level'],
                        'exp' => (int)$data['exp'] | 0,
                        'image' => isset($data['image']) ? html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8') : '',
                        // 'tags' => $data['tags']|'' ,
                        'tags' => (isset($data['tags'])) ? $data['tags'] : 0,
                        'status' => (bool)$data['status'],
                        'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                        'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s")))
                    );
                    $exp_id = $this->mongo_db->insert('playbasis_client_exp_table', $data_insert);
                    return $exp_id;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }
    }

    public function editLevel($level_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($level_id));
        if (isset($data['level_title'])) {
            $this->mongo_db->set('level_title', $data['level_title']);
        }
        if (isset($data['level'])) {
            $this->mongo_db->set('level', (int)$data['level']);
        }
        if (isset($data['exp'])) {
            $this->mongo_db->set('exp', (int)$data['exp']);
        }
        if (isset($data['image'])) {
            $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($data['tags'])) {
            $this->mongo_db->set('tags', $data['tags']);
        }
        if (isset($data['status'])) {
            $this->mongo_db->set('status', (bool)$data['status']);
        }
        if (isset($data['sort_order'])) {
            $this->mongo_db->set('sort_order', (int)$data['sort_order']);
        }
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_exp_table');
    }

    public function editLevelSite($level_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $toInsertLevel = $data['level'];

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where_lt('level', floatval($toInsertLevel));
        $this->mongo_db->order_by(array('level' => 'DESC'));
        $lowerLevels = $this->mongo_db->get('playbasis_client_exp_table');
        $nextLowerLevel = isset($lowerLevels[0]) ? $lowerLevels[0] : null;

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where_gt('level', floatval($toInsertLevel));
        $this->mongo_db->order_by(array('level' => 'ASC'));
        $higerLevels = $this->mongo_db->get('playbasis_client_exp_table');
        $nextHigherLevel = isset($higerLevels[0]) ? $higerLevels[0] : null;

        if (!$nextLowerLevel && $nextHigherLevel) {
            if ($data['exp'] < $nextHigherLevel['exp']) {

                //Can make group of code into a function

                $this->mongo_db->where('_id', new MongoID($level_id));
                if (isset($data['level_title'])) {
                    $this->mongo_db->set('level_title', $data['level_title']);
                }
                if (isset($data['level'])) {
                    $this->mongo_db->set('level', (int)$data['level']);
                }
                if (isset($data['exp'])) {
                    $this->mongo_db->set('exp', (int)$data['exp']);
                }
                if (isset($data['image'])) {
                    $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($data['tags'])) {
                    $this->mongo_db->set('tags', $data['tags']);
                }
                if (isset($data['status'])) {
                    $this->mongo_db->set('status', (bool)$data['status']);
                }
                if (isset($data['sort_order'])) {
                    $this->mongo_db->set('sort_order', (int)$data['sort_order']);
                }
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                return $this->mongo_db->update('playbasis_client_exp_table');
                // return true;
            } else {
                return false;
            }
        }

        if ($nextLowerLevel && !$nextHigherLevel) {
            if ($data['exp'] > $nextLowerLevel['exp']) {

                //Can make group of code into a function

                $this->mongo_db->where('_id', new MongoID($level_id));
                if (isset($data['level_title'])) {
                    $this->mongo_db->set('level_title', $data['level_title']);
                }
                if (isset($data['level'])) {
                    $this->mongo_db->set('level', (int)$data['level']);
                }
                if (isset($data['exp'])) {
                    $this->mongo_db->set('exp', (int)$data['exp']);
                }
                if (isset($data['image'])) {
                    $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
                }
                if (isset($data['tags'])) {
                    $this->mongo_db->set('tags', $data['tags']);
                }
                if (isset($data['status'])) {
                    $this->mongo_db->set('status', (bool)$data['status']);
                }
                if (isset($data['sort_order'])) {
                    $this->mongo_db->set('sort_order', (int)$data['sort_order']);
                }
                $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
                return $this->mongo_db->update('playbasis_client_exp_table');
                // return true;

            } else {
                return false;
            }
        }

        if ($data['exp'] > $nextLowerLevel['exp'] && $data['exp'] < $nextHigherLevel['exp']) {

            //Can make group of code into a function

            $this->mongo_db->where('_id', new MongoID($level_id));
            if (isset($data['level_title'])) {
                $this->mongo_db->set('level_title', $data['level_title']);
            }
            if (isset($data['level'])) {
                $this->mongo_db->set('level', (int)$data['level']);
            }
            if (isset($data['exp'])) {
                $this->mongo_db->set('exp', (int)$data['exp']);
            }
            if (isset($data['image'])) {
                $this->mongo_db->set('image', html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($data['tags'])) {
                $this->mongo_db->set('tags', $data['tags']);
            }
            if (isset($data['status'])) {
                $this->mongo_db->set('status', (bool)$data['status']);
            }
            if (isset($data['sort_order'])) {
                $this->mongo_db->set('sort_order', (int)$data['sort_order']);
            }
            $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));
            return $this->mongo_db->update('playbasis_client_exp_table');
            // return true;
        } else {
            return false;
        }


    }

    public function deleteLevel($level_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($level_id));
        $this->mongo_db->delete('playbasis_exp_table');
    }

    public function deleteLevelSite($level_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($level_id));
        $this->mongo_db->delete('playbasis_client_exp_table');
    }

    /*
     * Purge all levels in playbasis_exp_table
     * if not client_id and site_id given
     * or playbasis_client_exp_table
     * if client_id and site_id is given
     * @param string $client_id optional
     * @param string $site_id optional
     * @return object
     */
    private function purgeLevel($client_id = "", $site_id = "")
    {
        if (filter_var($client_id, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($site_id, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        $this->set_site_mongodb($this->session->userdata("site_id"));
        if ($client_id && $site_id) {
            $this->mongo_db->where("client_id", new MongoID($client_id));
            $this->mongo_db->where("site_id", new MongoID($site_id));
            return $this->mongo_db->delete_all('playbasis_client_exp_table');
        } else {
            return $this->mongo_db->delete_all('playbasis_exp_table');
        }
    }

    /*
     * Add Template to playbasis_exp_table
     * if no client_id and site_id
     * playbasis_client_exp_table
     * if client_id and site_id is given
     * @param string $template which template will be use
     * @param string $client_id
     * @param string $site_id
     * @param int $max_level optional
     * @throws "Invalid Template"
     * @return void
     */
    public function addTemplate($template, $client_id = "", $site_id = "", $max_level = 100)
    {
        if (filter_var($client_id, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($site_id, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }
        // empty set of level
        $levels = array();
        $time = new MongoDate(strtotime(date("Y-m-d H:i:s")));
        for ($i = 1; $i <= $max_level; ++$i) {
            $levels[$i] = array(
                "level_title" => "Level " . strval($i),
                "level" => $i,
                "exp" => 0,
                "image" => "",
                "tags" => 0,
                "status" => true,
                "date_modified" => $time,
                "date_added" => $time
            );
            if ($client_id) {
                $levels[$i]["client_id"] = new MongoID($client_id);
                $levels[$i]["site_id"] = new MongoID($site_id);
            }
        }
        // set exp based on template
        switch ($template) {
            case "Curve":
                for ($i = 1; $i <= $max_level; ++$i) {
                    $levels[$i]["exp"] = (25 * $i * $i) - (25 * $i);
                }
                break;
            case "Pokemon":
                for ($i = 1; $i <= $max_level; ++$i) {
                    $levels[$i]["exp"] = round((4 * (pow($i, 3))) / 5) - 1;
                }
                break;
            case "Disgea":
                for ($i = 1; $i <= $max_level; ++$i) {
                    $levels[$i]["exp"] = round(0.04 * (pow($i, 3)) + 0.8 * (pow($i, 2)) + 2 * $i) - 3;
                }
                break;
            default:
                throw new Exception("error_invalid_template");
                break;
        }
        // purge all levels
        if (!$this->purgeLevel($client_id, $site_id)) {
            throw new Exception("error_database");
        }
        // add template to database
        $this->set_site_mongodb($this->session->userdata("site_id"));
        if ($client_id) {
            return $this->mongo_db->batch_insert("playbasis_client_exp_table", $levels);
        } else {
            return $this->mongo_db->batch_insert("playbasis_exp_table", $levels);
        }
    }

    /*
     * Count all levels from playbasis_exp_table
     * if no client_id and site_id
     * playbasis_client_exp_table
     * if client_id and site_id is given
     * @param string $client_id
     * @param string $site_id
     * @return int
     */
    public function countLevels($client_id = "", $site_id = "")
    {
        if (filter_var($client_id, FILTER_VALIDATE_BOOLEAN) !=
            filter_var($site_id, FILTER_VALIDATE_BOOLEAN)
        ) {
            throw new Exception("error_xor_client_site");
        }

        // count number of level documents
        $this->set_site_mongodb($this->session->userdata("site_id"));
        if ($client_id && $site_id) {
            $this->mongo_db->where("client_id", new MongoID($client_id));
            $this->mongo_db->where("site_id", new MongoID($site_id));
            return $this->mongo_db->count("playbasis_client_exp_table");
        } else {
            return $this->mongo_db->count("playbasis_exp_table");
        }
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }

    public function checkLevelExists($data)
    {
        $toInsertLevel = $data['level'];
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('level', (int)$toInsertLevel);
        $check = $this->mongo_db->get('playbasis_client_exp_table');

        if ($check) {
            return true;
        } else {
            return false;
        }
    }

    public function checkLevelExistsEdit($data, $level_id)
    {
        $toInsertLevel = $data['level'];

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('_id', new MongoID($level_id));
        $currentLevel = $this->mongo_db->get('playbasis_client_exp_table');

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        $this->mongo_db->where('level', (int)$toInsertLevel);
        $check = $this->mongo_db->get('playbasis_client_exp_table');

        if (isset($currentLevel[0]['level']) && $toInsertLevel == $currentLevel[0]['level']) {
            return false;
        } elseif ($check) {
            return true;
        } else {
            return false;
        }

    }
}

?>
