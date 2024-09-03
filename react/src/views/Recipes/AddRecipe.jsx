import axiosClient from "../../axios-client.js";
import {useState} from "react";

export default function AddRecipe() {
    const [errors, setErrors] = useState(null);

    const productData = [
        {form_name: "name", visible_name: "Nazwa produktu"},
        {form_name: "amount", visible_name: "Ilość produktu"},
        {form_name: "ean", visible_name: "Kod ean produktu"},
        {form_name: "net_weight", visible_name: "Masa netto całkowita"},
        {form_name: "unit_weight", visible_name: "Masa netto jednostki"},
        {form_name: "img", visible_name: "Zdjęcie produktu"},
    ];

    function addProduct(ev) {

        let data = new FormData(ev.target);
        axiosClient.post('/products', data)
            .then(({}) => {
            })
            .catch(err => {
                const response = err.response;
                if (response && response.status === 422) {
                    setErrors(response.data.errors);
                    window.scrollTo(0, 0);
                }
            })
        ;
        ev.preventDefault();
    }

    return (
        <div>
            {errors && <div className="alert">
                {Object.keys(errors).map(key => (
                    <p key={key}>{errors[key][0]}</p>
                ))}
            </div>}
            <form onSubmit={addProduct} method={"post"}>
                {productData.map((data, index) => (
                    <div key={index}>
                        <label htmlFor={data.form_name}>{data.visible_name}</label>
                        <input type="text" id={data.form_name} name={data.form_name}/>
                    </div>
                ))}
                <button className={"btn btn-primary"} id="submit_button">Dodaj produkt</button>
            </form>
        </div>
    )
}
