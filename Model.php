<?php
require("dbconfig.php");
function DelPic($iID){
	global $db;
    $sql2 = "select 2DimgLink from item where `item`.`iID` = ?;"; 
    $stmt2 = mysqli_prepare($db, $sql2); //prepare sql statement
    mysqli_stmt_bind_param($stmt2, "i",$iID); //bind parameters with variables(將變數bind到sql指令的問號中)
    mysqli_stmt_execute($stmt2);
    $result = mysqli_stmt_get_result($stmt2);
    if($rs = mysqli_fetch_assoc($result)){ 
        $imgLink = $rs["2DimgLink"];
        $d=substr($imgLink, 33,7);
        $file=substr($imgLink, 41);
        $fileName=substr($imgLink, 33);
        if($od=opendir($d)){ //$d是目錄名
            while(($f=readdir($od))!==false){ //讀取目錄內檔案
                if($f===$file)
                    unlink($fileName);
                else
                    echo"資料夾內沒有此檔案";                           
            }
        }           
    }
}

function getMyExhibition($usr){//顯示展覽//not yet
	global $db;
	$sql = "SELECT * FROM `exhibition` 
          WHERE exhibition.`creatorID`=?"; 
	$stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
    mysqli_stmt_bind_param($stmt, "i", $usr);
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array(); //用一個array存下面的每一筆資料(一筆資料也是一個array)
	while($rs = mysqli_fetch_assoc($result)){
		$tArr=array(); //一維陣列存下面個欄位變數
		$tArr['eID']=$rs['eID'];
		$tArr['name']=$rs['name'];
		$tArr['frontPicture']=$rs['frontPicture'];
		$tArr['eIntro']=$rs['eIntro'];
		$tArr['firstScene']=$rs['firstScene'];
		$tArr['mapImg']=$rs['mapImg'];
        $tArr['permission']=$rs['permission'];
		$retArr[] = $tArr;
	}
	return $retArr;//最後是回傳一個二維陣列
}

function EditItem($iID,$name, $intro, $img2D, $object3D, $status, $usr) {
    global $db;
    $sql = "select * from item where ownerID = ? AND name=? AND iID !=?;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "isi", $usr,$name,$iID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);  //將執行完的結果放到$result裏
    if($rs = mysqli_fetch_assoc($result)){ //檢查有無重複
        return false;
    }else{//看有沒有抓到result那張select出來的表 
        $sql2 = "update `item` set `name` =?,`intro`=?,`2DimgLink`=?,`3DobjectLink`=?,`status`=?  where iID=?;"; //sql指令的insert語法
        $stmt2 = mysqli_prepare($db, $sql2); //prepare sql statement
        mysqli_stmt_bind_param($stmt2, "sssssi",$name, $intro,$img2D,$object3D,$status,$iID); //bind parameters with variables(將變數bind到sql指令的問號中)
        mysqli_stmt_execute($stmt2);  //執行SQL
        return true;
    }
}

function ShowItem($iID){//修改顯示
	global $db;
	$sql = "select * from item where iID = ?;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"i",$iID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
    $tArr=array(); 
	while($rs = mysqli_fetch_assoc($result)){
		$tArr['iID']=$rs['iID'];
		$tArr['name']=$rs['name'];
		$tArr['object3D']=$rs['3DobjectLink'];
		$tArr['intro']=$rs['intro'];
		$tArr['status']=$rs['status'];
		$tArr['img2D']=$rs['2DimgLink'];
        $tArr['ownerID']=$rs['ownerID'];
         $tArr['tag']=$rs['tag'];
	}
	return $tArr;
}

//加入新展品
function AddItem($name, $intro, $img2D, $object3D, $status,$usr,$tag) {
    global $db;
    $sql = "select name from item where ownerID = ? AND name=?;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "is", $usr,$name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);  //將執行完的結果放到$result裏
    if($rs = mysqli_fetch_assoc($result)){ //檢查有無重複
        return false;
    }else{//看有沒有抓到result那張select出來的表 
        $sql2 = "insert into item (name, 3DobjectLink, 2DimgLink, intro, ownerID , status,tag) values (?, ?, ?, ?, ?, ?,?)"; //sql指令的insert語法
        $stmt2 = mysqli_prepare($db, $sql2); //prepare sql statement
        mysqli_stmt_bind_param($stmt2, "ssssiss", $name, $object3D , $img2D, $intro, $usr, $status,$tag); //bind parameters with variables(將變數bind到sql指令的問號中)
        mysqli_stmt_execute($stmt2);  //執行SQL
        return true;
    }
}

