<?php
/*
Plugin Name: WP GitHub Repo Card
Plugin URI: https://github.com/sucooer/wp-github-repo-card
Description: 在 WordPress 中优雅地展示 GitHub 仓库卡片
Version: 1.0
Author: sucooer
Author URI: https://github.com/sucooer
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-github-repo-card
*/

if (!defined('ABSPATH')) exit;

// 确保类文件存在
if (!class_exists('WP_GitHub_Repo_Card')) {
    class WP_GitHub_Repo_Card {
        public function __construct() {
            add_action('init', array($this, 'init'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'register_settings'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        }

        public function init() {
            add_shortcode('github_repo', array($this, 'github_repo_shortcode'));
        }

        public function enqueue_scripts() {
            wp_enqueue_style('github-repo-card', plugins_url('css/github-card.css', __FILE__));
            wp_enqueue_script('github-repo-card', plugins_url('js/github-card.js', __FILE__), array('jquery'), '1.0', true);
        }

        public function enqueue_block_editor_assets() {
            wp_enqueue_script(
                'github-repo-card-block',
                plugins_url('build/block.js', __FILE__),
                array(
                    'wp-blocks',
                    'wp-element',
                    'wp-editor',
                    'wp-components',
                    'wp-block-editor'
                ),
                filemtime(plugin_dir_path(__FILE__) . 'build/block.js'),
                true
            );
        }

        public function add_admin_menu() {
            add_options_page(
                'GitHub 仓库卡片设置',
                'GitHub 仓库卡片',
                'manage_options',
                'github-repo-card',
                array($this, 'settings_page')
            );
        }

        public function register_settings() {
            register_setting('github_repo_card_settings', 'github_repo_card_token');
            
            add_settings_section(
                'github_repo_card_main',
                'API 设置',
                array($this, 'settings_section_callback'),
                'github-repo-card'
            );

            add_settings_field(
                'github_token',
                'GitHub Personal Access Token',
                array($this, 'token_field_callback'),
                'github-repo-card',
                'github_repo_card_main'
            );
        }

        public function settings_section_callback() {
            echo '<p>请输入 GitHub Personal Access Token 以提高 API 请求限额。获取 Token 请访问：<a href="https://github.com/settings/tokens" target="_blank">GitHub Tokens 页面</a></p>';
        }

        public function token_field_callback() {
            $token = get_option('github_repo_card_token');
            echo '<input type="password" name="github_repo_card_token" value="' . esc_attr($token) . '" class="regular-text">';
            echo '<p class="description">Token 仅需要 public_repo 权限即可</p>';
        }

        public function settings_page() {
            ?>
            <div class="wrap">
                <h1>GitHub 仓库卡片设置</h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('github_repo_card_settings');
                    do_settings_sections('github-repo-card');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        public function github_repo_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user' => '',
                'repo' => '',
            ), $atts);

            if (empty($atts['user']) || empty($atts['repo'])) {
                return '请提供有效的 GitHub 用户名和仓库名';
            }

            $cache_key = 'github_repo_' . $atts['user'] . '_' . $atts['repo'];
            $cache_time = 3600; // 1小时缓存

            if (false === ($repo_data = get_transient($cache_key))) {
                $token = get_option('github_repo_card_token');
                $args = array(
                    'headers' => array(
                        'Accept' => 'application/vnd.github.v3+json',
                        'User-Agent' => 'WordPress/GitHub-Repo-Card'
                    )
                );

                // 如果有 token，添加到请求头中
                if (!empty($token)) {
                    $args['headers']['Authorization'] = 'token ' . $token;
                }

                $response = wp_remote_get(
                    "https://api.github.com/repos/{$atts['user']}/{$atts['repo']}", 
                    $args
                );
                
                if (is_wp_error($response)) {
                    return '获取仓库信息失败';
                }

                $repo_data = json_decode(wp_remote_retrieve_body($response), true);
                set_transient($cache_key, $repo_data, $cache_time);
            }

            ob_start();
            ?>
            <div class="github-repo-card">
                <div class="repo-content">
                    <div class="repo-header">
                        <div class="repo-owner">
                            <img src="<?php echo esc_url($repo_data['owner']['avatar_url']); ?>" 
                                 alt="<?php echo esc_attr($repo_data['owner']['login']); ?>" 
                                 class="owner-avatar">
                            <a href="<?php echo esc_url($repo_data['owner']['html_url']); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="owner-name" 
                               style="position: relative; z-index: 2;">
                                <?php echo esc_html($repo_data['owner']['login']); ?>
                            </a>
                        </div>
                        <h3 class="repo-name">
                            <a href="<?php echo esc_url($repo_data['html_url']); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               style="position: relative; z-index: 2;">
                                <?php echo esc_html($repo_data['name']); ?>
                            </a>
                        </h3>
                    </div>
                    <div class="repo-description">
                        <?php echo esc_html($repo_data['description']); ?>
                    </div>
                    <div class="stats-container">
                        <div class="repo-stats">
                            <span class="stars">⭐ <?php echo number_format($repo_data['stargazers_count']); ?></span>
                            <span class="forks">🔱 <?php echo number_format($repo_data['forks_count']); ?></span>
                            <span class="language">
                                <span class="lang-color"></span>
                                <?php echo esc_html($repo_data['language']); ?>
                            </span>
                        </div>
                        <a href="<?php echo esc_url($this->get_repo_download_url($atts['user'], $atts['repo'])); ?>" 
                           class="download-button" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           style="cursor: pointer; position: relative; z-index: 999;">
                            <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16">
                                <path fill="currentColor" d="M7.47 10.78a.75.75 0 001.06 0l3.75-3.75a.75.75 0 00-1.06-1.06L8.75 8.44V1.75a.75.75 0 00-1.5 0v6.69L4.78 5.97a.75.75 0 00-1.06 1.06l3.75 3.75zM3.75 13a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5z"></path>
                            </svg>
                            下载源码
                        </a>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        public function enqueue_admin_scripts($hook) {
            if ('settings_page_github-repo-card' === $hook) {
                wp_enqueue_style(
                    'github-repo-card-admin',
                    plugins_url('css/admin.css', __FILE__),
                    array(),
                    '1.0'
                );
            }
        }

        function get_repo_download_url($owner, $repo) {
            // 获取仓库信息的 API 端点
            $api_url = "https://api.github.com/repos/{$owner}/{$repo}";
            
            // 如果设置了 GitHub Token，添加到请求头中
            $args = array(
                'headers' => array(
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'WordPress/GitHub-Repo-Card'
                )
            );
            
            // 添加 GitHub Token（如果已设置）
            $github_token = get_option('github_repo_card_token');
            if (!empty($github_token)) {
                $args['headers']['Authorization'] = 'token ' . $github_token;
            }
            
            // 发起 API 请求
            $response = wp_remote_get($api_url, $args);
            
            // 检查请求是否成功
            if (is_wp_error($response)) {
                // 如果请求失败，默认使用 main 分支
                return "https://github.com/{$owner}/{$repo}/archive/refs/heads/main.zip";
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);
            
            // 获取默认分支名称，如果获取失败则使用 main
            $default_branch = isset($data->default_branch) ? $data->default_branch : 'main';
            
            // 返回对应默认分支的下载链接
            return "https://github.com/{$owner}/{$repo}/archive/refs/heads/{$default_branch}.zip";
        }

        function render_github_card($repo_url) {
            // ... 其他代码保持不变 ...
            
            $download_url = get_repo_download_url($owner, $repo_name);
            
            $html .= '<div class="github-card-footer">';
            // ... 其他 footer 内容 ...
            $html .= '<a href="' . esc_url($download_url) . '" class="github-card-download" target="_blank" rel="noopener noreferrer">';
            $html .= '<svg class="octicon" height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path d="M7.47 10.78a.75.75 0 001.06 0l3.75-3.75a.75.75 0 00-1.06-1.06L8.75 8.44V1.75a.75.75 0 00-1.5 0v6.69L4.78 5.97a.75.75 0 00-1.06 1.06l3.75 3.75zM3.75 13a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5z"></path></svg>';
            $html .= '<span>Download</span>';
            $html .= '</a>';
            $html .= '</div>';
            
            // ... 其他代码保持不变 ...
        }
    }
    
    // 初始化插件
    new WP_GitHub_Repo_Card();
} 