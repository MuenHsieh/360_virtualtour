import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Card, CardGroup } from 'react-bootstrap';
import "../index.css";

const CuratorCard = (props) => {
  const [cIntro, setCIntro] = useState(''); //策展者的介紹設為cIntro
  const { user } = props;
  const introLength = user.intro.length;
  const intro = user.intro;
  useEffect(() => {
    if (introLength > 50) {
      setCIntro(intro.substring(0, 50) + '...'); //介紹文字不能超過五十個字
    } else {
      setCIntro(intro);
    }
  }, [introLength, intro, user])
  return (
    <CardGroup>
      <Card className='curatorCard'>
        <Card.Img variant="top" src={user.photo} />
        <Card.Body>
          <Card.Title> {user.first_name} {user.last_name} </Card.Title>
          <Card.Text> {cIntro} </Card.Text>
        </Card.Body>
        <Card.Footer className='d-flex justify-content-center'>
          <Link to={"/DetailCurator?id=" + user.id} className="btn moreInfo">了解更多 ⮞</Link>
        </Card.Footer>
      </Card>
    </CardGroup>
  )
}
export default CuratorCard;