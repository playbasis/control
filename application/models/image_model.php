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

    public function uploadImage ($client_id,$site_id,$image,$filename,$directory=null,$pb_player_id=null){
        $result = $this->s3->putObjectFile($image['tmp_name'], S3_BUCKET, rtrim(S3_CONTENT_FOLDER . $directory, '/')."/". $filename, S3::ACL_PUBLIC_READ);
        $url = rtrim(S3_IMAGE.S3_CONTENT_FOLDER. $directory, '/')."/". urlencode($filename);

        if ($result){

            if ($this->getImageUrl($client_id,$site_id,$filename,$directory,$pb_player_id)){
                $mongoDate = new MongoDate(time());

                $this->mongo_db->set('date_modified',$mongoDate);
                $this->mongo_db->set('file_size',$image['size']);
                $this->mongo_db->set('url',$url);
                $this->mongo_db->where('client_id',$client_id);
                $this->mongo_db->where('site_id',$site_id);
                $this->mongo_db->where('file_name',$filename);

                $result  = $this->mongo_db->update('playbasis_file');
            }
            else{
                $mongoDate = new MongoDate(time());
                $this->set_site_mongodb($site_id);
                $data = array(
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'pb_player_id' => $pb_player_id,
                    'file_name' => $filename,
                    'directory' => $directory,
                    'url' => $url,
                    'file_size' => $image['size']
                );

                $data['date_added'] = $mongoDate;
                $data['date_modified'] = $mongoDate;

                $this->mongo_db->insert('playbasis_file', $data);
            }
        }

        return $result;

    }
    public function deleteImage ($client_id,$site_id,$filename,$directory=null,$pb_player_id=null){
        $uri = rtrim(S3_CONTENT_FOLDER . $directory, '/')."/". $filename;
        $result = $this->s3->deleteObject(S3_BUCKET,$uri);
        if ($result){
            $uri = rtrim(S3_CONTENT_FOLDER .THUMBNAIL_FOLDER. $directory, '/')."/". $filename;
            $result = $this->s3->deleteObject(S3_BUCKET,$uri);
        }
        if ($result){
            $this->mongo_db->where('client_id',$client_id);
            $this->mongo_db->where('site_id',$site_id);
            $this->mongo_db->where('file_name',$filename);
            if ($directory)$this->mongo_db->where('directory',$directory);
            if ($pb_player_id)$this->mongo_db->where('pb_player_id',$pb_player_id);

            $this->mongo_db->delete('playbasis_file');
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
    public function resize($filename, $width, $height,$cache_folder='cache/') {

        $filename = urldecode($filename );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filecopy = str_replace("/","\\",$filename);
        }else{
            $filecopy = str_replace("\\","/",$filename);
        }

        $info = pathinfo($filename);

        $old_image = $filename;
        $new_image = $cache_folder .$filename;


        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            if(@fopen(S3_IMAGE .S3_CONTENT_FOLDER. $filename,"r")){
                @copy(S3_IMAGE .S3_CONTENT_FOLDER. $filename, DIR_IMAGE . $filecopy);
            }else{
                return S3_IMAGE.$cache_folder."no_image.jpg";
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

            $data_image = @file_get_contents(DIR_IMAGE.$new_image);

            //move the file
            $this->s3->putObject($data_image, S3_BUCKET,S3_CONTENT_FOLDER.$new_image, S3::ACL_PUBLIC_READ);
        }

        return S3_IMAGE .S3_CONTENT_FOLDER. $new_image;
    }
    public function createThumbnail($filename){
        return $this->resize($filename,80,80,THUMBNAIL_FOLDER);
    }
}
?>