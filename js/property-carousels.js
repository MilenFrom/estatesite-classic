jQuery(document).ready(function ($) {
    const parseBool = (str) => str === 'true';

    const initializeCarousel = (selector, token, obj) => {
        if (!obj) {
            console.warn(`Carousel data not found for token: ${token}`);
            return;
        }

        const slides_to_show = parseInt(obj.slides_to_show);
        const slides_to_scroll = parseInt(obj.slides_to_scroll);
        const navigation = parseBool(obj.navigation);
        const auto_play = parseBool(obj.slide_auto);
        const auto_play_speed = parseInt(obj.auto_speed);
        const slide_infinite = parseBool(obj.slide_infinite);
        const dots = parseBool(obj.slide_dots);
        const houzez_rtl = houzez_vars.houzez_rtl === 'yes';

        const carousel = $(`#${selector}-${token}`);

        if (!carousel.length) {
            console.warn(`Carousel element not found: ${selector}-${token}`);
            return;
        }

        // Store arrow references before potentially destroying the carousel
        const prevArrow = $(`.slick-prev-js-${token}`);
        const nextArrow = $(`.slick-next-js-${token}`);

        // Check if slick is already initialized and destroy it if needed
        if (carousel.hasClass('slick-initialized')) {
            carousel.slick('unslick');
        }

        const slickConfig = {
            rtl: houzez_rtl,
            lazyLoad: 'ondemand',
            infinite: slide_infinite,
            autoplay: auto_play,
            autoplaySpeed: auto_play_speed,
            speed: 500,
            slidesToShow: slides_to_show,
            slidesToScroll: slides_to_scroll,
            arrows: navigation,
            adaptiveHeight: true,
            dots: dots,
            appendArrows: `.houzez-carousel-arrows-${token}`,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                    },
                },
                {
                    breakpoint: 769,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    },
                },
            ],
        };

        if (navigation && prevArrow.length && nextArrow.length) {
            slickConfig.prevArrow = prevArrow;
            slickConfig.nextArrow = nextArrow;
        }

        try {
            carousel.slick(slickConfig);
        } catch (error) {
            console.error(
                `Error initializing carousel ${selector}-${token}:`,
                error
            );
        }
    };

    // Initialize property carousels
    $('.houzez-properties-carousel-js[id^="houzez-properties-carousel-"]').each(
        function () {
            const $div = $(this);
            const token = $div.data('token');
            const obj = window[`houzez_prop_carousel_${token}`];
            initializeCarousel('houzez-properties-carousel', token, obj);
        }
    );

    // Initialize regular carousels
    $('.houzez-carousel-js[id^="houzez-carousel-"]').each(function () {
        const $div = $(this);
        const token = $div.data('token');
        const obj = window[`houzez_caoursel_${token}`];
        initializeCarousel('houzez-carousel', token, obj);
    });
});
