let inputAdresse = document.getElementById('adresse');
let listeAdresse = document.getElementById('listeAdresse');

let inputCodePostal = document.getElementById('codePostal');
let listeCode = document.getElementById('listeCodes');

let inputRecherche = document.getElementById('recherche');

let longitude = document.getElementById('lieu_longitude');
let latitude = document.getElementById('lieu_latitude');


function valeurAPI(){
    let codePostal = inputCodePostal.value.length = 5 ? inputCodePostal.value : '';

    fetch('https://api-adresse.data.gouv.fr/search/?q='+inputAdresse.value+'&postcode='+codePostal)
        .then((reponse) => reponse.json())
        .then((json) => {
            console.log(json);
            for (const lieu of json.features) {
                let optAdresse = document.createElement('option');
                optAdresse.value = lieu.properties.name;
                listeAdresse.appendChild(optAdresse);

                let optCodePostal = document.createElement('option');
                optCodePostal.value = lieu.properties.postcode;
                listeCode.appendChild(optCodePostal);
            }
        });
};

inputAdresse.addEventListener('keyup', valeurAPI);
inputAdresse.addEventListener('click', valeurAPI);

inputCodePostal.addEventListener('keyup', valeurAPI);

inputRecherche.addEventListener('click', function (){
    fetch('https://api-adresse.data.gouv.fr/search/?q='+inputAdresse.value+'&postcode='+inputCodePostal.value)
        .then((reponse) => reponse.json())
        .then((json) => {
            let coords = json.features[0].geometry.coordinates;

            longitude.value = coords[0];
            latitude.value = coords[1];

            point = {
                type: 'FeatureCollection',
                features: [
                    {
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'Point',
                            coordinates: coords
                        }
                    }
                ]
            };

            map.getSource('point').setData(point);

        });
});