$(document).ready(function() {
    let Servers = [
      '<a href="http://nefariouspkz.com/" target="_self"><img alt="nefariouspkz" class="responsive-ad" src="https://cdn.discordapp.com/attachments/748349075287441478/749007417035063406/image0.gif"></a>',

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
