
let section = document.getElementsByTagName("section")[0];

let jour = (Date.parse(dateSortie.value) - Date.now())/1000/60/60/24;

jour = Math.round(jour);

if (jour >= 0){

    let h2 = document.createElement("h2");
    h2.innerText = "Le matin";
    section.appendChild(h2);

    let article = document.createElement("article");
    article.setAttribute("id", "matin");
    section.appendChild(article);

    h2 = document.createElement("h2");
    h2.innerText = "Le midi";
    section.appendChild(h2);

    article = document.createElement("article");
    article.setAttribute("id", "midi");
    section.appendChild(article);

    h2 = document.createElement("h2");
    h2.innerText = "Le soir";
    section.appendChild(h2);

    article = document.createElement("article");
    article.setAttribute("id", "soir");
    section.appendChild(article);


    let dateSortie = document.getElementById("dateSortie");
    let longitude = document.getElementById("longitude");
    let latitude = document.getElementById("latitude");

    fetch(`https://api.meteo-concept.com/api/forecast/daily/${jour}/periods?token=${meteoAccessToken}&latlng=${latitude.value},${longitude.value}`, { method: 'GET'})
        .then((reponse) => reponse.json())
        .then((json) => {
            let matin = document.getElementById("matin");
            let midi = document.getElementById("midi");
            let soir = document.getElementById("soir");
            let periode = [matin,midi,soir];

            let forecast = json.forecast;

            console.log(forecast);

            for (let i = 1; i < 4; i++){

                let temps = document.createElement("p");
                let temperature = document.createElement("p");
                let pluivio = document.createElement("p");
                let probPluie = document.createElement("p");
                let probGele = document.createElement("p");
                let probBrouillard = document.createElement("p");

                temps.innerText = "temps : " + tabCodeTemps[forecast[i].weather];
                temperature.innerText = "Température : " + forecast[i].temp2m + "degré";
                probPluie.innerText = "Probabilité de pluie : " + forecast[i].probarain + "%";
                pluivio.innerText = "Cumul de pluie sur la journée : " + forecast[i].rr10 + "mm";
                probBrouillard.innerText = "Probabilité de brouillard : " + forecast[i].probafog + "%";
                probGele.innerText = "Probabilité de gelée : " + forecast[i].probafrost + "%";
                periode[i-1].appendChild(temps);
                periode[i-1].appendChild(temperature);
                periode[i-1].appendChild(probPluie);
                periode[i-1].appendChild(pluivio);
                periode[i-1].appendChild(probBrouillard);
                periode[i-1].appendChild(probGele);
            }

        });

}else {

    let h2 = document.createElement("h2");
    h2.innerText = "La météo est indsiponible, la sortie est passée";
    section.appendChild(h2);

}