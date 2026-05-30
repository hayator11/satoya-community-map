<?php
/**
 * Plugin Name: 里親コミュニティMAP
 * Plugin URI: https://onokun.com
 * Description: おのくんの里親コミュニティMAP - 日本・世界マップ表示と登録フォーム
 * Version: 1.0.0
 * Author: おのくん管理者
 * Text Domain: satoya-map
 */

if (!defined('ABSPATH')) exit;

define('SATOYA_VERSION', '1.9.0');
define('SATOYA_PATH', plugin_dir_path(__FILE__));
define('SATOYA_URL', plugin_dir_url(__FILE__));

require_once SATOYA_PATH . 'includes/cpt.php';
require_once SATOYA_PATH . 'includes/form-handler.php';
require_once SATOYA_PATH . 'includes/map-shortcode.php';
require_once SATOYA_PATH . 'includes/admin.php';

register_activation_hook(__FILE__, function () {
    satoya_register_post_type();
    flush_rewrite_rules();
    if (!get_option('satoya_settings')) {
        add_option('satoya_settings', [
            'japan_label'   => '日本マップ',
            'world_label'   => '世界マップ',
            'counter_label' => '総里親数',
        ]);
    }
    satoya_seed_origin_spots();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

// ---- 既に有効化済みのプラグインにも適用（admin_init 経由）----
add_action('admin_init', 'satoya_seed_origin_spots');

/**
 * おのくんゆかりの場所をDBに自動登録
 * ※ 毎回ポスト存在チェック → 存在しない場合のみ作成（オプション依存なし）
 */
function satoya_seed_origin_spots() {
    // 古いオプションが残っていても動作するよう削除
    delete_option('satoya_origin_seeded_v1');

    // 既にoriginタイプのポストが存在すればスキップ
    $exists = get_posts([
        'post_type'      => 'satoya_member',
        'post_status'    => ['publish', 'pending', 'draft'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => [
            ['key' => 'satoya_type', 'value' => 'origin', 'compare' => '='],
        ],
    ]);
    if (!empty($exists)) return;

    // 存在しない場合のみ作成
    $post_id = wp_insert_post([
        'post_title'  => '空の駅（おのくんの実家）',
        'post_type'   => 'satoya_member',
        'post_status' => 'publish',
        'post_author' => 1,
    ]);

    if (is_wp_error($post_id)) return;

    update_post_meta($post_id, 'satoya_type',           'origin');
    update_post_meta($post_id, 'satoya_store_name',     '空の駅');
    update_post_meta($post_id, 'satoya_location_type',  'japan');
    update_post_meta($post_id, 'satoya_prefecture',     '宮城県');
    update_post_meta($post_id, 'satoya_city',           '東松島市');
    update_post_meta($post_id, 'satoya_latitude',       '38.3964117');
    update_post_meta($post_id, 'satoya_longitude',      '141.1750738');
    update_post_meta($post_id, 'satoya_google_map_url', 'https://www.google.com/maps/place/%E7%A9%BA%E3%81%AE%E9%A7%85/@38.3964117,141.1750738,17z');
    update_post_meta($post_id, 'satoya_website_url',    'https://onokun.com/');
    update_post_meta($post_id, 'satoya_message',        '宮城県東松島市にある、おのくん発祥の地。東日本大震災の記憶と希望を伝え続ける場所です。ここから全国・世界へ旅立ったおのくんの"実家"です。');
}
