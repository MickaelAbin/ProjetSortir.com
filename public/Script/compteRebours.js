function CompteRebours()
{
    var date_actuelle = new Date();//date du jour
    var annee = date_actuelle.getFullYear();
    let dateSortie = document.getElementById(dateDebut);

    var tps_restant = dateSortie.getTime()-date_actuelle.getTime();

    //========= Conversions

    var s_restantes = tps_restant/1000;
    var i_resstantes = tps_restant/60;
    var H_restantes = i_resstantes/60;
    var d_restants = H_restantes/24;

    s_restantes = Math.floor(s_restantes % 60);
    i_resstantes=Math.floor(i_resstantes % 60);
    H_restantes=Math.floor(H_restantes % 24);
    d_restants=Math.floor(d_restants);

    texte+="Il reste" + d_restants+ "J/ " + H_restantes+ "H/ "+i_resstantes+ "mn/" + s_restantes + "s avant la fin de l'inscription.";

    document.getElementById("affichage").innerHTML = texte;
}

setInterval(CompteRebours, 1000);