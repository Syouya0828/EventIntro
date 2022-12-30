//console.log("読み込まれてるよ");
function getData(userName){
    
    let data = {
        "select": document.getElementById("displaySetting").value,
        "userName": userName
    }
    //console.log(data.select);
    let json = JSON.stringify(data);
    let req = new XMLHttpRequest();
    req.open("POST", "getDisplayCont.php");
    req.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=UTF-8");
    req.send(json);

    req.onload = function(){
            if(req.readyState === 4 && req.status === 200){
                //console.log(req.responseText);
                console.log(req.response);
                let result = JSON.parse(req.response);
                console.log(result);
                $("#result").children().remove();
                $("#result").append("<p>"+ result.value +"<p>");
            }else{
                console.log("エラー");
            }
    }
}