function DeleteItem($iID){
	global $db;
    $sql2 = "select 2DimgLink from item where `item`.`iID` = ?;"; 
    $stmt2 = mysqli_prepare($db, $sql2); //prepare sql statement
    mysqli_stmt_bind_param($stmt2, "i",$iID); //bind parameters with variables(將變數bind到sql指令的問號中)
    mysqli_stmt_execute($stmt2);
    $result = mysqli_stmt_get_result($stmt2);
    if($rs = mysqli_fetch_assoc($result)){ 
        $imgLink = $rs["2DimgLink"];
        $d=substr($imgLink, 33,7);
        $file=substr($imgLink, 41);
        $fileName=substr($imgLink, 33);
        if($od=opendir($d)){ //$d是目錄名
            while(($f=readdir($od))!==false){ //讀取目錄內檔案
                if($f===$file)
                    unlink($fileName);
                else
                    echo"資料夾內沒有此檔案";                           
            }
        }           
    }
      
	$sql = "delete from `item` where `item`.`iID` = ? ;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"i",$iID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
    echo "delete done !!!";
}

function getItem($usr){//顯示展品
	global $db;
	$sql = "SELECT * FROM item 
          WHERE item.`ownerID`=?"; 
	$stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
    mysqli_stmt_bind_param($stmt, "i", $usr);
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array(); //用一個array存下面的每一筆資料(一筆資料也是一個array)
	while($rs = mysqli_fetch_assoc($result)){
		$tArr=array(); //一維陣列存下面個欄位變數
		$tArr['iID']=$rs['iID'];
		$tArr['name']=$rs['name'];
		$tArr['object3D']=$rs['3DobjectLink'];
		$tArr['intro']=$rs['intro'];
		$tArr['status']=$rs['status'];
		$tArr['img2D']=$rs['2DimgLink'];
        $tArr['ownerID']=$rs['ownerID'];
        $tArr['tag']=$rs['tag'];
		$retArr[] = $tArr;
	}
	return $retArr;//最後是回傳一個二維陣列
}

function getUsername($userID){
    global $db;
    $sql = "select * from user where id = ? ;"; 
    $stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
    mysqli_stmt_bind_param($stmt,"i",$userID);//將使用者輸入的password，用字串的形式，去bind到$sql指令的?
    mysqli_stmt_execute($stmt);//執行一個sql指令
    $result = mysqli_stmt_get_result($stmt);
    if($rs = mysqli_fetch_assoc($result)){ 
        $ret = $rs["last_name"];
    }
    return $ret;
}
//加入新使用者
function addMember($first_name,$last_name,$pwd,$email,$gender,$intro) {
    $pwdHash=password_hash($pwd, PASSWORD_DEFAULT); //將密碼hash
    global $db;
    $sql = "select email from user where email = ? ;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);  //將執行完的結果放到$result裏
    if($rs = mysqli_fetch_assoc($result)){ //檢查用戶的電子郵件有無重複
        return false;
    }else{//看有沒有抓到result那張select出來的表 
        $sql2 = "insert into user (first_name, last_name, email, password, gender , intro) values (?, ?, ?, ?, ?, ?)"; //sql指令的insert語法
        $stmt2 = mysqli_prepare($db, $sql2); //prepare sql statement
        mysqli_stmt_bind_param($stmt2, "ssssss", $first_name, $last_name , $email, $pwdHash, $gender, $intro); //bind parameters with variables(將變數bind到sql指令的問號中)
        mysqli_stmt_execute($stmt2);  //執行SQL
        return true;
    }
}
function loginCheck($email,$pwd){
    global $db;
    $sql = "select * from user where email = ? ;"; 
    //先寫一個sql指令，將使用者輸入的值?，用PASSWORD加密過，在跟password欄位比較是否相同
    //盡量用statement物件($stat)會比較安全
    $stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
    mysqli_stmt_bind_param($stmt,"s",$email);//將使用者輸入的password，用字串的形式，去bind到$sql指令的?
    mysqli_stmt_execute($stmt);//執行一個sql指令
    $result = mysqli_stmt_get_result($stmt);  //將執行完的結果放到$result裏
    while($rs = mysqli_fetch_assoc($result)){ //看有沒有抓到result那張select出來的表 
        $correct_pwd = $rs['password'];//將密碼取出
        if(password_verify($pwd,$correct_pwd)){ //之後再比較相同用戶名欄位
            $_SESSION["userID"] = $rs['id'];
            return true;
        }else{
            $_SESSION["userID"] = '';
        }
    }  
    return false;
}
function getExhibitionList(){//顯示所有公開展覽
	global $db;
	$sql = "select * from `exhibition` INNER JOIN `user` ON `exhibition`.`creatorID` = `user`.`id` where `permission` = 'public' AND NOW() < `closeTime`;"; //AND NOW() > `startTime` 
	$stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array(); //用一個array存下面的每一筆資料(一筆資料也是一個array)
	while($rs = mysqli_fetch_assoc($result)){
		$tArr=array(); //一維陣列存下面個欄位變數
		$tArr['eID']=$rs['eID'];
		$tArr['name']=$rs['name'];
		$tArr['creatorID']=$rs['creatorID'];
		$tArr['eIntro']=$rs['eIntro'];
		$tArr['frontPicture']=$rs['frontPicture'];
		$tArr['first_name']=$rs['first_name'];
		$tArr['last_name']=$rs['last_name'];
		$retArr[] = $tArr;
	}
	return $retArr;//最後是回傳一個二維陣列
}

