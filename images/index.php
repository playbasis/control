<?php

function setSize($im, $width, $height){
    $imageprops = $im->getImageGeometry();
    $origin_width = $imageprops['width'];
    $origin_height = $imageprops['height'];

    if(!$width){
        $per = (float)((float)$height * (float)100)/(float)$origin_height;

        $width = round((((float)$origin_width * (float)$per)/(float)100));
    }

    if(!$height){
        $per = (float)((float)$width * (float)100)/(float)$origin_width;

        $height = round((((float)$origin_height * (float)$per)/(float)100));
    }

    $size = array($width,$height);

    return $size;
}

function resizeImage($im, $width, $height){

    $im->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, true);

    return $im;
}

$image = isset($_GET['image'])?$_GET['image']:null;
$width = isset($_GET['width'])?$_GET['width']:null;
$height = isset($_GET['height'])?$_GET['height']:null;
$redirect = isset($_GET['redirect'])?$_GET['redirect']:true;


if(preg_match('/^(?:http(?:s)?:\/\/)(images.pbapp.net\/cache\/data|images.pbapp.net\/data)+\/(.+)$/', $image, $filename_s) ){

    $filename_s = $filename_s[(count($filename_s)-1)];
    $filename_a = explode('.', $filename_s);
    $filename = $filename_a[(count($filename_a)-2)];

    try
    {
        /*** a new imagick object ***/
        $im = new Imagick();

        $tmp = "/tmp/".$filename.".".$filename_a[(count($filename_a)-1)];
        $remote_image = file_get_contents($image);
        file_put_contents($tmp, $remote_image);

        /*** ping the image ***/
        $im->pingImage($tmp);

        /*** read the image into the object ***/
        $im->readImage($tmp);

        if($width || $height){
            list($width,$height) = setSize($im, $width, $height);
            $im = resizeImage($im, $width, $height);
        }

        $format = isset($_GET['format'])?$_GET['format']:null;
        $format_require = array('png','jpg','gif');

        if(!$format){
            $format = $filename_a[(count($filename_a)-1)];
        }

        if($width || $height){
            $exit_filename = $filename.'_'.$width.'x'.$height.'.'.$format;
        }else{
            $exit_filename = $filename.'.'.$format;
        }
        $exit_url= 'http://images.pbapp.net/cache/data/'.$exit_filename;
        $exit_url_headers = @get_headers($exit_url);

        if(($exit_url_headers[0] == 'HTTP/1.0 404 Not Found') || ( $exit_url_headers[0] == 'HTTP/1.1 403 Forbidden') || ($exit_url_headers[0] == 'HTTP/1.0 302 Found' && $exit_url_headers[7] == 'HTTP/1.0 404 Not Found')){

            if($format && in_array($format, $format_require)){

                /**** convert to format ***/
                $im->setImageFormat($format);

                /*** write image to disk ***/
                $im->writeImage($exit_filename);

                require_once('s3.php');
                $s3 = new S3();

                $s3->setEndpoint("s3-ap-southeast-1.amazonaws.com");
                if ($s3->putObjectFile($exit_filename, "elasticbeanstalk-ap-southeast-1-007834438823", 'cache/data/'.$exit_filename, S3::ACL_PUBLIC_READ)){
                    unlink($exit_filename);
                }

                if($redirect){
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$exit_url);
                }else{
                    echo json_encode(array("url"=>$exit_url));
                }
            }

        } else {
            if($redirect){
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$exit_url);
            }else{
                echo json_encode(array("url"=>$exit_url));
            }
        }
        unlink($tmp);
    }
    catch(Exception $e)
    {
        if($redirect){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$image);
        }else{
            echo json_encode(array("url"=>$image));
        }
    }
}else{
    header("HTTP/1.1 404 Not Found");
}
?>