// キャラクター情報の配列
const characterData = [
    { name: '雪原ペン次', pledge: 'コンビニを学内に作る' },
    { name: '虹宮ニョロ明', pledge: '教室に落書きし放題' },
    { name: '鳥谷コケ蔵', pledge: '屋上にビアガーデンを作る' },
    { name: '犬山イチ郎', pledge: '学内に屋内喫煙所を作る' },
    { name: '猫川ニャミ子', pledge: 'ゲーミングルームを作る' }
];

// DOM要素を取得
const modal = document.getElementById('voteModal');
const voteCompleteModal = document.getElementById('voteCompleteModal');
const voteCompleteImage = document.getElementById('voteCompleteImage');
const modalCharacterName = document.getElementById('modalCharacterName');
const modalPledge = document.getElementById('modalPledge');
const characterNumInput = document.getElementById('characterNum');
const cancelVoteBtn = document.getElementById('cancelVoteBtn');
const voteForm = document.getElementById('voteForm');

// モーダルを閉じる
function closeModal() {
    modal.classList.remove('is-active');
}

// 投票ボタンクリックで確認モーダルを表示
function voteModal(characterNum) {
    modalCharacterName.textContent = characterData[characterNum].name;
    modalPledge.textContent = `「${characterData[characterNum].pledge}」`;
    characterNumInput.value = characterNum;
    modal.classList.add('is-active');
}

// 選びなおすボタンでモーダルを閉じる
cancelVoteBtn.addEventListener('click', closeModal);

// 投票するボタンで投票処理
voteForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const characterNum = characterNumInput.value;
    
    // 投票を送信
    fetch('./vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ characterNum: characterNum })
    })
    .then(response => response.json())
    .then(data => {
        // 確認モーダルを閉じる
        closeModal();
        
        // 投票完了モーダルに画像を表示
        voteCompleteImage.src = data.imagePath;
        voteCompleteModal.classList.add('is-active');
        
        // 5秒後に投票完了ページに遷移
        setTimeout(function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = './vote_result.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'characterNum';
            input.value = characterNum;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }, 5000);
    });
});
