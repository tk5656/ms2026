// Swiper初期化
const swiper = new Swiper(".swiper", {
    centeredSlides: true,
    loop: true,
    slidesPerView: 1, // 1つのスライドを全画面表示
    spaceBetween: 0, // スライド間の余白
    loopAdditionalSlides: 2,
    // Navigation button
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});