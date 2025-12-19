// インフォグラフィックページのランキング更新とアニメーション

(function() {
    'use strict';
    
    // DOM要素を取得
    const rankingContainer = document.getElementById('rankingContainer');
    if (!rankingContainer) {
        return;
    }
    
    // 現在のランキング状態を保持
    let currentRanking = [];
    
    // 初期状態を取得
    function getCurrentRanking() {
        return Array.from(rankingContainer.querySelectorAll('.ranking-item')).map(function(item) {
            return {
                characterId: parseInt(item.getAttribute('data-character-id')),
                rank: parseInt(item.getAttribute('data-rank')),
                votes: parseInt(item.getAttribute('data-votes')),
                maxVotes: parseInt(item.getAttribute('data-max-votes')),
                element: item
            };
        });
    }
    
    // データを取得してランキングを更新
    function updateRanking() {
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
                console.error('サーバーエラー:', data.error);
                return;
            }
            if (!data || !data.ranking || data.ranking.length === 0) {
                console.warn('ランキングデータが空です');
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
                    pledge: item.pledge,
                    pledgeShort: item.pledgeShort,
                    catchCopy: item.catchCopy,
                    image: item.image
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
            console.error('ランキング更新エラー:', error);
        });
    }
    
    // 順位が変わったかチェック
    function checkRankChange(oldRanking, newRanking) {
        if (oldRanking.length !== newRanking.length) {
            return true;
        }
        
        return oldRanking.some(function(oldItem) {
            const newItem = newRanking.find(function(item) {
                return item.characterId === oldItem.characterId;
            });
            return !newItem || oldItem.rank !== newItem.rank;
        });
    }
    
    // 順位変動のアニメーション
    function animateRankChange(oldRanking, newRanking) {
        rankingContainer.classList.add('ranking-updating');
        
        const itemMargin = 16; // margin-bottom
        const getItemHeight = function(item) {
            return item.element.offsetHeight + itemMargin;
        };
        
        // 現在の位置を計算
        const currentPositions = [];
        let currentTop = 0;
        oldRanking.forEach(function(item) {
            currentPositions.push({
                characterId: item.characterId,
                top: currentTop,
                element: item.element
            });
            currentTop += getItemHeight(item);
        });
        
        // 新しい位置を計算してアニメーション
        let newTop = 0;
        newRanking.forEach(function(newItem) {
            const oldItem = oldRanking.find(function(item) {
                return item.characterId === newItem.characterId;
            });
            if (oldItem) {
                const currentPos = currentPositions.find(function(pos) {
                    return pos.characterId === newItem.characterId;
                });
                
                // 位置が変わった場合はアニメーション
                if (currentPos && currentPos.top !== newTop) {
                    const moveDistance = newTop - currentPos.top;
                    oldItem.element.classList.add('moving');
                    oldItem.element.style.transform = 'translateY(' + moveDistance + 'px)';
                }
                
                // 内容を更新
                updateItemContent(oldItem.element, newItem);
                newTop += getItemHeight(oldItem);
            }
        });
        
        // アニメーション完了後にDOMを再構築
        setTimeout(function() {
            rebuildRanking(newRanking);
            rankingContainer.classList.remove('ranking-updating');
            currentRanking = getCurrentRanking();
        }, 600);
    }
    
    // アイテムの内容を更新
    function updateItemContent(element, newItem) {
        const rankElement = element.querySelector('.rank');
        const votesSpan = element.querySelector('.name span');
        
        if (rankElement) rankElement.textContent = newItem.rank;
        if (votesSpan) votesSpan.textContent = '(' + newItem.votes + ')';
        
        element.setAttribute('data-rank', newItem.rank);
        element.setAttribute('data-votes', newItem.votes);
        element.setAttribute('data-max-votes', newItem.maxVotes);
        
        updateGauge(element, newItem.votes, newItem.maxVotes);
    }
    
    // グラフを更新
    function updateGauge(element, votes, maxVotes) {
        const gaugeElement = element.querySelector('.now-gauge');
        if (gaugeElement) {
            gaugeElement.style.width = (votes / maxVotes) * 100 + '%';
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
        const existingElements = {};
        Array.from(rankingContainer.children).forEach(function(element) {
            const characterId = parseInt(element.getAttribute('data-character-id'));
            if (!isNaN(characterId)) {
                existingElements[characterId] = element;
            }
        });
        
        rankingContainer.innerHTML = '';
        
        newRanking.forEach(function(newItem) {
            const element = existingElements[newItem.characterId];
            if (element) {
                element.classList.remove('moving');
                element.style.transform = '';
                rankingContainer.appendChild(element);
            }
        });
    }
    
    // 初期ランキングを取得
    currentRanking = getCurrentRanking();
    
    // ページ読み込み完了後に初回更新を実行
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(updateRanking, 1000);
        });
    } else {
        setTimeout(updateRanking, 1000);
    }
    
    // 定期的にランキングを更新（5秒ごと）
    setInterval(updateRanking, 5000);
    
})();
