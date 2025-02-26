import { Row, Col, Button } from 'react-bootstrap';
import MyNavbar from '../components/MyNavbar';
import './index.css';
import React, { useState, useEffect } from 'react';
import AddEx1 from './components/AddEx1';
import AddEx2 from './components/AddEx2';
import AddEx3 from './components/AddEx3';
import AddEx4 from './components/AddEx4';
import AddEx5 from './components/AddEx5';
import AddEx6 from './components/AddEx6';
import Modal from 'react-bootstrap/Modal';
import axios from 'axios';
import { useNavigate } from "react-router-dom";

const AddExPage = () => {
  const [windowSize, setWindowSize] = useState(window.innerHeight - 56);
  useEffect(() => {
    function handleWindowResize() {
      setWindowSize(window.innerHeight + window.scrollY - 56);
      console.log(window.scrollY);
    }
    window.addEventListener('resize', handleWindowResize);
    return () => {
      // 為了刪除之前的監聽事件
      window.removeEventListener('resize', handleWindowResize);
    };
  }, []);

  let history = useNavigate();
  const [phase, setPhase] = useState(1);
  const [data, setData] = useState({
    exhibitionName: "",
    eIntro: "",
    startTime: "",
    closeTime: "",
    frontPicture: "",
    picture2: "",
    picture3: "",
    permission: "",
    mapImg: "",
    frontPictureFile: null,
    picture2File: null,
    picture3File: null,
    mapImgFile: null,
    firstScene: -1,
    myPanoramaList: []
  });
  const [moveSpotsArray, setMoveSpotsArray] = useState([]); //陣列裏存放所有展場的移動點
  const [infoSpotsArray, setInfoSpotsArray] = useState([]); //陣列裏存放所有展場的資訊點
  const [customSpotsArray, setCustomSpotsArray] = useState([]);//陣列裏存放所有展場的客製化展品點
  const [fakeID, setFakeID] = useState(0); //假的展示中全景場ID
  const [fakeHotspotID, setFakeHotspotID] = useState(0); //假的展示中全景場ID
  const [failtxt, setFailtxt] = useState(""); //失敗報錯，印出原因
  const [leaveModalShow, setLeaveModalShow] = useState(false);//離開的彈出式視窗顯示控制
  const cancelProgress = () => {
    setLeaveModalShow(true);
  }
  const handleLeave = () => {
    history('/myExhibition');
  }
  const handleClose = () => {
    setLeaveModalShow(false);
  }
  const previousStep = () => {//上一步button
    if ((data.mapImg !== "") || (data.frontPicture !== "") || (data.picture2 !== "") || (data.picture3 !== "")) {
      setData({ ...data, mapImg: "", frontPicture: "", picture2: "", picture3: "" });
    } else if ((phase === 3)) {
      //複製一個panorama Div，清掉所有Listener
      var old_element = document.getElementById("panorama3");
      var new_element = old_element.cloneNode(true);
      old_element.parentNode.replaceChild(new_element, old_element);
      document.getElementById('panorama3').remove();//一定要刪掉舊的全景圖標籤
    }
    setFailtxt("");
    setPhase(phase - 1);
  }
  const nextStep = () => {//下一步button
    if ((phase === 1) && ((data.exhibitionName === "") || (data.eIntro === "") || (data.startTime === "") || (data.closeTime === "") || (data.frontPictureFile === null) || (data.permission === ""))) {
      setFailtxt(" 注意: *為必填資料，請勿空白 ");
    } else if ((phase === 2) && (data.myPanoramaList.length === 0)) {
      setFailtxt(" 注意: 請選擇至少一張全景圖 ");
    } else if ((phase === 2) && (data.firstScene === -1)) {
      setFailtxt(" 注意: 請勾選展場的第一張全景圖 ");
    } else if ((phase === 2) && (data.myPanoramaList.length > 20)) {
      setFailtxt(" 注意: 展場不能超過20張全景圖 ");
    } else if ((phase === 3)) {
      var pass3 = true;//預設會通過檢驗
      var noMoveSpot = false;//預設是有移動點
      if (data.myPanoramaList.length === 1) {
        pass3 = true;
      } else if (moveSpotsArray.length === 0) {//如果移動點陣列為空，一定過不了檢驗
        pass3 = false;
      } else {
        for (let i = 0; i < data.myPanoramaList.length; i++) {
          for (let j = 0; j < moveSpotsArray.length; j++) {
            if (data.myPanoramaList[i].fakeID.toString() === moveSpotsArray[j].currentSceneID) {//當現在場景有一個移動點
              break;//找到移動點了，直接往下一個場景找
            } else if (j === moveSpotsArray.length - 1) {//到了最後一個移動點都不是設置在該全景圖ID
              noMoveSpot = true;//代表該全景圖沒有任何移動點
              break;
            }
          }
          if (noMoveSpot) {
            pass3 = false;//檢驗沒有通過
            break;
          }
        }
      }
      if (pass3) { //檢驗判斷各場景是否都有至少一個移動點(boolean)
        //複製一個panorama Div，清掉所有Listener
        var old_element = document.getElementById("panorama3");
        var new_element = old_element.cloneNode(true);
        old_element.parentNode.replaceChild(new_element, old_element);
        document.getElementById('panorama3').remove();//一定要刪掉舊的全景圖標籤
        setFailtxt("");
        setPhase(phase + 1);
      } else {
        setFailtxt(" 注意: 每個全景圖至少要設置一個移動點 ");
      }
    } else if ((phase === 4) && (data.mapImgFile !== null) && (data.mapImgFile !== "")) {//第四階段如果有設置展場平面圖要做判斷
      var pass4 = true;
      for (let i = 0; i < data.myPanoramaList.length; i++) {//檢查每個全景圖在平面圖上都有座標
        if ((data.myPanoramaList[i].mapX === undefined) || (data.myPanoramaList[i].mapY === undefined) || (data.myPanoramaList[i].mapX === null) || (data.myPanoramaList[i].mapY === null) ||
          (data.myPanoramaList[i].mapX === "") || (data.myPanoramaList[i].mapY === "")) {
          pass4 = false;
          setFailtxt("注意: 每個全景圖都要標記一個位置在展場平面圖上");
          break;
        }
      }
      if (pass4) {
        setPhase(phase + 1);
        setFailtxt("");
      }
    } else {
      setFailtxt("");
      setPhase(phase + 1);
    }
  }
  const addMyExhibition = (e) => { //當按下完成後將表單所有資料送入後端
    e.preventDefault();
    const sendData = new FormData();
    if ((data.exhibitionName !== "") && (data.eIntro !== "") && (data.startTime !== "") && (data.closeTime !== "") && (data.frontPictureFile !== null) && (data.firstScene !== -1) && (data.myPanoramaList.length !== 0)) {
      sendData.append('exhibitionName', data.exhibitionName);
      sendData.append('eIntro', data.eIntro);
      sendData.append('startTime', data.startTime);
      sendData.append('closeTime', data.closeTime);
      sendData.append('frontPicture', data.frontPictureFile, data.frontPictureFile.name);
      sendData.append('permission', data.permission);
      sendData.append('firstScene', data.firstScene);
      if (data.picture2File !== null) {
        sendData.append('picture2', data.picture2File, data.picture2File.name);
      } else {
        sendData.append('picture2', null);
      }
      if (data.picture3File !== null) {
        sendData.append('picture3', data.picture3File, data.picture3File.name);
      } else {
        sendData.append('picture3', null);
      }
      if (data.mapImgFile !== null) {
        sendData.append('mapImg', data.mapImgFile, data.mapImgFile.name);
      } else {
        sendData.append('mapImg', null);
      }
      sendData.append('myPanoramaList', JSON.stringify(data.myPanoramaList));//object array --> Json
      for (let i = 0; i < data.myPanoramaList.length; i++) { //因為json不能傳File,所以要額外往上一層處理(移到formData層)
        //後端處理方式就是如果該json欄位value是空物件，表示有傳檔案要去收。若是空字串或是字串就照常處理
        if ((typeof data.myPanoramaList[i].smallimgLink === "object") && (data.myPanoramaList[i].smallimgLink !== null)) {
          sendData.append('smallimg' + data.myPanoramaList[i].fakeID, data.myPanoramaList[i].smallimgLink, data.myPanoramaList[i].smallimgLink.name);
        }
        if ((typeof data.myPanoramaList[i].music === "object") && (data.myPanoramaList[i].music !== null)) {
          sendData.append('music' + data.myPanoramaList[i].fakeID, data.myPanoramaList[i].music, data.myPanoramaList[i].music.name);
        }
      }
      if ((moveSpotsArray.length > 0)) {
        sendData.append('MoveSpotsArray', JSON.stringify(moveSpotsArray));
      } else {
        sendData.append('MoveSpotsArray', null);
      }
      if ((infoSpotsArray.length > 0)) {
        sendData.append('InfoSpotsArray', JSON.stringify(infoSpotsArray));
      } else {
        sendData.append('InfoSpotsArray', null);
      }
      if ((customSpotsArray.length > 0)) {
        sendData.append('CustomSpotsArray', JSON.stringify(customSpotsArray));
        for (let i = 0; i < customSpotsArray.length; i++) {
          if ((typeof customSpotsArray[i].imageLink === "object") && (customSpotsArray[i].imageLink !== null)) {
            sendData.append('hotspotItemImage' + customSpotsArray[i].id, customSpotsArray[i].imageLink, customSpotsArray[i].imageLink.name);
          }
          if ((typeof customSpotsArray[i].musicLink === "object") && (customSpotsArray[i].musicLink !== null)) {
            sendData.append('hotspotItemMusic' + customSpotsArray[i].id, customSpotsArray[i].musicLink, customSpotsArray[i].musicLink.name);
          }
        }
      } else {
        sendData.append('CustomSpotsArray', null);
      }
      axios({
        method: "post",
        url: "http://360.systemdynamics.tw/backendPHP/Control.php?act=addMyExhibition",
        data: sendData,
        withCredentials: true,
        headers: { "Content-Type": "multipart/form-data" }
      })
        .then((res) => {
          if (res.data.Login === true) {
            if (res.data.illegal) { //如果有引用到別人的全景圖或展品但已經不是公開的話
              alert(res.data.cause);
              var errorPMsg = ""
              if (res.data.illegalPanoList.length > 0) {
                errorPMsg = "請刪除引用到的非公開全景圖: ";
                for (let i = 0; i < res.data.illegalPanoList.length; i++) {
                  if (i === res.data.illegalPanoList.length - 1) {
                    errorPMsg = errorPMsg + res.data.illegalPanoList[i] + ' ';
                  } else {
                    errorPMsg = errorPMsg + res.data.illegalPanoList[i] + ', ';
                  }
                }
              }
              var errorIMsg = ""
              if (res.data.illegalItemList.length > 0) {
                errorIMsg = "請刪除引用到的非公開展品: ";
                for (let i = 0; i < res.data.illegalItemList.length; i++) {
                  if (i === res.data.illegalItemList.length - 1) {
                    errorIMsg = errorIMsg + res.data.illegalItemList[i] + ' ';
                  } else {
                    errorIMsg = errorIMsg + res.data.illegalItemList[i] + ', ';
                  }
                }
              }
              setFailtxt("錯誤: " + errorPMsg + errorIMsg);
            } else {//如果都沒有引用到非法的全景圖或展品
              setFailtxt("");
              alert(res.data.cause);
              history('/MyExhibition');
            }
          } else {
            setFailtxt("錯誤:" + res.data.cause);
          }
        })
        .catch((error) => {
          console.log(error);
        })
    } else {
      setFailtxt("錯誤: 有必填項目遺失，請重新整理表單");
    }
  }
  return (
    <div>
      <Row className='pt-0 me-0' style={{ height: windowSize }}>
        <Col md={2} className='navbar_menu'>
          <MyNavbar />
        </Col>
        <Col className='d-flex flex-column m-3'>
          <h1 className="text-center">新增展場</h1>
          {
            (phase === 1) && <AddEx1 phase={phase} data={data} setData={setData} setFailtxt={setFailtxt} />
          }
          {
            (phase === 2) && <AddEx2 phase={phase} data={data} setData={setData} setFailtxt={setFailtxt}
              fakeID={fakeID} setFakeID={setFakeID} moveSpotsArray={moveSpotsArray} setMoveSpotsArray={setMoveSpotsArray}
              infoSpotsArray={infoSpotsArray} setInfoSpotsArray={setInfoSpotsArray} customSpotsArray={customSpotsArray}
              setCustomSpotsArray={setCustomSpotsArray} />
          }
          {
            (phase === 3) && <AddEx3 phase={phase} data={data} setData={setData} setFailtxt={setFailtxt}
              fakeHotspotID={fakeHotspotID} setFakeHotspotID={setFakeHotspotID}
              moveSpotsArray={moveSpotsArray} setMoveSpotsArray={setMoveSpotsArray} />
          }
          {
            (phase === 4) && <AddEx4 phase={phase} data={data} setData={setData} setFailtxt={setFailtxt} />
          }
          {
            (phase === 5) && <AddEx5 phase={phase} data={data} setData={setData} setFailtxt={setFailtxt}
              fakeHotspotID={fakeHotspotID} setFakeHotspotID={setFakeHotspotID} moveSpotsArray={moveSpotsArray}
              infoSpotsArray={infoSpotsArray} setInfoSpotsArray={setInfoSpotsArray} customSpotsArray={customSpotsArray}
              setCustomSpotsArray={setCustomSpotsArray} />
          }
          {
            (phase === 6) && <AddEx6 phase={phase} data={data} setFailtxt={setFailtxt}
              moveSpotsArray={moveSpotsArray} infoSpotsArray={infoSpotsArray} customSpotsArray={customSpotsArray} />
          }
          <Row>
            <span className="text-center" style={{ color: 'red' }}>{failtxt}</span>
            {
              (phase > 1) && (<Col className='d-flex justify-content-start'>
                <Button className="update_btn" onClick={previousStep}> 上一步 </Button>
              </Col>)
            }
            {
              (phase < 6)
                ?
                <Col className='d-flex justify-content-end'>
                  <Button onClick={cancelProgress} className="cancel_btn me-3"> 取消 </Button>
                  <Button onClick={nextStep} className="update_btn"> 下一步 </Button>
                </Col>
                :
                <Col className='d-flex justify-content-end'>
                  <Button onClick={cancelProgress} className="cancel_btn me-3"> 取消 </Button>
                  <Button onClick={addMyExhibition} className="finish_btn"> 完成 </Button>
                </Col>
            }
          </Row>
        </Col>
      </Row>
      <Modal
        show={leaveModalShow}
        size="lg"
        aria-labelledby="contained-modal-title-vcenter"
        centered
      >
        <Modal.Header>
          <Modal.Title>確定要離開新增展場嗎?</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <p style={{ color: "red" }}>您若離開所有新增紀錄將會消失</p>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="danger" onClick={handleLeave}>離開</Button>
          <Button className="cancel_btn" onClick={handleClose}>取消</Button>
        </Modal.Footer>
      </Modal>
    </div>
  );
}

