<?php

class apiFunc
{
    public $ARRAY = array(
        'event_category' => array(
            'name' => 'event-categories'
        ),
        'place_category' => array(
            'name' => 'place-categories'
        ),
        'city_detail' => array(
            'name' => 'locations'
        ),
        'place_detail' => array(
            'name' => 'places'
        )

    );

    function getEventCategory() {
        $db = Db::getConnection();
        $url = URL."/".VERSION."/".$this->ARRAY['event_category']['name'];
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        if(count($arr)>0){
            $s1 = 0; $s2 = 0;
            foreach ($arr as $k=>$v){
                $result = $db->query("SELECT ID FROM `LIST_CATEGORY` WHERE SLUG = '{$v['slug']}' AND NAME_RU = '{$v['name']}' AND TYPE = 1");
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if($row) {

                    $s1++;
                }
                else {
                    $sql = 'INSERT INTO `LIST_CATEGORY` '
                        . '(SLUG,NAME_RU,`TYPE`) VALUES (:SLUG, :RU, 1)';
                    $result = $db->prepare($sql);
                    $result->bindParam(':SLUG', $v['slug'], PDO::PARAM_STR);
                    $result->bindParam(':RU', $v['name'], PDO::PARAM_STR);
                    $result->execute();
                    $s2++;
                }
            }
            echo "Найдено событий: ".count($arr)." | Событий в базе: ".$s1." | Добавлено: ".$s2.PHP_EOL;
        }
    }

    function getPlaceCategory() {
        $db = Db::getConnection();
        $url = URL."/".VERSION."/".$this->ARRAY['place_category']['name'];
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        if(count($arr)>0){
            $s1 = 0; $s2 = 0;
            foreach ($arr as $k=>$v){
                $result = $db->query("SELECT ID FROM `LIST_CATEGORY` WHERE SLUG = '{$v['slug']}' AND NAME_RU = '{$v['name']}' AND TYPE = 2");
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if($row) {
                    $s1++;
                }
                else {
                    $s2++;
                    $sql = 'INSERT INTO `LIST_CATEGORY` '
                        . '(SLUG,NAME_RU,`TYPE`) VALUES (:ENG, :RU, 2)';
                    $result = $db->prepare($sql);
                    $result->bindParam(':SLUG', $v['slug'], PDO::PARAM_STR);
                    $result->bindParam(':RU', $v['name'], PDO::PARAM_STR);
                    $result->execute();
                }
            }
            echo "Найдено мест: ".count($arr)." | Мест в базе: ".$s1." | Добавлено: ".$s2.PHP_EOL;
        }
    }

    function getCityDetail() {
        $db = Db::getConnection();
        $url = URL."/".VERSION."/".$this->ARRAY['city_detail']['name'];
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        if(count($arr)>0){
            $s1 = 0; $s2 = 0;
            foreach ($arr as $k=>$v){
                $result = $db->query("SELECT ID FROM `LIST_CITIES` WHERE SLUG = '{$v['slug']}'");
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if($row) {
                    $s1++;
                }
                else {
                    $url2 = URL."/".VERSION."/".$this->ARRAY['city_detail']['name']."/".$v['slug'];
                    $result2 = file_get_contents($url2);
                    $arr2 = json_decode($result2, true);
                    if(count($arr2)>0){
                        $sql = 'INSERT INTO `LIST_CITIES` '
                            . '(`NAME`,`SLUG`,`TIMEZONE`,`LAT`, `LON`,`LANGUAGE`,`CURRENCY`) VALUES (:NAME, :SLUG, :TIMEZONE, :LAT, :LON, :LANGUAGE, :CURRENCY)';
                        $result = $db->prepare($sql);
                        $result->bindParam(':NAME', $arr2['name'], PDO::PARAM_STR);
                        $result->bindParam(':SLUG', $arr2['slug'], PDO::PARAM_STR);
                        $result->bindParam(':TIMEZONE', $arr2['timezone'], PDO::PARAM_STR);
                        $result->bindParam(':LAT', $arr2['coords']['lat'], PDO::PARAM_STR);
                        $result->bindParam(':LON', $arr2['coords']['lon'], PDO::PARAM_STR);
                        $result->bindParam(':LANGUAGE', $arr2['language'], PDO::PARAM_STR);
                        $result->bindParam(':CURRENCY', $arr2['currency'], PDO::PARAM_STR);
                        $result->execute();
                    }
                    $s2++;
                }
            }
            echo "Найдено мест: ".count($arr)." | Мест в базе: ".$s1." | Добавлено: ".$s2.PHP_EOL;
        }
    }

