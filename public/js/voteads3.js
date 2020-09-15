$(document).ready(function() {
    let VoteAds3 = [
      '<a href="https://runead.com/ads" rel="nofollow" target="_self"><img alt="RuneAd" class="responsive-ad" src="https://runead.com/public/img/banners/banner728.png"></a>',
   ];

    let AdVotePage3 = $('#AdVotePage3');
    let random = Math.floor(Math.random() * (VoteAds3.length - 0)) + 0;
    AdVotePage3.html(VoteAds3[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (VoteAds3.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            AdVotePage3.html(VoteAds3[random]);
        })
    }, 6500);
    console.log(random);
});