export default AddExPage;

/*===================新增展場 全部的資料結構=============================
[data , setData] = 
{
    exhibitionName: "",
    eIntro: "",
    startTime: "",
    closeTime: "",
    frontPicture: "",
    picture2: "",
    picture3: "",
    permission: "",
    mapImg: "",
    frontPictureFile:null,
    picture2File:null,
    picture3File:null,
    mapImgFile:null,
    firstScene: -1, //(number)
    myPanoramaList:[
      {
          fakeID: "" //往上累加不會去補缺露(number)
          pID: 0,   //一定要有pID，因為每一張全景圖的引用都來自後端的panorama資料表
          panoramaName:""
          imgLink: 網址
          smallimgLink: "空字串" / File(物件) / 網址(字串)
          music: null / File(物件),
          ownerID:0,
          authorName:"",
          mapX: 0.xxx(四捨五入制小數第三位，圖片的左上角到網頁的左上角寬度，加上圖片寬度(px)乘上這個比例(mapX)就是標記在網頁的px)
          mapY:
      }
    ]
}
[moveSpotsArray,setMoveSpotsArray] = ([
  {
      //注意不需要紀錄currentScene因為他在該場景底下就一定隸屬於該場景
      id: string   // hotspot fakeID =  數字 + "h"
      currentSceneID= "",  //use fakeID 
      currentSceneName:"",
      destinationID: "",  //use fakeID
      destinationName:"",
      clickHandlerFunc: "ZoomIn"、"FadeOut",
      pitch: 0,
      yaw: 0
  }
])
[customSpotsArray,setCustomSpotsArray]:[
  {
      id: string
      iID:參考哪一個展品ID(必要)
      currentSceneID= "",  //use fakeID 
      currentSceneName:"",
      pitch: 0,
      yaw:0,
      itemName: "展品名稱",
      itemIntro: "介紹文字",
      imageLink: 可能是URL或是File,
      imageWidth:"100px",
      imageHeight:"100px",
      modelLink:"只能是URL",
      musicLink: 可能是URL或是File或是null,
      ownerID:"",
      authorName:""
  }
]
[infoSpotsArray,setInfoSpotsArray]:[
  {
      id: string   // hotspot fakeID = 數字 + "h"
      currentSceneID= "",  // use fakeID 
      currentSceneName:"",
      pitch: 0,
      yaw:0
      title:"標題或是外面的文字",
      detailtxt:"內文"
  }
] 
*/