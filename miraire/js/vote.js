// キャラクター情報の配列
const characterData = [
    { name: "雪原ペン次", pledge: "コンビニを学内に作る" },
    { name: "虹宮ニョロ明", pledge: "教室に落書きし放題" },
    { name: "鳥谷コケ蔵", pledge: "屋上にビアガーデンを作る" },
    { name: "犬山イチ郎", pledge: "学内に屋内喫煙所を作る" },
    { name: "猫川ニャミ子", pledge: "ゲーミングルームを作る" }
];

// DOM要素を取得
const modal = document.getElementById("voteModal");
const voteCompleteModal = document.getElementById("voteCompleteModal");
const voteBeforeImage = document.getElementById("voteBeforeImage");
const voteAfterImage = document.getElementById("voteAfterImage");
const votePartsImage = document.getElementById("votePartsImage");
const modalCharacterName = document.getElementById("modalCharacterName");
const modalPledge = document.getElementById("modalPledge");
const characterNumInput = document.getElementById("characterNum");
const cancelVoteBtn = document.getElementById("cancelVoteBtn");
const voteForm = document.getElementById("voteForm");

// モーダルを閉じる
function closeModal() {
    if (modal) {
        modal.classList.remove("is-active");
    }
}

// 投票ボタンクリックで確認モーダルを表示
function voteModal(characterNum) {
    if (characterData[characterNum] && modal && modalCharacterName && modalPledge && characterNumInput) {
        modalCharacterName.textContent = characterData[characterNum].name;
        modalPledge.textContent = "「" + characterData[characterNum].pledge + "」";
        characterNumInput.value = characterNum;
        modal.classList.add("is-active");
    }
}

// 選びなおすボタンでモーダルを閉じる
if (cancelVoteBtn) {
    cancelVoteBtn.addEventListener("click", closeModal);
}

// 投票するボタンで投票処理
if (voteForm) {
    voteForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        const characterNum = characterNumInput.value;
        
        // 投票を送信
        fetch("./vote.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ character_num: characterNum })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            closeModal();
            
            if (voteCompleteModal) {
                voteCompleteModal.classList.add("is-active");
            }
            
            startVoteAnimation(data, characterNum);
        });
    });
}

