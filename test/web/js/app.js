console.log('ok');

$('#btnTest').on('click', function() {
    $(this).next('ul').toggle();

        //$('#popup').css('opacity', 1); //non animée
        //$('#popup').css('width', 200); //non animée
        $('#popup').animate({
            opacity: 1, //premier argument _objet
            width: 200,
            height: 200

        }, 500, 'ease-out'); //2ieme argument, sur quel laps de temps

});