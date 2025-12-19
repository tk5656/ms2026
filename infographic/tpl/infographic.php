<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>infographic</title>
    <!-- cssファイル読み込み -->
    <link rel="stylesheet" href="./css/destyle.css">
    <link rel="stylesheet" href="./css/infographic.css">
</head>
<body>
    <div class="ranking" id="rankingContainer">
        <?php foreach ($ranking as $index => $item): ?>
        <div class="ranking-item" 
             data-character-id="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>"
             data-rank="<?php echo htmlspecialchars($item['rank'], ENT_QUOTES, 'UTF-8'); ?>"
             data-votes="<?php echo htmlspecialchars($item['votes'], ENT_QUOTES, 'UTF-8'); ?>"
             data-max-votes="<?php echo htmlspecialchars($maxVotes, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="pledge-item">
                <div class="rank"><?php echo htmlspecialchars($item['rank'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="pledge-name"><?php echo htmlspecialchars($item['pledgeShort'], ENT_QUOTES, 'UTF-8'); ?></div>
                <img src="images/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="キャラクターの画像">
                <div class="text">
                    <div class="catch-copy"><?php echo htmlspecialchars($item['catchCopy'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?><span>(<?php echo htmlspecialchars($item['votes'], ENT_QUOTES, 'UTF-8'); ?>)</span></div>
                </div>
            </div>
            <div class="gauge">
                <div class="sector">
                    <div class="now-gauge" style="width: <?php echo ($item['votes'] / $maxVotes) * 100; ?>%;"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="bottom-logo">
        <img src="images/title_logo.svg" alt="ミライレのロゴ">
    </div>
    <script src="./js/infographic.js"></script>
</body>
</html>