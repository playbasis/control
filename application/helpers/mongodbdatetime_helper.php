<?php


function datetimeMongotoReadable($dateTimeMongo){
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

function dateMongotoReadable($dateTimeMongo){
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00";
        }
        return $dateTimeMongo;
}
function MonthMongotoReadable($dateTimeMongo){
    if ($dateTimeMongo) {
        if (isset($dateTimeMongo->sec)) {
            $dateTimeMongo = date("Y-m", $dateTimeMongo->sec);
        } else {
            $dateTimeMongo = $dateTimeMongo;
        }
    } else {
        $dateTimeMongo = "0000-00-00";
    }
    return $dateTimeMongo;
}