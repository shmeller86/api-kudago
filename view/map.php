<!DOCTYPE html>
<html>
<head>
    <title>Примеры. Добавление в objectManager меток с разнообразными опциями и данными</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Если вы используете API локально, то в URL ресурса необходимо указывать протокол в стандартном виде (http://...)-->
    <script src="//api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
    <script src="//yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
    <!-- Обратите внимание, что для добавления на карту меток, содержащих глиф-иконки, нужно подключить Bootstrap-->
    <link rel="stylesheet" type="text/css" href="https://yastatic.net/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="view/object_manager.js" type="text/javascript"></script>
    <style>
        html, body, #map {
            width: 100%; height: 100%; padding: 0; margin: 0;
        }
    </style>
</head>
<body>
<div id="map"></div>
</body>
</html>
