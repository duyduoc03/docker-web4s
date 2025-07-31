 $(".btn-a").click(function() {
    $('.content-category').toggleClass('transform-active');
    if(!$('.content-category').hasClass('transform-active')){
        $('html, body').animate({
            scrollTop: $('#content-block').offset().top - 85
        }, 'slow');
    }
});