$(document).ready(function() {
    var banners = [];
    var index = 0;
    banners[1] = '<div class="banners banner1">https://cdn.discordapp.com/attachments/722931780105011260/757807184229826560/728x90.gif</div>';
    banners[2] = '<div class="banners banner2">https://cdn.discordapp.com/attachments/722931780105011260/757807298222489610/Comp_5.gif</div>';
    index = Math.floor(Math.random() * banners.length);
    $("#banner").html(banners[index]);
   });