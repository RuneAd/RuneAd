$(document).ready(function() {
    let VoteAds = [
      '<a href="http://nefariouspkz.com/" target="_self"><img alt="nefariouspkz" class="responsive-ad" src="https://cdn.discordapp.com/attachments/748349075287441478/749007417035063406/image0.gif"></a>',
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
