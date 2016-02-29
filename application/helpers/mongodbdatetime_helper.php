<?php
function datetimeMongotoReadable($dateTimeMongo)
{
    if ($dateTimeMongo) {
        if (isset($dateTimeMongo->sec)) {
//                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            $dateTimeMongo = date(DATE_ISO8601, $dateTimeMongo->sec);
        } else {
            $dateTimeMongo = date(DATE_ISO8601, strtotime($dateTimeMongo));
        }
    } else {
        $dateTimeMongo = date(DATE_ISO8601, "0000-00-00 00:00:00");
    }
    return $dateTimeMongo;
}

?>
