import React from 'react';
import { Link, Outlet, useLocation } from 'react-router-dom';
import '../assets/componentNavButtons.css';

export default function Recipes() {
    const location = useLocation();
    const links = [
        { to: "/przepisy/lista", label: "Lista przepisów" },
        { to: "/przepisy/dodaj-przepis", label: "Dodaj przepis" },
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
