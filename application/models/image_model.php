<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Image_model extends MY_Model
{
    public function resize($filename, $width, $height) {

        $filename = urldecode($filename );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filecopy = str_replace("/","\\",$filename);
        }else{
            $filecopy = str_replace("\\","/",$filename);
        }

        $info = pathinfo($filename);
        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'data/' . utf8_substr($filename, 0,
                utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

//        $headers = get_headers(S3_IMAGE.$new_image, 1);

//        if($headers[0] != 'HTTP/1.1 404 Not Found' || $headers[0] != 'HTTP/1.0 403 Forbidden'){
//            return S3_IMAGE.$new_image;
//        }

        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            if(@fopen(S3_IMAGE . 'data/' . $filename,"r")){
                @copy(S3_IMAGE . 'data/' . $filename, DIR_IMAGE . $filecopy);
            }else{
                return S3_IMAGE."cache/no_image-".$width.'x'.$height.".jpg";
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

            $this->s3->setEndpoint("s3-ap-southeast-1.amazonaws.com");

            //move the file
            if ($this->s3->putObjectFile(DIR_IMAGE . $new_image, "elasticbeanstalk-ap-southeast-1-007834438823",
                $new_image, S3::ACL_PUBLIC_READ)
            ) {
                unlink(DIR_IMAGE . $old_image);
                unlink(DIR_IMAGE . $new_image);
            }
        }

        return S3_IMAGE . $new_image;
    }

    public function getTotalSize ($client_id){

        $match = array(
            'client_id' => $client_id
        );
        $result = $this->mongo_db->aggregate('playbasis_file', array(
            array(
                '$match' => $match,
            ),
            array(
                '$group' => array('_id' => null,'size' => array('$sum' => '$file_size'))
            ),
        ));

        if (isset($result['result'][0])){
            $result = $result['result'][0]['size'];
        }else{
            $result = 0;
        }
        return $result;

    }

    public function registerImageToSite($client_id, $site_id, $image_size, $filename, $url, $directory = null)
    {
        if ($this->getImageUrl($client_id, $site_id, $filename, $directory)) {
            $mongoDate = new MongoDate(time());

            $this->mongo_db->set('date_modified', $mongoDate);
            $this->mongo_db->set('file_size', $image_size);
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
                'url' => $url,
                'file_size' => $image_size
            );
            if (isset($filename)) {
                $data['file_name'] = $filename;
            }
            if (isset($filename)) {
                $data['directory'] = $directory;
            }

            $data['date_added'] = $mongoDate;
            $data['date_modified'] = $mongoDate;

            try{
                $result = $this->mongo_db->insert('playbasis_file', $data);
            }catch (Exception $e){
                $result = false;
            }
        }

        return $result;

    }

    public function getImageUrl ($client_id,$site_id,$filename,$directory=null,$pb_player_id=null){

        $this->mongo_db->select(array ('url'));
        $this->mongo_db->select(array (),array('_id'));
        $this->mongo_db->where('client_id',$client_id);
        $this->mongo_db->where('site_id',$site_id);
        $this->mongo_db->where('file_name',$filename);
        $this->mongo_db->where('directory',$directory);

        if ($pb_player_id)$this->mongo_db->where('pb_player_id',$pb_player_id);

        $result = $this->mongo_db->get('playbasis_file');

        return $result? $result[0]['url']:null;

    }

    public function countImages($client_id, $site_id = null)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        if ($site_id) {
            $this->mongo_db->where('site_id', new MongoId($site_id));
        }
        $total = $this->mongo_db->count('playbasis_file');

        return $total;
    }

    public function retrieveImages($client_id, $site_id, $optionalParams = array())
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $sort_data = array(
            '_id',
            'sort_order'
        );

        if (isset($optionalParams['order']) && (utf8_strtolower($optionalParams['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($optionalParams['sort']) && in_array($optionalParams['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($optionalParams['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('_id' => $order));
        }

        if (isset($optionalParams['offset']) || isset($optionalParams['limit'])) {
            if ($optionalParams['offset'] < 0) {
                $optionalParams['offset'] = 0;
            }

            if ($optionalParams['limit'] < 1) {
                $optionalParams['limit'] = 20;
            }

            $this->mongo_db->limit((int)$optionalParams['limit']);
            $this->mongo_db->offset((int)$optionalParams['offset']);
        }

        $this->mongo_db->select(array(
            "_id",
            "date_added",
            "date_modified",
            "directory",
            "file_name",
            "file_size",
            "url"
        ));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        return $this->mongo_db->get("playbasis_file");
    }

    public function retrieveImage($image_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        try {
            $this->mongo_db->where('_id', new MongoId($image_id));
        } catch (Exception $e) {
            return null;
        }
        $c = $this->mongo_db->get('playbasis_file');

        if ($c) {
            return $c[0];
        } else {
            return null;
        }
    }
}