function LoginExhibitionList($usr){//登入後顯示展覽
	global $db;
	$sql = "SELECT * FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id
		   LEFT JOIN subscribe ON subscribe.`creator`=exhibition.creatorID  
          WHERE NOW()<exhibition.closeTime
          AND (exhibition.permission='public' OR (exhibition.permission='subscribeOnly' AND  subscribe.`subscriber`=? AND subscribe.`status`='true' ))
          GROUP BY exhibition.eID
		  ORDER BY  case when subscribe.status is null then 1 else 0 end,subscribe.status;"; // NOW()>exhibition.startTime &&
	$stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
    mysqli_stmt_bind_param($stmt, "i", $usr);
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array(); //用一個array存下面的每一筆資料(一筆資料也是一個array)
	while($rs = mysqli_fetch_assoc($result)){
		$tArr=array(); //一維陣列存下面個欄位變數
		$tArr['eID']=$rs['eID'];
		$tArr['name']=$rs['name'];
		$tArr['creatorID']=$rs['creatorID'];
		$tArr['eIntro']=$rs['eIntro'];
		$tArr['frontPicture']=$rs['frontPicture'];
		$tArr['first_name']=$rs['first_name'];
		$tArr['last_name']=$rs['last_name'];
		$retArr[] = $tArr;
	}
	return $retArr;//最後是回傳一個二維陣列
}


function getExhibitionData($eID) {
    global $db;
	$sql = "select * from `exhibition` INNER JOIN `user` ON `exhibition`.`creatorID` = `user`.`id` where `exhibition`.`eID` = ? ;";
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"i",$eID);
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$tArr=array(); 
	while($rs = mysqli_fetch_assoc($result)){
		$tArr['eID']=$rs['eID'];
		$tArr['name']=$rs['name'];
		$tArr['creatorID']=$rs['creatorID'];
		$tArr['eIntro']=$rs['eIntro'];
		$tArr['frontPicture']=$rs['frontPicture'];
		$tArr['startTime']=$rs['startTime'];
		$tArr['closeTime']=$rs['closeTime'];
		$tArr['permission']=$rs['permission'];
		$tArr['first_name']=$rs['first_name'];
		$tArr['last_name']=$rs['last_name'];
	}
	return $tArr;
}
function getCuratorList(){//顯示公開策展人
	global $db;
	$sql = "SELECT * FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id GROUP BY user.id;"; //WHERE exhibition.permission='public' && NOW()>exhibition.startTime && NOW()<exhibition.closeTime 
	$stmt = mysqli_prepare($db, $sql);//$db是另一個程式生成的資料庫連線物件,  prepare:表示用這個資料庫($db)把sql指令compile好
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array(); //用一個array存下面的每一筆資料(一筆資料也是一個array)
	while($rs = mysqli_fetch_assoc($result)){
		$tArr=array(); //一維陣列存下面個欄位變數
		$tArr['id']=$rs['id'];
		$tArr['name']=$rs['name'];
		$tArr['creatorID']=$rs['creatorID'];
		$tArr['intro']=$rs['intro'];
		$tArr['photo']=$rs['photo'];
		$tArr['first_name']=$rs['first_name'];
		$tArr['last_name']=$rs['last_name'];
		$retArr[] = $tArr;
	}
	return $retArr;//最後是回傳一個二維陣列
}

