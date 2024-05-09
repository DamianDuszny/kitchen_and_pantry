import {Link} from "react-router-dom";

export default function Login() {

    const onSubmit = (ev) => {
        ev.preventDefault();
    }

    return (
        <div className="login-signup-form animated fadeInDown">
            <div className="form">
                <form onSubmit="onSubmit">
                    <h1 className="title">Logowanie</h1>
                    <input type="email" placeholder="email"/>
                    <input type="password" placeholder="hasło"/>
                    <button className="btn btn-block">Login</button>
                    <p className="message">
                        Nie masz konta? <Link to="/rejestracja">Utwórz konto</Link>
                    </p>
                </form>
            </div>
        </div>
    )
}
