$(document).ready(function() {
    $('.header__burger').click(function(event) {
        $('.header__burger,.menu,.menu__mobile').toggleClass('active');
        $('body').toggleClass('lock');
    });
});

// OWL CAROUSEL - SLIDER FOR ALL HOSTELS
var owl = $('.owl-carousel');
owl.owlCarousel({
    loop: true,
    margin: 10,
    padding: 10,
    smartSpeed: 1000,
    autoplay: true,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,
    nav: false,
    responsive: {
        310: {
            items: 1
        },
        375: {
            items: 2
        },
        500: {
            items: 2
        },
        768: {
            items: 3
        },
        900: {
            items: 4
        },
        1300: {
            items: 5
        },
        1500: {
            items: 6
        },
        1800: {
            items: 7
        },
        2300: {
            items: 11
        }
    }
});
owl.on('mousewheel', '.owl-stage', function(e) {
    if (e.deltaY > 0) {
        owl.trigger('prev.owl');
    } else {
        owl.trigger('next.owl');
    }
    e.preventDefault();
});