function LoginCuratorList($usr) {//登入後顯示有訂閱的策展者
    global $db;   
    //訂閱的
    $sql="SELECT * FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id
		  LEFT JOIN subscribe ON subscribe.`creator`=exhibition.creatorID  
          WHERE subscribe.`subscriber`=? AND subscribe.`status`='true' 
          GROUP BY exhibition.creatorID";// NOW()>exhibition.startTime && NOW()<exhibition.closeTime AND 
	$stmt = mysqli_prepare($db, $sql); //prepare sql statement
	mysqli_stmt_bind_param($stmt, "i", $usr);  
    mysqli_stmt_execute($stmt); //執行SQL
	$result = mysqli_stmt_get_result($stmt);
	$rows = array();//宣告空陣列
	while($r = mysqli_fetch_assoc($result)) {//用欄位名稱當助標 抓出一筆       
        $temp=array();
        $temp['id']=$r['id'];
        $temp['intro']=$r['intro'];
        $temp['photo']=$r['photo'];
        $temp['last_name']=$r['last_name'];
        $temp['first_name']=$r['first_name'];  
        $rows[] = $temp;
	}  
    //除了訂閱的
    $sql_query="SELECT * FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id 
            where exhibition.creatorID not in 
               (SELECT exhibition.creatorID FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id
                LEFT JOIN subscribe ON subscribe.`creator`=exhibition.creatorID  
                WHERE subscribe.`subscriber`=? AND subscribe.`status`='true' GROUP BY exhibition.creatorID)
            GROUP BY exhibition.creatorID";// NOW()>exhibition.startTime && NOW()<exhibition.closeTime AND 
	$stmt = mysqli_prepare($db, $sql_query); //prepare sql statement
	mysqli_stmt_bind_param($stmt, "i", $usr);  
    mysqli_stmt_execute($stmt); //執行SQL
	$data = mysqli_stmt_get_result($stmt);
    while($r = mysqli_fetch_assoc($data)) {//用欄位名稱當助標 抓出一筆       
        $temp=array();
        $temp['id']=$r['id'];
        $temp['intro']=$r['intro'];
        $temp['photo']=$r['photo'];
        $temp['last_name']=$r['last_name'];
        $temp['first_name']=$r['first_name'];  
        $rows[] = $temp;
	}
    
return json_encode($rows);//json_encode轉成符合json的字串
}

/*function LoginCuratorList($usr) {//登入後顯示有訂閱的策展者
    global $db;   
    $sql="SELECT * FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id
		  LEFT JOIN subscribe ON subscribe.`creator`=exhibition.creatorID  
          WHERE(exhibition.permission='public' OR (exhibition.permission='subscribeOnly'
          AND  subscribe.`subscriber`=? AND subscribe.`status`='true' ))
          GROUP BY exhibition.creatorID
          ORDER BY  case when subscribe.status is null then 1 else 0 end,subscribe.status";// NOW()>exhibition.startTime && NOW()<exhibition.closeTime AND 
	$stmt = mysqli_prepare($db, $sql); //prepare sql statement
	mysqli_stmt_bind_param($stmt, "i", $usr);  
    mysqli_stmt_execute($stmt); //執行SQL
	$result = mysqli_stmt_get_result($stmt);
	$rows = array();//宣告空陣列
	while($r = mysqli_fetch_assoc($result)) {//用欄位名稱當助標 抓出一筆       
        $temp=array();
        $temp['id']=$r['id'];
        $temp['intro']=$r['intro'];
        $temp['photo']=$r['photo'];
        $temp['last_name']=$r['last_name'];
        $temp['first_name']=$r['first_name'];  
        $rows[] = $temp;
	}  
return json_encode($rows);//json_encode轉成符合json的字串
}*/

function getCuratorData($id) {
    global $db;
	$sql = "SELECT * FROM user WHERE id=? ;";
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"i",$id);
	mysqli_stmt_execute($stmt);//執行一個sql指令
	$result = mysqli_stmt_get_result($stmt);
	$tArr=array(); 
	while($rs = mysqli_fetch_assoc($result)){
		$tArr['id']=$rs['id'];
		$tArr['intro']=$rs['intro'];
		$tArr['photo']=$rs['photo'];
		$tArr['first_name']=$rs['first_name'];
		$tArr['last_name']=$rs['last_name'];
        
	}
	
	return $tArr;
}

function getExLikeCount($eID){
	global $db;
	$sql = "select count(*) as likeCount from `likes` where `likes`.`eID` = ? AND `status` = 'true' ;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"i",$eID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array();
	if($rs = mysqli_fetch_assoc($result)){
		$retArr["likeCount"] = $rs["likeCount"];
	}
	return $retArr;
}

