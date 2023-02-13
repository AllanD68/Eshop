// function less(){
//     let elem = $('.dec').next('input[type=number]');
//     let qty = elem.val();
//     //stocker le prix unitaire
//     //stocker ID

//     if(qty > 1){
//         qty--;
//         elem.val(qty);
//         //calculer le nouveau montant du prix et l'afficher
//     }

//     $.ajax({
//         url:'/panier/edit_remove',
//         // data: {
//         //     id: //Passer l'id
//         // }
//     }).done(function(data){
//         console.log(data)
//     })
// }


// function more(){
//     let elem = $('.inc').next('input[type=number]');
//     let qty = elem.val();
//     //stocker le prix unitaire
//     //stocker ID

//     if(qty > 1){
//         qty--;
//         elem.val(qty);
//         //calculer le nouveau montant du prix et l'afficher
//     }

//     $.ajax({
//         url:'/panier/edit_add',
//         // data: {
//         //     id: //Passer l'id
//         // }
//     }).done(function(data){
//         console.log(data)
//     })
// }