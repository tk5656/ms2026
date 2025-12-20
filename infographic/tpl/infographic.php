<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>infographic</title>
    <!-- cssファイル読み込み -->
    <link rel="stylesheet" href="css/destyle.css">
    <link rel="stylesheet" href="css/infographic.css">
    <!-- favicon -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="top-line">
            <p><img src="images/top-line.png" alt=""></p>
        </div>
        <div class="ranking" id="rankingContainer">
            <?php foreach ($ranking as $item): ?>
            <div class="ranking-item <?php echo h($item['characterClass']); ?>" 
                 data-character-id="<?php echo h($item['id']); ?>"
                 data-rank="<?php echo h($item['rank']); ?>"
                 data-votes="<?php echo h($item['votes']); ?>"
                 data-max-votes="<?php echo h($maxVotes); ?>"
                 style="order: <?php echo h($item['order']); ?>;">
                <div class="pledge-item">
                    <p class="rank"><?php echo h($item['rank']); ?></p>
                    <p class="pledge-name" style="background-color: <?php echo h($item['color']); ?>;">
                        <?php echo h($item['pledgeShort']); ?>
                    </p>
                    <img src="images/<?php echo h($item['image']); ?>" alt="キャラクターの画像">
                    <div class="text">
                        <p class="catch-copy"><?php echo h($item['catchCopy']); ?></p>
                        <p class="name"><?php echo h($item['name']); ?><span>(<?php echo h($item['age']); ?>)</span></p>
                    </div>
                </div>
                <div class="gauge">
                    <div class="sector">
                        <div class="now-gauge" style="width: <?php echo h($item['gaugeWidth']); ?>%; background-color: <?php echo h($item['color']); ?>;"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="bottom-logo">
                <img src="images/title_logo.svg" alt="ミライレのロゴ">
        </div>
    </div>
    <script src="js/infographic.js"></script>
</body>
</html>