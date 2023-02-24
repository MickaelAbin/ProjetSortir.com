
let mapCenter = [
    longitude.value !== '' ? longitude.value : -1.5,
    latitude.value !== '' ? latitude.value : 47.3
];

let point = mapCenter;

let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v12',
    center: mapCenter, // starting position
    zoom: 7
});

// create a function to make a directions request
async function getRoute() {
    // make a directions request using cycling profile
    // an arbitrary start will always be the same
    // only the end or destination will change
    const query = await fetch(
        `https://api.mapbox.com/directions/v5/mapbox/cycling/${start[0]},${start[1]}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`,
        { method: 'GET' }
    );
    const json = await query.json();
}

map.on('load', () => {
    // make an initial directions request that
    // starts and ends at the same location
    getRoute();

    // Add starting point to the map
    map.addLayer({
        id: 'point',
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
                            coordinates: point
                        }
                    }
                ]
            }
        },
        paint: {
            'circle-radius': 5,
            'circle-color': '#3887be'
        }
    });

    map.on('click', (event) => {
        const coords = Object.keys(event.lngLat).map((key) => event.lngLat[key]);
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

        longitude.value = coords[0];
        latitude.value = coords[1];

        fetch('https://api-adresse.data.gouv.fr/reverse/?lon='+coords['0']+'&lat='+coords['1'])
            .then((reponse) => reponse.json())
            .then((json) => {
                console.log(json);
                inputAdresse.value = json.features[0].properties.name;
                inputCodePostal.value = json.features[0].properties.postcode;
            });
    });

});