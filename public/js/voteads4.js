$(document).ready(function() {
    let VoteAds4 = [
      '<a href="https://drinkmaw.com/?rfsn=4679051.fb6bb6e" rel="nofollow" target="_blank"><img alt="Maw Energy" class="responsive-ad" src="https://cdn.discordapp.com/attachments/707676831129665559/756246088150679722/Untitled-5.png"></a>',
   ];

    let AdVotePage4 = $('#AdVotePage4');
    let random = Math.floor(Math.random() * (VoteAds4.length - 0)) + 0;
    AdVotePage4.html(VoteAds4[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (VoteAds4.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            AdVotePage4.html(VoteAds4[random]);
        })
    }, 6500);
    console.log(random);
});
