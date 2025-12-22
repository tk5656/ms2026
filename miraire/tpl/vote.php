<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投票する｜ミライレ</title>
    <!-- destyle CSS -->
    <link rel="stylesheet" href="css/destyle.css">
    <!-- Adobe fonts -->
    <script src="./js/Adobe_fonts.js"></script>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/b7038e28dd.js" crossorigin="anonymous"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css?v=1.0.0">
    <link rel="stylesheet" href="css/page/vote/vote.css?v=1.0.0">
    <!-- favicon -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js" defer></script>
    <!-- JS -->
    <script src="./js/swiper_vote.js" defer></script>
    <script src="./js/vote.js" defer></script>
</head>
<body>

    <div id="container">
        <header>
            <div class="header-inner">
                <h1>
                    <a href="./index.php"><img class="logo rotate-10" src="./images/header_logo.svg" alt="ミライレのロゴ"></a>
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
                        <div class="slide-item slide-item1">
                            <img src="./images/vote/isometric1.png" alt="">
                            <div class="text-wrapper">
                                <p>選択中の公約：<br><span>雪原ペン次</span></p>
                                <button onclick="voteModal(0)" type="button" class="btn-main-color vote-btn opacity-down">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide-item slide-item2">
                            <img src="./images/vote/isometric2.png" alt="">
                            <div class="text-wrapper">
                                <p>選択中の公約：<br><span>虹宮ニョロ明</span></p>
                                <button onclick="voteModal(1)" type="button" class="btn-main-color vote-btn opacity-down">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide-item slide-item3">
                            <img src="./images/vote/isometric3.png" alt="">
                            <div class="text-wrapper">
                                <p>選択中の公約：<br><span>鳥谷コケ蔵</span></p>
                                <button onclick="voteModal(2)" type="button" class="btn-main-color vote-btn opacity-down">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide-item slide-item4">
                            <img src="./images/vote/isometric4.png" alt="">
                            <div class="text-wrapper">
                                <p>選択中の公約：<br><span>犬山イチ郎</span></p>
                                <button onclick="voteModal(3)" type="button" class="btn-main-color vote-btn opacity-down">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide-item slide-item5">
                            <img src="./images/vote/isometric5.png" alt="">
                            <div class="text-wrapper">
                                <p>選択中の公約：<br><span>猫川ニャミ子</span></p>
                                <button onclick="voteModal(4)" type="button" class="btn-main-color vote-btn opacity-down">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                </div><!-- /swiper-wrapper -->  
                <!-- navigation buttons --> 
                <div class="swiper-button-prev opacity-up"></div>
                <div class="swiper-button-next opacity-up"></div>
            </div><!-- /swiper -->
            <div class="character">
                <img class="character-img" src="images/vote/character/character1.png" alt="キャラクター画像">
            </div>
        </main>

        <footer>
            <div class="footer-inner">
                <div class="footer-logo">
                    <img class="logo" src="./images/footer_logo.png" alt="">
                </div>
                <div class="footer-nav">
                    <ul>
                        <li><a class="underline" href="index.php#about">本展示について</a></li>
                        <li><a class="underline" href="character.php">公約・キャラクター</a></li>
                        <li><a class="underline" href="">最新情報</a></li>
                    </ul>
                </div>
                <div class="sns-link">
                    <img class="sns" src="./images/footer_SNS.png" alt="">
                </div>
            </div>
        </footer>
    </div>

    <!-- 確認確認モーダル -->
    <div id="voteModal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="modal-title">確認</h2>
                <p class="modal-question">この公約でよろしいですか?</p>
                <p class="modal-label">選択中の公約:</p>
                <p class="modal-pledge" id="modalPledge">「○○○○○○○○○」</p>
                <p class="modal-character-name" id="modalCharacterName">○○ ○○</p>
                <div class="modal-buttons">
                    <form id="voteForm" method="post">
                        <input type="hidden" name="characterNum" id="characterNum" value="キャラクターナンバー">
                        <button type="submit" class="btn-main-color opacity-down" id="confirmVoteBtn">投票する</button>
                    </form>
                    <button type="button" class="btn-white opacity-down" id="cancelVoteBtn">選びなおす</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 投票完了モーダル -->
    <div id="voteCompleteModal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content modal-content-complete">
            <div class="modal-body-complete">
                <div class="vote-animation-container">
                    <img id="voteBeforeImage" src="" alt="投票前" class="vote-complete-image vote-before-image">
                    <img id="voteAfterImage" src="" alt="投票後" class="vote-complete-image vote-after-image">
                    <img id="votePartsImage" src="" alt="パーツ" class="vote-complete-image vote-parts-image">
                </div>
            </div>
        </div>
    </div>

    <!-- 投票完了モーダル -->
    <div id="voteCompleteModal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content modal-content-complete">
            <div class="modal-body-complete">
                <img id="voteCompleteImage" src="" alt="投票完了" class="vote-complete-image">
            </div>
        </div>
    </div>

</body>
</html>