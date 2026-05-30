<?php
if (!defined('ABSPATH')) exit;

// ---- Admin Menu ----
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=satoya_member',
        '里親マップ 設定',
        '⚙ 設定',
        'manage_options',
        'satoya-settings',
        'satoya_settings_page_cb'
    );
});

function satoya_settings_page_cb() {
    if (isset($_POST['satoya_save']) && check_admin_referer('satoya_settings_nonce')) {
        $new = [
            'japan_label'   => sanitize_text_field($_POST['japan_label']   ?? '日本マップ'),
            'world_label'   => sanitize_text_field($_POST['world_label']   ?? '世界マップ'),
            'counter_label' => sanitize_text_field($_POST['counter_label'] ?? '総里親数'),
            'register_url'  => esc_url_raw($_POST['register_url'] ?? ''),
        ];
        update_option('satoya_settings', $new);
        echo '<div class="notice notice-success is-dismissible"><p>設定を保存しました。</p></div>';
    }

    $s = get_option('satoya_settings', [
        'japan_label'   => '日本マップ',
        'world_label'   => '世界マップ',
        'counter_label' => '総里親数',
    ]);
    $pending_count = wp_count_posts('satoya_member')->pending ?? 0;
    ?>
    <div class="wrap">
        <h1>🗺 里親コミュニティMAP 設定</h1>

        <?php if ($pending_count > 0): ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php echo $pending_count; ?>件</strong>の未承認申請があります。
                <a href="<?php echo admin_url('edit.php?post_type=satoya_member&post_status=pending'); ?>">確認する</a>
            </p>
        </div>
        <?php endif; ?>

        <div class="card" style="max-width:700px;padding:20px;margin:20px 0;">
            <h2>📌 ショートコードの使い方</h2>
            <table class="widefat" style="margin-top:10px;">
                <thead><tr><th>ショートコード</th><th>機能</th></tr></thead>
                <tbody>
                    <tr><td><code>[satoya_map]</code></td><td>日本・世界マップと総里親数カウンターを表示</td></tr>
                    <tr><td><code>[satoya_form]</code></td><td>里親登録フォームを表示</td></tr>
                </tbody>
            </table>
            <p style="margin-top:12px;">
                <strong>ページへの設置方法：</strong><br>
                固定ページの本文に上記ショートコードを貼り付けるだけで表示されます。<br>
                例：「里親コミュニティMAP」ページに <code>[satoya_map]</code> を、<br>
                「里親登録フォーム」ページに <code>[satoya_form]</code> を設置してください。
            </p>
        </div>

        <div class="card" style="max-width:700px;padding:20px;margin:20px 0;">
            <h2>📝 里親申請の承認方法</h2>
            <ol>
                <li>左メニュー「里親メンバー」→「すべての里親メンバー」を開く</li>
                <li>「保留中」タブで未承認の申請を確認する</li>
                <li>内容を確認し、問題なければ「公開」に変更する</li>
                <li>公開に変更した時点で、マップのカウントに自動で反映される</li>
            </ol>
            <p><strong>店舗登録の場合：</strong> 管理画面で「緯度」「経度」を入力するとマップにピンが表示されます。<br>
            <a href="https://www.latlong.net/" target="_blank">latlong.net</a> でお店の住所から緯度・経度を調べられます。</p>
        </div>

        <form method="post" style="max-width:700px;">
            <?php wp_nonce_field('satoya_settings_nonce'); ?>
            <h2>🔤 表示テキストの設定</h2>
            <table class="form-table">
                <tr>
                    <th><label for="japan_label">日本マップボタンのラベル</label></th>
                    <td>
                        <input type="text" id="japan_label" name="japan_label"
                               value="<?php echo esc_attr($s['japan_label']); ?>"
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="world_label">世界マップボタンのラベル</label></th>
                    <td>
                        <input type="text" id="world_label" name="world_label"
                               value="<?php echo esc_attr($s['world_label']); ?>"
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="counter_label">カウンターのラベル</label></th>
                    <td>
                        <input type="text" id="counter_label" name="counter_label"
                               value="<?php echo esc_attr($s['counter_label']); ?>"
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="register_url">里親登録フォームのURL</label></th>
                    <td>
                        <input type="url" id="register_url" name="register_url"
                               value="<?php echo esc_attr($s['register_url'] ?? ''); ?>"
                               class="regular-text"
                               placeholder="例：https://onokun.com/revolink/#revolink-form-bottom">
                        <p class="description">都道府県クリック時の「里親になる」ボタンのリンク先</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="satoya_save" value="設定を保存" class="button-primary">
            </p>
        </form>
    </div>
    <?php
}

// ---- Admin list columns for satoya_member ----
add_filter('manage_satoya_member_posts_columns', function ($cols) {
    return [
        'cb'               => $cols['cb'],
        'title'            => '名前',
        'satoya_email'     => 'メール',
        'satoya_type'      => '区分',
        'satoya_location'  => '居住地',
        'satoya_line'      => 'LINE URL',
        'date'             => '申請日',
    ];
});

add_action('manage_satoya_member_posts_custom_column', function ($col, $post_id) {
    switch ($col) {
        case 'satoya_email':
            echo esc_html(get_post_meta($post_id, 'satoya_email', true));
            break;
        case 'satoya_type':
            $t = get_post_meta($post_id, 'satoya_type', true);
            echo $t === 'store' ? '🏪 店舗' : '👤 個人';
            break;
        case 'satoya_location':
            $type = get_post_meta($post_id, 'satoya_location_type', true);
            if ($type === 'japan') {
                echo esc_html(get_post_meta($post_id, 'satoya_prefecture', true));
                $city = get_post_meta($post_id, 'satoya_city', true);
                if ($city) echo ' ' . esc_html($city);
            } else {
                echo '🌍 ' . esc_html(get_post_meta($post_id, 'satoya_country', true));
            }
            break;
        case 'satoya_line':
            $url = get_post_meta($post_id, 'satoya_line_url', true);
            if ($url) echo '<a href="' . esc_url($url) . '" target="_blank">リンク</a>';
            break;
    }
}, 10, 2);
