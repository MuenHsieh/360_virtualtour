<?php
require('./Model.php');


if(isset($_REQUEST['act'])){
    $act = $_REQUEST['act'];
}else{
    $act = '';
}

switch ($act) { //用switch語法，判斷act這個變數要做哪件事
    case "getMyExhibition"://展品
        $usr = $_SESSION["userID"];
		$list = getMyExhibition($usr);
        echo json_encode($list);
        break;

    case "EditItem":
        $usr = $_SESSION["userID"];

        $name = $_REQUEST["name"];//前端用formData加上POST傳值
        //$img2D = $_REQUEST["img2D"];
        $intro = $_REQUEST["intro"];
        $status = $_REQUEST["status"];
        $iID = $_REQUEST["iID"];
        $object3D = $_REQUEST["object3D"];
        
        $response = array();
        if ((isset($iID)) &(isset($name)) & (isset($intro)) & (isset($status)) & ($name != "") & ($intro != "")& ($status != "")& ($iID != "")) {//& (isset($img2D)) & (isset($object3D)) & ($object3D != "") 
            if((isset($_FILES["img2D"]))){
                $imgFilename = uniqid($_SESSION["userID"]).'.'.pathinfo($_FILES["img2D"]["name"], PATHINFO_EXTENSION);
                //uniqid是生成微分秒，裡面的參數就是你可以加上的前綴
                //因為有可能發生多使用者同時上傳全景圖，還是可能導致微分咬相同，所以特別再用userID當作前綴分開 
                $imgLink = "http://localhost:8080/backendPHP/ItemImg/".$imgFilename;
                move_uploaded_file($_FILES["img2D"]["tmp_name"], "ItemImg/".$imgFilename);
                //刪除資料夾的檔案
                DelPic($iID);
            }else{
                $imgLink =$_REQUEST["img2D"];
            }
            if (EditItem($iID,$name, $intro, $imgLink, $object3D, $status,$usr)) {
                $response['state'] = 'valid';
                $response['cause'] = 'edit成功';
                echo json_encode($response);
            } else {
                $response['state'] = 'invalid';
                $response['cause'] = '已有此作品';
                echo json_encode($response);
            }
        } else {
            $response['state'] = 'invalid';
            $response['cause'] = '表單不能有空值';
            echo json_encode($response);
        }
        break;

    case "ShowItem"://展品
        if(isset($_REQUEST['iID'])){
			$iID = $_REQUEST['iID'];
		}else{
			$iID = '';
		}
		$list = ShowItem($iID);
        echo json_encode($list);
        break;

    case "AddItem":
        $response = array();
        $usr = $_SESSION["userID"];
        $name = $_REQUEST["name"];//前端用formData加上POST傳值
        $imageType = $_FILES["img2D"]["type"];//他的副檔名，但是是用MIME格式紀錄image/png、image/jpeg、image/gif
        $intro = $_REQUEST["intro"];
        $status = $_REQUEST["status"];
        $tag = $_REQUEST["tag"];
        //$object3D = $_REQUEST["object3D"];
        $object3DType = $_FILES["object3D"]["type"];
                
        if ((isset($name)) & (isset($intro)) & (isset($status)) & ($name != "") & ($intro != "")& ($status != "")) {//& (isset($object3D))  & ($object3D != "") 
            if(($imageType === 'image/png')||($imageType === 'image/jpeg')||($imageType === 'image/gif')){
            //上面有可能縮圖報錯，所以全景圖在上方先不要存，移動到這邊確定縮圖沒問題再存
            $imgFilename = uniqid($_SESSION["userID"]).'.'.pathinfo($_FILES["img2D"]["name"], PATHINFO_EXTENSION);
            //uniqid是生成微分秒，裡面的參數就是你可以加上的前綴
            //因為有可能發生多使用者同時上傳全景圖，還是可能導致微分咬相同，所以特別再用userID當作前綴分開 
            $imgLink = "http://localhost:8080/backendPHP/ItemImg/".$imgFilename;
            move_uploaded_file($_FILES["img2D"]["tmp_name"], "ItemImg/".$imgFilename);
            //move_uploaded_file(file,newloc) 解釋: file(必需。規定要移動的檔案) newloc(必需。規定檔案的新位置)
            // $FILES["file"]["name"]  客戶端電腦上文件的原始名稱。
            // $FILES["file"]["tmp_name"] 上傳文件儲存在伺服器上的臨時文件名。
            //3D
            $modelFilename = uniqid($_SESSION["userID"]).'.'.pathinfo($_FILES["object3D"]["name"], PATHINFO_EXTENSION);
            $object3D = "http://localhost:8080/backendPHP/Item3D/".$modelFilename;
            move_uploaded_file($_FILES["object3D"]["tmp_name"], "Item3D/".$modelFilename);

            if (AddItem($name, $intro, $imgLink, $object3D, $status,$usr,$tag)) {
                $response['state'] = 'valid';
                $response['cause'] = '新增成功';
                echo json_encode($response);
            } else {
                $response['state'] = 'invalid';
                $response['cause'] = '已有此作品';
                echo json_encode($response);
            }
            }else{
                $response['state']='invalid';
                $response['cause']='全景圖格式錯誤，只能是.jpg/.jpeg/.png/.gif';
                echo json_encode($response);
            }           
        } else {
            $response['state'] = 'invalid';
            $response['cause'] = '表單不能有空值';
            echo json_encode($response);
        }
        break;

    case "DeleteItem"://刪展品
        if(isset($_REQUEST['iID'])){
			$iID = $_REQUEST['iID'];
		}else{
			$iID = '';
		}
		$list = DeleteItem($iID);
        echo json_encode($list);
        break;

    case "getItem"://展品
        $usr = $_SESSION["userID"];
		$list = getItem($usr);
        echo json_encode($list);
        break;

    case "getUsername":
        $retArr = array();
        if (isset($_SESSION["userID"]) && $_SESSION["userID"] != "") {
            $retArr["username"] = getUsername($_SESSION["userID"]);
        } else {
            $retArr["username"] = "Visitor";
        }
        echo json_encode($retArr);
        break;
    case "logout":
        $_SESSION["userID"] = "";
        $retArr = array();
        $retArr["sessionIsClean"] = true;
        echo json_encode($retArr);
        break;

	case "isLogin": //檢查使用者有沒有login
        $isLogin = array();
        $isLogin["Login"] = true;
        if (!isset($_SESSION["userID"]) || $_SESSION["userID"] === '') { //session未定義或者是空值時
            $isLogin["Login"] = false; //表示沒有登入過
            echo json_encode($isLogin);
            break;
        }
        echo json_encode($isLogin);
        break;
    case "loginCheck": //檢查登入(用戶名不能重複且不能為空)
        $_SESSION["userID"] = ""; //將session清空
        $json = json_decode(file_get_contents("php://input")); //json_decode第二個參數加上true會變成返回陣列，否者返回物件
        $email = $json->email;
        $pwd = $json->password;
        $response = array();
        if ((isset($email)) & (isset($pwd)) & ($email != "") & ($pwd != "")) { //白手套的概念，先確認id大於一，再將它導入函數
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response["status"] = "invalid";
                $response["cause"] = "{$email}: A valid email" . "<br>";
                echo json_encode($response);
                break;
            } else if (loginCheck($email, $pwd)) {
                $response["status"] = "valid";
                $response["cause"] = "登入成功!!!";
                echo json_encode($response);
                break;
            } else {
                $response["status"] = "invalid";
                $response["cause"] = "用戶名或密碼錯誤";
                echo json_encode($response);
                break;
            }
        } else {
            $response["status"] = "invalid";
            $response["cause"] = "用戶名或密碼不能為空值";
            echo json_encode($response);
            break;
        }
        break;
	
	case "addMember":
        $json = json_decode(file_get_contents("php://input")); //json_decode第二個參數加上true會變成返回陣列，否者返回物件

        $first_name = $json->first_name;
        $last_name = $json->last_name;
        $pwd = $json->password;
        $email = $json->email;
        $gender = $json->gender;
        $intro = $json->intro;

        $response = array();
        if ((isset($first_name)) & (isset($last_name)) & (isset($pwd)) & (isset($email)) & (isset($gender)) & ($first_name != "") & ($last_name != "") & ($pwd != "") & ($email != "") & ($gender != "")) {
            //防呆，一樣做簡單邏輯判斷，當title不是空的，再將它導入函數
            if (filter_var($email, FILTER_VALIDATE_EMAIL) != true) {
                //$message = "輸入無效的電子郵件";
                $response['status'] = 'invalid';
                $response['cause'] = '輸入無效的電子郵件';
                echo json_encode($response);
            } elseif (addMember($first_name, $last_name, $pwd, $email, $gender, $intro)) {
                //$message = "註冊成功";
                $response['status'] = 'valid';
                $response['cause'] = '註冊成功';
                echo json_encode($response);
            } else {
                //$message = "此用戶帳號已經被註冊過了";
                $response['status'] = 'invalid';
                $response['cause'] = '此用戶電子郵件已經被註冊過了';
                echo json_encode($response);
            }
        } else {
            //$message = "表單不能有空值";
            $response['status'] = 'invalid';
            $response['cause'] = '表單不能有空值';
            echo json_encode($response);
        }
        break;
	case "getExhibitionList":
        $list = getExhibitionList(); // 從Model端得到未完成工作清單
		echo json_encode($list); //將陣列變成JSON字串傳回
		break; 
	case "getExhibitionData":
		if(isset($_REQUEST['eID'])){
			$eID = $_REQUEST['eID'];
		}else{
			$eID = '';
		}
		$data = getExhibitionData($eID); // 從Model端得到未完成工作清單
		echo json_encode($data); //將陣列變成JSON字串傳回
		break; 
	case "getCuratorList":
        $list = getCuratorList(); // 從Model端得到未完成工作清單
		echo json_encode($list); //將陣列變成JSON字串傳回
		break; 
	case "getCuratorData":
		if(isset($_REQUEST["id"])){ 
			$id =$_REQUEST["id"];
		}else{
			$id = '';
		}
		echo json_encode(getCuratorData($id));
		break;
	case "getExhibitionLikes":
		$eID = $_REQUEST['eID'];
		$list = getExLikeCount($eID);
		echo json_encode($list);
		break;
	case "getLikeorNot":
		$usr = $_SESSION["userID"];
		if(isset($_REQUEST['eID']) && isset($usr)){
			$eID = $_REQUEST['eID'];
			$list = getLikeorNot($eID, $usr); 
			echo json_encode($list);
		}else{
			$retArr=array();
			$retArr['like'] = false;
			echo json_encode($retArr);
		}
		break;

    case "AddLike":
		$usr = $_SESSION["userID"];
		if(isset($_REQUEST['eID'])){
			$eID = $_REQUEST['eID'];
			AddLike($usr,$eID); 
		}
		break;

    case "CancelLike":
		$usr = $_SESSION["userID"];
		if(isset($_REQUEST['eID'])){
			$eID = $_REQUEST['eID'];
			CancelLike($usr,$eID); 
		}
		break;
	case "getCuratorSubs":
		$id = $_REQUEST['id'];
		$list = getCuSubsCount($id);
		echo json_encode($list);
		break;
	case "SubscribeOrNot"://確認是否訂閱
        $usr = $_SESSION["userID"];
		if(isset($_REQUEST['id']) && isset($usr)){
			$cid = $_REQUEST['id'];
			$list = SubscribeOrNot($cid, $usr); 
			echo json_encode($list);
		}else{
			$retArr=array();
			$retArr['sub'] = false;
			echo json_encode($retArr);
		}
        break;
	case "subscribe":
        $cid =$_REQUEST["id"];
        $sid =$_SESSION["userID"];
        subscribe($sid,$cid);
        break;
    case "unSubscribe":
        $cid =$_REQUEST["id"];
        $sid =$_SESSION["userID"];
        unSubscribe($sid,$cid);
        break;
    case "CuratorEx"://未登入顯示策展者公開展覽
		$id = $_REQUEST['id'];
		$list = CuratorEx($id);
		echo json_encode($list);
		break;
    case "LoginCuratorEx"://登入顯示策展者展覽
		$id = $_REQUEST['id'];
        $usr = $_SESSION["userID"];
		$list = LoginCuratorEx($id,$usr);
		echo json_encode($list);
		break;
    case "LoginCuratorList"://登入後列策展人
        $usr = $_SESSION["userID"];//userID
		echo LoginCuratorList($usr);
        break;
        
    case "LoginExhibitionList"://登入後列展覽
        $usr = $_SESSION["userID"];//userID
		$list = LoginExhibitionList($usr);
        echo json_encode($list);
        break;
    case "getFirstPanoramaData": //抓第一場景的全部資訊
        $eID = $_REQUEST['eID'];
        if(isset($eID) && $eID != ""){
            $list = getFirstPanoramaData($eID);
            echo json_encode($list);
        break;
        }
        $list = array();
        echo json_encode($list);
        break;
    case "getOtherPanoramaData": //抓其他場景的全部資訊
        $epID = $_REQUEST['epID'];
        if(isset($epID) && $epID != ""){
            $list = getOtherPanoramaData($epID);
            echo json_encode($list);
            break;
        }
        $list = array();
        echo json_encode($list);
        break;
	default:
}

?>