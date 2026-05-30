<?php
if (!defined('ABSPATH')) exit;

// ---- Shortcode ----
add_shortcode('satoya_form', 'satoya_render_form');

function satoya_render_form() {
    $redirect = set_url_scheme(get_permalink() ?: home_url('/'));
    $prefectures = [
        '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
        '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
        '新潟県','富山県','石川県','福井県','山梨県','長野県',
        '岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府',
        '兵庫県','奈良県','和歌山県',
        '鳥取県','島根県','岡山県','広島県','山口県',
        '徳島県','香川県','愛媛県','高知県',
        '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県',
    ];
    ob_start();
    ?>
    <div id="satoya-form-wrapper">
        <?php if (!empty($_GET['satoya_ok'])): ?>
        <div class="satoya-notice satoya-success">
            <p>✅ 登録申請を受け付けました！管理者が確認後、マップに反映されます。</p>
        </div>
        <?php elseif (!empty($_GET['satoya_err'])): ?>
        <div class="satoya-notice satoya-error">
            <p>⚠️ <?php echo esc_html(urldecode($_GET['satoya_err'])); ?></p>
        </div>
        <?php endif; ?>

        <form id="satoya-reg-form" method="post" enctype="multipart/form-data"
              action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('satoya_register', 'satoya_nonce'); ?>
            <input type="hidden" name="action" value="satoya_register_member">
            <input type="hidden" name="satoya_redirect" value="<?php echo esc_attr($redirect); ?>">

            <div class="satoya-field">
                <label>ニックネーム <span class="req">*</span></label>
                <input type="text" name="satoya_nickname" required maxlength="50"
                       placeholder="例：おのくんファン">
            </div>

            <div class="satoya-field">
                <label>メールアドレス（重複防止用・公開されません）<span class="req">*</span></label>
                <input type="email" name="satoya_email" required
                       placeholder="your@email.com">
                <small>同じメールアドレスでの二重登録はできません</small>
            </div>

            <div class="satoya-field">
                <label>居住地</label>
                <div class="satoya-radio-group">
                    <label class="satoya-radio">
                        <input type="radio" name="satoya_location_type" value="japan" checked>
                        <span>🇯🇵 国内</span>
                    </label>
                    <label class="satoya-radio">
                        <input type="radio" name="satoya_location_type" value="abroad">
                        <span>🌍 海外</span>
                    </label>
                </div>
            </div>

            <div id="satoya-japan-fields">
                <div class="satoya-field">
                    <label>都道府県</label>
                    <select name="satoya_prefecture">
                        <option value="">選択してください</option>
                        <?php foreach ($prefectures as $pref): ?>
                        <option value="<?php echo esc_attr($pref); ?>"><?php echo esc_html($pref); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="satoya-field">
                    <label>市区町村</label>
                    <input type="text" name="satoya_city" maxlength="100" placeholder="例：仙台市">
                </div>
            </div>

            <div id="satoya-abroad-fields" style="display:none;">
                <div class="satoya-field">
                    <label>国名</label>
                    <input type="text" name="satoya_country" maxlength="100"
                           placeholder="例：United States / アメリカ">
                </div>
                <div class="satoya-field">
                    <label>都市名</label>
                    <input type="text" name="satoya_city_abroad" maxlength="100"
                           placeholder="例：New York">
                </div>
            </div>

            <div class="satoya-field">
                <label>区分 <span class="req">*</span></label>
                <div class="satoya-radio-group">
                    <label class="satoya-radio">
                        <input type="radio" name="satoya_type" value="individual" checked>
                        <span>👤 個人</span>
                    </label>
                    <label class="satoya-radio">
                        <input type="radio" name="satoya_type" value="store">
                        <span>🏪 飲食店・店舗（来客歓迎）</span>
                    </label>
                    <label class="satoya-radio">
                        <input type="radio" name="satoya_type" value="corporate">
                        <span>🏢 法人・企業・団体</span>
                    </label>
                </div>
            </div>

            <div id="satoya-store-fields" style="display:none;">
                <div class="satoya-field">
                    <label>お店の名前</label>
                    <input type="text" name="satoya_store_name" maxlength="100"
                           placeholder="例：カフェおのくん">
                </div>
                <div class="satoya-field">
                    <label>GoogleマップのURL（お店の場所）</label>
                    <input type="url" name="satoya_google_map_url"
                           placeholder="https://maps.google.com/...">
                    <small>Googleマップでお店を開き、URLをコピーして貼り付けてください</small>
                </div>
                <div class="satoya-field">
                    <label>お店のウェブサイト（任意）</label>
                    <input type="url" name="satoya_website_url"
                           placeholder="https://example.com/">
                </div>
            </div>

            <div id="satoya-corporate-fields" style="display:none;">
                <div class="satoya-field">
                    <label>法人名・団体名</label>
                    <input type="text" name="satoya_store_name" maxlength="100"
                           placeholder="例：株式会社〇〇 / NPO法人〇〇">
                </div>
                <div class="satoya-field">
                    <label>ウェブサイトURL（任意）</label>
                    <input type="url" name="satoya_website_url"
                           placeholder="https://example.com/">
                </div>
                <div class="satoya-field">
                    <label>GoogleマップのURL（所在地・任意）</label>
                    <input type="url" name="satoya_google_map_url"
                           placeholder="https://maps.google.com/...">
                    <small>マップにピン表示したい場合はGoogleマップURLを入力してください</small>
                </div>
            </div>

            <div class="satoya-field">
                <label>ひとことメッセージ</label>
                <textarea name="satoya_message" rows="4" maxlength="500"
                          placeholder="自己紹介や一言メッセージをどうぞ（500文字以内）"></textarea>
            </div>

            <div class="satoya-field">
                <label>LINEオープンチャット等のURL</label>
                <input type="url" name="satoya_line_url"
                       placeholder="https://line.me/ti/g2/...">
            </div>

            <div class="satoya-field">
                <label>写真（任意・5MB以内）</label>
                <input type="file" name="satoya_photo" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="satoya-field satoya-submit-area">
                <button type="submit" class="satoya-submit-btn">
                    里親として登録申請する ➤
                </button>
                <p class="satoya-submit-note">
                    ※管理者が内容を確認後、マップに反映されます
                </p>
            </div>
        </form>
    </div>

    <script>
    (function(){
        var form = document.getElementById('satoya-reg-form');
        if (!form) return;

        // Toggle Japan / Abroad
        form.querySelectorAll('[name="satoya_location_type"]').forEach(function(r){
            r.addEventListener('change', function(){
                document.getElementById('satoya-japan-fields').style.display =
                    this.value === 'japan' ? '' : 'none';
                document.getElementById('satoya-abroad-fields').style.display =
                    this.value === 'abroad' ? '' : 'none';
            });
        });

        // Toggle store / corporate fields
        form.querySelectorAll('[name="satoya_type"]').forEach(function(r){
            r.addEventListener('change', function(){
                document.getElementById('satoya-store-fields').style.display =
                    this.value === 'store' ? '' : 'none';
                document.getElementById('satoya-corporate-fields').style.display =
                    this.value === 'corporate' ? '' : 'none';
            });
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}

// ---- Form Submission Handler ----
add_action('admin_post_satoya_register_member', 'satoya_handle_registration');
add_action('admin_post_nopriv_satoya_register_member', 'satoya_handle_registration');

function satoya_handle_registration() {
    $redirect = !empty($_POST['satoya_redirect'])
        ? esc_url_raw($_POST['satoya_redirect'])
        : home_url('/');

    // Nonce check
    if (empty($_POST['satoya_nonce']) || !wp_verify_nonce($_POST['satoya_nonce'], 'satoya_register')) {
        wp_redirect(add_query_arg('satoya_err', 'セキュリティエラーが発生しました。', $redirect));
        exit;
    }

    $nickname = sanitize_text_field($_POST['satoya_nickname'] ?? '');
    $email    = sanitize_email($_POST['satoya_email'] ?? '');

    if (empty($nickname) || empty($email) || !is_email($email)) {
        wp_redirect(add_query_arg('satoya_err', 'ニックネームとメールアドレスは必須です。', $redirect));
        exit;
    }

    // ===== DUPLICATE PREVENTION =====

    // 1. Email uniqueness check (most reliable)
    $existing = get_posts([
        'post_type'      => 'satoya_member',
        'post_status'    => ['publish', 'pending', 'draft'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => [
            ['key' => 'satoya_email', 'value' => $email, 'compare' => '='],
        ],
    ]);
    if (!empty($existing)) {
        wp_redirect(add_query_arg('satoya_err', 'このメールアドレスは既に登録されています。', $redirect));
        exit;
    }

    // 2. Browser cookie check (prevents same browser re-submitting within 7 days)
    if (!empty($_COOKIE['satoya_registered_v1'])) {
        wp_redirect(add_query_arg('satoya_err', '既に登録申請を受け付けています。審査をお待ちください。', $redirect));
        exit;
    }

    // 3. IP rate-limit (max 3 submissions per IP per 24h via WordPress transients)
    $ip             = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    $ip_key         = 'satoya_ip_' . md5($ip);
    $ip_submissions = (int) get_transient($ip_key);
    if ($ip_submissions >= 3) {
        wp_redirect(add_query_arg('satoya_err', '短時間に多くの申請が届いています。しばらくお待ちください。', $redirect));
        exit;
    }

    // ===== SAVE REGISTRATION =====
    $location_type = sanitize_text_field($_POST['satoya_location_type'] ?? 'japan');
    $member_type   = sanitize_text_field($_POST['satoya_type'] ?? 'individual');
    $store_name    = sanitize_text_field($_POST['satoya_store_name'] ?? '');

    $post_title = ($member_type === 'store' && !empty($store_name))
        ? $store_name . '（' . $nickname . '）'
        : $nickname;

    $post_id = wp_insert_post([
        'post_title'  => $post_title,
        'post_type'   => 'satoya_member',
        'post_status' => 'pending',
        'post_author' => 1,
    ], true);

    if (is_wp_error($post_id)) {
        wp_redirect(add_query_arg('satoya_err', '登録処理中にエラーが発生しました。お手数ですが再度お試しください。', $redirect));
        exit;
    }

    // Save meta
    $meta = [
        'satoya_email'         => $email,
        'satoya_nickname'      => $nickname,
        'satoya_location_type' => $location_type,
        'satoya_type'          => $member_type,
        'satoya_message'       => sanitize_textarea_field($_POST['satoya_message'] ?? ''),
        'satoya_ip'            => $ip,
    ];
    if (!empty($_POST['satoya_line_url'])) {
        $meta['satoya_line_url'] = esc_url_raw($_POST['satoya_line_url']);
    }
    if ($location_type === 'japan') {
        $meta['satoya_prefecture'] = sanitize_text_field($_POST['satoya_prefecture'] ?? '');
        $meta['satoya_city']       = sanitize_text_field($_POST['satoya_city'] ?? '');
    } else {
        $meta['satoya_country']     = sanitize_text_field($_POST['satoya_country'] ?? '');
        $meta['satoya_city_abroad'] = sanitize_text_field($_POST['satoya_city_abroad'] ?? '');
    }
    if ($member_type === 'store' || $member_type === 'corporate') {
        $meta['satoya_store_name']     = $store_name;
        $meta['satoya_google_map_url'] = esc_url_raw($_POST['satoya_google_map_url'] ?? '');
        if (!empty($_POST['satoya_website_url'])) {
            $meta['satoya_website_url'] = esc_url_raw($_POST['satoya_website_url']);
        }
        // Attempt to extract lat/lng from Google Maps URL
        $gmap_url = $_POST['satoya_google_map_url'] ?? '';
        if (preg_match('/@([-\d.]+),([-\d.]+)/', $gmap_url, $m)) {
            $meta['satoya_latitude']  = $m[1];
            $meta['satoya_longitude'] = $m[2];
        } elseif (preg_match('/\?q=([-\d.]+),([-\d.]+)/', $gmap_url, $m)) {
            $meta['satoya_latitude']  = $m[1];
            $meta['satoya_longitude'] = $m[2];
        }
    }
    foreach ($meta as $key => $val) {
        update_post_meta($post_id, $key, $val);
    }

    // Photo upload
    if (!empty($_FILES['satoya_photo']['name']) && $_FILES['satoya_photo']['error'] === UPLOAD_ERR_OK) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        $att_id = media_handle_upload('satoya_photo', $post_id);
        if (!is_wp_error($att_id)) {
            set_post_thumbnail($post_id, $att_id);
        }
    }

    // Update IP rate-limit counter
    set_transient($ip_key, $ip_submissions + 1, DAY_IN_SECONDS);

    // Set cookie (7 days, prevents same browser from re-submitting)
    setcookie('satoya_registered_v1', '1', time() + (7 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN, is_ssl());

    // Notify admin
    wp_mail(
        get_option('admin_email'),
        '【おのくん】新しい里親登録申請が届きました',
        sprintf(
            "新しい里親登録申請が届きました。\n\nニックネーム: %s\nメール: %s\n区分: %s\n\n管理画面で確認: %s",
            $nickname,
            $email,
            $member_type === 'store' ? '店舗' : ($member_type === 'corporate' ? '法人・企業' : '個人'),
            admin_url('edit.php?post_type=satoya_member&post_status=pending')
        )
    );

    wp_redirect(add_query_arg('satoya_ok', '1', $redirect));
    exit;
}
