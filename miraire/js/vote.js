// キャラクター情報の配列
// スライドの順序に対応: 0: 雪原ペン次, 1: 虹宮ニョロ明, 2: 鳥谷コケ蔵, 3: 犬山イチ郎, 4: 猫川ニャミ子
const characterData = [
    {
        name: '雪原ペン次',
        pledge: '○○○○○○○○○'
    },
    {
        name: '虹宮ニョロ明',
        pledge: '○○○○○○○○○'
    },
    {
        name: '鳥谷コケ蔵',
        pledge: '○○○○○○○○○'
    },
    {
        name: '犬山イチ郎',
        pledge: '○○○○○○○○○'
    },
    {
        name: '猫川ニャミ子',
        pledge: '○○○○○○○○○'
    }
];

// DOM要素の取得
const modal = document.getElementById('voteModal');
const modalCharacterName = document.getElementById('modalCharacterName');
const modalPledge = document.getElementById('modalPledge');
const confirmVoteBtn = document.getElementById('confirmVoteBtn');
const cancelVoteBtn = document.getElementById('cancelVoteBtn');
const voteButtons = document.querySelectorAll('.vote-btn');
const modalOverlay = document.querySelector('.modal-overlay');

// DOM要素の存在チェック
if (!modal || !modalCharacterName || !modalPledge || !confirmVoteBtn || !cancelVoteBtn) {
    console.error('モーダル関連のDOM要素が見つかりません');
}

// モーダルを閉じる関数
function closeModal() {
    if (modal) {
        modal.classList.remove('is-active');
    }
}

// 投票ボタンクリック時の処理
function voteModal(characterNum){
    // イベントの最初の処理（コンソール）
    console.log("click");
    // 引数（キャラクター番号）
    console.log(characterNum);
    modalCharacterName.textContent = characterData[characterNum].name;
    modalPledge.textContent = `「${characterData[characterNum].pledge}」`;
    characterNum.value = characterNum;
    modal.classList.add('is-active');
}

// 選びなおすボタンクリック時の処理
if (cancelVoteBtn) {
    cancelVoteBtn.addEventListener('click', closeModal);
}

// オーバーレイクリック時の処理
if (modalOverlay) {
    modalOverlay.addEventListener('click', closeModal);
}

// 投票するボタンクリック時の処理（現時点では何もしない）
if (confirmVoteBtn) {
    confirmVoteBtn.addEventListener('click', function() {
        // 将来的に投票処理を実装する場合はここに記述
        // 例: 投票APIを呼び出す、モーダルを閉じるなど
        // closeModal();
    });
}
