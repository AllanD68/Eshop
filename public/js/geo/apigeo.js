$(document).ready(function() {
    //on stocke dans une constante l'url qui permet d'envoyer la requÃªte vers l'API
    const apiUrl = 'https://geo.api.gouv.fr/communes?codePostal=';
    //format auquel on veut rÃ©cupÃ©rer les donnÃ©es
    const format = '&format=json';

    //On stocke dans des variables (#...=id en html)
    let cp = $('#cp');
    let ville = $('#ville');
    let departement = $('#departement');
    //idem pour les messages d'erreur
    let errorMessage = $('#errorMessage');

    console.log(cp);

    //On Ã©coute l'Ã©vÃ©nement lorsqu'on quitte le champ cp
    $(cp).on('blur', function() {
        //la variable code reÃ§oit la valeur du champ cp
        let code = $(this).val();
        //on envoie une requÃªte Ã  l'api que s'il y a 5 chiffres min
        if (code.length >= 5) {
            console.log(code);
            let url = apiUrl + code + format;
            //         // console.log(url); //permet de vÃ©rifier si c'est la bonne url et si Ã§a fonctionne

            //on envoie la requÃªte vers l'API
            fetch(url, { method: 'get' }).then(response => response.json()).then(results => {
                //console.log(results);
                //si on fait appel Ã  l'api, on trouve tous les champs option de ville et on les supprime
                $(ville).find('option').remove();
                //on remplit s'il y a des rÃ©sultats
                //1. on vÃ©rifie s'il y a des rÃ©sultats envoyÃ©s par l'api
                if (results.length) {
                    $(errorMessage).text('').hide();
                    //s'il y a des rÃ©sultats, on boucle dessus

                    $("#cp").empty();

                    $.each(results, function(key, value) {
                        //console.log(value);
                        //console.warn(value.nom); // rÃ©cupÃ¨re juste le nom
                        // $("#cp").empty().append("<a>"+value.nom+"</a>");
                        //pour chaque rÃ©sultat on rÃ©cupÃ¨re le nom
                        //$(ville).val(value.nom);
                        $(ville).append('<option value="' + value.nom + '">' + value.nom + '</option>');
                        console.log(ville)
                            // $("#cp").append("<a>"+value.nom+"</a><br/>");
                    });
                }
                //s'il n'y a pas de rÃ©sultats
                else {
                    //on vÃ©rifie que le cp contient bien une valeur
                    if ($(cp).val()) {
                        console.log('Erreur de code postal');
                        $('#cp').empty();
                        $(errorMessage).text('Aucune commune avec ce code postal').show();
                    } else {
                        $(errorMessage).text('').hide();
                    }

                }

            }).catch(err => {
                console.log(err);
                $(ville).find('option').remove();
            });
        }

    });

    $('#cp').on('click', 'a', function() {
        $(ville).val($(this).text());
        $('#cp').empty();
    });

    // if('#search_cp' = '#search_ville'){
    //     $(errorMessage).text('Le code postal et la ville ne correspondent pas').show();
    // if('#cp' == '#ville'){
    //     $(errorMessage).text('Tout va bien').show();    

    // };
})