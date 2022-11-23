<?php
/*
    ・GoogleMapのピンを指した場所の座標を取得して⇒APIをたたいて座標から住所を出す
    ・住所から座標を出してGoogleMapにピンを指す
    ・buttonを押したら地図が表示される
    ・ピンを動かせるようにする
*/
require_once "vendor/autoload.php";

Dotenv\Dotenv::createImmutable(__DIR__)->load();

$key = $_ENV['key'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoogleMap API</title>

    <script src="http://maps.google.com/maps/api/js?key=<?=$key?>&language=ja"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script> 
    <link rel="stylesheet" href="css/GoogleMap.css">

</head>
<body>
    <div id="map"></div>
    <input type="text" id="address" placeholder="住所か地名を入力"><br>
    <!-- このボタンを押したら入力確認(nullだったら無視)⇒住所か場所の名前を検索 -->
    <button id="getAddress">ピンの住所を取得</button>
    <button id="search">住所から検索</button>
    <script>
        function generateMap(){
            //最初のMap生生成
            let FirstLatLng = new google.maps.LatLng(35.6809591, 139.7673068);
            let Options = {
                zoom: 15,
                center: FirstLatLng,
                mapTypeId: 'roadmap'
            };
            map = new google.maps.Map(mapElement, Options);
            marker = new google.maps.Marker({
                position: FirstLatLng,
                map: map,
                draggable:true
            })
            //geneMarker.setMap(geneMap);
        }
        let inputAddress,
            marker,
            map,
            latlng;
        let geocoder = new google.maps.Geocoder();
        const mapElement = document.getElementById('map');

        generateMap();

        $("#getAddress").click(function(){
            let pos = marker.getPosition();
            // console.log(position);
            // console.log(position.lat());
            // console.log(position.lng());
            latlng = {
                lat: parseFloat(pos.lat()),
                lng: parseFloat(pos.lng()),
            };

            geocoder.geocode({location: latlng})
            .then((response) => {
                if(response.results[0]){
                    let markerAddress = response.results[0].formatted_address.split(' ');
                    //console.log(markerAddress[1]);
                    $('#address').val(markerAddress[1]);
                }else{
                    window.alert("正しい情報を入力してください");
                }
            }).catch((e) => window.alert(e));
        });
        $("#search").click(function(){
            inputAddress = $("#address").val();
            //$("#address").val("");
            //console.log(inputAddress);//これをgeocodingAPIに渡す

            geocoder.geocode( { address: inputAddress}, (results, status) => {
                if(status == "OK" && results[0]){
                    //console.log(results[0].geometry.location);
                    map = new google.maps.Map(mapElement, {
                        center: results[0].geometry.location,
                        zoom: 15
                    });
                    marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        draggable:true
                    });
                }else{
                    alert("mapの読み込みに失敗しました\n正しい情報を入力してください")
                }

            })
        });
    </script>
</body>
</html>