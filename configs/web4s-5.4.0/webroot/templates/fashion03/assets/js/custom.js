 $(".btn-a").click(function() {
    $('.content-category').toggleClass('transform-active');
    if(!$('.content-category').hasClass('transform-active')){
        $('html, body').animate({
            scrollTop: $('#content-block').offset().top - 85
        }, 'slow');
    }
});

$(document).ready(function() {

	$('.counter-value').each(function() {
	  var $this = $(this),
		countTo = $this.attr('data-count');
	  $({
		countNum: $this.text()
	  }).animate({
		  countNum: countTo
		},

		{

		  duration: 2000,
		  easing: 'swing',
		  step: function() {
			$this.text(Math.floor(this.countNum));
		  },
		  complete: function() {
			$this.text(this.countNum);
			//alert('finished');
		  }

		});
	});
});
