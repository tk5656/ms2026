<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投票する</title>
    <!-- destyle CSS -->
    <link rel="stylesheet" href="css/destyle.css">
    <!-- Adobe fonts -->
    <script src="./js/Adobe_fonts.js"></script>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/b7038e28dd.js" crossorigin="anonymous"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/page/vote/vote.css">
</head>
<body>

    <div id="container">
        <header>
            <h1>
                <img class="logo" src="./images/header_logo.svg" alt="ミライレのロゴ">
            </h1>
            <nav>
                <ul>
                    <li><a href="./index.php">About<span>本展示について</span></a></li>
                    <li><a href="">News<span>最新情報</span></a></li>
                    <li><a href="./character.html">Chara<span>公約・キャラクター</span></a></li>
                </ul>
            </nav>
            <p class="vote">
                <a href=""><img class="vote-img" src="./images/header_box.png" alt="投票箱"></a>
            </p>
        </header>
        <main>
            <!-- Swiper -->
            <div class="swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="slide_item slide_item1">
                            <img src="./images/vote/isometric1.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>雪原ペン次</span></p>
                                <button class="btn_mainColor">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item2">
                            <img src="./images/vote/isometric2.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>虹宮ニョロ明</span></p>
                                <button class="btn_mainColor">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item3">
                            <img src="./images/vote/isometric3.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>鳥谷コケ蔵</span></p>
                                <button class="btn_mainColor">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item4">
                            <img src="./images/vote/isometric4.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>犬山イチ郎</span></p>
                                <button class="btn_mainColor">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item5">
                            <img src="./images/vote/isometric5.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>猫川ニャミ子</span></p>
                                <button class="btn_mainColor">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                </div><!-- /swiper-wrapper -->  
                <!-- navigation buttons --> 
                <div class="btn_wrapper">
                    <div class="swiper-button-prev opacity_up"><i class="fa-solid fa-caret-left"></i></div>
                    <div class="swiper-button-next opacity_up"><i class="fa-solid fa-caret-right"></i></div>
                </div>
            </div><!-- /swiper -->
        </main>
        <footer>
            <div class="footer-logo">
                <img class="logo" src="./images/hooter_logo.png" alt="">
            </div>
            <ul>
                <li><a href="./index.php">本展示について</a></li>
                <li><a href="./character.html">公約・キャラクター</a></li>
                <li><a href="">最新情報</a></li>
            </ul>
            <div class="sns-link">
                <img class="sns" src="./images/hooter_SNS.png" alt="">
            </div>
        </footer>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- JS -->
    <script src="./js/swiper_vote.js"></script>

</body>
</html>