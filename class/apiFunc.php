<?php

class apiFunc
{
    public $ARRAY = array(
        'event_category' => array(
            'name' => 'event-categories'
        ),
        'place_category' => array(
            'name' => 'place-categories'
        )

    );

    function updateEventCategory(){
        $db = Db::getConnection();
        $url = URL."/".VERSION."/".$this->ARRAY['event_category']['name'];
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        if(count($arr)>0){
            $s1 = 0; $s2 = 0;
            foreach ($arr as $k=>$v){
                $result = $db->query("SELECT ID FROM `LIST_EVENT_CATEGORY` WHERE NAME_ENG = '{$v['slug']}' AND NAME_RU = '{$v['name']}'");
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if($row) {

                    $s1++;
                }
                else {
                    $sql = 'INSERT INTO `LIST_EVENT_CATEGORY` '
                        . '(NAME_ENG,NAME_RU) VALUES (:ENG, :RU)';
                    $result = $db->prepare($sql);
                    $result->bindParam(':ENG', $v['slug'], PDO::PARAM_STR);
                    $result->bindParam(':RU', $v['name'], PDO::PARAM_STR);
                    $result->execute();
                    $s2++;
                }
            }
            echo "Найдено событий: ".count($arr)." | Событий в базе: ".$s1." | Добавлено: ".$s2.PHP_EOL;
        }
    }

    function updatePlaceCategory(){
        $db = Db::getConnection();
        $url = URL."/".VERSION."/".$this->ARRAY['place_category']['name'];
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        if(count($arr)>0){
            $s1 = 0; $s2 = 0;
            foreach ($arr as $k=>$v){
                $result = $db->query("SELECT ID FROM `LIST_PLACE_CATEGORY` WHERE NAME_ENG = '{$v['slug']}' AND NAME_RU = '{$v['name']}'");
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if($row) {
                    $s1++;
                }
                else {
                    $s2++;
                    $sql = 'INSERT INTO `LIST_PLACE_CATEGORY` '
                        . '(NAME_ENG,NAME_RU) VALUES (:ENG, :RU)';
                    $result = $db->prepare($sql);
                    $result->bindParam(':ENG', $v['slug'], PDO::PARAM_STR);
                    $result->bindParam(':RU', $v['name'], PDO::PARAM_STR);
                    $result->execute();
                }
            }
            echo "Найдено мест: ".count($arr)." | Мест в базе: ".$s1." | Добавлено: ".$s2.PHP_EOL;
        }
    }


}