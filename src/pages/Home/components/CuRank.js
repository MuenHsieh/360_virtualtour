import React from "react";

const CuRank = (props) => {
    const { subscribe } = props;
    const { index } = props;
    return (
        <div className="cuRankCard">
            <div className='d-flex flex-row justify-content-center' style={{ margin: `10px` }}>
                <div className="e-card e-card-horizontal align-items-center" style={{ width: `500px`, height: `200px`, backgroundColor:`#ffffffaa` }}>
                    <div className="e-card-stacked d-flex flex-row justify-content-center" style={{ fontSize: "32px" }}> No.{index + 1} </div>
                    <img className="p-2" src={subscribe.photo} alt={subscribe.first_name} style={{ height: `200px`, width: '200px' }} />
                    <div className="e-card-stacked">
                        <div className="e-card-header-caption">
                            <div className="e-card-header-title" style={{ fontSize: "24px" }}>{subscribe.first_name} {subscribe.last_name}</div>
                            <div className="e-card-sub-title" style={{ fontSize: "18px" }}> 訂閱人數: {subscribe.SubCount}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default CuRank;