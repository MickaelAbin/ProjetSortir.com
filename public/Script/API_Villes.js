let codePostal = document.getElementById('codePostal');

codePostal.addEventListener('change', function(){
    let code = codePostal.value;
    let monSelect = document.getElementById('villes');
    monSelect.innerHTML = '';
    fetch('https://geo.api.gouv.fr/departements/'+code+'/communes')
        .then((reponse) => reponse.json())
        .then((json) => {
            for(const ville of json){
                let monOpt = document.createElement('option');
                monOpt.innerText = ville.nom;
                monOpt.value = ville.nom;
                monSelect.appendChild(monOpt);
            }
        })
});