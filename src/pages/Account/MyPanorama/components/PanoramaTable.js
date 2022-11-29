import React, { useState } from "react";
import Table from 'react-bootstrap/Table';
import TableData from './TableData.js';
import Pagination from './Pagination.js';
import { Col, Row } from 'react-bootstrap';
import '../index.css';

const PanoramaTable = ({ show, searchTerm }) => {
    const [currentPage, setCurrentPage] = useState(1);
    const [postsPerPage] = useState(5);
    const indexOfLastPost = currentPage * postsPerPage; // 1*5 2*5
    const indexOfFirstPost = indexOfLastPost - postsPerPage; // 5-5 10-5
    const paginate = pageNumber => setCurrentPage(pageNumber);
    return (
        <div>
            <Table striped bordered hover>
                <thead>
                    <tr>
                        <th>全景圖名稱</th>
                        <th>預覽</th>
                        <th>底部圖</th>
                        <th>狀態</th>
                        <th>存取權限</th>
                        <th>管理功能</th>
                    </tr>
                </thead>
                <tbody>
                    {
                        show && (
                            searchTerm.slice(indexOfFirstPost, indexOfLastPost).map((panorama) => {
                                return (
                                    <TableData key={panorama.pID} panorama={panorama} />
                                )
                            })
                        )
                    }
                </tbody>
            </Table>
            <Row className="justify-content-md-center">
                <Col md="auto">
                    <Pagination
                        postsPerPage={postsPerPage}
                        totalPosts={searchTerm.length}
                        paginate={paginate}
                    />
                </Col>
            </Row>
        </div>
    )
}

export default PanoramaTable;