// 投票完了アニメーション
function startVoteAnimation(data, characterNum) {
    if (!voteBeforeImage || !voteAfterImage || !votePartsImage) {
        return;
    }
    
    voteBeforeImage.src = data.before_image_path;
    voteBeforeImage.style.display = "block";
    voteBeforeImage.style.opacity = "0";
    voteAfterImage.style.display = "none";
    votePartsImage.style.display = "none";
    votePartsImage.classList.remove("parts-falling");
    
    // 少し待ってから不透明にして自然に表示
    setTimeout(function() {
        voteBeforeImage.style.opacity = "1";
    }, 50);
    
    // 1.5秒後にパーツが落ちてくるアニメーション
    setTimeout(function() {
        if (data.parts_path) {
            votePartsImage.src = data.parts_path;
            votePartsImage.style.display = "block";
            votePartsImage.style.width = "30%";
            votePartsImage.style.maxWidth = "30%";
            votePartsImage.style.maxHeight = "30%";
            votePartsImage.style.height = "auto";
            
            // パーツが落ちてくるアニメーション開始
            setTimeout(function() {
                votePartsImage.classList.add("parts-falling");
            }, 50);
            
            // パーツが落ちた後（0.9秒後）、パーツをフェードアウト
            setTimeout(function() {
                votePartsImage.style.transition = "opacity 0.5s ease-out";
                votePartsImage.style.opacity = "0";
                
                // パーツが完全に消えた後（0.5秒後）、新しい画像のアニメーション開始
                setTimeout(function() {
                    votePartsImage.style.display = "none";
                    
                    // 投票後の画像を準備
                    voteAfterImage.src = data.after_image_path;
                    voteAfterImage.style.display = "block";
                    
                    // clip-pathアニメーション（縦線が左から右へ）
                    voteAfterImage.style.clipPath = "inset(0 100% 0 0)";
                    voteAfterImage.style.transition = "clip-path 1.5s ease-in-out";
                    
                    // アニメーション開始
                    setTimeout(function() {
                        voteAfterImage.style.clipPath = "inset(0 0% 0 0)";
                    }, 100);
                    
                    // アニメーション完了後（1.6秒後）、投票前の画像を非表示
                    setTimeout(function() {
                        voteBeforeImage.style.display = "none";
                        
                        // さらに3秒後に投票完了ページに遷移
                        setTimeout(function() {
                            const submitForm = document.createElement("form");
                            submitForm.method = "POST";
                            submitForm.action = "./voting_completed" + characterNum + ".php";
                            
                            const hiddenInput = document.createElement("input");
                            hiddenInput.type = "hidden";
                            hiddenInput.name = "characterNum";
                            hiddenInput.value = characterNum;
                            
                            submitForm.appendChild(hiddenInput);
                            document.body.appendChild(submitForm);
                            submitForm.submit();
                        }, 3000);
                    }, 1600);
                }, 500); // パーツがフェードアウトする時間
            }, 900); // パーツが落ちる時間（0.9秒）
        } else {
            // パーツがない場合、1.5秒待機してから新しい画像のアニメーション開始
            setTimeout(function() {
                // 投票後の画像を準備
                voteAfterImage.src = data.after_image_path;
                voteAfterImage.style.display = "block";
                
                // clip-pathアニメーション（縦線が左から右へ）
                voteAfterImage.style.clipPath = "inset(0 100% 0 0)";
                voteAfterImage.style.transition = "clip-path 1.5s ease-in-out";
                
                // アニメーション開始
                setTimeout(function() {
                    voteAfterImage.style.clipPath = "inset(0 0% 0 0)";
                }, 100);
                
                // アニメーション完了後（1.6秒後）、投票前の画像を非表示
                setTimeout(function() {
                    voteBeforeImage.style.display = "none";
                    
                    // さらに3秒後に投票完了ページに遷移
                    setTimeout(function() {
                        const submitForm = document.createElement("form");
                        submitForm.method = "POST";
                        submitForm.action = "./voting_completed" + characterNum + ".php";
                        
                        const hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "characterNum";
                        hiddenInput.value = characterNum;
                        
                        submitForm.appendChild(hiddenInput);
                        document.body.appendChild(submitForm);
                        submitForm.submit();
                    }, 3000);
                }, 1600);
            }, 1500); // 1.5秒待機
        }
    }, 1500); // 最初の1.5秒待機
}

// 左下のキャラクターの表示操作
const btnAfter = document.getElementsByClassName("swiper-button-next").item(0);
const btnBefore = document.getElementsByClassName("swiper-button-prev").item(0);
const characterImg = document.getElementsByClassName("character-img").item(0);
let countNum = 1;

// 画像をフェードアニメーションで切り替え
function changeImage(newSrc) {
    if (!characterImg) {
        return;
    }
    characterImg.style.transition = "opacity 0.3s ease-in-out";
    characterImg.style.opacity = "0";
    setTimeout(function() {
        characterImg.src = newSrc;
        characterImg.onload = function() {
            characterImg.style.opacity = "1";
        };
    }, 300);
}

// 次へボタンクリック時の処理
if (btnAfter) {
    btnAfter.addEventListener("click", function(){
        countNum++;
        if(countNum <= 5){
            changeImage("images/vote/character/character" + countNum + ".png");
        } else {
            countNum = 1;
            changeImage("images/vote/character/character" + countNum + ".png");
        }
    });
}

// 前へボタンクリック時の処理
if (btnBefore) {
    btnBefore.addEventListener("click", function(){
        countNum--;
        if(1 <= countNum){
            changeImage("images/vote/character/character" + countNum + ".png");
        } else {
            countNum = 5;
            changeImage("images/vote/character/character" + countNum + ".png");
        }
    });
}

// Swiperのスライド変更時にキャラクター画像を同期
function updateCharacterImage(characterNum) {
    // characterNumは0-4の範囲
    const imageNum = characterNum + 1;
    changeImage("images/vote/character/character" + imageNum + ".png");
    countNum = imageNum;
}

// Swiperの初期化とスライド変更イベントの設定
function initCharacterImageSync() {
    updateCharacterImage(mySwiper.realIndex);
    
    mySwiper.on("slideChange", function() {
        updateCharacterImage(mySwiper.realIndex);
    });
}

// ページ読み込み完了時に初期化
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCharacterImageSync);
} else {
    initCharacterImageSync();
}
