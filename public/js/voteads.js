$(document).ready(function() {
    let VoteAds = [
    '<a href="https://azura-rsps.com" target="_blank"><img alt="Azura RSPS" class="responsive-ad" src="https://media.discordapp.net/attachments/708243799448485909/730167768132878456/728x90_with_Effect.gif"></a>',
    '<a href="https://discord.gg/vmyHKy4" target="_blank"><img alt="Turmoil" class="responsive-ad" src="https://cdn.discordapp.com/attachments/702213830360563783/712411383152771113/728x90.gif"></a>',
   ];

    let AdVotePage = $('#AdVotePage');
    let random = Math.floor(Math.random() * (VoteAds.length - 0)) + 0;
    AdVotePage.html(VoteAds[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (VoteAds.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            AdVotePage.html(VoteAds[random]);
        })
    }, 6500);
    console.log(random);
});
