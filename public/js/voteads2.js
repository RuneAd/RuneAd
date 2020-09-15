$(document).ready(function() {
    let VoteAds2 = [
      '<a href="https://runead.com/ads" rel="nofollow" target="_self"><img alt="RuneAd" class="responsive-ad" src="https://runead.com/public/img/banners/banner728.png"></a>',
   ];

    let AdVotePage2 = $('#AdVotePage2');
    let random = Math.floor(Math.random() * (VoteAds2.length - 0)) + 0;
    AdVotePage2.html(VoteAds2[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (VoteAds2.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            $(this).fadeIn();
            AdVotePage2.html(VoteAds2[random]);
        })
    }, 6500);
    console.log(random);
});
