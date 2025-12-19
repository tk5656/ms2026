// インフォグラフィックページのランキング更新とアニメーション

(function() {
    'use strict';
    
    // 定数定義
    const UPDATE_INTERVAL = 5000; // 5秒
    const INITIAL_DELAY = 1000; // 1秒
    const ANIMATION_DURATION = 1500; // 1.5秒
    const GAP_PX = 16; // gap: 1rem = 16px
    const MOVE_THRESHOLD = 1; // アニメーション閾値（px）
    
    // DOM要素を取得
    const rankingContainer = document.getElementById('rankingContainer');
    if (!rankingContainer) {
        return;
    }
    
    // 状態管理
    let currentRanking = [];
    let isAnimating = false;
    
    // 初期状態を取得
    function getCurrentRanking() {
        return Array.from(rankingContainer.querySelectorAll('.ranking-item')).map(function(item) {
            return {
                characterId: Number(item.getAttribute('data-character-id')),
                rank: Number(item.getAttribute('data-rank')),
                votes: Number(item.getAttribute('data-votes')),
                maxVotes: Number(item.getAttribute('data-max-votes')),
                element: item
            };
        });
    }
    
    // データを取得してランキングを更新
    function updateRanking() {
        // アニメーション中は更新をスキップ
        if (isAnimating) {
            return;
        }
        
        fetch('./infographic.php', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.error) {
                // エラーは静かに終了
                return;
            }
            if (!data || !data.ranking || data.ranking.length === 0) {
                return;
            }
            
            // 新しいランキングデータを整形
            const newRanking = data.ranking.map(function(item) {
                return {
                    characterId: item.id,
                    rank: item.rank,
                    votes: item.votes,
                    maxVotes: data.maxVotes,
                    name: item.name,
                    age: item.age,
                    pledgeShort: item.pledgeShort,
                    catchCopy: item.catchCopy,
                    image: item.image,
                    color: item.color
                };
            });
            
            // 順位が変わったかチェック
            if (currentRanking.length > 0) {
                const hasRankChanged = checkRankChange(currentRanking, newRanking);
                if (hasRankChanged) {
                    animateRankChange(currentRanking, newRanking);
                } else {
                    // 順位が変わらなくても投票数とグラフを更新
                    updateVotesAndGauge(currentRanking, newRanking);
                }
            } else {
                // 初回読み込み時は現在の状態を保存
                currentRanking = getCurrentRanking();
            }
        })
        .catch(function(error) {
            // エラーは静かに終了
        });
    }
    
    // 順位が変わったかチェック
    function checkRankChange(oldRanking, newRanking) {
        if (oldRanking.length !== newRanking.length) {
            return true;
        }
        
        const oldRankMap = new Map();
        oldRanking.forEach(function(item) {
            oldRankMap.set(item.characterId, item.rank);
        });
        
        return newRanking.some(function(newItem) {
            const oldRank = oldRankMap.get(newItem.characterId);
            return oldRank === undefined || oldRank !== newItem.rank;
        });
    }
    
    // 順位変動のアニメーション
    function animateRankChange(oldRanking, newRanking) {
        isAnimating = true;
        rankingContainer.classList.add('ranking-updating');
        
        // 現在の位置を取得
        const currentPositions = new Map();
        const containerRect = rankingContainer.getBoundingClientRect();
        
        oldRanking.forEach(function(item) {
            const rect = item.element.getBoundingClientRect();
            currentPositions.set(item.characterId, {
                element: item.element,
                top: rect.top - containerRect.top
            });
        });
        
        // 新しい位置を計算してアニメーション
        let newTop = 0;
        const movingElements = [];
        
        newRanking.forEach(function(newItem) {
            const current = currentPositions.get(newItem.characterId);
            if (current) {
                const moveDistance = newTop - current.top;
                
                if (Math.abs(moveDistance) > MOVE_THRESHOLD) {
                    // 移動する要素にクラスを追加（拡大効果用）
                    current.element.classList.add('ranking-item-moving');
                    movingElements.push({
                        element: current.element,
                        moveDistance: moveDistance
                    });
                    
                    // 最初はscale(1.05)で拡大しながら移動開始
                    current.element.style.transform = 'scale(1.05) translateY(' + moveDistance + 'px)';
                    current.element.style.transition = 'transform ' + (ANIMATION_DURATION / 1000) + 's ease-in-out';
                    
                    // アニメーションの早い段階（0.3秒後）からscaleを1.0に戻し始める
                    // より長い時間（1.2秒）をかけてスムーズに縮小
                    setTimeout(function() {
                        current.element.style.transform = 'scale(1.0) translateY(' + moveDistance + 'px)';
                        current.element.style.transition = 'transform 1.2s ease-in-out';
                    }, 300);
                }
                
                newTop += current.element.offsetHeight + GAP_PX;
            }
        });
        
        // アニメーション完了後にDOMを再構築
        setTimeout(function() {
            // 移動していた要素からクラスを削除
            movingElements.forEach(function(item) {
                item.element.classList.remove('ranking-item-moving');
            });
            
            rebuildRanking(newRanking);
            rankingContainer.classList.remove('ranking-updating');
            isAnimating = false;
        }, ANIMATION_DURATION);
    }
    
    // アイテムの内容を更新
    function updateItemContent(element, newItem) {
        const rankElement = element.querySelector('.rank');
        const votesSpan = element.querySelector('.name span');
        
        if (rankElement) {
            rankElement.textContent = newItem.rank;
        }
        if (votesSpan) {
            votesSpan.textContent = '(' + newItem.votes + ')';
        }
        
        element.setAttribute('data-rank', newItem.rank);
        element.setAttribute('data-votes', newItem.votes);
        element.setAttribute('data-max-votes', newItem.maxVotes);
        
        updateGauge(element, newItem.votes, newItem.maxVotes, newItem.color);
    }
    
    // グラフを更新
    function updateGauge(element, votes, maxVotes, color) {
        const gaugeElement = element.querySelector('.now-gauge');
        if (gaugeElement) {
            gaugeElement.style.width = (votes / maxVotes) * 100 + '%';
            gaugeElement.style.backgroundColor = color;
        }
    }
    
    // 投票数とグラフのみ更新（順位が変わらない場合）
    function updateVotesAndGauge(oldRanking, newRanking) {
        oldRanking.forEach(function(oldItem) {
            const newItem = newRanking.find(function(item) {
                return item.characterId === oldItem.characterId;
            });
            if (newItem) {
                updateItemContent(oldItem.element, newItem);
            }
        });
        currentRanking = getCurrentRanking();
    }
    
    // ランキングを再構築
    function rebuildRanking(newRanking) {
        const existingElements = new Map();
        Array.from(rankingContainer.children).forEach(function(element) {
            const characterId = Number(element.getAttribute('data-character-id'));
            if (!isNaN(characterId)) {
                existingElements.set(characterId, element);
            }
        });
        
        // 既存要素を一時的に退避（innerHTMLを使わない）
        const fragment = document.createDocumentFragment();
        
        newRanking.forEach(function(newItem, index) {
            const element = existingElements.get(newItem.characterId);
            if (element) {
                // transformをリセット
                element.style.transform = '';
                element.style.transition = '';
                
                // orderプロパティを更新
                element.style.order = index + 1;
                
                // 内容を更新
                updateItemContent(element, newItem);
                
                fragment.appendChild(element);
            }
        });
        
        // 既存の子要素を削除してから新しい順序で追加
        while (rankingContainer.firstChild) {
            rankingContainer.removeChild(rankingContainer.firstChild);
        }
        rankingContainer.appendChild(fragment);
        
        currentRanking = getCurrentRanking();
    }
    
    // 初期ランキングを取得
    currentRanking = getCurrentRanking();
    
    // ページ読み込み完了後に初回更新を実行
    function init() {
        setTimeout(updateRanking, INITIAL_DELAY);
        setInterval(updateRanking, UPDATE_INTERVAL);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
