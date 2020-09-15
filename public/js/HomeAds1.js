$(document).ready(function() {
    let Servers = [
        '<a href="https://runead.com/ads" rel="nofollow" target="_self"><img alt="RuneAd" class="responsive-ad" src="https://runead.com/public/img/banners/banner468.png"></a>',

    ];

    let HomeAds1 = $('#HomeAds1');

    let random = Math.floor(Math.random() * (Servers.length - 0)) + 0;

    HomeAds1.html(Servers[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (Servers.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            HomeAds1.html(Servers[random]);
        })
    }, 6500);

    console.log(random);

});
