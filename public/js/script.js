// function myFunction() {
//     $(".alert")
//         .delay(3000)
//         .hide(400);
// }


// let valueQty = document.querySelector("value" , id)

// $('.carousel').carousel()


// <---------------------------------------Multi Carousel home-------------------------------------------------------->

$('#recipeCarousel').carousel({
    interval: 7000
})

$('.carousel .carousel-item').each(function() {
    var minPerSlide = 4;
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));

    for (var i = 0; i < minPerSlide; i++) {
        next = next.next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));
    }
});



$('#recipeCarousel2').carousel({
    interval: 7000
})

$('.carousel2 .carousel2-item').each(function() {
    var minPerSlide = 4;
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));

    for (var i = 0; i < minPerSlide; i++) {
        next = next.next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));
    }
});




// <-----------------------------------------------------Contact Form------------------------------------------------------>


// $('.custom-file-input').on('change', function(event) {
//     var inputFile = event.currentTarget;
//     $(inputFile).parent()
//         .find('.custom-file-label')
//         .html(inputFile.files[0].name);
// });