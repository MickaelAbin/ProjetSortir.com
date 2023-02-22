function CompteRebours() {
    let date_actuelle = new Date();//date du jour
    // var annee = date_actuelle.getFullYear();
    let dateSortie = document.getElementById('dateDebut');
    dateSortie = new Date(dateSortie.value);

    let tps_restant = (dateSortie.getTime()-date_actuelle.getTime())/1000;

    //========= Conversions

     let s_restantes = tps_restant;
    let i_resstantes = tps_restant / 60;
    let H_restantes = i_resstantes / 60;
    let d_restants = H_restantes / 24;

    s_restantes = Math.floor(s_restantes % 60);
    i_resstantes = Math.floor(i_resstantes % 60);
    H_restantes = Math.floor(H_restantes % 24);
    d_restants = Math.floor(d_restants);

    let compteur = "Il reste" + d_restants + "J/ " + H_restantes + "h:" + i_resstantes + "mn:" + s_restantes + "s avant la fin de l'inscription.";

    document.getElementById("affichage").innerText = compteur;

}
    document.addEventListener('DOMContentLoaded', function () {
        setInterval(CompteRebours, 1000);
    });


