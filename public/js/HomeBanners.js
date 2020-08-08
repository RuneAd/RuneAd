$(document).ready(function() {
    let Servers = [
      '<a href="https://discord.gg/vmyHKy4" target="_blank"><img alt="RuneAd Ad network" class="responsive-ad" src="https://i.imgur.com/PMtnbjR.png"></a>',

    ];

    let HomeBanner = $('#HomeBanner');

    let random = Math.floor(Math.random() * (Servers.length - 0)) + 0;

    HomeBanner.html(Servers[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (Servers.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            HomeBanner.html(Servers[random]);
        })
    }, 6500);

    console.log(random);

});
