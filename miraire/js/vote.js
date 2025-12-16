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
const voteBeforeImage = document.getElementById('voteBeforeImage');
const voteAfterImage = document.getElementById('voteAfterImage');
const votePartsImage = document.getElementById('votePartsImage');
const modalCharacterName = document.getElementById('modalCharacterName');
const modalPledge = document.getElementById('modalPledge');
const characterNumInput = document.getElementById('characterNum');
const cancelVoteBtn = document.getElementById('cancelVoteBtn');
const voteForm = document.getElementById('voteForm');

// DOM要素が存在しない場合のチェック
if (!modal || !voteCompleteModal || !voteBeforeImage || !voteAfterImage || !votePartsImage || 
    !modalCharacterName || !modalPledge || !characterNumInput || !cancelVoteBtn || !voteForm) {
    console.error('必要なDOM要素が見つかりません');
}

// モーダルを閉じる
function closeModal() {
    if (modal) {
        modal.classList.remove('is-active');
    }
}

// 投票ボタンクリックで確認モーダルを表示
function voteModal(characterNum) {
    if (!modal || !modalCharacterName || !modalPledge || !characterNumInput) {
        console.error('モーダル表示に必要な要素が見つかりません');
        return;
    }
    
    if (characterData[characterNum]) {
        modalCharacterName.textContent = characterData[characterNum].name;
        modalPledge.textContent = `「${characterData[characterNum].pledge}」`;
        characterNumInput.value = characterNum;
        modal.classList.add('is-active');
    }
}

// 選びなおすボタンでモーダルを閉じる
if (cancelVoteBtn) {
    cancelVoteBtn.addEventListener('click', closeModal);
}

// 投票するボタンで投票処理
if (voteForm) {
    voteForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!characterNumInput) {
        console.error('characterNumInputが見つかりません');
        return;
    }
    
    const characterNum = characterNumInput.value;
    
    // 投票を送信
    fetch('./vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ characterNum: characterNum })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('投票の送信に失敗しました');
        }
        return response.json();
    })
    .then(data => {
        // エラーチェック
        if (data.error) {
            alert(data.error);
            return;
        }
        
        // 確認モーダルを閉じる
        closeModal();
        
        // 投票完了モーダルを表示
        if (voteCompleteModal) {
            voteCompleteModal.classList.add('is-active');
        }
        
        // アニメーション開始
        startVoteAnimation(data, characterNum);
    })
    .catch(error => {
        console.error('エラー:', error);
        alert('投票処理中にエラーが発生しました。もう一度お試しください。');
    });
    });
}

// 投票完了アニメーション
function startVoteAnimation(data, characterNum) {
    if (!voteBeforeImage || !voteAfterImage || !votePartsImage || !voteCompleteModal) {
        console.error('アニメーションに必要な要素が見つかりません');
        return;
    }
    // 初期状態：投票前の画像を表示
    voteBeforeImage.src = data.beforeImagePath;
    voteBeforeImage.style.display = 'block';
    voteAfterImage.style.display = 'none';
    votePartsImage.style.display = 'none';
    votePartsImage.classList.remove('parts-falling');
    
    // 2秒後にパーツが落ちてくるアニメーション
    setTimeout(function() {
        if (data.partsPath) {
            votePartsImage.src = data.partsPath;
            votePartsImage.style.display = 'block';
            votePartsImage.style.width = '30%';
            votePartsImage.style.maxWidth = '30%';
            votePartsImage.style.maxHeight = '30%';
            votePartsImage.style.height = 'auto';
            // パーツが落ちてくるアニメーション開始
            setTimeout(function() {
                votePartsImage.classList.add('parts-falling');
            }, 50);
            
            // パーツが落ちた後（1.5秒後）、パーツをフェードアウト
            setTimeout(function() {
                votePartsImage.style.transition = 'opacity 0.5s ease-out';
                votePartsImage.style.opacity = '0';
                
                // パーツが完全に消えた後（0.5秒後）、新しい画像のアニメーション開始
                setTimeout(function() {
                    votePartsImage.style.display = 'none';
                    
                    // 投票後の画像を準備
                    voteAfterImage.src = data.afterImagePath;
                    voteAfterImage.style.display = 'block';
                    
                    // clip-pathアニメーション（縦線が左から右へ）
                    voteAfterImage.style.clipPath = 'inset(0 100% 0 0)';
                    voteAfterImage.style.webkitClipPath = 'inset(0 100% 0 0)';
                    voteAfterImage.style.transition = 'clip-path 1.5s ease-in-out, -webkit-clip-path 1.5s ease-in-out';
                    
                    // アニメーション開始
                    setTimeout(function() {
                        voteAfterImage.style.clipPath = 'inset(0 0% 0 0)';
                        voteAfterImage.style.webkitClipPath = 'inset(0 0% 0 0)';
                    }, 100);
                    
                    // アニメーション完了後（1.6秒後）、投票前の画像を非表示
                    setTimeout(function() {
                        voteBeforeImage.style.display = 'none';
                        
                        // さらに3秒後に投票完了ページに遷移（最後のアイソメ画像を長く表示）
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
                        }, 3000);
                    }, 1600);
                }, 500); // パーツがフェードアウトする時間
            }, 1500); // パーツが落ちる時間（1.5秒）
        } else {
            // パーツがない場合、2秒待機してから新しい画像のアニメーション開始
            setTimeout(function() {
                // 投票後の画像を準備
                voteAfterImage.src = data.afterImagePath;
                voteAfterImage.style.display = 'block';
                
                // clip-pathアニメーション（縦線が左から右へ）
                voteAfterImage.style.clipPath = 'inset(0 100% 0 0)';
                voteAfterImage.style.webkitClipPath = 'inset(0 100% 0 0)';
                voteAfterImage.style.transition = 'clip-path 1.5s ease-in-out, -webkit-clip-path 1.5s ease-in-out';
                
                // アニメーション開始
                setTimeout(function() {
                    voteAfterImage.style.clipPath = 'inset(0 0% 0 0)';
                    voteAfterImage.style.webkitClipPath = 'inset(0 0% 0 0)';
                }, 100);
                
                // アニメーション完了後（1.6秒後）、投票前の画像を非表示
                setTimeout(function() {
                    voteBeforeImage.style.display = 'none';
                    
                    // さらに3秒後に投票完了ページに遷移（最後のアイソメ画像を長く表示）
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
                    }, 3000);
                }, 1600);
            }, 2000); // 2秒待機（パーツがある場合と同じタイミング）
        }
    }, 2000); // 最初の2秒待機
}
