const UPDATE_INTERVAL = 5000;
const INITIAL_DELAY = 1000;
const ANIMATION_DURATION = 1500;
const GAP_PX = 16;

const rankingContainer = document.getElementById("rankingContainer");
let currentRanking = [];
let isAnimating = false;

// 現在表示されているランキングの情報を取得
function getCurrentRanking() {
    const items = rankingContainer ? rankingContainer.querySelectorAll(".ranking-item") : [];
    const list = [];
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        list.push({
            characterId: Number(item.getAttribute("data-character-id")),
            rank: Number(item.getAttribute("data-rank")),
            votes: Number(item.getAttribute("data-votes")),
            maxVotes: Number(item.getAttribute("data-max-votes")),
            element: item
        });
    }
    return list;
}

// サーバーから最新のランキングを取得して更新
function updateRanking() {
    if (isAnimating || !rankingContainer) {
        return;
    }

    const cacheBuster = "?_=" + new Date().getTime();
    fetch("./infographic.php" + cacheBuster, {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        const newRanking = data.ranking.map(function(item) {
            return {
                characterId: item.id,
                rank: item.rank,
                votes: item.votes,
                maxVotes: data.max_votes,
                name: item.name,
                age: item.age,
                pledgeShort: item.pledge_short,
                catchCopy: item.catch_copy,
                image: item.image,
                color: item.color
            };
        });

        if (currentRanking.length === 0) {
            currentRanking = getCurrentRanking();
            return;
        }

        const changed = hasRankChangedSimple(currentRanking, newRanking);
        if (changed) {
            animateRankChangeSimple(currentRanking, newRanking);
        } else {
            updateVotesAndGauge(currentRanking, newRanking);
        }
    });
}

// 順位が変わったかどうかをチェック
function hasRankChangedSimple(oldRanking, newRanking) {
    if (oldRanking.length !== newRanking.length) {
        return true;
    }
    for (let i = 0; i < newRanking.length; i++) {
        const id = newRanking[i].characterId;
        for (let j = 0; j < oldRanking.length; j++) {
            if (oldRanking[j].characterId === id) {
                if (oldRanking[j].rank !== newRanking[i].rank) {
                    return true;
                }
                break;
            }
        }
    }
    return false;
}

// 順位が変わったときにアニメーション
function animateRankChangeSimple(oldRanking, newRanking) {
    isAnimating = true;

    // 各項目の現在の位置を記録
    const positions = {};
    const containerRect = rankingContainer.getBoundingClientRect();
    for (let i = 0; i < oldRanking.length; i++) {
        const item = oldRanking[i];
        const rect = item.element.getBoundingClientRect();
        positions[item.characterId] = {
            element: item.element,
            top: rect.top - containerRect.top
        };
    }

    // 新しい位置を計算してアニメーション
    let newTop = 0;
    const moving = [];

    for (let k = 0; k < newRanking.length; k++) {
        const newItem = newRanking[k];
        const current = positions[newItem.characterId];
        if (!current) continue;

        const moveDistance = newTop - current.top;
        if (Math.abs(moveDistance) > 1) {
            current.element.classList.add("ranking-item-moving");
            current.element.style.transform = "translateY(" + moveDistance + "px)";
            current.element.style.transition = "transform " + (ANIMATION_DURATION / 1000) + "s ease-in-out";
            moving.push(current.element);
        }
        newTop += current.element.offsetHeight + GAP_PX;
    }

    // アニメーション終了後に並び替え
    setTimeout(function() {
        for (let i = 0; i < moving.length; i++) {
            moving[i].classList.remove("ranking-item-moving");
            moving[i].style.transform = "";
            moving[i].style.transition = "";
        }
        rebuildRankingSimple(newRanking);
        isAnimating = false;
    }, ANIMATION_DURATION);
}

// ランキング項目の内容を更新
function updateItemContent(element, newItem) {
    const rankElement = element.querySelector(".rank");
    if (rankElement) {
        rankElement.textContent = newItem.rank;
    }
    element.setAttribute("data-rank", newItem.rank);
    element.setAttribute("data-votes", newItem.votes);
    element.setAttribute("data-max-votes", newItem.maxVotes);
    updateGauge(element, newItem.votes, newItem.maxVotes, newItem.color);
}

// ゲージを更新
function updateGauge(element, votes, maxVotes, color) {
    const gaugeElement = element.querySelector(".now-gauge");
    if (gaugeElement) {
        gaugeElement.style.width = (votes / maxVotes) * 100 + "%";
        gaugeElement.style.backgroundColor = color;
    }
}

// 順位が変わらなかった場合、票数とゲージだけ更新
function updateVotesAndGauge(oldRanking, newRanking) {
    for (let i = 0; i < oldRanking.length; i++) {
        const oldItem = oldRanking[i];
        for (let j = 0; j < newRanking.length; j++) {
            if (oldItem.characterId === newRanking[j].characterId) {
                updateItemContent(oldItem.element, newRanking[j]);
                break;
            }
        }
    }
    currentRanking = getCurrentRanking();
}

// ランキングを新しい順番に並び替え
function rebuildRankingSimple(newRanking) {
    const children = rankingContainer.children;
    const existing = {};
    for (let i = 0; i < children.length; i++) {
        const element = children[i];
        const id = Number(element.getAttribute("data-character-id"));
        if (!isNaN(id)) {
            existing[id] = element;
        }
    }

    while (rankingContainer.firstChild) {
        rankingContainer.removeChild(rankingContainer.firstChild);
    }
    
    for (let j = 0; j < newRanking.length; j++) {
        const item = newRanking[j];
        const element = existing[item.characterId];
        if (element) {
            element.style.order = (j + 1);
            updateItemContent(element, item);
            rankingContainer.appendChild(element);
        }
    }
    currentRanking = getCurrentRanking();
}

// 初期化
function initInfographic() {
    if (!rankingContainer) return;
    currentRanking = getCurrentRanking();
    setTimeout(updateRanking, INITIAL_DELAY);
    setInterval(updateRanking, UPDATE_INTERVAL);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initInfographic);
} else {
    initInfographic();
}
