(function($) {
    'use strict';
    
    // 为编程语言添加对应的颜色
    const languageColors = {
        'JavaScript': '#f1e05a',
        'Python': '#3572A5',
        'Java': '#b07219',
        'PHP': '#4F5D95',
        'C++': '#f34b7d',
        'TypeScript': '#2b7489',
        'Ruby': '#701516',
        'Go': '#00ADD8',
        'Swift': '#ffac45',
        'Kotlin': '#F18E33',
        'Rust': '#dea584'
    };

    $(document).ready(function() {
        // 为每个仓库卡片设置语言颜色
        $('.github-repo-card').each(function() {
            const $card = $(this);
            const $langColor = $card.find('.lang-color');
            const language = $card.find('.language').text().trim();
            
            if (language && languageColors[language]) {
                $langColor.css('background-color', languageColors[language]);
            }
        });

        // 添加鼠标悬停效果
        $('.github-repo-card').hover(
            function() {
                // 移除直接的 CSS 修改，改用 CSS 过渡效果
            },
            function() {
                // 移除直接的 CSS 修改，改用 CSS 过渡效果
            }
        );

        // 为数字添加更平滑的动画效果
        $('.repo-stats span').each(function() {
            const $stat = $(this);
            const text = $stat.text();
            const number = parseInt(text.replace(/[^0-9]/g, ''));
            
            if (!isNaN(number)) {
                $stat.attr('title', text);
                
                if (number > 1000) {
                    const formatted = (number / 1000).toFixed(1) + 'k';
                    $stat.text($stat.text().replace(number.toString(), formatted));
                }
                
                // 添加数字动画效果
                $stat.css('opacity', '0').animate({
                    opacity: 1
                }, 500);
            }
        });
    });
})(jQuery); 