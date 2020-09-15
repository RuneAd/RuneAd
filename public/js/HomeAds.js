$(document).ready(function() {
    let Servers = [
      '<a href="http://nefariouspkz.com/?ref=runead" rel="sponsored" target="_blank"><img class="responsive-ad" src="https://runead.com/public/img/banners/apache.gif"></a>',

    ];

    let HomeAds = $('#HomeAds');

    let random = Math.floor(Math.random() * (Servers.length - 0)) + 0;

    HomeAds.html(Servers[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (Servers.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            HomeAds.html(Servers[random]);
        })
    }, 6500);

    console.log(random);

});
