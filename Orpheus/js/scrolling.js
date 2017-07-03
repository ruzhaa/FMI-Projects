$('a.page-scroll').bind('click', function(event) {
    var $this = $(this);
    $('html, body').stop().animate({
        scrollTop: ($($this.attr('href')).offset().top)
    }, 1500);
    event.preventDefault();
});