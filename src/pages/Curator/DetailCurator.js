import React, { useState, useEffect } from 'react';
import axios from 'axios';
import SubscribeB from './components/SubscribeB.js';
import { Row, Col } from 'react-bootstrap';
import { Link } from "react-router-dom";
import TheCuratorEx from './components/TheCuratorEx.js';
import "./index.css";
import { Splide, SplideSlide } from '@splidejs/react-splide';
import '@splidejs/react-splide/css';
import FOG from "vanta/dist/vanta.fog.min";
import * as THREE from "three";

const DetailCurator = () => {
  const [data, setData] = useState("");
  const [List, setList] = useState([]);
  useEffect(() => {
    var url = window.location.href;
    var ary1 = url.split('?');
    var ary2 = ary1[1].split('=');
    axios({
      method: "get",
      url: "http://360.systemdynamics.tw/backendPHP/Control.php?act=getCuratorData&id=" + ary2[1],
      dataType: "JSON",
      withCredentials: true
    })
      .then((res) => {
        setData(res.data);
      })
      .catch(console.error);
    axios({
      method: "get",
      url: "http://360.systemdynamics.tw/backendPHP/Control.php?act=isLogin",
      dataType: "JSON",
      withCredentials: true
    })
      .then((res) => {
        console.log(res);
        if (res.data.Login) { //使用者有登入
          axios({ //LoginCuratorEx
            method: "get",
            url: "http://360.systemdynamics.tw/backendPHP/Control.php?act=LoginCuratorEx&id=" + ary2[1],//name
            dataType: "JSON",
            withCredentials: true
          })
            .then((res) => {
              setList(res.data);
            })
        } else {
          //未登入顯示策展者的公開展覽
          //CuratorEx()
          axios({
            method: "get",
            url: "http://360.systemdynamics.tw/backendPHP/Control.php?act=CuratorEx&id=" + ary2[1],
            dataType: "JSON",
            withCredentials: true
          })
            .then((res) => {
              setList(res.data);
            })
        }
      })
      .catch(console.error);
  }, []);

  const [vantaEffect, setVantaEffect] = useState(0);
  useEffect(() => {
    if (!vantaEffect) {
      setVantaEffect(
        FOG({
          el: "#vanta",
          THREE: THREE,
          mouseControls: true,
          touchControls: true,
          gyroControls: false,
          scale: 2,
          scaleMobile: 1.0,
          size: 0.90,
          minHeight: 650.00,
          minWidth: 500.00,
          highlightColor: 0xd6e3e3,
          midtoneColor: 0x6ad0e8,
          lowlightColor: 0xd1a5a5,
          baseColor: 0xcceac9,
          speed: 1.10
        })
      );
    }
    return () => {
      if (vantaEffect) vantaEffect.destroy();
    };
  }, [vantaEffect]);

  return (
    <div>
      <div id='vanta'></div>
      <div className="square border border-0 text-center p-1">
        <h1 className="activity"> 策展人 </h1>
      </div>
      <div className="p-3">
        <Row className="p-3 pl-4">
          <Col sm={6} >
            <p ><Link to="/home"> 首頁 </Link> / <Link to="/curators"> 策展人 </Link> /  {data.first_name} {data.last_name}</p>
          </Col>
        </Row>
        <Row>
          <Col sm={2} md={2} className="text-center justify-content-center">
            <img className="myphoto"
              src={data.photo}
              alt="myphoto"
            />
            <h1 className="curatorname p-3"><b>{data.first_name} {data.last_name}</b> </h1>
            <SubscribeB id={data.id} />
          </Col>
          <Col sm={10} md={10}>
            <h3 className='pb-3'><span style={{ color: '#e38970' }}>| </span><b> 自我介紹 </b></h3>
            <p className='pb-3'> &emsp;{data.intro}{data.intro}{data.intro}{data.intro}</p>
            <h3 className='pb-3'><span style={{ color: '#e38970' }}>| </span><b>{data.first_name} {data.last_name}的展覽</b></h3>
            <div className='p-3'>
              <Splide data-splide='{"perPage":3}'>
                {List.map((exhibition) => (
                  <SplideSlide key={exhibition.eID}>
                    <div className='p-2' style={{ color: '#87a7aa' }}><TheCuratorEx key={exhibition.eID} exhibition={exhibition} /></div>
                  </SplideSlide>
                ))}
              </Splide>
            </div>
          </Col>
        </Row>
      </div >
    </div >

  )
}

export default DetailCurator;