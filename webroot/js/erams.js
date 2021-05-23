function EramsAlart(mess) {
    if ( window.confirm(mess) ) {
	return true;
    }
    return false;
}

function EramsForbidden() {
    history.pushState(null, null, null);
    window.addEventListener("popstate", function () {
	history.pushState(null, null, null);
    });
}

function EramsDate() {
    let dt = (new Date());
    let Y = dt.getFullYear();
    let M = ("0" + (dt.getMonth() + 1)).slice(-2);
    let D = ("0" + dt.getDate()).slice(-2);
    let h = ("0" + dt.getHours()).slice(-2);
    let m = ("0" + dt.getMinutes()).slice(-2);
    let s = ("0" + dt.getSeconds()).slice(-2);
    let ds = "" + Y + "-" + M + "-" + D + " " + h + ":" + m + ":" + s;
    return ds;
}

function EramsOnLoad() {
    let ds = EramsDate();
    let form = document.forms["testing"];
    form.stime.value = ds;
    return true;
}

function EramsEndTime() {
    let ds = EramsDate();
    let form = document.forms["testing"];
    form.etime.value = ds;
    return true;
}

function EramsAtEndOfAsking() {
    let flag = false;
    let form = document.forms["testing"];
    let c = 0;
    //　ラジオボタンの数だけ判定を繰り返す
    for(let i = 0 ; i < form.answer.length ; i++ ) {
	if ( form.answer[i].checked ){
	    flag = true;
	    break;
	}
    }
    // 何も選択されていない場合の処理
    if( ! flag ) {
	alert("解答を選択してください。");
	return false;
    }
    let ds = EramsDate();
    form.etime.value = ds;
    return true;    
}

function EramsTimeout() {
    //alert('時間が来たよ');
    let form = document.forms["testing"];
    form.next.click();
}
