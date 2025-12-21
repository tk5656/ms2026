<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>キャラクター・公約紹介｜ミライレ</title>
    <!-- destyle CSS -->
    <link rel="stylesheet" href="css/destyle.css">
    <!-- Adobe fonts -->
    <script src="./js/Adobe_fonts.js"></script>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/b7038e28dd.js" crossorigin="anonymous"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/page/character/character.css?v=<?php echo time(); ?>">
    <!-- favicon -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
</head>  
<body>
    <div id="container">
        <header>
            <div class="header-inner">
                <h1>
                    <a href="./index.php"><img class="logo rotate_10" src="./images/header_logo.svg" alt="ミライレのロゴ"></a>
                </h1>
                <nav>
                    <ul>
                        <li><a class="marker" href="index.php#about">About<span>本展示について</span></a></li>
                        <li><a class="marker" href="">News<span>最新情報</span></a></li>
                        <li><a class="marker" href="./character.php">Chara<span>公約・キャラクター</span></a></li>
                    </ul>
                </nav>
                <p class="vote">
                    <a href="./vote.php"><img class="vote-img shake" src="./images/menu_box.png" alt="投票箱"></a>
                </p>
            </div>
        </header>
        <main>
            <!-- Swiper -->
            <div class="swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="slide_item slide_item1">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item2">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item3">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item4">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item5">
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
            <div class="footer-inner">
                <div class="footer-logo">
                    <img class="logo" src="./images/hooter_logo.png" alt="">
                </div>
                <div class="footer-nav">
                    <ul>
                        <li><a class="underline" href="index.php#about">本展示について</a></li>
                        <li><a class="underline" href="character.php">公約・キャラクター</a></li>
                        <li><a class="underline" href="">最新情報</a></li>
                    </ul>
                </div>
                <div class="sns-link">
                    <img class="sns" src="./images/hooter_SNS.png" alt="">
                </div>
            </div>
        </footer>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- JS -->
    <script src="./js/swiper_character.js"></script>

</body>
</html>