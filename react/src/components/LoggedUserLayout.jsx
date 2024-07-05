import {Link, Navigate, Outlet} from "react-router-dom";
import {useStateContext} from "../contexts/ContextProvider.jsx";
import axiosClient from "../axios-client.js";
import {useEffect} from "react";

export default function LoggedUserLayout() {
    const {user, token, setUser, setToken} = useStateContext();

    if (!token) {
        return <Navigate to="/Logowanie"/>
    }

    const onLogout = (ev) => {
        axiosClient.get('/user/logout')
            .then(() => {
                setUser({});
                setToken(null);
            })
            .catch(err => {
                const response = err.response;
                if(response && response.status === 422) {
                    console.log(response.data.errors);
                }
            })
        ;
        ev.preventDefault();
    }

    useEffect(() => {
        axiosClient.get('/user/data')
            .then(({data}) => {
                setUser(data)
            })
    }, []);
    return (
        <div id="loggedUserLayout">
            <nav className="navbar bg-body-tertiary fixed-top">
                <div className="container-fluid">
                    <a className="navbar-brand" href="#">Pantry!</a>
                    <button className="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                            aria-label="Toggle navigation">
                        <span className="navbar-toggler-icon"></span>
                    </button>
                    <div className="offcanvas offcanvas-start" tabIndex="-1" id="offcanvasNavbar"
                         aria-labelledby="offcanvasNavbarLabel">
                        <div className="offcanvas-header">
                            <h5 className="offcanvas-title" id="offcanvasNavbarLabel">Pantry</h5>
                            <button type="button" className="btn-close" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                        </div>
                        <div className="offcanvas-body">
                            <ul className="navbar-nav justify-content-end flex-grow-1 pe-3">
                                <li className="nav-item">
                                    {<Link className={"nav-link"} to="/profil">Profil</Link>}
                                </li>
                                <li className="nav-item">
                                    {<Link className={"nav-link"} to="/przepisy">Przepisy</Link>}
                                </li>
                                <li className="nav-item">
                                    {<Link className={"nav-link"} to="/spizarnia">Spiżarnia</Link>}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div className="content">
                <header>
                    <div>
                        Header
                    </div>
                    <div>
                        {user?.name}
                        <a href="#" onClick={onLogout} className="btn-logout">Wyloguj się</a>
                    </div>
                </header>
                <main>
                    <Outlet/>
                </main>
            </div>
        </div>
    )
}
