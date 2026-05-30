<?php
if (!defined('ABSPATH')) exit;

// ---- Asset Enqueue ----
add_action('wp_enqueue_scripts', 'satoya_enqueue_map_assets');
function satoya_enqueue_map_assets() {
    global $post;
    if (!is_a($post, 'WP_Post')) return;
    $has_map  = has_shortcode($post->post_content, 'satoya_map');
    $has_form = has_shortcode($post->post_content, 'satoya_form');
    if (!$has_map && !$has_form) return;

    // Leaflet CSS/JS (free, no API key needed)
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
    wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);

    // CSS はファイル配信（変更頻度が低いため許容）
    $css_ver = SATOYA_VERSION . '.' . @filemtime(SATOYA_PATH . 'assets/css/satoya-map.css');
    wp_enqueue_style('satoya-map', SATOYA_URL . 'assets/css/satoya-map.css', [], $css_ver);

    // JS はインライン出力（ファイルキャッシュを完全回避）
    wp_register_script('satoya-map', false, ['leaflet'], null, true);
    wp_enqueue_script('satoya-map');

    $s = get_option('satoya_settings', []);
    wp_localize_script('satoya-map', 'satoyaData', [
        'prefData'    => satoya_get_prefecture_counts(),
        'countryData' => satoya_get_country_counts(),
        'storeData'   => satoya_get_store_locations(),
        'total'       => satoya_get_total_count(),
        'settings'    => $s,
        'registerUrl' => !empty($s['register_url']) ? esc_url($s['register_url']) : 'https://onokun.com/satoya-form/',
    ]);

    // JSファイルの内容をインラインとして登録
    $js_content = @file_get_contents(SATOYA_PATH . 'assets/js/satoya-map.js');
    if ($js_content) {
        wp_add_inline_script('satoya-map', $js_content);
    }
}

// ---- Data Queries ----
function satoya_get_total_count() {
    // origin（場所登録）は里親数に含めない
    $all = (int) (wp_count_posts('satoya_member')->publish ?? 0);
    $origin = count(get_posts([
        'post_type'      => 'satoya_member',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [['key' => 'satoya_type', 'value' => 'origin', 'compare' => '=']],
    ]));
    return max(0, $all - $origin);
}

function satoya_get_prefecture_counts() {
    // origin（場所登録）は里親数にカウントしない
    $members = get_posts([
        'post_type'      => 'satoya_member',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            'relation' => 'AND',
            ['key' => 'satoya_location_type', 'value' => 'japan',  'compare' => '='],
            ['key' => 'satoya_type',           'value' => 'origin', 'compare' => '!='],
        ],
    ]);
    $counts = [];
    foreach ($members as $id) {
        $pref = get_post_meta($id, 'satoya_prefecture', true);
        if ($pref) $counts[$pref] = ($counts[$pref] ?? 0) + 1;
    }
    return $counts;
}

function satoya_get_country_counts() {
    $members = get_posts([
        'post_type'      => 'satoya_member',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            ['key' => 'satoya_location_type', 'value' => 'abroad', 'compare' => '='],
        ],
    ]);
    $counts = [];
    foreach ($members as $id) {
        $country = get_post_meta($id, 'satoya_country', true);
        if ($country) $counts[$country] = ($counts[$country] ?? 0) + 1;
    }
    return $counts;
}

function satoya_get_store_locations() {
    $stores = get_posts([
        'post_type'      => 'satoya_member',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'     => 'satoya_type',
                'value'   => ['store', 'corporate', 'origin'],
                'compare' => 'IN',
            ],
        ],
    ]);
    $out = [];
    foreach ($stores as $id) {
        $lat = get_post_meta($id, 'satoya_latitude', true);
        $lng = get_post_meta($id, 'satoya_longitude', true);
        if (!$lat || !$lng) continue;
        $out[] = [
            'lat'     => (float) $lat,
            'lng'     => (float) $lng,
            'type'    => get_post_meta($id, 'satoya_type', true),
            'pref'    => get_post_meta($id, 'satoya_prefecture', true),
            'name'    => get_post_meta($id, 'satoya_store_name', true) ?: get_the_title($id),
            'msg'     => get_post_meta($id, 'satoya_message', true),
            'line'    => get_post_meta($id, 'satoya_line_url', true),
            'gmap'    => get_post_meta($id, 'satoya_google_map_url', true),
            'website' => get_post_meta($id, 'satoya_website_url', true),
            'img'     => get_the_post_thumbnail_url($id, 'thumbnail') ?: '',
        ];
    }
    return $out;
}

