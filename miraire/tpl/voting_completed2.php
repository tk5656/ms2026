<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投票完了｜ミライレ</title>
    <!-- destyle CSS -->
    <link rel="stylesheet" href="css/destyle.css">
    <!-- Adobe fonts -->
     <script src="./js/Adobe_fonts.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/page/voting_completed/voting_completed.css?v=<?php echo time(); ?>">
    <!-- favicon -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
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
        <div class="completion">
            <img src="images/kanryo_title.png" alt="投票完了">
        </div>
        <div class="contents">
            <div class="isome-img">
                <img src="images/voting_completed_isome/voting_completed3.png" alt="アイソメの画像">
            </div>
            <div class="completion-message">
                <h2>投票済証</h2>
                <div class="text-area">
                    <p class="election-title">第一回<br>HAL大阪改革総選挙</p>
                    <div class="committee">
                        <p class="committee-name">HAL大阪改革総選挙実行委員会</p>
                        <img src="images/kanryo_hanko.png" alt="実行委員会のハンコ">
                    </div>
                    <div class="selected-pledge">
                        <h3>投票済の公約：</h3>
                        <p class="pledge-title">「屋上にビアガーデンをつくる」</p>
                        <p class="pledge-name">鳥谷 コケ蔵</p>
                    </div>
                    <ul class="detail">
                        <li>集計結果は月日に、こちらのWeb上で公開予定です。投票は一人につき一回のみとさせて頂きます。ご了承ください。</li>
                        <li>投票がお済みで無い方への、演出のネタバレ等はご遠慮ください。</li>
                        <li>当ホームページURLを、まだブースに訪れていない第三者に共有する行為はお控えください。</li>
                    </ul>
                    <p class="add-wallet"><a href="">投票済証をウォレットに追加する</a></p>
                    <div class="sns-contents">
                        <p class="text">投票完了証明を<br>SNSでシェアしよう！</p>
                        <p class="X-link"><a href="">Xでポストする</a></p>
                        <p class="instagram-link"><a href="">ストーリーズに追加する</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-inner">
            <div class="footer-logo">
                <img class="logo" src="./images/hooter_logo.png" alt="">
            </div>
            <ul>
                <li><a class="underline" href="index.php#about">本展示について</a></li>
                <li><a class="underline" href="character.php">公約・キャラクター</a></li>
                <li><a class="underline" href="">最新情報</a></li>
            </ul>
            <div class="sns-link">
                <img class="sns" src="./images/hooter_SNS.png" alt="">
            </div>
        </div>
    </footer>
</body>
</html>