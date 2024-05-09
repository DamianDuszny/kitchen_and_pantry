import {Link, Navigate, Outlet} from "react-router-dom";
import {useStateContext} from "../contexts/ContextProvider.jsx";

export default function LoggedUserLayout() {
    const {user, token} = useStateContext();

    if (!token) {
        return <Navigate to="/Logowanie"/>
    }

    const onLogout = (ev) => {
        ev.preventDefault();
    }

    return (
        <div id="loggedUserLayout">
            <aside>
                <Link to="/profil">Profil</Link>
                <Link to="/przepisy">Przepisy</Link>
            </aside>
            <div className="content">
                <header>
                    <div>
                        Header
                    </div>
                    <div>
                        {user.name}
                        <a href="#" onClick="onLogout" className="btn-logout">Wyloguj się</a>
                    </div>
                </header>
                <main>
                    <Outlet/>
                </main>
            </div>
        </div>
    )
}
