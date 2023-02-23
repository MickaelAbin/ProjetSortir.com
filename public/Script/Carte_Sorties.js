
let donnees = [];

let query = fetch('/sortie/donnees')
    .then((reponse) => reponse.json())
    .then((json) => {
        for (const coord of json) {
            donnees.push(coord);
        }
    });

let mapCenter = [
    -1,
    47
];

let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v12',
    center: mapCenter, // starting position
    zoom: 5.5
});


map.on('load', () => {

    // Add starting point to the map

    for (const point of donnees) {
        map.addLayer({
            id: point.nom,
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
                                coordinates: point.coord
                            }
                        }
                    ]
                }
            },
            paint: {
                'circle-radius': 6,
                'circle-color': '#485fc7'
            }
        });
    }

});