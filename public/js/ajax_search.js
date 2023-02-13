let valuespe = document.querySelector(".setPro");
let entities = document.querySelector("#entitiesNav");

let valueSpeWidth = valuespe.clientWidth;

entities.style.display = "block";

// if( document.querySelector('.listSearch').value.length != 0)[
// document.querySelector('.listSearch').style.display = "flex"
// document.querySelector('.listSearch').style.flexDirection = "column"
// ]
// else {
//   document.querySelector('.listSearch').style.display = "none";
// }
function valueSpe(value, id) {
  value = value.replace(/MldPpshhqk/g, " ");
  //  let valeur = idSp[i];

  valuespe.setAttribute("value", id);
  valuespe.value = value;

  document.querySelector(".listSearch").style.display = "none";
}

jQuery(document).ready(function () {
  var searchRequest = null;
  $("#search").keyup(function () {
    var minlength = 3;
    var maxlength = 30;
    var that = this;
    let value = $(this).val();
    let urllll = document.querySelector("#ajax_search").dataset.test;
    var entitySelector = $("#entitiesNav").html("");
    console.log(urllll);
    if (value.length >= minlength && value.length <= maxlength  )  {
      if (searchRequest != null) searchRequest.abort();
      searchRequest = $.ajax({
        type: "GET",
        url: urllll,
        data: {
          q: value,
        },
        dataType: "text",
        success: function (msg) {
          $(".listSearch").width(valueSpeWidth);

          document.querySelector(".listSearch").style.display = "block";
          //we need to check if the value is the same
          if (value == $(that).val()) {
            var result = JSON.parse(msg);

            $.each(result, function (key, arr) {
              $.each(arr, function (id, value) {
                if (key == "entities") {
                  if (id != "error") {
                    //  document.querySelector("#entitiesNav").innerHTML = '<li><a onclick=valueSpe('+id+','+arr[6]+')>'+arr[6]+'</a></li>';
                    entitySelector.append(
                      '<li class="childEl"><a style="color:black;" href="/Produit/' + id + '">' + value + "</a></li>"
                    );

                  
                  } else {
                    entitySelector.append(
                      '<li class="errorLi">' + value + "</li>"
                    );
                  }
                }
              });
            });
            let liste = document.querySelector("#entitiesNav");
            let elementChild = document.querySelectorAll(".childEl");
            
            if (liste.childElementCount > 10) {
                // console.log(elementChild.length)
                for (let i = 0;  i < elementChild.length; i++) {
                    
                    if (i > 10) {
                        // console.log(liste.childElementCount);
                        elementChild[i].remove();
                }
              }
            }
          }
        },
      });
    }
  });
});