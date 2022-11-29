import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Card, CardGroup } from 'react-bootstrap';
import '../index.css';

const ExhibitionCard = (props) => {
  const [intro, setIntro] = useState('');
  const { exhibition } = props;
  const introLength = exhibition.eIntro.length;
  const eIntro = exhibition.eIntro;
  useEffect(() => {
    if (introLength > 50) {
      setIntro(eIntro.substring(0, 50) + '...'); //介紹文字不能超過五十個字
    } else {
      setIntro(eIntro);
    }
  }, [introLength, eIntro])

  return (
    <CardGroup>
      <Card className='exhibitionCard'>
        <Card.Img variant="top" src={exhibition.frontPicture} />
        <Card.Body>
          <Card.Title> {exhibition.name} </Card.Title>
          <Card.Text> 策展人：{exhibition.first_name} {exhibition.last_name} </Card.Text>
          <Card.Text> {intro} </Card.Text>
        </Card.Body>
        <Card.Footer className='d-flex justify-content-center'>
          <Link to={"/DetailExhibition?eID=" + exhibition.eID} className="btn moreInfo">了解更多 ⮞</Link>
        </Card.Footer>
      </Card>
    </CardGroup>
  )
}
export default ExhibitionCard;