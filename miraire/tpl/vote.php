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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
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
                <a href="./index.php"><img class="logo" src="./images/header_logo.svg" alt="ミライレのロゴ"></a>
            </h1>
            <nav>
                <ul>
                    <li><a href="./index.php#about">About<span>本展示について</span></a></li>
                    <li><a href="">News<span>最新情報</span></a></li>
                    <li><a href="./character.html">Chara<span>公約・キャラクター</span></a></li>
                </ul>
            </nav>
            <a href="./vote.php"><a href="./vote.php"><img class="vote" src="./images/header_box.png" alt="投票箱"></a></a>
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
                                <button onclick="voteModal(0)" type="button" class="btn_mainColor vote-btn">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item2">
                            <img src="./images/vote/isometric2.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>虹宮ニョロ明</span></p>
                                <button onclick="voteModal(1)" type="button" class="btn_mainColor vote-btn">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item3">
                            <img src="./images/vote/isometric3.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>鳥谷コケ蔵</span></p>
                                <button onclick="voteModal(2)" type="button" class="btn_mainColor vote-btn">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item4">
                            <img src="./images/vote/isometric4.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>犬山イチ郎</span></p>
                                <button onclick="voteModal(3)" type="button" class="btn_mainColor vote-btn">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slide_item slide_item5">
                            <img src="./images/vote/isometric5.png" alt="">
                            <div class="text_wrapper">
                                <p>選択中の公約：<br><span>猫川ニャミ子</span></p>
                                <button onclick="voteModal(4)" type="button" class="btn_mainColor vote-btn">このキャラに投票する</button>
                            </div>
                        </div>
                    </div>
                </div><!-- /swiper-wrapper -->  
                <!-- ボタンのレスポンシブ対応ができていない、階層を変更する必要あり -->
                <!-- navigation buttons --> 
                <div class="swiper-button-prev opacity_up"></div>
                <div class="swiper-button-next opacity_up"></div>
            </div><!-- /swiper -->
        </main>
        <footer>
            <img class="logo" src="./images/hooter_logo.png" alt="">
            <ul>
                <li><a href="">本展示について</a></li>
                <li><a href="">公約・キャラクター</a></li>
                <li><a href="">最新情報</a></li>
            </ul>
            <img class="sns" src="./images/hooter_SNS.png" alt="">
        </footer>
    </div>

    <!-- 確認確認モーダル -->
    <div id="voteModal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="modal-title">確認:</h2>
                <p class="modal-question">この公約でよろしいですか?</p>
                <p class="modal-label">選択中の公約:</p>
                <p class="modal-pledge" id="modalPledge">「○○○○○○○○○」</p>
                <p class="modal-character-name" id="modalCharacterName">○○ ○○</p>
                <div class="modal-buttons">
                    <form id="voteForm" method="post">
                        <input type="hidden" name="characterNum" id="characterNum" value="キャラクターナンバー">
                        <button type="submit" class="btn_mainColor opacity_down" id="confirmVoteBtn">投票する</button>
                    </form>
                    <button type="button" class="btn_cancel" id="cancelVoteBtn">選びなおす</button>
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

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <!-- JS -->
    <script src="./js/swiper_vote.js"></script>
    <script src="./js/vote.js"></script>

</body>
</html>