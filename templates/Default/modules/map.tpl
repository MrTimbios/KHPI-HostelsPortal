<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
<script>
    //  Яндекс карта
    var myMap;

    // Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);

    function init () {
        myMap = new ymaps.Map('map', {
            center: [55.99803,92.898377],
            zoom: 17,
            controls: ['zoomControl', 'typeSelector',  'fullscreenControl']
        });
        var myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
            balloonContentBody: [
                '<address>',
                '<strong>ООО "Софтньюс Медиа Групп"</strong>',
                '<br/>',
                'Адрес: 660093 г. Красноярск, ул. Капитанская, дом 12, офис 43',
                '</address>'
            ].join('')
        }, {
            preset: 'islands#darkGreenDotIcon'
        });
        myMap.geoObjects.add(myPlacemark);
        myMap.behaviors.disable('scrollZoom');
    } 
</script>
<div class="map_resp">
    <div id="map"></div>
</div>