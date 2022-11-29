import React from "react";

const ExRank = (props) => {
    const { likes } = props;
    const { idx } = props;
    return (
        <div className="exRankCard">
            <div className='d-flex flex-row justify-content-center' style={{ margin: `10px` }}>
                <div className="e-card e-card-horizontal align-items-center" style={{ width: `500px`, height: `200px`, backgroundColor: `#ffffffaa` }}>
                    <div className="e-card-stacked d-flex flex-row justify-content-center" style={{ fontSize: "32px" }}>No.{idx + 1}</div>
                    <img className="p-2" src={likes.frontPicture} alt={likes.exhibition} style={{ height: `200px`, width: '200px' }} />
                    <div className="e-card-stacked m-3">
                        <div className="e-card-header-caption">
                            <div className="e-card-header-title" style={{ fontSize: "24px" }}>{likes.exhibition}</div>
                            <div className="e-card-sub-title" style={{ fontSize: "18px" }}>
                                按讚數: {likes.LikeCount}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ExRank;