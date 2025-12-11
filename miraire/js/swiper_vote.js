// Swiper初期化
const swiper = new Swiper('.swiper', {
    spaceBetween: 80,
    centeredSlides: true,
    loop: true,
    loopAdditionalSlides: 2,
    slidesPerView: 'auto',
    // Navigation button
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});