function getLikeorNot($eID,$userID){ //確認有沒有按愛心
	global $db;
	$sql = "select * from `likes` where `likes`.`userID` = ? AND `likes`.`eID` = ? ;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"ii",$userID,$eID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array();
	if($rs = mysqli_fetch_assoc($result)){
		if($rs['status'] === 'true'){//表示你有按愛心
			$retArr['like'] = true;
		}else{//表示你沒有按愛心
			$retArr['like'] = false;
		}
	}else{//沒有抓到資料，表示使用者完全沒按過該展覽喜歡
		$retArr['like'] = false; //表示你沒有按愛心
	}
	return $retArr;
}
function AddLike($userID,$eID){
	global $db;
	$sql = "select * from `likes` where `likes`.`userID` = ? AND `likes`.`eID` = ? ;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"ii",$userID,$eID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
    if($rs = mysqli_fetch_assoc($result)){//如果有撈到資料，就將該資料改個狀態就好
		$sql2 = "update `likes` set `status` = 'true' where `likes`.`userID` = ? AND `likes`.`eID` = ? ;"; 
		$stmt2 = mysqli_prepare($db, $sql2);
		mysqli_stmt_bind_param($stmt2,"ii",$userID,$eID);
		mysqli_stmt_execute($stmt2);
		echo "update done !!!";
	}else{//如果撈不到資料，就自己新增一個
		$sql3 = "insert INTO `likes` (`userID`,`eID`,`status`) values (? , ? , true);"; 
		$stmt3 = mysqli_prepare($db, $sql3);
		mysqli_stmt_bind_param($stmt3,"ii",$userID,$eID);
		mysqli_stmt_execute($stmt3);
		echo "insert done !!!";
	}
}
function CancelLike($eID,$userID){
	global $db;
	$sql = "update `likes` set `status` = 'false' where `likes`.`userID` = ? AND `likes`.`eID` = ? ;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"ii",$userID,$eID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	echo "update done !!!";
}
function Search($n) {
    global $db;
    
    $sql="SELECT * FROM user INNER JOIN exhibition ON exhibition.creatorID=user.id WHERE exhibition.permission='public' && NOW()>exhibition.startTime && NOW()<exhibition.closeTime && (user.first_name LIKE ? OR user.last_name LIKE ?)";//'%".$n."%' 
	$stmt = mysqli_prepare($db, $sql); //prepare sql statement
    mysqli_stmt_bind_param($stmt, "ss", $n, $n);
	mysqli_stmt_execute($stmt);  //執行SQL
	$result = mysqli_stmt_get_result($stmt);
	$rows = array();//宣告空陣列
	while($r = mysqli_fetch_assoc($result)) {//用欄位名稱當助標 抓出一筆
        
        $temp=array();
        $temp['intro']=$r['intro'];
        $temp['照片']="<img src='".$r['photo']."' />";
        $temp['姓']=$r['last_name'];
        $temp['名']=$r['first_name'];
        $rows[] = $temp;
	}
return json_encode($rows);//json_encode轉成符合json的字串
}

