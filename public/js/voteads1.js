$(document).ready(function() {
    let VoteAds1 = [
      '<a href="http://nefariouspkz.com/" target="_self"><img alt="nefariouspkz" class="responsive-ad" src="https://cdn.discordapp.com/attachments/748349075287441478/749007417035063406/image0.gif"></a>',
   ];

    let AdVotePage1 = $('#AdVotePage1');
    let random = Math.floor(Math.random() * (VoteAds1.length - 0)) + 0;
    AdVotePage1.html(VoteAds1[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (VoteAds1.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            AdVotePage1.html(VoteAds1[random]);
        })
    }, 6500);
    console.log(random);
});
