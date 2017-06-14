ymaps.ready(init);

function init () {
    var myMap = new ymaps.Map('map', {
            center: [59.911556,30.267808],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        }),
        objectManager = new ymaps.ObjectManager();

    myMap.geoObjects.add(objectManager);

    $.ajax({
        // В файле data.json заданы геометрия, опции и данные меток .
        url: "view/data.json"
    }).done(function(data) {
        objectManager.add(data);
    });

}