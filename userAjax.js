//console.log("読み込まれてるよ");
function getData(){
    let userID = document.getElementById('userID').value;

    let data = {
        "select": document.getElementById("displaySetting").value,
        "userID": userID
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
                $("#events").empty();
                for(let i in result){
                    console.log(i, result[i]["eventdate"]);
                    // console.log(result[]["eventdate"]);
                    $("#events").append("<div class='event-"+ i +"'></div><br>");
                    $(".event-"+i).append("<div class='event-body'></div>");
                    $(".event-"+i+" div").append("<a class='eventpage' href='/eventintro/view.php?commentPage=1&id="+result[i]["id"]+"'>"+result[i]["eventname"]+"</a>");
                    $(".event-"+i+" div").append("<p class='date'>開催日時: "+ result[i]["eventdate"].slice(0, -3) +"<p>");

        
                }
                $("#result").append("<p>"+ result.value +"<p>");
            }else{
                console.log("エラー");
            }
    }
}