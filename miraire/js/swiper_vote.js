// Swiper初期化
const mySwiper = new Swiper(".swiper", {
    centeredSlides: true,
    slidesPerView: 1.3,
    spaceBetween: 16,
    loop: true,
    loopAdditionalSlides: 1,
    grabCursor: true,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      600: {
        slidesPerView: 1.8,
        spaceBetween: 32,
      },
      1025: {
        slidesPerView: 2.2,
        spaceBetween: 100,
      }
    },
});