// ---- Featured / Hardcoded Spots（おのくんゆかりの場所）----
function satoya_get_featured_spots() {
    return [
        [
            'lat'     => 38.3964117,
            'lng'     => 141.1750738,
            'type'    => 'origin',
            'pref'    => '宮城県',
            'name'    => '空の駅（おのくんの実家）',
            'desc'    => '宮城県東松島市にある、おのくん発祥の地。東日本大震災の記憶と希望を伝え続ける場所です。',
            'gmap'    => 'https://www.google.com/maps/place/%E7%A9%BA%E3%81%AE%E9%A7%85/@38.3964117,141.1750738,17z',
            'website' => 'https://onokun.com/',
        ],
    ];
}

// ---- Map Shortcode ----
add_shortcode('satoya_map', 'satoya_render_map');

function satoya_render_map() {
    $s     = get_option('satoya_settings', []);
    $total = satoya_get_total_count();
    $japan_label  = esc_html($s['japan_label']   ?? '日本マップ');
    $world_label  = esc_html($s['world_label']   ?? '世界マップ');
    $counter_label = esc_html($s['counter_label'] ?? '総里親数');

    ob_start();
    ?>
    <div id="satoya-map-root">

        <!-- スポット選択パネルはマップ下に表示 -->

        <!-- 総里親数カウンター -->
        <div class="satoya-counter">
            <div class="satoya-counter-inner">
                <span class="satoya-counter-label"><?php echo $counter_label; ?></span>
                <span class="satoya-counter-num"><?php echo $total; ?></span>
                <span class="satoya-counter-unit">名</span>
            </div>
        </div>

        <!-- 切り替えボタン -->
        <div class="satoya-tabs" role="tablist">
            <button class="satoya-tab active" data-target="satoya-japan-view" role="tab">
                🗾 <?php echo $japan_label; ?>
            </button>
            <button class="satoya-tab" data-target="satoya-world-view" role="tab">
                🌍 <?php echo $world_label; ?>
            </button>
        </div>

        <!-- 日本マップ -->
        <div id="satoya-japan-view" class="satoya-map-panel">
            <div id="satoya-japan-map" style="height:520px;width:100%;"></div>
            <div class="satoya-legend">
                <button id="satoya-map-reset" class="satoya-map-reset" style="display:none;" title="全体表示に戻る">🗾 全体表示に戻る</button>
                <span class="legend-ttl">里親数：</span>
                <span class="legend-chip" style="background:#fff5f0;border:1px solid #fdd;"> 0 </span>
                <span class="legend-chip" style="background:#fddbc7;"> 1〜2 </span>
                <span class="legend-chip" style="background:#fc8d59;color:#fff;"> 3〜5 </span>
                <span class="legend-chip" style="background:#d73027;color:#fff;"> 6+ </span>
                <span class="legend-chip" style="background:#fff;border:1px solid #ddd;margin-left:12px;">🏪 店舗</span>
                <span class="legend-chip" style="background:#fff;border:1px solid #ddd;">🏢 法人</span>
                <span class="legend-chip" style="background:#fff5e6;border:1px solid #ffcc88;">🐙 おのくんの実家</span>
            </div>
        </div>

        <!-- スポット詳細パネル（都道府県クリック後に表示） -->
        <div id="satoya-spot-panel" style="display:none;" role="region" aria-label="登録スポット一覧"></div>

        <!-- ===== Googleマップ ピンポイントパネル（スポット選択時に表示）===== -->
        <div id="satoya-gmap-panel" class="satoya-gmap-panel" style="display:none;">
            <div class="satoya-gmap-panel-header">
                <span id="satoya-gmap-title" class="satoya-gmap-title"></span>
                <button class="satoya-gmap-close" id="satoya-gmap-close">✕ 閉じる</button>
            </div>
            <iframe id="satoya-gmap-iframe"
                    class="satoya-gmap-iframe"
                    src=""
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="ピンポイントマップ">
            </iframe>
        </div>

        <!-- ===== 登録スポット一覧（初期非表示・スポット選択時に表示）===== -->
        <?php $spots = satoya_get_store_locations(); ?>
        <div class="satoya-spot-directory" id="satoya-spot-directory" style="display:none;">
            <div class="satoya-spot-directory-header">
                <div class="satoya-spot-directory-title">
                    📍 登録スポット一覧
                    <span class="satoya-spot-directory-badge"><?php echo count($spots); ?>件</span>
                </div>
                <p class="satoya-spot-directory-desc">クリックすると地図がその場所にズームします</p>
            </div>

            <?php if (!empty($spots)): ?>
            <div class="satoya-spot-directory-grid">
                <?php foreach ($spots as $i => $spot):
                    $type      = $spot['type'] ?? 'store';
                    $type_icon = $type === 'origin' ? '🏠' : ($type === 'corporate' ? '🏢' : '🏪');
                    $type_label= $type === 'origin' ? 'おのくんの実家' : ($type === 'corporate' ? '法人・企業' : '店舗・カフェ');
                    $city      = $spot['city'] ?? '';
                    $addr      = trim(($spot['pref'] ?? '') . ($city ? ' ' . $city : ''));
                ?>
                <button class="satoya-spot-card"
                        data-lat="<?php echo esc_attr($spot['lat']); ?>"
                        data-lng="<?php echo esc_attr($spot['lng']); ?>"
                        data-idx="<?php echo $i; ?>"
                        data-type="<?php echo esc_attr($type); ?>">
                    <span class="satoya-spot-card-icon"><?php echo $type_icon; ?></span>
                    <span class="satoya-spot-card-body">
                        <span class="satoya-spot-card-type"><?php echo esc_html($type_label); ?></span>
                        <span class="satoya-spot-card-name"><?php echo esc_html($spot['name']); ?></span>
                        <?php if ($addr): ?>
                        <span class="satoya-spot-card-addr"><?php echo esc_html($addr); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="satoya-spot-card-zoom">地図で見る →</span>
                </button>
                <?php endforeach; ?>

                <!-- 登録募集カード（デザインの見せ方） -->
                <div class="satoya-spot-card satoya-spot-card--add">
                    <span class="satoya-spot-card-icon">＋</span>
                    <span class="satoya-spot-card-body">
                        <span class="satoya-spot-card-type">あなたのお店・施設</span>
                        <span class="satoya-spot-card-name">里親登録するとここに表示</span>
                        <span class="satoya-spot-card-addr">店舗・法人・カフェ歓迎</span>
                    </span>
                    <span class="satoya-spot-card-zoom">登録する →</span>
                </div>
            </div>
            <?php else: ?>
            <p class="satoya-spot-directory-empty">まだスポット登録がありません。最初の登録をお待ちしています！</p>
            <?php endif; ?>
        </div>

        <!-- 世界マップ -->
        <div id="satoya-world-view" class="satoya-map-panel" style="display:none;">
            <div id="satoya-world-map" style="height:520px;width:100%;"></div>
        </div>

    </div>
    <?php
    return ob_get_clean();
}

// ---- AJAX: get latest count (for live update if needed) ----
add_action('wp_ajax_satoya_get_count', 'satoya_ajax_count');
add_action('wp_ajax_nopriv_satoya_get_count', 'satoya_ajax_count');
function satoya_ajax_count() {
    wp_send_json_success(['total' => satoya_get_total_count()]);
}
