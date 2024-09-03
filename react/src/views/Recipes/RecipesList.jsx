import { useState, useEffect } from "react";
import axiosClient from "../../axios-client.js";
import '../../assets/noMoreTables.css';
import '../../assets/loading.css';
import { useParams, Link } from 'react-router-dom';

export default function RecipesList() {
    const [response, setResponse] = useState([]);
    const { page } = useParams();

    useEffect(() => {
        getRecipesData();
    }, [page]); // Dodajemy `page` do zależności, aby ponownie załadować dane przy zmianie strony

    function getRecipesData() {
        let url = '/recipes';
        setResponse({});

        if (page) {
            url += '?page=' + page;
        }
        axiosClient.get(url)
            .then((response) => {
                console.log(response.data);
                setResponse(response.data);
            })
            .catch(error => {
                console.error("There was an error fetching the products!", error);
            });
    }

    let links = [];

    if (response?.last_page > 1) {
        for (let i = 1; i <= response.last_page; i++) {
            links.push(
                <Link key={i} className="page-link" to={`/spizarnia/lista/${i}`}>{i}</Link>
            );
        }
    }

    return (
        response.data?.length > 0 ? (
            <>
                <nav aria-label="Page navigation example">
                    <ul className="pagination">
                        {
                            links.map(
                                (link, index) => (
                                    <li key={index} className={`page-item ${parseInt(page) === index + 1 ? 'active' : ''}`}>
                                        {link}
                                    </li>
                                )
                            )
                        }
                    </ul>
                </nav>
                <div id="no-more-tables">
                    <table className="col-md-12 table-bordered table-striped table-condensed cf">
                        <thead className="cf">
                        <tr>
                            <th>Nazwa</th>
                            <th>Opis</th>
                            <th className="numeric">Data przydatności</th>
                            <th className="numeric">Masa netto</th>
                            <th className="numeric">Cena</th>
                            <th className="numeric">Masa jednostki</th>
                        </tr>
                        </thead>
                        <tbody>
                        {response.data.map(product => (
                            <tr key={product.id} className="grid-row">
                                <td data-title="Nazwa">{product.name}</td>
                                <td data-title="Ilość">{product.description}</td>
                                <td data-title="Data przydatności">{product.expiration_date}</td>
                                <td data-title="Masa netto">{product.net_weight}</td>
                                <td data-title="Cena">{product.price}</td>
                                <td data-title="Masa jednostki">{product.unit_weight}</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
            </>
        ) : (
            <div className="loader"></div>
        )
    );
}
