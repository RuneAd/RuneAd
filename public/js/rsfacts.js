$(document).ready(function() {
    let facts = [
        "Runescape was created by Andrew Gower and Paul Gower.",
        "Vannaka has a 2 hand sword in one have and a DRAGON shield in another",
        "Fastest time you will get fish is one per 2 secs",
        "\"Gielinor\" is an anagram for \"religion\". Weird right?",
        "OldSchool Runescape was released February 22, 2013",
        "In the song \"Norse Code,\" a flute plays in the beginning. The tune the flute is playing is actually the word \"RuneScape\" in Morse code",
        "After being in a monsters territory and killing them for a certain amount of time, they will no longer be aggressive.",
        "When searching bails of hay or piles of hay, it is possible to find a needle.",
        "The cabbage in Draynor Manson tastes better than normal ones, also it gives a +1 defense bonus",
        "Tutorial Island was released on September 24, 2002",
        "Dueling was added on March 25, 2004",
        "When you use raw herring on a tree, it says \"This is not the mightiest tree in the forest.\"",
        "Magic and arrows follow you if you run",
        "When you first log in, you always face the same direction (South)",
        "Wizard skirts do not give any def bonus",
        "The Duelling Arena hospital room doesn't have any glass in the window",
        "Darts are the fastest weapon in the game",
        "When you high alch while your cannon is spinning, it stops spinning until you finish alching",
        "When you eat an onion it says, \"It's always sad to see a grown man/woman cry\"",
        "Your character complains a lot during member quests",
        "Chickens cannot kill you, no matter what, except the Evil Chicken",
        "When you examine a maple tree it says I bet this will make good syrup",
        "There not a single proper toilet in Runescape",
        "If you examine a snake somewhere it says, \"Snake? Snaaaaaaaaaaaaaaaaaaake!\" - Reference to Metal Gear Solid.",
        "There are a lot of Welsh words in the game, mostly from the Elf area/quests.",
        "The goddess Seren, her name, Seren means Star",
        "Prifddinas, means Main City",
        "Arianwyn's name means White Silver",
        "Slayer and Prayer are the only two skills that rhyme",
        "Neitiznot is a German/English mixture for \"No it's not\" or \"Nay, 'tis not\".",
        "Jatizso is German/English mixture for \"Ya 'tis so\" or \"Yes it is\"",
        "The four levels of the Stronghold of Security were based on the Four Horsemen of the Apocalypse. They were known as Pestilence, Famine, War, and Death, which are the names of the levels",
        "When you examine the Penace Queen, it says \"Run away! Run away!\" this is reference to Monty Python in the Holy Grail sketch when the Knights are being attacked by a giant bunny",
        "Highwayman Rick Turpentine is a parody of Dick Turpin, a real-life highwayman from the early 18th century",
        "Use a Herring on the entrance door to the Grand Tree, and it says 'It cannot be done.'",
        "The Dancing Knights in the Falador Party Room are a reference to Monty Python & The Holy Grail",
        "When you ask Sir Amik Varze for your quest, he will tell you: \"Your Mission, if you decide to accept it\", and when he gives you the dossier it self-destructs, reference to Mission: Impossible",
        "Vinesweeper: The house in the farm is similar to that of the Hobbit holes in The Lord of the Rings",
        "If you take all the first letters from the wilderness cape merchant's names it will spell the word \"Wilderness\".",
        "After an update on 28 February 2019 there was a Twisted Bow spawn, which was hot fixed in 32 minutes!",
        "Mahogany Homes were released on 26 August 2020!",
        "The Rune scimitar was released on 13 August 2001!",
        "The Dragon scimitar was released on 29 March 2005, about 4 YEARS after the Rune scimitar!",
        "Monkey Madness I was released on 6 December 2004!",
        "Leagues II - Trailblazer was released on 28 October 2020",

    ];

    let factNum = $('#factNum');
    let factTxt = $('#factTxt');

    let random = Math.floor(Math.random() * (facts.length - 0)) + 0;

    factNum.html(random);
    factTxt.html(facts[random]);

    setInterval(function() {
        random = Math.floor(Math.random() * (facts.length - 0)) + 0;
        $('#factBox div').fadeOut(function() {
            factNum.html(random+1);
            $(this).fadeIn();
            factTxt.html(facts[random]);
        })
    }, 6500);

    console.log(random);

});
