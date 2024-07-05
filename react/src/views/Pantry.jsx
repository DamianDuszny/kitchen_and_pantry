import React from 'react';
import { Link, Outlet, useLocation } from 'react-router-dom';
import axiosClient from "../axios-client.js";
import '../assets/componentNavButtons.css';

export default function Pantry() {
    const location = useLocation();
    const links = [
        { to: "/spizarnia/lista", label: "Lista produktów" },
        { to: "/spizarnia/dodaj-produkt", label: "Dodaj produkt" },
    ];

    return (
        <div>
            <div className="nav nav-tabs" id={"nav-tab"} role={"tablist"}>
                {links.map(link => (
                    <Link
                        key={link.to}
                        className={`nav-item nav-link ${location.pathname === link.to ? 'active' : ''}`}
                        to={link.to}
                    >
                        {link.label}
                    </Link>
                ))}
            </div>
            <span>
                <Outlet/>
            </span>
        </div>
    );
}
