$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

	$('#toTop').on('click',function (e) {
		e.preventDefault();

		var target = this.hash;
		var $target = $(target);

		$('html, body').stop().animate({
			'scrollTop': 0
		}, 900, 'swing');
    });

    let dropdown = $('.navbar .dropdown');

    dropdown.mouseenter(function() {
        let menu = $(this).find(".dropdown-menu");
        menu.addClass("show");
        $(this).addClass("show");
    });

    dropdown.mouseleave(function() {
        let menu = $(this).find(".dropdown-menu");
        menu.removeClass("show");
        $(this).removeClass("show");
    });

    $('#darkmode').click(function(event) {
        event.preventDefault();

        let cookie = Cookies.get("darkmode");

        if (cookie) {
            Cookies.remove("darkmode");
        } else {
            Cookies.set("darkmode", 1, { expires: 365 });
        }

        window.location.reload();
    });

    $(document).find('img').each(function(index, element) {
        let pc = $(this).closest(".card");
        if (pc) {
            $(this).addClass("img-fluid");
        }
    });

    refresh_handler = function(e) {
        var elements = document.querySelectorAll("img");

        for (var i = 0; i < elements.length; i++) {
            var boundingClientRect = elements[i].getBoundingClientRect();
            var inViewport = boundingClientRect.top < window.innerHeight;

            if (elements[i].hasAttribute("data-src") && inViewport) {
                let src = elements[i].getAttribute("data-src");

                elements[i].setAttribute("src", src);
                elements[i].removeAttribute("data-src");
            }
        }
    };

    window.addEventListener('scroll', refresh_handler);
    window.addEventListener('load', refresh_handler);
    window.addEventListener('resize', refresh_handler);
});
