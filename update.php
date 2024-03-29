<?php
session_start();

require("dbc.php");
require_once "vendor/autoload.php";
require_once "headerLogin.php";
if(isset($_SESSION["userID"]) && isset($_GET['id'])){
    $userID = $_SESSION["userID"];
    $id = $_GET['id'];
}else{
    header("Location:Error.php");
}



Dotenv\Dotenv::createImmutable(__DIR__)->load();

$key = $_ENV['key'];

function getEvent($id){
    try {
        $dbh = dbConnect();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $sql = "SELECT * FROM events WHERE id=:id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id',$id, PDO::PARAM_STR);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        $dbh = NULL;
        //var_dump($event);
        return($event);
    } catch (\Throwable $th) {
        //throw $th;
    }
}

$event = getEvent($id);
$dateTime = explode(' ', $event["eventdate"]);
$time = substr($dateTime[1], 0, -3);

if($event["userid"] != $userID){
    //header("Location:Error.php");
}
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
    <form id="update-form" data-persist="garlic" class="validationForm" action="updateEvent.php" method="POST">
		<input type="hidden" value="<?=$event["id"]?>"name="id">  
		<h1>イベントを作成</h1>

      <p class="holder"><span>イベント名</span></p>
      <input id="title"name="title" type="text" size="50" placeholder="例： ○○募集 ××開催" value="<?=$event["eventname"]?>">

      <p class="holder"><span>イベント内容</span></p>
      <textarea id="content" name="content" cols="50" rows="4"><?=$event["eventdetail"]?></textarea>

      <p class="holder"><span>場所</span></p>

      <div style="text-align:center;">
        <div id="map"></div>
      </div>
      
        <input type="text" name="place" id="address" value="<?=$event['place']?>" placeholder="場所名か住所を入力/ピンの場所で保存されます"><br>
        <!-- このボタンを押したら入力確認(nullだったら無視)⇒住所か場所の名前を検索 -->
        <button type="button" id="getAddress">ピンの住所を取得</button>
        <button type="button" id="search">住所から検索</button>
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">

      <p><span class="holder">日時</span></p>
      <input id="date" name="date" type="date" value="<?=$dateTime[0]?>"/>
      <div>
        <select name="time" id="time">
        <?php
            $selectTimes = array("00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00", "05:30", "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30");
                foreach ($selectTimes as $selectTime) {
                    if($time == $selectTime){?>
                    <option value="<?=$selectTime?>" selected><?=$selectTime?></option>
            <?php   }?>
                
                    <option value="<?=$selectTime?>"><?=$selectTime?></option>
            <?php }
            ?>
        </select>
      </div>
      <p><span class="holder">参加人数</span></p>
      <input id="capacity"name="capacity" type="number" value = "<?=$event["capacity"]?>" min="1" placeholder="半角数字を入力" /><br> 
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
	<form action="delete.php" method="post" id="delete-form">
		<input type="text" id="eventID" name="eventID" value="<?=$event["id"]?>">
	</form>
    <div class='btns'>
        <input type="button" id="updateBtn" value="更新する">
        <input type="button" id="deleteBtn" value="削除する" >
    </div>
    <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>


    <script>
        function generateMap(){
            //最初のMap生生成
			let firstLng = <?=$event["lng"]?>;
			let firstLat = <?=$event["lat"]?>;
            let FirstLatLng = new google.maps.LatLng(firstLat, firstLng);
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
		const deleteBtn = document.querySelector('#deleteBtn');
        const updateBtn = document.querySelector('#updateBtn');
		deleteBtn.addEventListener('click', ()=> {
			var result = window.confirm("削除しますか？");
            if(result){
				// console.log("delete");
                const form = document.getElementById('delete-form');
                form.submit();
            }
		},false);


        updateBtn.addEventListener('click', ()=> {
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
                const form = document.getElementById('update-form');
                form.submit();
            }
        }, false);
    </script>
</body>
</html>