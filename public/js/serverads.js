$(document).ready(function() {
    let Ads = [
      '<a href="https://discord.gg/vmyHKy4" target="_blank"><img alt="Turmoil" class="responsive-ad" src="https://cdn.discordapp.com/attachments/702213830360563783/712411383152771113/728x90.gif"></a>',
      '<a href="http://sinhaza.com/forums/index.php?/topic/16-welcome-to-sinhaza/" target="_blank"><img alt="Shinhaza" class="responsive-ad" src="https://cdn.discordapp.com/attachments/710641741258293289/713072012532383855/728x90.gif"></a>',
      '<a href="https://discord.gg/kf5AjNw" target="_blank"><img alt="SnowPS" class="responsive-ad" src="https://cdn.discordapp.com/attachments/698524813437960242/701602535861125180/BannerNew.gif"></a>',
    ];

    let AdBanner = $('#AdBanner');

    let random = Math.floor(Math.random() * (Ads.length - 0)) + 0;

    AdBanner.html(Ads[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (Ads.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            AdBanner.html(Ads[random]);
        })
    }, 6500);

    console.log(random);

});
