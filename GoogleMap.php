<?php
session_start();
require("headerLogin.php");
if(isset($_SESSION["userID"]) ){
  $userName = $_SESSION["userName"];
  $userID = $_SESSION["userID"];
}else{
  header("Location:Error.php");
}
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
    <link href="./css/event_createForm.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><img src="logo/logo.png"></a>
        </div>
        <nav>
            <ul class="nav_header">
                <li>
                    <a href="event_createForm.php">募集</a>
                </li>
                <li>
                    <a href="search.php">検索</a>
                </li>
                <?=$log?>
            </ul>
        </nav>
    </header>
    <form id="post-form" data-persist="garlic" class="validationForm" action="event_create.php" method="POST">
      <h1>イベントを作成</h1>

      <p class="holder"><span>イベント名</span></p>
      <input id="title"name="title" type="text" size="50" placeholder="例： ○○募集 ××開催">

      <p class="holder"><span>イベント内容</span></p>
      <textarea id="content" name="content" cols="50" rows="4"></textarea>

      <p class="holder"><span>場所</span></p>

      <div style="text-align:center;">
        <div id="map"></div>
      </div>
      
        <input type="text" name="place" id="address" placeholder="場所名か住所を入力"><br>
        <!-- このボタンを押したら入力確認(nullだったら無視)⇒住所か場所の名前を検索 -->
        <button type="button" id="getAddress">ピンの住所を取得</button>
        <button type="button" id="search">住所から検索</button>
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">

      <p><span class="holder">日時</span></p>
      <input id="date" name="date" type="date" />
      <div>
        <select name="time" id="time">
          <option value="00:00">00:00</option><option value="00:30">00:30</option><option value="01:00">01:00</option><option value="01:30">01:30</option><option value="02:00">02:00</option><option value="02:30">02:30</option><option value="03:00">03:00</option><option value="03:30">03:30</option><option value="04:00">04:00</option><option value="04:30">04:30</option><option value="05:00">05:00</option><option value="05:30">05:30</option><option value="06:00">06:00</option><option value="06:30">06:30</option><option value="07:00">07:00</option><option value="07:30">07:30</option><option value="08:00">08:00</option><option value="08:30">08:30</option><option value="09:00">09:00</option><option value="09:30">09:30</option><option value="10:00">10:00</option><option value="10:30">10:30</option><option value="11:00">11:00</option><option value="11:30">11:30</option><option value="12:00">12:00</option><option value="12:30">12:30</option><option value="13:00">13:00</option><option value="13:30">13:30</option><option value="14:00">14:00</option><option value="14:30">14:30</option><option value="15:00">15:00</option><option value="15:30">15:30</option><option value="16:00">16:00</option><option value="16:30">16:30</option><option value="17:00">17:00</option><option value="17:30">17:30</option><option value="18:00">18:00</option><option value="18:30">18:30</option><option value="19:00">19:00</option><option value="19:30">19:30</option><option value="20:00">20:00</option><option value="20:30">20:30</option><option value="21:00">21:00</option><option value="21:30">21:30</option><option value="22:00">22:00</option><option value="22:30">22:30</option><option value="23:00">23:00</option><option value="23:30">23:30</option>
        </select>
      </div>
      <p><span class="holder">参加人数</span></p>
      <input id="capacity"name="capacity" type="number" min="1" placeholder="半角数字を入力" /><br> 
      <!-- <p for="keywords"><span>キーワード</span></p>
      <input id="keywords" name="keywords" class="keywords" type="text" /><br /> -->
      <div id='alert'>
            <p id='emptyTitle' class="hide">イベント名を入力してください</p>
            <p id='emptyContent' class="hide">詳細を入力してください</p>
            <p id='emptyAddress' class="hide">住所を入力してください</p>
            <p id='emptyDate' class="hide">日にちを入力してください</p>
            <p id='emptyCapacity' class="hide">人数を入力してください</p>
        </div>
        <button type="button" id='postBtn'>投稿</button>
    </form>


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

        const post = document.querySelector('#postBtn');
        post.addEventListener('click', ()=> {
            var title = document.getElementById('title').value;
            var content = document.getElementById('content').value;
            var address = document.getElementById('address').value;
            var date = document.getElementById('date').value;
            var capacity = document.getElementById('capacity').value;
            let pos = marker.getPosition();
            let lat = parseFloat(pos.lat());
            let lng = parseFloat(pos.lng());
            
            // console.log(lat + " " + lng);
            //console.log(title + " " + content + " " + address + " " + date + " " + capacity);
            let hideElement = document.querySelector('.show');
            //console.log(hideElement);
            if(hideElement !== null){
                hideElement.className = 'hide';
                //console.log("a");
            }
            if(title==""){
                let element = document.querySelector('#emptyTitle');
                element.className = 'show';
                return;
            }
            if(content==""){
                let element = document.querySelector('#emptyContent');
                element.className = 'show';
                return;
            }
            if(address == ""){
                let element = document.querySelector('#emptyAddress');
                element.className = 'show';
                return;
            }
            if(date == ""){
                let element = document.querySelector('#emptyDate');
                element.className = 'show';
                return;
            }
            if(capacity == ""){
                let element = document.querySelector('#emptyCapacity');
                element.className = 'show';
                return;
            }
            var result = window.confirm("投稿しますか？");
            if(result){
                $("#lat").val(lat);
                $("#lng").val(lng);
                const form = document.getElementById('post-form');
                form.submit();
            }
        }, false);
    </script>
</body>
</html>