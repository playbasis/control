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

        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            if(@fopen(S3_IMAGE . $filename,"r")){
                @copy(S3_IMAGE . $filename, DIR_IMAGE . $filecopy);
            }else{
                return;
            }
        }

        $info = pathinfo($filename);
        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

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

            $data_image = @file_get_contents(DIR_IMAGE.$new_image);

            //move the file
            $this->s3->putObject($data_image, "elasticbeanstalk-ap-southeast-1-007834438823", $new_image, S3::ACL_PUBLIC_READ);
        }

        return S3_IMAGE . $new_image;
    }
}
?>