$(function() {
    $('#slideshow img:gt(0)').hide();
    setInterval(function() {
    $('#slideshow :first-child')
    .fadeOut(500)
    .next('img')
    .fadeIn(500)
    .end()
    .appendTo('#slideshow');
    }, 7000);
    });