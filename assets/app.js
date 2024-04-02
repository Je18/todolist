/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
// start the Stimulus application
import './bootstrap';


const line = document.getElementsByClassName("line");
Array.from(line).forEach(element => {
    const id = element.querySelector(".id");
    const done = element.querySelector(".done");
    done.addEventListener('click', (event) => {
        event.preventDefault();
        let url = new URL("https://127.0.0.1:8000/todo/update/" + id.textContent);
        fetch(url, {
            method: "GET",
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            if(data.NewStatus == true) {
                done.textContent = "Fait"
                alert("Todo mis à jour (Nouveau status : FAIT)");
            } else {
                done.textContent = "A faire"
                alert("Todo mis à jour (Nouveau status : A FAIRE)");
            }
        })
    });
})

const search = document.querySelector('#search');
search.addEventListener('keyup', () => {
    let url = new URL("https://127.0.0.1:8000/todo/search");
    fetch(url, {
        method: "POST",
        body: JSON.stringify({"terms": search.value})
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        console.log(data);
    })
})