function getCuSubsCount($id){ //subscribe數量
    global $db;
	$sql = "select count(*) as SubCount from `subscribe` where `creator` = ? AND `status` = 'true' ;"; 
	$stmt = mysqli_prepare($db, $sql);
	mysqli_stmt_bind_param($stmt,"i",$id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$retArr=array();
	if($rs = mysqli_fetch_assoc($result)){
		$retArr["SubCount"] = $rs["SubCount"];
	}
	return $retArr;
    /*global $db;
    $sql_query = "SELECT * FROM `subscribe` WHERE `creator`='".$id."' && `status`='true';";
    $data = mysqli_query($db,$sql_query) or die("Query Fail! ".mysqli_error($db));
    $numRow = mysqli_num_rows($data);
    $retArr=array();
    $retArr["SubCount"] = $numRow;
    return $retArr;*/
}

function SubscribeOrNot($cid, $usr){
    global $db;
    $sql = "SELECT * FROM `subscribe` WHERE `subscriber`='".$usr."' && `creator`='".$cid."' && `status`='true';"; 
	$data = mysqli_query($db,$sql) or die("Query Fail! ".mysqli_error($db));
    $numRow = mysqli_num_rows($data);
    $retArr=array();
    if ($numRow ==0) {
        $retArr['sub'] = false;
	}else{
        $retArr['sub'] = true;
    }
	return $retArr;
}

function subscribe($sid,$cid){  
		global $db;
		
		$sql_query = "SELECT * FROM `subscribe` WHERE `subscriber`='".$sid."' && `creator`='".$cid."' && `status`='false';";
		$data = mysqli_query($db,$sql_query) or die("Query Fail! ".mysqli_error($db));
		$numRow = mysqli_num_rows($data);
		if ($numRow ==0){ 
			$sql="insert into subscribe (subscriber, creator) values (?, ?)"; 
			$stmt = mysqli_prepare($db, $sql); //prepare sql statement
			mysqli_stmt_bind_param($stmt, "ii", $sid, $cid);
			mysqli_stmt_execute($stmt);  //執行SQL
		}else{
			$sql="UPDATE `subscribe` SET `status`='true' WHERE subscriber=? && creator=? ;"; 
			$stmt = mysqli_prepare($db, $sql); //prepare sql statement
			mysqli_stmt_bind_param($stmt, "ii", $sid, $cid);
			mysqli_stmt_execute($stmt);  //執行SQL
		}
		//"<script type='text/javascript'>alert('訂閱成功');location.href='".$_SERVER["HTTP_REFERER"]."';</script>"
		echo "subscribe success";
}
	
function unSubscribe($sid,$cid){
		global $db;
		$sql="UPDATE `subscribe` SET `status`='false' WHERE subscriber=? && creator=? ;"; 
		$stmt = mysqli_prepare($db, $sql); //prepare sql statement
		mysqli_stmt_bind_param($stmt, "ii", $sid, $cid);
		mysqli_stmt_execute($stmt);  //執行SQL
		//"<script type='text/javascript'>alert('成功退訂');location.href='".$_SERVER["HTTP_REFERER"]."';</script>"
		echo "unsubscribe!";
}

function CuratorEx1($id){
    global $db;
    $sql_query = "SELECT * FROM `exhibition` WHERE `creatorID`='".$id."' && exhibition.permission='public' && NOW()>exhibition.startTime && NOW()<exhibition.closeTime;";
    $data = mysqli_query($db,$sql_query) or die("Query Fail! ".mysqli_error($db));
    $tArr=array(); 
    $temp=array();
    while($row=mysqli_fetch_assoc($data)){        
        $temp[]=$row['name']."/";        
    }
    $tArr["exhibition"] = $temp;
    return $tArr;
}

function CuratorEx($id){//沒登入顯示策展者公開展覽
    global $db;
    $sql_query = "SELECT * FROM `exhibition` WHERE `creatorID`='".$id."' && exhibition.permission='public' ORDER BY exhibition.createTime DESC;";// && NOW()>exhibition.startTime && NOW()<exhibition.closeTime
    $data = mysqli_query($db,$sql_query) or die("Query Fail! ".mysqli_error($db));
    $tArr=array(); 
    while($row=mysqli_fetch_assoc($data)){ 
        $temp=array();
        $temp["exhibition"]=$row['name'];  
        $temp["eID"]=$row['eID'];
        $temp["createTime"]=$row['createTime'];
        $temp["startTime"]=$row['startTime'];
        $temp["closeTime"]=$row['closeTime'];
        $tArr[] = $temp;
    }    
    return $tArr;
}

function LoginCuratorEx($id,$usr){//登入顯示策展者展覽(詳細)
    global $db;
    $sql_query = "SELECT * FROM `exhibition` LEFT JOIN subscribe ON subscribe.`creator`=exhibition.creatorID WHERE `creatorID`='".$id."'  
     && (exhibition.permission='public' OR (exhibition.permission='subscribeOnly' AND  subscribe.`subscriber`='".$usr."' AND subscribe.`status`='true' ))
     GROUP BY exhibition.eID ORDER BY exhibition.createTime DESC;";//&& NOW()>exhibition.startTime && NOW()<exhibition.closeTime 
    $data = mysqli_query($db,$sql_query) or die("Query Fail! ".mysqli_error($db));
    $tArr=array(); 
    while($row=mysqli_fetch_assoc($data)){ 
        $temp=array();
        $temp["exhibition"]=$row['name'];  
        $temp["eID"]=$row['eID'];
        $temp["createTime"]=$row['createTime'];
        $temp["frontPicture"]=$row['frontPicture'];
        $temp["startTime"]=$row['startTime'];
        $temp["closeTime"]=$row['closeTime'];
        $tArr[] = $temp;
    }    
    return $tArr;
}

function getFirstPanoramaData($eID){ //撈展場的第一場景資訊
    global $db;
	$sql = "select * from `exhibition` INNER JOIN `exhibitivepanorama`
	ON `exhibitivepanorama`.`epID` = `exhibition`.`firstScene`  
	where `exhibition`.`eID` = ? AND `exhibitivepanorama`.`eID` = ? ;";
    //$sql = "select * from `exhibition` where `exhibition`.`eID` = ? ;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt,"ii",$eID,$eID); 
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $retArr = array(); //創建一個陣列，將所有要傳到前端的東西都丟進來
    if($rs = mysqli_fetch_assoc($result)){
		$retArr["exhibitionName"] = $rs["name"];
		$retArr["mapImg"] = $rs["mapImg"];
		$creatorID = $rs["creatorID"];
	    $firstScene = $rs["firstScene"];
	    //去撈第一個場景資訊
		$firstConfig = array();//將第一個場景的全部config存入這個陣列，後續再存到retArr中

	    $sql = "select * from `exhibitivepanorama` 
		    INNER JOIN `panorama` ON `exhibitivepanorama`.`pID` = `panorama`.`pID`  
		    where `exhibitivepanorama`.`epID` = ? ;";
	    $stmt = mysqli_prepare($db, $sql);
	    mysqli_stmt_bind_param($stmt,"i",$firstScene); 
	    mysqli_stmt_execute($stmt);  
	    $result = mysqli_stmt_get_result($stmt);
		
	    if($rs2 = mysqli_fetch_assoc($result)){
			$firstConfig["sceneId"] = (string)$firstScene;
		    $firstConfig["firstpID"] = $rs2["pID"];
		    $firstConfig["firstmapX"] = $rs2["mapX"];
		    $firstConfig["firstmapY"] = $rs2["mapY"];
		    $firstConfig["imgLink"] = $rs2["imgLink"];
		    $firstConfig["smallimgLink"] = $rs2["smallimgLink"];
		    $firstConfig["ownerID"] = $rs2["ownerID"];
		    
			$hotspot = array(); // 裝場景一的所有hotspots
			//資訊 熱點
		    $sql = "select * from `infospot` where `infospot`.`epID` = ? ;";
            $stmt = mysqli_prepare($db, $sql);
	        mysqli_stmt_bind_param($stmt,"i",$firstScene); 
	        mysqli_stmt_execute($stmt);  
	        $result = mysqli_stmt_get_result($stmt);
		    
	        while($rs3 = mysqli_fetch_assoc($result)){
				$tArr = array();
				$tArr["type"] = "info";
			    $tArr["pitch"] = $rs3["pitch"];
			    $tArr["yaw"] = $rs3["yaw"];
			    $tArr["text"] = $rs3["intro"];
				$tArr["scale"] = true;
			    $hotspot[] = $tArr;
		    }
			//移動 熱點
		    $sql = "select * from `movespot` where `movespot`.`epID` = ? ;";
		    $stmt = mysqli_prepare($db, $sql);
	        mysqli_stmt_bind_param($stmt,"i",$firstScene); 
	        mysqli_stmt_execute($stmt);  
	        $result = mysqli_stmt_get_result($stmt);
			
	        while($rs4 = mysqli_fetch_assoc($result)){
				$tArr = array(); //存該movespot裡面的所有參數
				$argsArr = array(); //存clickHandlerArgs裡面的所有參數
				if($rs4["type"] === "ZoomIn"){
					$tArr["clickHandlerFunc"] = "CameraZoomIn"; //可能報縒
                    $argsArr["pitch"] = $rs4["pitch"];
					$argsArr["yaw"] = $rs4["yaw"];
					$argsArr["sceneId"] = (string)$rs4["nextScene"];
					$tArr["clickHandlerArgs"] = $argsArr; //將要傳入function的參數準備好
				}elseif($rs4["type"] ==="FadeOut"){
					$tArr["clickHandlerFunc"] = "SceneFadeOut";  //可能報縒
					$argsArr["sceneId"] = (string)$rs4["nextScene"];
					$tArr["clickHandlerArgs"] = $argsArr; //將要傳入function的參數準備好
				}
				$tArr["type"] = "scene";
			    $tArr["pitch"] = $rs4["pitch"];
			    $tArr["yaw"] = $rs4["yaw"];
				$tArr["scale"] = true;
			    $hotspot[] = $tArr;
		    }

			//客製化 熱點
		    $sql = "select * from  `customspot` INNER JOIN `item` ON `customspot`.`iID` = `item`.`iID` where `customspot`.`epID` = ? AND `item`.status = 'public' ;";
		    $stmt = mysqli_prepare($db, $sql);
	        mysqli_stmt_bind_param($stmt,"i",$firstScene); 
	        mysqli_stmt_execute($stmt);  
	        $result = mysqli_stmt_get_result($stmt);
	        while($rs5 = mysqli_fetch_assoc($result)){
				$tArr = array();
				$tArr["type"] = "custom";
			    $tArr["pitch"] = $rs5["pitch"];
			    $tArr["yaw"] = $rs5["yaw"];
				$tArr["scale"] = true;
				$tArr["iID"] = $rs5["iID"];
				$tArr["3DobjectLink"] = $rs5["3DobjectLink"];

				$Args = array();
				$Args["id"] = $rs5["csID"];
			    $Args["txt"] = $rs5["name"];
				$Args["img"] = $rs5["2DimgLink"];
				$tArr["createTooltipArgs"] = $Args;
				
			    $hotspot[] = $tArr;
		    }
			$firstConfig["hotspots"] = $hotspot;
        }
		$retArr["firstConfig"] = $firstConfig;
   }
   return $retArr;
}

function getOtherPanoramaData($epID){
	global $db;
    $sql = "select * from `exhibitivepanorama` 
		    INNER JOIN `panorama` ON `exhibitivepanorama`.`pID` = `panorama`.`pID`  
		    where `exhibitivepanorama`.`epID` = ? ;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt,"i",$epID); 
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $retArr = array();
	$retArr["sceneId"] = (string)$epID; //就是該展場場景的ID
	$config = array();
	if($rs = mysqli_fetch_assoc($result)){
		$config["pID"] = $rs["pID"];
		$config["mapX"] = $rs["mapX"];
		$config["mapY"] = $rs["mapY"];
		$config["imgLink"] = $rs["imgLink"];
		$config["smallimgLink"] = $rs["smallimgLink"];
		$config["ownerID"] = $rs["ownerID"];
		
		$hotspot = array(); // 裝場景一的所有hotspots
		//資訊 熱點
		$sql = "select * from `infospot` where `infospot`.`epID` = ? ;";
        $stmt = mysqli_prepare($db, $sql);
	    mysqli_stmt_bind_param($stmt,"i",$epID); 
	    mysqli_stmt_execute($stmt);  
	    $result = mysqli_stmt_get_result($stmt);
	    while($rs2 = mysqli_fetch_assoc($result)){
			$tArr = array();
			$tArr["type"] = "info";
			$tArr["pitch"] = $rs2["pitch"];
			$tArr["yaw"] = $rs2["yaw"];
			$tArr["text"] = $rs2["intro"];
			$tArr["scale"] = true;
			$hotspot[] = $tArr;
		}
		//移動 熱點
		$sql = "select * from `movespot` where `movespot`.`epID` = ? ;";
		$stmt = mysqli_prepare($db, $sql);
	    mysqli_stmt_bind_param($stmt,"i",$epID); 
	    mysqli_stmt_execute($stmt);  
	    $result = mysqli_stmt_get_result($stmt);
	    while($rs3 = mysqli_fetch_assoc($result)){
			$tArr = array(); //存該movespot裡面的所有參數
			$argsArr = array(); //存clickHandlerArgs裡面的所有參數
			if($rs3["type"] === "ZoomIn"){
				$tArr["clickHandlerFunc"] = "CameraZoomIn"; 
                $argsArr["pitch"] = $rs3["pitch"];
				$argsArr["yaw"] = $rs3["yaw"];
				$argsArr["sceneId"] = (string)$rs3["nextScene"];
				$tArr["clickHandlerArgs"] = $argsArr; //將要傳入function的參數準備好
			}elseif($rs3["type"] ==="FadeOut"){
				$tArr["clickHandlerFunc"] = "SceneFadeOut"; 
				$argsArr["sceneId"] = (string)$rs3["nextScene"];
				$tArr["clickHandlerArgs"] = $argsArr; //將要傳入function的參數準備好
			}
			$tArr["type"] = "scene";
			$tArr["pitch"] = $rs3["pitch"];
			$tArr["yaw"] = $rs3["yaw"];
			$tArr["scale"] = true;
			$hotspot[] = $tArr;
		}
		//客製化 熱點
		$sql = "select * from  `customspot` INNER JOIN `item` ON `customspot`.`iID` = `item`.`iID` where `customspot`.`epID` = ? AND `item`.status = 'public' ;";
		$stmt = mysqli_prepare($db, $sql);
	    mysqli_stmt_bind_param($stmt,"i",$epID); 
	    mysqli_stmt_execute($stmt);  
	    $result = mysqli_stmt_get_result($stmt);
	    while($rs4 = mysqli_fetch_assoc($result)){
			$tArr = array();
			$tArr["type"] = "custom";
			$tArr["pitch"] = $rs4["pitch"];
			$tArr["yaw"] = $rs4["yaw"];
			$tArr["scale"] = true;
			$tArr["iID"] = $rs4["iID"];
			$tArr["3DobjectLink"] = $rs4["3DobjectLink"];
			
			$Args = array();
			$Args['id'] = $rs4["csID"];
			$Args["txt"] = $rs4["name"];
			$Args["img"] = $rs4["2DimgLink"];
			$tArr["createTooltipArgs"] = $Args;
			
			$hotspot[] = $tArr;
		}
		$config["hotspots"] = $hotspot;
    }
	$retArr["config"] = $config;
	return $retArr;
}
?>