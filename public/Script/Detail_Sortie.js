
let longitude = document.getElementById('longitude');
let latitude = document.getElementById('latitude');

let ville = document.getElementById('ville');
let adresse = document.getElementById('adresse');
let codePostal = document.getElementById('codePostal');

// let defaut = document.getElementById("defaut");

fetch('https://api-adresse.data.gouv.fr/reverse/?lon=' + longitude.value + '&lat=' + latitude.value)
    .then((reponse) => reponse.json())
    .then((json) => {
        ville.innerText += ' ' + json.features[0].properties.city;
        adresse.innerText += ' ' + json.features[0].properties.name;
        codePostal.innerText += ' ' + json.features[0].properties.postcode;

    });

let mapCenter = [
    parseFloat(longitude.value),
    parseFloat(latitude.value)
];

let end = mapCenter;
let start = mapCenter;

let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v12',
    center: mapCenter, // starting position
    zoom: 12
});

// defaut.addEventListener('click', function (){
//
//     getRoute(start);
//
//     start = {
//         type: 'FeatureCollection',
//         features: [
//             {
//                 type: 'Feature',
//                 properties: {},
//                 geometry: {
//                     type: 'Point',
//                     coordinates: start
//                 }
//             }
//         ]
//     };
//
//     map.getSource("start").setData(start);
// });

// create a function to make a directions request
async function getRoute(start) {
    // make a directions request using cycling profile
    // an arbitrary start will always be the same
    // only the end or destination will change
    const query = await fetch(
        `https://api.mapbox.com/directions/v5/mapbox/cycling/${start[0]},${start[1]};${end[0]},${end[1]}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`,
        {method: 'GET'}
    );
    const json = await query.json();
    const data = json.routes[0];
    const route = data.geometry.coordinates;
    const geojson = {
        type: 'Feature',
        properties: {},
        geometry: {
            type: 'LineString',
            coordinates: route
        }
    };
    // if the route already exists on the map, we'll reset it using setData
    if (map.getSource('route')) {
        map.getSource('route').setData(geojson);
    }
    // otherwise, we'll make a new request
    else {
        map.addLayer({
            id: 'route',
            type: 'line',
            source: {
                type: 'geojson',
                data: geojson
            },
            layout: {
                'line-join': 'round',
                'line-cap': 'round'
            },
            paint: {
                'line-color': '#33d754',
                'line-width': 5,
                'line-opacity': 0.75
            }
        });
    }
    // add turn instructions here at the end
}

map.on('load', () => {
    // make an initial directions request that
    // starts and ends at the same location
    getRoute(start);

    // Add starting point to the map
    map.addLayer({
        id: 'start',
        type: 'circle',
        source: {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: [
                    {
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'Point',
                            coordinates: start
                        }
                    }
                ]
            }
        },
        paint: {
            'circle-radius': 10,
            'circle-color': '#3887be'
        }
    });

    map.addLayer({
        id: 'end',
        type: 'circle',
        source: {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: [
                    {
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'Point',
                            coordinates: end
                        }
                    }
                ]
            }
        },
        paint: {
            'circle-radius': 10,
            'circle-color': '#ef181f'
        }
    });

    getRoute(end);

    map.on('click', (event) => {
        const coords = Object.keys(event.lngLat).map((key) => event.lngLat[key]);
        start = {
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

        map.getSource('start').setData(start);

        getRoute(coords);
    });

});