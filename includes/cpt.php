<?php
if (!defined('ABSPATH')) exit;

function satoya_register_post_type() {
    register_post_type('satoya_member', [
        'labels' => [
            'name'               => '里親メンバー',
            'singular_name'      => '里親メンバー',
            'add_new'            => '新規追加',
            'add_new_item'       => '新しい里親メンバーを追加',
            'edit_item'          => '里親メンバーを編集',
            'view_item'          => '里親メンバーを表示',
            'all_items'          => 'すべての里親メンバー',
            'search_items'       => '里親メンバーを検索',
            'not_found'          => '里親メンバーが見つかりません',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'supports'      => ['title', 'thumbnail'],
        'menu_icon'     => 'dashicons-groups',
        'menu_position' => 25,
    ]);
}
add_action('init', 'satoya_register_post_type');

// ---- Meta Box ----
add_action('add_meta_boxes', function () {
    add_meta_box(
        'satoya_member_details',
        '里親メンバー詳細情報',
        'satoya_member_meta_box_cb',
        'satoya_member',
        'normal',
        'high'
    );
});

function satoya_member_meta_box_cb($post) {
    wp_nonce_field('satoya_member_save', 'satoya_member_nonce');
    $fields = [
        'satoya_email'          => 'メールアドレス',
        'satoya_nickname'       => 'ニックネーム',
        'satoya_location_type'  => '居住地タイプ（japan / abroad）',
        'satoya_prefecture'     => '都道府県',
        'satoya_city'           => '市区町村',
        'satoya_country'        => '国名（海外）',
        'satoya_city_abroad'    => '都市名（海外）',
        'satoya_type'           => '区分（individual / store / corporate）',
        'satoya_store_name'     => '店舗名・法人名',
        'satoya_google_map_url' => 'GoogleマップURL',
        'satoya_website_url'    => 'ウェブサイトURL（店舗・法人）',
        'satoya_line_url'       => 'LINEオープンチャットURL',
        'satoya_latitude'       => '緯度（ピン用）',
        'satoya_longitude'      => '経度（ピン用）',
        'satoya_ip'             => '登録IPアドレス',
    ];
    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $val = get_post_meta($post->ID, $key, true);
        echo "<tr><th><label for='{$key}'>{$label}</label></th><td>";
        echo "<input type='text' id='{$key}' name='{$key}' value='" . esc_attr($val) . "' style='width:100%'>";
        echo '</td></tr>';
    }
    $msg = get_post_meta($post->ID, 'satoya_message', true);
    echo "<tr><th><label for='satoya_message'>メッセージ</label></th><td>";
    echo "<textarea id='satoya_message' name='satoya_message' rows='3' style='width:100%'>" . esc_textarea($msg) . '</textarea>';
    echo '</td></tr></table>';

    // Store geocode helper
    echo '<p style="margin-top:12px;"><strong>店舗の緯度・経度の調べ方：</strong> GoogleマップでURLをコピー→ <code>https://maps.google.com/?q=緯度,経度</code> 形式から取得、または <a href="https://www.latlong.net/" target="_blank">latlong.net</a> で検索できます。</p>';
}

add_action('save_post_satoya_member', function ($post_id) {
    if (!isset($_POST['satoya_member_nonce']) || !wp_verify_nonce($_POST['satoya_member_nonce'], 'satoya_member_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_fields = [
        'satoya_email', 'satoya_nickname', 'satoya_location_type',
        'satoya_prefecture', 'satoya_city', 'satoya_country', 'satoya_city_abroad',
        'satoya_type', 'satoya_store_name', 'satoya_latitude', 'satoya_longitude', 'satoya_ip',
    ];
    foreach ($text_fields as $f) {
        if (isset($_POST[$f])) {
            update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
        }
    }
    if (isset($_POST['satoya_message'])) {
        update_post_meta($post_id, 'satoya_message', sanitize_textarea_field($_POST['satoya_message']));
    }
    foreach (['satoya_google_map_url', 'satoya_line_url', 'satoya_website_url'] as $url_field) {
        if (isset($_POST[$url_field])) {
            update_post_meta($post_id, $url_field, esc_url_raw($_POST[$url_field]));
        }
    }
});
