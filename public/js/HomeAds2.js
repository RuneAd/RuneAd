$(document).ready(function() {
    let Servers2 = [
        '<a href="https://runead.com/ads" rel="nofollow" target="_self"><img alt="RuneAd" class="responsive-ad" src="https://runead.com/public/img/banners/banner728.png"></a>',

    ];

    let HomeAds2 = $('#HomeAds2');

    let random = Math.floor(Math.random() * (Servers2.length - 0)) + 0;

    HomeAds2.html(Servers2[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (Servers2.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            HomeAds2.html(Servers2[random]);
        })
    }, 6500);

    console.log(random);

});