    function getPlaceDetail($page) {
        $db = Db::getConnection();
        //$page = 1;
        $url = URL."/".VERSION."/".$this->ARRAY['place_detail']['name']."/".$page."/?expand=images,title,short_title,slug,address,location,timetable,phone,is_stub,images,description,body_text,site_url,foreign_url,coords,subway,favorites_count,comments_count,is_closed,categories,tags";
        if($result = @file_get_contents($url)) $arr = json_decode($result, true);
        if(@count($arr)>0){
                $result = $db->query("SELECT ID FROM `LIST_PLACES` WHERE ID = '{$arr['id']}' AND SLUG = '{$arr['slug']}'");
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    echo "isset this record";
                } else {
                    // проверяем координаты
                    if (isset($arr['coords']) AND $arr['coords'] > 0) {
                        $arr['lat'] = $arr['coords']['lat'];
                        $arr['lon'] = $arr['coords']['lon'];
                    }
                    // добавляем фото
                    if (isset($arr['images']) AND $arr['images'] > 0) {
                        foreach ($arr['images'] as $v => $k) {
                            if(!is_dir('images/place')) mkdir('images/place');
                            if(!is_dir('images/place/'.$arr['id'])) mkdir('images/place/'.$arr['id']);
                            $img = "images/place/".$arr['id']."/".$arr['slug']."_".$v.".jpg";
                            $result = $db->query("SELECT ID FROM `LIST_PLACES_IMG` WHERE IMAGE = '{$img}' AND ID_PLACES = '{$arr['id']}'");
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            if(!$row){
                                file_put_contents($img,file_get_contents($k['image']));
                                $sql_fields  =  "`ID_PLACES`, `IMAGE`";
                                $sql_values = $arr['id'].", '".$img."'";
                                if(isset($k['thumbnails']['640x384'])) {
                                    $thumb_640x384 = "images/place/".$arr['id']."/thumb_".$v."_640x384.jpg";
                                    file_put_contents($thumb_640x384,file_get_contents($k['thumbnails']['640x384']));
                                    $sql_fields  =  $sql_fields.", `THUMB_640x384`";
                                    $sql_values = $sql_values.", '".$thumb_640x384."'";
                                }
                                if(isset($k['thumbnails']['144x96'])) {
                                    $thumb_144x96 = "images/place/".$arr['id']."/thumb_".$v."_144x96.jpg";
                                    file_put_contents($thumb_144x96,file_get_contents($k['thumbnails']['144x96']));
                                    $sql_fields  =  $sql_fields.", `THUMB_144x96`";
                                    $sql_values = $sql_values.", '".$thumb_144x96."'";
                                }
                                if(isset($k['source']['link'])) {
                                    $sql_fields  =  $sql_fields.", `SOURCE_LINK`";
                                    $sql_values = $sql_values.", '".$k['source']['link']."'";
                                }
                                if(isset($k['source']['name'])) {
                                    $sql_fields  =  $sql_fields.", `SOURCE_NAME`";
                                    $sql_values = $sql_values.", '".$k['source']['name']."'";
                                }
                                echo $v.PHP_EOL;
                                $sql1 = "INSERT INTO `LIST_PLACES_IMG` (".$sql_fields.") VALUES (".$sql_values.")";
                                $result1 = $db->prepare($sql1);
                                $result1->execute();
                            }
                            else echo "img isset";
                        }
                    }
                    // поиск идентификаторов категорий
                    if (isset($arr['categories']) AND $arr['categories'] > 0) {
                        $id_categories = '';
                        foreach ($arr['categories'] as $k => $v) {
                            $result = $db->query("SELECT ID FROM `LIST_CATEGORY` WHERE SLUG = '{$v}'");
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            $id_categories .= $row['ID'].",";
                        }
                        $id_categories = substr($id_categories, 0, -1);
                    }
                    // поиск и добавление тегов
                    if (isset($arr['tags']) AND $arr['tags'] > 0) {
                        $id_tags = '';
                        foreach ($arr['tags'] as $k => $v) {
                            $result = $db->query("SELECT ID FROM `LIST_TAGS` WHERE NAME = '{$v}'");
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            if($row) {
                                $id_tags .= $row['ID'] . ",";
                            }
                            else {
                                $sql = 'INSERT INTO `LIST_TAGS` '
                                    . '(`NAME`) VALUES (:NAME)';
                                try {
                                    $result = $db->prepare($sql);
                                    $result->bindParam(':NAME', $v, PDO::PARAM_STR);
                                    $result->execute();
                                    $id_tags .= $db->lastInsertId() . ",";
                                }
                                catch (PDOException $e) {
                                    die($e);
                                }
                            }
                        }
                        $id_tags = substr($id_tags, 0, -1);
                    }
                    //поиск локации
                    if (isset($arr['location'])) {
                            $result = $db->query("SELECT ID FROM `LIST_CITIES` WHERE SLUG = '{$arr['location']}'");
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            if($row) {
                                $id_location = $row['ID'];
                            }
                        }
                    $sql = 'INSERT INTO `LIST_PLACES` '
                        . '(`ID`, `TITLE`, `SLUG`, `ADDRESS`, `TIMETABLE`, `PHONE`, `IS_STUB`, '
                        . '`BODY_TEXT`, `DESCRIPTION`, `SITE_URL`, `FOREIGN_URL`, `LAT`, `LON`, '
                        . '`SUBWAY`, `FAVORITES_COUNT`, `COMMENTS_COUNT`, `IS_CLOSED`, `ID_CATEGORIES` , '
                        . '`SHORT_TITLE`, `ID_LIST_TAGS`, `ID_CITIES`, `AGE_RESTRICTION`, `DISABLE_COMMENTS`, '
                        . '`HAS_PARKING_LOT`) VALUES (:ID, :TITLE, :SLUG, :ADDRESS, :TIMETABLE, :PHONE, :IS_STUB, '
                        . ':BODY_TEXT, :DESCRIPTION, :SITE_URL, :FOREIGN_URL, :LAT, :LON, '
                        . ':SUBWAY, :FAVORITES_COUNT, :COMMENTS_COUNT, :IS_CLOSED, :ID_CATEGOTIES , '
                        . ':SHORT_TITLE, :ID_LIST_TAGS, :ID_CITIES, :AGE_RESTRICTION, :DISABLE_COMMENTS, '
                        . ':HAS_PARKING_LOT)';
                   // echo $sql;
                    $result = $db->prepare($sql);
                    $result->bindParam(':ID', $arr['id'], PDO::PARAM_STR);
                    $result->bindParam(':TITLE', $arr['title'], PDO::PARAM_STR);
                    $result->bindParam(':SLUG', $arr['slug'], PDO::PARAM_STR);
                    $result->bindParam(':ADDRESS', $arr['address'], PDO::PARAM_STR);
                    $result->bindParam(':TIMETABLE', $arr['timetable'], PDO::PARAM_STR);
                    $result->bindParam(':PHONE', $arr['phone'], PDO::PARAM_STR);
                    $result->bindParam(':IS_STUB', $arr['is_stub'], PDO::PARAM_STR);
                    $result->bindParam(':BODY_TEXT', $arr['body_text'], PDO::PARAM_STR);
                    $result->bindParam(':DESCRIPTION', $arr['description'], PDO::PARAM_STR);
                    $result->bindParam(':SITE_URL', $arr['site_url'], PDO::PARAM_STR);
                    $result->bindParam(':FOREIGN_URL', $arr['foreign_url'], PDO::PARAM_STR);
                    $result->bindParam(':LAT', $arr['lat'], PDO::PARAM_STR);
                    $result->bindParam(':LON', $arr['lon'], PDO::PARAM_STR);
                    $result->bindParam(':SUBWAY', $arr['subway'], PDO::PARAM_STR);
                    $result->bindParam(':FAVORITES_COUNT', $arr['favorites_count'], PDO::PARAM_STR);
                    $result->bindParam(':COMMENTS_COUNT', $arr['comments_count'], PDO::PARAM_STR);
                    $result->bindParam(':IS_CLOSED', $arr['is_closed'], PDO::PARAM_STR);
                    $result->bindParam(':ID_CATEGOTIES', $id_categories, PDO::PARAM_STR);
                    $result->bindParam(':SHORT_TITLE', $arr['short_title'], PDO::PARAM_STR);
                    $result->bindParam(':ID_LIST_TAGS', $id_tags, PDO::PARAM_STR);
                    $result->bindParam(':ID_CITIES', $id_location, PDO::PARAM_STR);
                    $result->bindParam(':AGE_RESTRICTION', $arr['age_restriction'], PDO::PARAM_STR);
                    $result->bindParam(':DISABLE_COMMENTS', $arr['disable_comments'], PDO::PARAM_STR);
                    $result->bindParam(':HAS_PARKING_LOT', $arr['has_parking_lot'], PDO::PARAM_STR);
                    $result->execute();
                }

            //echo "Найдено событий: ".count($arr)." | Событий в базе: ".$s1." | Добавлено: ".$s2.PHP_EOL;
        }
    }

    function getPlaceFromRadiys($lat, $lon)
    {
        $db = Db::getConnection();
        //  (х-а)²+(у-b)²=r²
        // $s = ($lat)
        $radius = (0.150 *0.1988)/2;
        $result = $db->query("SELECT * FROM LIST_PLACES WHERE lat BETWEEN ({$lat} - {$radius}) AND ({$lat} + {$radius}) AND lon BETWEEN ({$lon} - {$radius}) AND ({$lon} + {$radius})");
        $i=0;
        $datajson = array(
            'type' => 'FeatureCollection',
            'features' => array()
        );
        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
            // {"type": "Feature",
            // "id": 0,
            // "geometry": {"type": "Point", "coordinates": [55.831903, 37.411961]},
            // "properties": {"balloonContent": "Содержимое балуна", "clusterCaption": "Метка с iconContent", "hintContent": "Текст подсказки", "iconContent": "1"},
            // "options": {"iconColor": "#ff0000", "preset": "islands#blueCircleIcon"}},
            $datajson['features'][] = array(
                'type' => 'Feature',
                'id' => $i ,
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => [$row['LAT'],$row['LON']]
                ),
                'properties' => array(
                    'balloonContent' => "{$row['SHORT_TITLE']}",
                    'clusterCaption' => "{$row['TIMETABLE']}",
                    'hintContent' => "{$row['ADDRESS']}",
                    'iconContent' => "{$row['SLUG']}"
                ),
                'options' => array(
                    'iconColor' => '#ff0000',
                    'preset' => 'islands#blueCircleIcon'
                )
            );
            //echo $row['ADDRESS'].PHP_EOL;
            $i++;
        }
        echo  $jsondata = json_encode($datajson);
        /*echo "<pre>";
        print_r();
        echo "</pre>";*/
        require_once ("view/map.php");
    }

}