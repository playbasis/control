<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Image_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('s3');
        $this->load->library('image');
    }

    public function uploadImage($client_id, $site_id, $image, $filename, $directory = null, $pb_player_id = null, $user_id = null)
    {
        $this->s3->setEndpoint("s3-ap-southeast-1.amazonaws.com");

        $result = $this->s3->putObjectFile($image['tmp_name'], S3_BUCKET,
                    rtrim(S3_DATA_FOLDER . $directory, '/') . "/" . $filename, S3::ACL_PUBLIC_READ);
        $url = rtrim(S3_DATA_FOLDER . $directory, '/') . "/" . urlencode($filename);

        if ($result) {

            if ($this->getImageUrl($client_id, $site_id, $filename, $directory, $pb_player_id)) {
                $mongoDate = new MongoDate(time());

                $this->mongo_db->set('date_modified', $mongoDate);
                $this->mongo_db->set('file_size', $image['size']);
                $this->mongo_db->set('url', $url);
                $this->mongo_db->where('client_id', $client_id);
                $this->mongo_db->where('site_id', $site_id);
                $this->mongo_db->where('file_name', $filename);

                $result = $this->mongo_db->update('playbasis_file');
            } else {
                $mongoDate = new MongoDate(time());
                $this->set_site_mongodb($site_id);
                $data = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'file_name' => $filename,
                    'directory' => $directory,
                    'url' => $url,
                    'type' => $image['type'],
                    'file_size' => $image['size'],
                    'width' => $image['width'],
                    'height' => $image['height']
                );

                // Assign pb_player_id to null if not assign pb_player_id or user_id
                if ($pb_player_id){
                    $data['pb_player_id'] = new MongoId($pb_player_id);
                }elseif($user_id){
                    $data['user_id'] = new MongoId($user_id);
                }else{
                    $data['pb_player_id'] = null;
                }

                $data['date_added'] = $mongoDate;
                $data['date_modified'] = $mongoDate;

                $result = $this->mongo_db->insert('playbasis_file', $data);
            }
        }
        return $result;
    }

    public function deleteImage($client_id, $site_id, $filename, $directory = null)
    {

        $uri = rtrim(S3_DATA_FOLDER . $directory, '/') . "/" . $filename;
        $result = $this->s3->deleteObject(S3_BUCKET, $uri);
        if ($result) {
            $extension = substr(strrchr($filename,'.'), 0);
            $name = basename($filename, $extension);
            $uri = rtrim(THUMBNAIL_FOLDER . S3_DATA_FOLDER . $directory, '/') . "/" . $name . '-' .
                MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH . 'x' . MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT . $extension;
            $result = $this->s3->deleteObject(S3_BUCKET, $uri);

            if ($result){
                $uri = rtrim(THUMBNAIL_FOLDER . S3_DATA_FOLDER . $directory, '/') . "/" . $name . '-' .
                    MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH . 'x' . MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT . $extension;
                $result = $this->s3->deleteObject(S3_BUCKET, $uri);
            }
        }
        if ($result) {
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('file_name', $filename);
            if ($directory) {
                $this->mongo_db->where('directory', $directory);
            }

            $result = $this->mongo_db->delete('playbasis_file');
        }

        return $result;

    }

    public function getImageUrl($client_id, $site_id, $filename, $directory = null, $pb_player_id = null)
    {

        $this->mongo_db->select(array('url'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('file_name', $filename);
        $this->mongo_db->where('directory', $directory);

        if ($pb_player_id) {
            $this->mongo_db->where('pb_player_id', $pb_player_id);
        }

        $result = $this->mongo_db->get('playbasis_file');

        return $result ? $result[0]['url'] : null;

    }

    public function getTotalSize($client_id)
    {

        $match = array(
            'client_id' => $client_id
        );
        $result = $this->mongo_db->aggregate('playbasis_file', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => null, 'size' => array('$sum' => '$file_size'))
            ),
        ));

        if (isset($result['result'][0])) {
            $result = $result['result'][0]['size'];
        } else {
            $result = 0;
        }
        return $result;

    }

    private function resize($filename, $width, $height, $cache_folder = 'thumb/')
    {

        $filename = urldecode($filename);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filecopy = str_replace("/", "\\", $filename);
        } else {
            $filecopy = str_replace("\\", "/", $filename);
        }

        $info = pathinfo($filename);
        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = $cache_folder . S3_DATA_FOLDER . utf8_substr($filename, 0,
                utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;


        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            if (@fopen(S3_IMAGE . S3_DATA_FOLDER . $filename, "r")) {
                @copy(S3_IMAGE . S3_DATA_FOLDER . $filename, DIR_IMAGE . $filecopy);
            } else {
                return S3_IMAGE . $cache_folder . "no_image.jpg";
            }
        }

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            $image = new Image();
            $image->load(DIR_IMAGE . $old_image);
            $image->resize($width, $height);
            $image->save(DIR_IMAGE . $new_image);

            $data_image = @file_get_contents(DIR_IMAGE . $new_image);

            //move the file
            $this->s3->putObject($data_image, S3_BUCKET, $new_image, S3::ACL_PUBLIC_READ);

            @unlink(DIR_IMAGE . $new_image);

        }

        return S3_IMAGE . $new_image;
    }

    public function retrieveData($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($site_id);

        if (isset($optionalParams['id']) && !empty($optionalParams['id'])) {
            try {
                $id = new MongoId($optionalParams['id']);
                $this->mongo_db->where('_id', $id);
            } catch (Exception $e) {
                return null;
            }
        }

        if (isset($optionalParams['pb_player_id']) && !empty($optionalParams['pb_player_id'])) {
            try {
                $pb_player_id = new MongoId($optionalParams['pb_player_id']);
                $this->mongo_db->where('pb_player_id', $pb_player_id);
            } catch (Exception $e) {
                return null;
            }
        }

        // Sorting
        $sort_data = array('_id', 'date_added', 'date_modified', 'type', 'file_size');

        if (isset($optionalParams['order']) && (mb_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('date_added' => $order));
        }

        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));

        $result = $this->mongo_db->get('playbasis_file');

        return $result;
    }

    public function createThumbnailSmall($filename)
    {
        return $this->resize($filename, MEDIA_MANAGER_SMALL_THUMBNAIL_WIDTH, MEDIA_MANAGER_SMALL_THUMBNAIL_HEIGHT, THUMBNAIL_FOLDER);
    }

    public function createThumbnailLarge($filename)
    {
        return $this->resize($filename, MEDIA_MANAGER_LARGE_THUMBNAIL_WIDTH, MEDIA_MANAGER_LARGE_THUMBNAIL_HEIGHT, THUMBNAIL_FOLDER);
    }
}

?>