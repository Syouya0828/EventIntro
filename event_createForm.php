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
      
        <input type="text" name="place" id="address" placeholder="場所名か住所を入力/ピンの場所で保存されます"><br>
        <!-- このボタンを押したら入力確認(nullだったら無視)⇒住所か場所の名前を検索 -->
        <button type="button" id="getAddress">ピンの住所を取得</button>
        <button type="button" id="search">住所から検索</button>
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">

      <p><span class="holder">日時</span></p>
      <input id="date" name="date" type="date" />
      <div>

        <select name="time" id="time">
            <?php
            $times = array("00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00", "05:30", "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30");
                foreach ($times as $time) {?>
                    <option value="<?=$time?>"><?=$time?></option>
            <?php }
            ?>
          <!-- ">01:0001:30">01:3002:00">02:0002:30">02:3003:00">03:0003:30">03:3004:00">04:0004:30">04:3005:00">05:0005:30">05:3006:00">06:0006:30">06:3007:00">07:0007:30">07:3008:00">08:0008:30">08:3009:00">09:0009:30">09:3010:00">10:0010:30">10:3011:00">11:0011:30">11:3012:00">12:0012:30">12:3013:00">13:0013:30">13:3014:00">14:0014:30">14:3015:00">15:0015:30">15:3016:00">16:0016:30">16:3017:00">17:0017:30">17:3018:00">18:0018:30">18:3019:00">19:0019:30">19:3020:00">20:0020:30">20:3021:00">21:0021:30">21:3022:00">22:0022:30">22:3023:00">23:0023:30">23:30 -->
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

    </form>
    <div class="center">
      <input id="postBtn" type="submit" value="投稿">
    </div>

    <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>

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