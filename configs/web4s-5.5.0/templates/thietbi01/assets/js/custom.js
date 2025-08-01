function reloadJs() {
    var elements = [
        {
            selector: '.product_new .height_max',
            mediaQuery: '(min-width: 768px)',
            itemsPerRow: 2
        },
        {
            selector: '.article_news .height_max',
            mediaQuery: '(min-width: 768px)',
            itemsPerRow: 12
        },
        {
            selector: '.page_list_article .article-title',
            mediaQuery: '(min-width: 768px)',
            itemsPerRow: 3
        }
    ];
    
    elements.forEach(function(element) {
        var swiperSlides = $(element.selector);
        swiperSlides.css('height', '');
    
        if (window.matchMedia(element.mediaQuery).matches) {
            swiperSlides.each(function(index) {
                if (index % element.itemsPerRow === 0) {
                    var itemsInRow = swiperSlides.slice(index, index + element.itemsPerRow);
                    var maxHeight = 0;
    
                    itemsInRow.each(function() {
                        var slideHeight = $(this).outerHeight();
                        maxHeight = Math.max(maxHeight, slideHeight);
                    });
    
                    itemsInRow.css('height', maxHeight + 'px');
                }
            });
        }
    });
}

$(document).ready(function() {
    $(window).scroll(function() {
        if ($(this).scrollTop() > 500) {
            $('.setting-menu').addClass('fixed');
        } else {
            $('.setting-menu').removeClass('fixed');
        }
    });
    
    var counted = 0;
    $(window).scroll(function() {
      var $counter = $('#counter');
      
      if ($counter.length) {
        var oTop = $counter.offset().top - window.innerHeight;
        
        if (counted == 0 && $(window).scrollTop() > oTop) {
          $('.count').each(function() {
            var $this = $(this),
                countTo = $this.attr('data-count');
            
            $({ countNum: $this.text() }).animate({ countNum: countTo }, {
              duration: 3000,
              easing: 'swing',
              step: function() {
                $this.text(Math.floor(this.countNum));
              },
              complete: function() {
                $this.text(this.countNum);
              }
            });
          });
          
          counted = 1;
        }
      }
    });
    
     $('.bg-trainghiem').on('mousemove', function(e) {
        var moveX = (window.innerWidth / 2 - e.pageX) / 50;
        var moveY = (window.innerHeight / 2 - e.pageY) / 50;
        $(this).find('.transformOnMouse').css('transform', 'translateX(' + moveX + 'px) translateY(' + moveY + 'px)');
    });
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 800) {
          $('.back-to-top').fadeIn();
        } else {
          $('.back-to-top').fadeOut();
        }
    });
    
    $('.back-to-top').click(function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
    
    reloadJs();
});

var resizeTimer;
$(window).on('resize', function() {
  reloadJs();
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
    reloadJs();
  }, 1000);
});