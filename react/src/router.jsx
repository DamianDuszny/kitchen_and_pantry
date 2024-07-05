import {createBrowserRouter, Navigate} from "react-router-dom";
import Login from "./views/Login.jsx";
import Register from "./views/Register.jsx";
import Profile from "./views/Profile.jsx";
import NotFound from "./views/NotFound.jsx";
import LoggedUserLayout from "./components/LoggedUserLayout.jsx";
import GuestLayout from "./components/GuestLayout.jsx";
import Recipes from "./views/Recipes.jsx";
import Pantry from "./views/Pantry.jsx";
import PantryList from "./views/PantryList.jsx";
import AddPantryProduct from "./views/AddPantryProduct.jsx";

const router = createBrowserRouter([
    {
        path: '/',
        element: <LoggedUserLayout />,
        children: [
            {
                path: '/profil',
                element: <Profile />
            },
            {
                path: '/przepisy',
                element: <Recipes />
            },
            {
                path: '/spizarnia',
                element: <Pantry />,
                children: [
                    {
                        path:'/spizarnia/lista/:page?',
                        element: <PantryList />
                    },
                    {
                        path:'/spizarnia/dodaj-produkt',
                        element: <AddPantryProduct />
                    },
                ]
            },
            {
                path: '/',
                element: <Navigate to="/przepisy" />
            }
        ]
    },
    {
        path: '/',
        element: <GuestLayout />,
        children: [
            {
                path: '/logowanie',
                element: <Login />
            },
            {
                path: '/rejestracja',
                element: <Register />
            }
        ],
    },
    {
        path: '*',
        element: <NotFound />
    }
]);

export default router;
