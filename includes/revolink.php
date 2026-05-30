<?php
/**
 * Revolink LP — shortcode [revolink_lp]
 * 里親コミュニティMAP プラグイン同梱
 * v1.8.0
 */
if (!defined('ABSPATH')) exit;

// ---- Asset enqueue (revolink ページのみ) ----
add_action('wp_enqueue_scripts', function () {
    global $post;
    if (!is_a($post, 'WP_Post')) return;
    if (!has_shortcode($post->post_content, 'revolink_page') && !has_shortcode($post->post_content, 'revolink_lp')) return;
    wp_enqueue_style('satoya-map', SATOYA_URL . 'assets/css/satoya-map.css', [], SATOYA_VERSION);
});

// ---- SEO: Schema.org JSON-LD + OGP + Meta (priority 5 = wp_head より前) ----
add_action('wp_head', function () {
    global $post;
    if (!is_a($post, 'WP_Post')) return;
    if (!has_shortcode($post->post_content, 'revolink_page') && !has_shortcode($post->post_content, 'revolink_lp')) return;

    $page_url = get_permalink();
    $img_base = SATOYA_URL . 'assets/images/';

    // ── プリロード（LCP最適化）──
    $hero_img = esc_url($img_base . 'chara-a.jpeg');
    echo '<link rel="preload" as="image" href="' . $hero_img . '" fetchpriority="high">' . "\n";

    // ── DNS Prefetch（外部リソース高速化）──
    echo '<link rel="dns-prefetch" href="//onokun.com">' . "\n";
    echo '<link rel="preconnect" href="https://onokun.com" crossorigin>' . "\n";

    // ── Author / Publisher ──
    echo '<meta name="author" content="レボリストLab / おのくん公式">' . "\n";
    echo '<link rel="author" href="https://onokun.com/about/">' . "\n";
    echo '<link rel="publisher" href="https://onokun.com/">' . "\n";

    // ── Meta Description / Keywords（Yoast・RankMath が未導入の場合のみ）──
    if (!defined('WPSEO_VERSION') && !defined('RANK_MATH_VERSION')) {
        $desc = 'おのくんを中心とした里親コミュニティ「Revolink（レボリンク）」。あなたのホームページに社会貢献型広告収入バナーを設置するだけで東日本大震災から続くおのくんの活動を支援できます。参加無料・審査なし。';
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
        echo '<meta name="keywords" content="Revolink,レボリンク,おのくん,里親コミュニティ,社会貢献,広告収入,スポンサープログラム,東松島,防災,レボリストLab,防災×帽祭,里親カフェ,東日本大震災">' . "\n";
        echo '<meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">' . "\n";
        echo '<link rel="canonical" href="' . esc_url($page_url) . '">' . "\n";

        // OGP
        echo '<meta property="og:type"        content="website">' . "\n";
        echo '<meta property="og:locale"      content="ja_JP">' . "\n";
        echo '<meta property="og:url"         content="' . esc_url($page_url) . '">' . "\n";
        echo '<meta property="og:site_name"   content="おのくん公式サイト | レボリストLab">' . "\n";
        echo '<meta property="og:title"       content="Revolink（レボリンク）— おのくんが繋ぐ、共創コミュニティ | 参加無料">' . "\n";
        echo '<meta property="og:description" content="日常の消費を社会貢献の連鎖に変える。HPにバナーを1つ設置するだけで東日本大震災から続くおのくんの活動を支援できます。参加無料・審査なし。">' . "\n";
        echo '<meta property="og:image"       content="' . esc_url($img_base . 'revolist-lab.png') . '">' . "\n";
        echo '<meta property="og:image:width"  content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
        echo '<meta property="og:image:alt"    content="レボリストLab 6つのプロジェクト エコシステム図">' . "\n";

        // Twitter Card
        echo '<meta name="twitter:card"        content="summary_large_image">' . "\n";
        echo '<meta name="twitter:site"        content="@hayator">' . "\n";
        echo '<meta name="twitter:creator"     content="@hayator">' . "\n";
        echo '<meta name="twitter:title"       content="Revolink — おのくんが繋ぐ共創コミュニティ | 参加無料">' . "\n";
        echo '<meta name="twitter:description" content="日常消費を社会貢献の連鎖に変える。HPにバナー1つ設置するだけ。参加無料・審査なし。">' . "\n";
        echo '<meta name="twitter:image"       content="' . esc_url($img_base . 'revolist-lab.png') . '">' . "\n";
        echo '<meta name="twitter:image:alt"   content="レボリストLab 6つのプロジェクト エコシステム図">' . "\n";
    }

    // ── Schema.org JSON-LD ──
    $modified = get_the_modified_date('c', $post);
    $published = get_the_date('c', $post);

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [

            /* WebSite with SearchAction（サイトリンク検索ボックス） */
            [
                '@type'  => 'WebSite',
                '@id'    => 'https://onokun.com/#website',
                'url'    => 'https://onokun.com/',
                'name'   => 'おのくん公式サイト | レボリストLab',
                'inLanguage' => 'ja',
                'publisher'  => ['@id' => 'https://onokun.com/#organization'],
            ],

            /* Organization */
            [
                '@type'       => 'Organization',
                '@id'         => 'https://onokun.com/#organization',
                'name'        => 'レボリストLab / おのくん公式',
                'alternateName' => ['おのくん', 'REVOLIST Lab', 'Revolink'],
                'url'         => 'https://onokun.com/',
                'logo'        => [
                    '@type'  => 'ImageObject',
                    '@id'    => 'https://onokun.com/#logo',
                    'url'    => esc_url($img_base . 'chara-a.jpeg'),
                    'width'  => 420,
                    'height' => 500,
                    'caption'=> 'おのくん（東松島発・里親キャラクター）',
                ],
                'description' => '東日本大震災から生まれた里親キャラクター「おのくん」を軸に、防災文化・地域コミュニティ・社会貢献型広告収入エコシステムを運営するクリエイティブラボ。',
                'foundingDate' => '2011',
                'areaServed'  => 'JP',
                'sameAs'      => [
                    'https://twitter.com/hayator',
                    'https://www.instagram.com/hayator/',
                    'https://revosong.onokun.com/',
                    'https://onokun.com/',
                ],
                'contactPoint' => [
                    '@type'       => 'ContactPoint',
                    'contactType' => 'customer support',
                    'url'         => esc_url($page_url) . '#revolink-form-bottom',
                    'availableLanguage' => 'Japanese',
                ],
            ],

            /* Person（E-E-A-T強化）*/
            [
                '@type'       => 'Person',
                '@id'         => 'https://onokun.com/#founder',
                'name'        => '早冨彩子（はやとみ さやこ）',
                'alternateName' => 'hayator',
                'url'         => 'https://onokun.com/about/',
                'sameAs'      => [
                    'https://twitter.com/hayator',
                    'https://www.instagram.com/hayator/',
                ],
                'worksFor'    => ['@id' => 'https://onokun.com/#organization'],
                'jobTitle'    => 'おのくん活動代表 / レボリストLab ファウンダー',
            ],

            /* WebPage */
            [
                '@type'         => 'WebPage',
                '@id'           => esc_url($page_url) . '#webpage',
                'url'           => esc_url($page_url),
                'name'          => 'Revolink（レボリンク）— おのくんが繋ぐ、共創コミュニティ | 参加無料',
                'description'   => 'あなたのHPに社会貢献型広告収入バナーを設置するだけで、おのくんの活動を支援できる仕組み。参加無料・審査なし。東日本大震災から15年続く里親コミュニティを支えよう。',
                'inLanguage'    => 'ja',
                'isPartOf'      => ['@id' => 'https://onokun.com/#website'],
                'about'         => ['@id' => 'https://onokun.com/#organization'],
                'author'        => ['@id' => 'https://onokun.com/#founder'],
                'publisher'     => ['@id' => 'https://onokun.com/#organization'],
                'datePublished' => $published ?: '2024-01-01',
                'dateModified'  => $modified  ?: date('c'),
                'speakable'     => ['@type' => 'SpeakableSpecification', 'cssSelector' => ['.rl-hero-title', '.rl-hero-catch', '.rl-section-h2']],
                'primaryImageOfPage' => [
                    '@type'   => 'ImageObject',
                    'url'     => esc_url($img_base . 'revolist-lab.png'),
                    'width'   => 1200,
                    'height'  => 900,
                    'caption' => 'レボリストLab 6つのプロジェクト・エコシステム図',
                ],
            ],

            /* BreadcrumbList */
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'おのくん公式サイト', 'item' => 'https://onokun.com/'],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Revolink — 社会貢献型スポンサープログラム', 'item' => esc_url($page_url)],
                ],
            ],

            /* HowTo（STEP1→STEP3 参加フロー）*/
            [
                '@type'       => 'HowTo',
                'name'        => 'Revolinkに参加する3ステップ',
                'description' => 'おのくんを「知る」「広げる」「行動する」の3ステップで、誰でも里親コミュニティを支援できます。',
                'totalTime'   => 'PT10M',
                'step'        => [
                    [
                        '@type' => 'HowToStep',
                        'position' => 1,
                        'name'     => 'STEP 01 — 知る',
                        'text'     => 'おのくんとレボリストLabの活動を知る。東日本大震災から生まれた里親キャラクターの物語を理解することが最初の一歩です。',
                        'url'      => esc_url($page_url) . '#rl-step1',
                    ],
                    [
                        '@type' => 'HowToStep',
                        'position' => 2,
                        'name'     => 'STEP 02 — 広げる',
                        'text'     => 'SNSでシェア・リポストし、おのくんの活動を身近な人に伝える。投稿・友人への紹介・体験発信など、できる形でコミュニティを広げます。',
                        'url'      => esc_url($page_url) . '#rl-step2',
                    ],
                    [
                        '@type' => 'HowToStep',
                        'position' => 3,
                        'name'     => 'STEP 03 — 行動する',
                        'text'     => 'おのくんグッズの購入・里親カフェへの来訪・防災×帽祭イベント参加など、直接的な行動で活動資金と認知拡大につながります。',
                        'url'      => esc_url($page_url) . '#rl-step3',
                    ],
                ],
            ],

            /* FAQPage — よくある質問 */
            [
                '@type' => 'FAQPage',
                'mainEntity' => [
                    [
                        '@type' => 'Question',
                        'name'  => 'Revolink（レボリンク）とは何ですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Revolinkは、あなたのホームページやブログに専用バナーリンクを設置するだけで、日常の消費を社会貢献型の広告収入に変える仕組みです。おのくんを中心とした里親コミュニティの活動基盤を支えます。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => 'Revolinkのスポンサーになるには費用がかかりますか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => '参加費・月額費用は一切かかりません。審査も不要です。申込みフォームに記入し、専用バナーをホームページに設置するだけで参加できます。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => 'どのようなウェブサイトでも参加できますか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => '個人ブログ・店舗公式サイト・アーティストのポートフォリオ・里親カフェのHP・SNSリンクページ（Linktree等）・noteなど、発信の場であればほぼどこでも参加可能です。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => 'おのくんとは何者ですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'おのくんは2011年の東日本大震災後に宮城県東松島市で生まれた靴下のキャラクターです。全国の「里親」さんたちに届けられ、防災文化・地域コミュニティづくりの活動を続けています。詳しくはおのくん公式サイト（https://onokun.com/）をご覧ください。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => 'Revolinkの広告収入はどこに使われますか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'おのくんの活動継続・防災×帽祭イベントの運営費・レボリストLabの6つのプロジェクト全体の持続可能な運営資金として活用されます。補助金に頼らない自走型の仕組みです。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => '防災×帽祭とはどんなプロジェクトですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => '楽しみながら防災体験を積むエンタメイベントです。ハット・ファッションショー・アート・ダンスで防災を楽しい体験の入口に変え、1500万人へのリーチを目指しています。詳しくはhttps://onokun.com/bousai/をご覧ください。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => '里親コミュニティMAPとは何ですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => '全国のおのくん里親さんの活動場所をマップで可視化したサービスです。あなたの近くの里親カフェやお店を地図から探せます。https://onokun.com/satoya-map/でご覧いただけます。'],
                    ],
                ],
            ],

        ], // @graph end
    ]; // $schema end

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 5);

// ---- Shortcode（両方対応）----
add_shortcode('revolink_page', 'revolink_render_lp');
add_shortcode('revolink_lp',   'revolink_render_lp');

function revolink_render_lp() {
    $img_url = SATOYA_URL . 'assets/images/';

    /* ---- プロジェクトURL ---- */
    $url = [
        'bousai'   => 'https://revolist.earth/bosai-bosai',
        'revoart'  => 'https://revofunding.onokun.com/revo-art.html',
        'revosong' => 'https://revosong.onokun.com/',
        'revofund' => 'https://revofunding.onokun.com/',
        'revohat'  => 'https://onokun.com/hat-model-academy/',
        'revolink' => 'https://onokun.com/socially-responsible-sponsorship/',
        'onokun'   => 'https://onokun.com/',
        'map'      => 'https://onokun.com/satoya-map/',
    ];
    ob_start();
    ?>

    <!-- ===== Sticky Nav ===== -->
    <nav class="rl-lp-nav" id="rl-lp-nav" aria-label="Revolink 目次">
        <div class="rl-lp-nav-inner">
            <a href="#rl-what"       class="rl-lp-nav-item" data-for="what">🐙 Revolinkとは</a>
            <a href="#rl-contents"   class="rl-lp-nav-item" data-for="contents">📋 できること</a>
            <a href="#rl-sponsor"    class="rl-lp-nav-item" data-for="sponsor">💡 スポンサー</a>
            <a href="#rl-step1"      class="rl-lp-nav-item" data-for="step1">👀 知る</a>
            <a href="#rl-step2"      class="rl-lp-nav-item" data-for="step2">📢 広げる</a>
            <a href="#rl-step3"      class="rl-lp-nav-item" data-for="step3">🙌 行動する</a>
            <a href="#rl-values"       class="rl-lp-nav-item" data-for="values">💡 想いを知る</a>
            <a href="#rl-circulation"  class="rl-lp-nav-item" data-for="circulation">🔄 循環の仕組み</a>
            <a href="#rl-activities"   class="rl-lp-nav-item" data-for="activities">🌏 プロジェクト</a>
            <a href="#rl-ecosystem"  class="rl-lp-nav-item" data-for="ecosystem">🔬 エコシステム</a>
        </div>
    </nav>

    <!-- ===== Hero ===== -->
    <section class="rl-hero" id="rl-hero">
        <div class="rl-hero-inner">
            <div class="rl-hero-text">
                <span class="rl-hero-badge">Revolink — レボリンク</span>
                <h1 class="rl-hero-title">
                    おのくん🐙が繋ぐ、<br>
                    <span>共創コミュニティ</span>へ
                </h1>
                <p class="rl-hero-catch">
                    日常のつながりを、社会貢献の連鎖に変える。<br>
                    あなたの「知る」「広げる」「行動する」が、<br>
                    おのくんと日本中の里親たちの活動を支えます。
                </p>
                <a href="#revolink-form-bottom" class="rl-hero-cta">📋 今すぐ参加申込む →</a>
                <div class="rl-hero-stats">
                    <div class="rl-stat">
                        <span class="rl-stat-num">15年</span>
                        <span class="rl-stat-label">東日本大震災から続く活動</span>
                    </div>
                    <div class="rl-stat">
                        <span class="rl-stat-num">6つ</span>
                        <span class="rl-stat-label">つながるプロジェクト</span>
                    </div>
                    <div class="rl-stat">
                        <span class="rl-stat-num">全国</span>
                        <span class="rl-stat-label">里親コミュニティ拡大中</span>
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url($img_url . 'chara-a.jpeg'); ?>"
                 alt="おのくん — 東松島発・靴下のキャラクター。全国の里親コミュニティをつなぐシンボル"
                 class="rl-hero-chara"
                 width="420" height="500"
                 fetchpriority="high"
                 decoding="async">
        </div>
    </section>

    <!-- ===== What is Revolink ===== -->
    <section class="rl-what" id="rl-what" data-nav-id="what">
        <div class="rl-what-inner">
            <div>
                <p class="rl-section-eyebrow">What is Revolink</p>
                <h2 class="rl-section-h2">
                    日常の消費を、<br><span>社会貢献の連鎖</span>に変える。
                </h2>
                <p class="rl-section-lead">
                    Revolinkは、「日常生活の消費を社会貢献型の広告収入に変える」仕組みです。
                    あなたがおのくんの活動を知り、SNSで広げ、グッズを手に取るたびに、
                    その行動がレボリストLabのエコシステム全体を循環させます。
                </p>
                <p class="rl-section-lead" style="margin-top:16px;">
                    東日本大震災から生まれたおのくんは、15年間ずっと
                    「いつかのために、今日から備える」という文化を広め続けてきました。
                    Revolinkはそのバックボーンとなる、持続可能な基盤です。
                    外部の補助金に頼らず、コミュニティの力で自走し続けます。
                </p>
                <div class="rl-quote" style="margin-top:28px;">
                    「里親になるとは、おのくんを通じて<br>
                    日本の文化・防災・つながりを育てることです。」
                </div>
            </div>
            <div class="rl-what-img rl-animate" style="--rl-delay:0.1s">
                <img src="<?php echo esc_url($img_url . 'chara-b.png'); ?>"
                     alt="おのくん（座りポーズ）— 日常の消費が社会貢献の連鎖になる「Revolink」を説明するイラスト"
                     width="380" height="380"
                     loading="lazy"
                     decoding="async">
            </div>
        </div>
    </section>

    <!-- ===== 5ブロック参加グリッド ===== -->
    <section id="rl-contents" data-nav-id="contents">
        <div class="rl-participate-wrapper">
            <div class="rl-participate-header">
                <p class="rl-section-eyebrow">5つの参加スタイル</p>
                <h2 class="rl-section-h2">あなたの<span>「できること」</span>から始めよう</h2>
                <p style="font-size:17px;color:#888;margin-top:8px;">— どれかひとつでも大丈夫。できる形で、できる範囲から —</p>
            </div>
            <div class="rl-participate-grid">

                <a href="#rl-step1" class="rl-pcard rl-pcard--amber rl-animate" style="--rl-delay:0s">
                    <span class="rl-pcard-num">01</span>
                    <span class="rl-pcard-icon">🔍</span>
                    <h3 class="rl-pcard-title">知る・見てみる</h3>
                    <p class="rl-pcard-desc">まず知ることが第一歩。SNSをフォローして、おのくんの世界へ。</p>
                    <div class="rl-pcard-tags"><span>検索する</span><span>フォロー</span><span>見てみる</span></div>
                    <span class="rl-pcard-arrow">→</span>
                </a>

                <a href="#rl-step2" class="rl-pcard rl-pcard--sky rl-animate" style="--rl-delay:0.08s">
                    <span class="rl-pcard-num">02</span>
                    <span class="rl-pcard-icon">📢</span>
                    <h3 class="rl-pcard-title">広げる</h3>
                    <p class="rl-pcard-desc">シェア・投稿・口コミ。あなたの一声がコミュニティを広げます。</p>
                    <div class="rl-pcard-tags"><span>シェア</span><span>投稿する</span><span>伝える</span></div>
                    <span class="rl-pcard-arrow">→</span>
                </a>

                <a href="#rl-step3" class="rl-pcard rl-pcard--sage rl-animate" style="--rl-delay:0.16s">
                    <span class="rl-pcard-num">03</span>
                    <span class="rl-pcard-icon">🙌</span>
                    <h3 class="rl-pcard-title">行動で応援する</h3>
                    <p class="rl-pcard-desc">グッズ購入・来店・参加が、活動を直接支える力になります。</p>
                    <div class="rl-pcard-tags"><span>購入する</span><span>来店する</span><span>参加</span></div>
                    <span class="rl-pcard-arrow">→</span>
                </a>

                <a href="#rl-sponsor" class="rl-pcard rl-pcard--teal rl-animate" style="--rl-delay:0.24s">
                    <span class="rl-pcard-num">04</span>
                    <span class="rl-pcard-icon">💡</span>
                    <h3 class="rl-pcard-title">スポンサーになる</h3>
                    <p class="rl-pcard-desc">自分のHPを活用して社会貢献型広告収入スポンサーに。このプロジェクトの核心。</p>
                    <div class="rl-pcard-tags"><span>HP掲載</span><span>広告収入</span><span>社会貢献</span></div>
                    <span class="rl-pcard-arrow">→</span>
                </a>

                <a href="#rl-activities" class="rl-pcard rl-pcard--rose rl-animate" style="--rl-delay:0.32s">
                    <span class="rl-pcard-num">05</span>
                    <span class="rl-pcard-icon">🌏</span>
                    <h3 class="rl-pcard-title">プロジェクトを応援する</h3>
                    <p class="rl-pcard-desc">6つのプロジェクトを応援。社会をアップデートする仲間になる。</p>
                    <div class="rl-pcard-tags"><span>支援する</span><span>広める</span><span>仲間になる</span></div>
                    <span class="rl-pcard-arrow">→</span>
                </a>

            </div>
        </div>
    </section>

    <!-- ===== スポンサープログラム ===== -->
    <section id="rl-sponsor" data-nav-id="sponsor">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f2027 100%);color:#fff;padding:80px 24px;">
        <div style="max-width:1040px;margin:0 auto;">

            <div style="text-align:center;margin-bottom:64px;">
                <p style="font-size:13px;font-weight:800;letter-spacing:3px;color:#38bdf8;margin-bottom:16px;">CORE MECHANISM — このProjectの核心</p>
                <h2 style="font-size:clamp(28px,4vw,48px);font-weight:900;line-height:1.2;margin-bottom:24px;">
                    あなたのホームページが、<br><span style="color:#fb923c;">社会貢献の入口</span>になる。
                </h2>
                <p style="font-size:18px;color:#94a3b8;max-width:680px;margin:0 auto;line-height:1.8;">
                    難しい仕組みは何もありません。<br>
                    あなたが持つホームページやブログに<br>
                    <strong style="color:#fff;">Revolinkのバナーリンクを1つ設置するだけ。</strong><br>
                    それだけで、日常の消費が社会貢献の資金に変わります。
                </p>
            </div>

            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:24px;padding:48px 40px;margin-bottom:56px;">
                <p style="font-size:13px;font-weight:800;letter-spacing:2px;color:#38bdf8;margin-bottom:28px;text-align:center;">HOW IT WORKS — 仕組みの全体像</p>

                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin-bottom:40px;">
                    <div style="text-align:center;padding:24px 12px;background:rgba(251,146,60,0.12);border-radius:16px 0 0 16px;border:1px solid rgba(251,146,60,0.3);">
                        <div style="font-size:32px;margin-bottom:10px;">🏠</div>
                        <div style="font-size:13px;font-weight:800;color:#fb923c;margin-bottom:6px;">あなたのHP</div>
                        <div style="font-size:12px;color:#94a3b8;line-height:1.5;">ブログ・サイト・<br>SNSプロフィールなど</div>
                    </div>
                    <div style="text-align:center;padding:24px 12px;background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.2);border-left:none;">
                        <div style="font-size:32px;margin-bottom:10px;">🔗</div>
                        <div style="font-size:13px;font-weight:800;color:#38bdf8;margin-bottom:6px;">Revolinkバナー掲載</div>
                        <div style="font-size:12px;color:#94a3b8;line-height:1.5;">専用バナー/リンクを<br>設置するだけ</div>
                    </div>
                    <div style="text-align:center;padding:24px 12px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);border-left:none;">
                        <div style="font-size:32px;margin-bottom:10px;">👥</div>
                        <div style="font-size:13px;font-weight:800;color:#4ade80;margin-bottom:6px;">訪問者がクリック</div>
                        <div style="font-size:12px;color:#94a3b8;line-height:1.5;">あなたのHPを訪れた人が<br>自然に広告に触れる</div>
                    </div>
                    <div style="text-align:center;padding:24px 12px;background:rgba(168,85,247,0.1);border-radius:0 16px 16px 0;border:1px solid rgba(168,85,247,0.2);border-left:none;">
                        <div style="font-size:32px;margin-bottom:10px;">💰</div>
                        <div style="font-size:13px;font-weight:800;color:#c084fc;margin-bottom:6px;">広告収入が発生</div>
                        <div style="font-size:12px;color:#94a3b8;line-height:1.5;">収益がおのくんの<br>活動基盤に循環</div>
                    </div>
                </div>

                <div style="background:rgba(0,0,0,0.3);border-radius:16px;padding:28px 32px;">
                    <p style="font-size:13px;font-weight:800;letter-spacing:2px;color:#64748b;margin-bottom:20px;">収益の循環先</p>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <span style="font-size:24px;">🐙</span>
                            <div>
                                <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;">おのくん活動継続</div>
                                <div style="font-size:12px;color:#64748b;line-height:1.5;">東日本大震災の記憶と<br>文化を伝え続ける基盤に</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <span style="font-size:24px;">🎩</span>
                            <div>
                                <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;">防災×帽祭の開催</div>
                                <div style="font-size:12px;color:#64748b;line-height:1.5;">楽しい防災体験イベントの<br>運営費・制作費に</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <span style="font-size:24px;">🌱</span>
                            <div>
                                <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;">エコシステム全体</div>
                                <div style="font-size:12px;color:#64748b;line-height:1.5;">6つのプロジェクト全体の<br>持続可能な運営資金に</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:56px;">
                <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:32px 28px;">
                    <div style="font-size:28px;margin-bottom:14px;">❌</div>
                    <h3 style="font-size:18px;font-weight:800;color:#f87171;margin-bottom:14px;">従来型の社会貢献の課題</h3>
                    <ul style="font-size:15px;color:#94a3b8;line-height:2;list-style:none;padding:0;">
                        <li>• 補助金・助成金に依存 → いつか終わる</li>
                        <li>• 寄付を頼み続ける → 疲弊する</li>
                        <li>• 一部の人だけが負担 → 広がらない</li>
                        <li>• 単発イベント → 文化にならない</li>
                    </ul>
                </div>
                <div style="background:rgba(251,146,60,0.08);border:1px solid rgba(251,146,60,0.25);border-radius:20px;padding:32px 28px;">
                    <div style="font-size:28px;margin-bottom:14px;">✅</div>
                    <h3 style="font-size:18px;font-weight:800;color:#fb923c;margin-bottom:14px;">Revolinkが実現すること</h3>
                    <ul style="font-size:15px;color:#cbd5e1;line-height:2;list-style:none;padding:0;">
                        <li>• 日常の消費が自動的に社会貢献へ</li>
                        <li>• HP掲載者が増えるほど基盤が強くなる</li>
                        <li>• 特別な出費・負担はゼロ</li>
                        <li>• 持続的に循環し続ける仕組み</li>
                    </ul>
                </div>
            </div>

            <div style="text-align:center;background:rgba(251,146,60,0.1);border:2px solid rgba(251,146,60,0.3);border-radius:24px;padding:48px 40px;">
                <p style="font-size:13px;font-weight:800;letter-spacing:3px;color:#fb923c;margin-bottom:16px;">HOW TO JOIN — 参加方法</p>
                <h3 style="font-size:28px;font-weight:900;color:#fff;margin-bottom:12px;">スポンサーになるのは、たった3ステップ</h3>
                <p style="font-size:16px;color:#94a3b8;margin-bottom:40px;">ホームページ・ブログ・リンクツリーなど、あなたが持つ発信の場であればどこでもOK。</p>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:40px;">
                    <div style="background:rgba(0,0,0,0.3);border-radius:16px;padding:28px 20px;">
                        <div style="width:40px;height:40px;background:#fb923c;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;margin:0 auto 16px;">1</div>
                        <div style="font-size:15px;font-weight:800;color:#fff;margin-bottom:8px;">申込みフォームに記入</div>
                        <div style="font-size:13px;color:#64748b;line-height:1.6;">下のフォームからホームページURLと基本情報を登録。審査は不要です。</div>
                    </div>
                    <div style="background:rgba(0,0,0,0.3);border-radius:16px;padding:28px 20px;">
                        <div style="width:40px;height:40px;background:#fb923c;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;margin:0 auto 16px;">2</div>
                        <div style="font-size:15px;font-weight:800;color:#fff;margin-bottom:8px;">専用バナーを受け取る</div>
                        <div style="font-size:13px;color:#64748b;line-height:1.6;">登録後、専用のバナー画像とリンクコードをお送りします。コピペするだけ。</div>
                    </div>
                    <div style="background:rgba(0,0,0,0.3);border-radius:16px;padding:28px 20px;">
                        <div style="width:40px;height:40px;background:#fb923c;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;margin:0 auto 16px;">3</div>
                        <div style="font-size:15px;font-weight:800;color:#fff;margin-bottom:8px;">HPに設置して完了</div>
                        <div style="font-size:13px;color:#64748b;line-height:1.6;">あとは何もしなくていい。あなたのHPを訪れる人が、自然に社会貢献に参加します。</div>
                    </div>
                </div>

                <div style="background:rgba(255,255,255,0.06);border-radius:14px;padding:20px 28px;margin-bottom:32px;text-align:left;display:inline-block;">
                    <p style="font-size:14px;color:#94a3b8;margin:0;line-height:1.7;">
                        <strong style="color:#fff;">対象となるHP・サイト例：</strong><br>
                        個人ブログ / 店舗公式サイト / 作家・アーティストのポートフォリオ / 里親カフェのHP / SNSリンクページ（Linktree等）/ note など
                    </p>
                </div>

                <div>
                    <a href="#revolink-form-bottom" style="display:inline-block;background:linear-gradient(135deg,#ff9f56,#ff6b35);color:#fff;padding:18px 52px;border-radius:40px;font-size:18px;font-weight:bold;box-shadow:0 8px 28px rgba(255,107,53,0.44);text-decoration:none;">
                        💡 今すぐスポンサーとして参加する →
                    </a>
                    <p style="font-size:13px;color:#475569;margin-top:14px;">参加費・月額費用は一切かかりません</p>
                </div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:48px;padding-top:32px;border-top:1px dashed rgba(255,255,255,0.1);">
                <a href="#rl-contents" style="font-size:14px;color:#475569;text-decoration:none;">▲ 目次に戻る</a>
                <a href="#rl-step1" style="font-size:14px;font-weight:700;color:#fb923c;text-decoration:none;">次へ：知る・見てみる →</a>
            </div>
        </div>
    </div>
    </section>

    <!-- ===== STEP1: 知る ===== -->
    <section id="rl-step1" data-nav-id="step1" class="rl-content-section">
        <div class="rl-content-inner">
            <div class="rl-content-sidebar">
                <span class="rl-step-badge">STEP 01</span>
                <h2 class="rl-content-title">👀 知る・<br><span>見てみる</span></h2>
                <p class="rl-sidebar-note">
                    まずは興味を持つことから。<br>
                    正しく説明しようとせず、<br>
                    「なんだろう？」と思ってもらうことが<br>
                    最初の一歩です。
                </p>
            </div>
            <div class="rl-content-body">
                <h3>おのくんって何者？</h3>
                <p>
                    おのくんは、2011年の東日本大震災後に宮城県東松島市で生まれた靴下のキャラクターです。
                    「もう15年経ったね」ではなく、「これからも伝え続ける」という思いを体に宿して、
                    全国の里親さんたちに届けられてきました。
                </p>
                <p>
                    その活動の背景には、「防災を楽しみながら身につく文化にしたい」という
                    レボリストLabのビジョンがあります。帽子をきっかけに、つながり、支え合う。
                    おのくんはそのすべての活動の"根っこ"です。
                </p>

                <h3>Revolinkを知る3つの方法</h3>
                <div class="rl-action-cards">
                    <a class="rl-action-card" href="https://twitter.com/hayator" target="_blank" rel="noopener" title="@hayator のX（旧Twitter）をフォロー">
                        <div class="rl-action-card-icon">📱</div>
                        <div class="rl-action-card-title">SNSをフォロー</div>
                        <div class="rl-action-card-text">@hayator のX（旧Twitter）・Instagram をフォローして最新情報を受け取る</div>
                    </a>
                    <a class="rl-action-card" href="<?php echo esc_url($url['onokun']); ?>" target="_blank" rel="noopener" title="おのくん公式サイト — 活動記録・里親コミュニティ">
                        <div class="rl-action-card-icon">🌐</div>
                        <div class="rl-action-card-title">サイトを見てみる</div>
                        <div class="rl-action-card-text">onokun.com でおのくんの里親コミュニティマップや活動記録を確認する</div>
                    </a>
                    <a class="rl-action-card" href="<?php echo esc_url($url['map']); ?>" target="_blank" rel="noopener" title="里親コミュニティMAP — 全国・世界の里親さんを地図で確認">
                        <div class="rl-action-card-icon">🗺️</div>
                        <div class="rl-action-card-title">里親マップを見る</div>
                        <div class="rl-action-card-text">全国・世界の里親さんがどこにいるかを地図で確認。コミュニティの広がりを実感する</div>
                    </a>
                </div>

                <div class="rl-section-footer">
                    <a href="#rl-contents">▲ 目次に戻る</a>
                    <a href="#rl-step2" class="rl-footer-next">次へ：広げる <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STEP2: 広げる ===== -->
    <section id="rl-step2" data-nav-id="step2" class="rl-content-section">
        <div class="rl-content-inner">
            <div class="rl-content-sidebar">
                <span class="rl-step-badge">STEP 02</span>
                <h2 class="rl-content-title">📢 広げる</h2>
                <p class="rl-sidebar-note">
                    あなたの「いいね」と「シェア」が、<br>
                    おのくんの存在を知らない人へ<br>
                    届ける最短ルートです。
                </p>
            </div>
            <div class="rl-content-body">
                <h3>発信は6段階で文化になる</h3>
                <p>
                    レボリストLabでは、発信を「6つのステージ」で捉えています。
                    あなたの一投稿が積み重なって、やがて文化として根付いていきます。
                </p>
                <div class="rl-steps-flow">
                    <div class="rl-flow-item">
                        <span class="rl-flow-num">STAGE 1</span>
                        <span class="rl-flow-icon">👀</span>
                        <span class="rl-flow-text">知ってもらう</span>
                    </div>
                    <div class="rl-flow-item">
                        <span class="rl-flow-num">STAGE 2</span>
                        <span class="rl-flow-icon">❤️</span>
                        <span class="rl-flow-text">好きになってもらう</span>
                    </div>
                    <div class="rl-flow-item">
                        <span class="rl-flow-num">STAGE 3</span>
                        <span class="rl-flow-icon">🙋</span>
                        <span class="rl-flow-text">自分ごとにしてもらう</span>
                    </div>
                    <div class="rl-flow-item">
                        <span class="rl-flow-num">STAGE 4</span>
                        <span class="rl-flow-icon">🤝</span>
                        <span class="rl-flow-text">関わってもらう</span>
                    </div>
                    <div class="rl-flow-item">
                        <span class="rl-flow-num">STAGE 5</span>
                        <span class="rl-flow-icon">📣</span>
                        <span class="rl-flow-text">口コミが生まれる</span>
                    </div>
                    <div class="rl-flow-item">
                        <span class="rl-flow-num">STAGE 6</span>
                        <span class="rl-flow-icon">🌸</span>
                        <span class="rl-flow-text">文化になる</span>
                    </div>
                </div>

                <h3>今すぐできる「広げ方」</h3>
                <div class="rl-action-cards">
                    <div class="rl-action-card">
                        <div class="rl-action-card-icon">🔁</div>
                        <div class="rl-action-card-title">投稿をリポスト</div>
                        <div class="rl-action-card-text">@hayator の投稿をX・Instagramでシェア。ハッシュタグ #おのくん も活用して</div>
                    </div>
                    <div class="rl-action-card">
                        <div class="rl-action-card-icon">💬</div>
                        <div class="rl-action-card-title">友人に話す</div>
                        <div class="rl-action-card-text">「こんな活動知ってる？」の一言が、新しい里親さんとの出会いになります</div>
                    </div>
                    <div class="rl-action-card">
                        <div class="rl-action-card-icon">✍️</div>
                        <div class="rl-action-card-title">体験を投稿する</div>
                        <div class="rl-action-card-text">グッズの写真、イベントレポートなど、あなたの体験をSNSで発信してください</div>
                    </div>
                </div>

                <div class="rl-section-footer">
                    <a href="#rl-contents">▲ 目次に戻る</a>
                    <a href="#rl-step3" class="rl-footer-next">次へ：行動する <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STEP3: 行動する ===== -->
    <section id="rl-step3" data-nav-id="step3" class="rl-content-section">
        <div class="rl-content-inner">
            <div class="rl-content-sidebar">
                <span class="rl-step-badge">STEP 03</span>
                <h2 class="rl-content-title">🙌 行動で<br><span>応援する</span></h2>
                <p class="rl-sidebar-note">
                    「購入する」「来店する」「使う」——<br>
                    あなたの行動がダイレクトに<br>
                    活動資金と認知拡大につながります。
                </p>
            </div>
            <div class="rl-content-body">
                <h3>おのくんグッズを手に取る</h3>
                <p>
                    おのくんグッズの購入は、里親コミュニティへの直接的な支援です。
                    靴下・バッジ・ポーチなど、日常で使えるアイテムを通じて
                    おのくんがいつもあなたのそばにいます。
                </p>

                <h3>里親カフェ・イベントに参加する</h3>
                <p>
                    全国の里親カフェや防災×帽祭のイベントに足を運ぶことで、
                    コミュニティの一員としてリアルな出会いが生まれます。
                    「初めて来た」という一歩が、長いつながりの始まりになります。
                </p>

                <h3>今すぐできる行動</h3>
                <div class="rl-action-cards">
                    <a class="rl-action-card" href="<?php echo esc_url($url['onokun']); ?>" target="_blank" rel="noopener" title="おのくん公式グッズを購入 — コミュニティを直接支援">
                        <div class="rl-action-card-icon">🧦</div>
                        <div class="rl-action-card-title">グッズを購入する</div>
                        <div class="rl-action-card-text">おのくん公式グッズを手に取って、コミュニティを直接支援する</div>
                    </a>
                    <a class="rl-action-card" href="<?php echo esc_url($url['map']); ?>" target="_blank" rel="noopener" title="里親カフェを地図で探す — 全国の里親コミュニティMAP">
                        <div class="rl-action-card-icon">🏪</div>
                        <div class="rl-action-card-title">里親カフェを訪れる</div>
                        <div class="rl-action-card-text">全国の里親カフェ・お店を訪問。里親マップで近くのお店を探してみて</div>
                    </a>
                    <a class="rl-action-card" href="<?php echo esc_url($url['bousai']); ?>" target="_blank" rel="noopener" title="防災×帽祭イベントに参加 — 楽しみながら防災体験">
                        <div class="rl-action-card-icon">🎪</div>
                        <div class="rl-action-card-title">イベントに参加</div>
                        <div class="rl-action-card-text">防災×帽祭、ランウェイショー、アートイベントなど体験型企画に参加する</div>
                    </a>
                </div>

                <div class="rl-section-footer">
                    <a href="#rl-contents">▲ 目次に戻る</a>
                    <a href="#rl-values" class="rl-footer-next">次へ：想いを知る <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== VALUES: 3つの想い ===== -->
    <section id="rl-values" data-nav-id="values" class="rl-content-section" style="background:#fff;">
        <div style="max-width:1040px;margin:0 auto;padding:0 4px;">
            <p class="rl-section-eyebrow" style="text-align:center;">3つの想い</p>
            <h2 class="rl-section-h2" style="text-align:center;margin-bottom:48px;">
                💡 Revolinkが大切に<span>していること</span>
            </h2>
            <div class="rl-values-grid">
                <div class="rl-value-card rl-animate" style="--rl-delay:0s">
                    <div class="rl-value-card-num">BELIEF 01</div>
                    <div class="rl-value-card-icon">🌱</div>
                    <h3 class="rl-value-card-title">地域とつながる</h3>
                    <p class="rl-value-card-text">
                        おのくんは東松島から全国・世界へと旅立ちました。
                        それぞれの地域に根ざした里親さんたちが、
                        地域の文化と被災地の記憶をつないでいます。
                        Revolinkはそのつながりをデジタルとリアルで支えます。
                    </p>
                </div>
                <div class="rl-value-card rl-animate" style="--rl-delay:0.1s">
                    <div class="rl-value-card-num">BELIEF 02</div>
                    <div class="rl-value-card-icon">🤲</div>
                    <h3 class="rl-value-card-title">共に育てる</h3>
                    <p class="rl-value-card-text">
                        「個人の発信から、共通の世界観へ、そして文化になる」——
                        この発信の6段階は、誰か一人が担うものではありません。
                        あなたの投稿・行動・参加が積み重なって、
                        はじめてムーブメントになります。
                    </p>
                </div>
                <div class="rl-value-card rl-animate" style="--rl-delay:0.2s">
                    <div class="rl-value-card-num">BELIEF 03</div>
                    <div class="rl-value-card-icon">✨</div>
                    <h3 class="rl-value-card-title">小さな奇跡を広げる</h3>
                    <p class="rl-value-card-text">
                        靴下のキャラクターが世界に旅立つなんて、誰が想像したでしょうか。
                        Revolinkは「日本ならではのアイデアで世の中の課題を実証する実験場」。
                        あなたの参加が、次の小さな奇跡の種になります。
                    </p>
                </div>
            </div>
            <div class="rl-section-footer" style="max-width:100%;">
                <a href="#rl-contents">▲ 目次に戻る</a>
                <a href="#rl-circulation" class="rl-footer-next">次へ：循環の仕組み <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <!-- ===== CIRCULATION: 応援が循環する新しい社会貢献 ===== -->
    <section id="rl-circulation" data-nav-id="circulation" class="rl-content-section" style="background:#fffaf7;">
        <div class="rl-circulation-wrap">

            <p class="rl-section-eyebrow" style="text-align:center;">循環の仕組み</p>

            <p class="rl-circulation-catch rl-animate">
                「つながりを育てるほど、<br>社会も、自分の未来も潤っていく。」
            </p>

            <div class="rl-circulation-body rl-animate" style="--rl-delay:0.1s">
                <p>
                    Revolinkは、誰かを一方的に支える仕組みではありません。
                </p>
                <p>
                    あなたの発信やホームページに置かれた小さな入口が、<br>
                    地域や活動への関心を生み、<br>
                    人と人のつながりを広げ、<br>
                    社会貢献型の広告収入として循環していきます。
                </p>
                <p>
                    関わる人が増えるほど、<br>
                    発信の価値が高まり、<br>
                    スポンサーの想いが届き、<br>
                    活動する人にも、紹介する人にも、新しい実りが生まれる。
                </p>
                <p>
                    支援して終わりではなく、<br>
                    広告を出して終わりでもない。
                </p>
                <p>
                    みんなで育てたつながりが、<br>
                    社会を少し良くして、<br>
                    関わった人の未来にも返ってくる。
                </p>
                <p class="rl-circulation-closing">
                    Revolinkは、<br>
                    <strong>応援が循環する、新しい社会貢献の形</strong>です。
                </p>
            </div>

            <p class="rl-circulation-note rl-animate" style="--rl-delay:0.2s">
                ※Revolinkは、応援の気持ちだけで終わらせない仕組みです。育った広告収入は、活動の継続と、掲載に関わる人への還元につながっていきます。詳しい流れは、仕組みページでご確認いただけます。
            </p>

            <div class="rl-circulation-cta rl-animate" style="--rl-delay:0.25s">
                <a href="#" class="rl-circulation-btn" data-circulation-link="true">
                    仕組みページを見る <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="rl-section-footer" style="max-width:100%;">
                <a href="#rl-contents">▲ 目次に戻る</a>
                <a href="#rl-activities" class="rl-footer-next">次へ：プロジェクト <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <!-- ===== ACTIVITIES: プロジェクト ===== -->
    <section id="rl-activities" data-nav-id="activities" class="rl-content-section">
        <div style="max-width:1040px;margin:0 auto;padding:0 4px;">
            <p class="rl-section-eyebrow">活動紹介</p>
            <h2 class="rl-section-h2">🌏 つながる<span>6つのプロジェクト</span></h2>
            <p style="font-size:17px;color:#555;margin-bottom:36px;max-width:700px;">
                Revolinkは単独の活動ではありません。
                おのくんを中心に6つのプロジェクトが連携しながら、
                社会に新しい文化を根付かせていきます。
            </p>
            <div class="rl-projects-grid">
                <a class="rl-project-card rl-animate" href="<?php echo esc_url($url['bousai']); ?>" title="防災×帽祭 — 楽しみながら防災体験を積むエンタメの場" target="_blank" rel="noopener" style="--rl-delay:0s">
                    <span class="rl-project-icon">🎩</span>
                    <div class="rl-project-content">
                        <div class="rl-project-name">防災×帽祭</div>
                        <div class="rl-project-desc">楽しみながら防災体験を積むエンタメの場。ハット・ファッションショー・アート・ダンスで防災を楽しい体験の入口に変える。1500万人へのリーチを目指す。</div>
                        <span class="rl-project-tag rl-project-tag--active">稼働中</span>
                    </div>
                    <span class="rl-project-arrow" aria-hidden="true">→</span>
                </a>
                <a class="rl-project-card rl-animate" href="<?php echo esc_url($url['revoart']); ?>" title="レボアート — アート力で被災地・課題地を支援する活動" target="_blank" rel="noopener" style="--rl-delay:0.06s">
                    <span class="rl-project-icon">🖼️</span>
                    <div class="rl-project-content">
                        <div class="rl-project-name">レボアート</div>
                        <div class="rl-project-desc">廃業ペンキの利活用など、被災地や課題を抱えた地域の問題を「アート力」で支える活動。地域の物語を作品に昇華する。</div>
                        <span class="rl-project-tag rl-project-tag--active">稼働中</span>
                    </div>
                    <span class="rl-project-arrow" aria-hidden="true">→</span>
                </a>
                <a class="rl-project-card rl-animate" href="<?php echo esc_url($url['revosong']); ?>" title="REVOSONG — 想いを音楽にして共有しあうプラットフォーム" target="_blank" rel="noopener" style="--rl-delay:0.12s">
                    <span class="rl-project-icon">🎵</span>
                    <div class="rl-project-content">
                        <div class="rl-project-name">REVOSONG / MUSIC CHARTS</div>
                        <div class="rl-project-desc">想いを音楽にしながら共有しあうプラットフォーム。応援ソングを通じて共感の輪を広げる。</div>
                        <span class="rl-project-tag rl-project-tag--active">稼働中</span>
                    </div>
                    <span class="rl-project-arrow" aria-hidden="true">→</span>
                </a>
                <a class="rl-project-card rl-animate" href="<?php echo esc_url($url['revofund']); ?>" title="レボファンディング — 次世代型クラウドファンディング" target="_blank" rel="noopener" style="--rl-delay:0.18s">
                    <span class="rl-project-icon">💰</span>
                    <div class="rl-project-content">
                        <div class="rl-project-name">レボファンディング</div>
                        <div class="rl-project-desc">資金を集めながら「循環」させ続ける、次世代型クラウドファンディング。支援が途切れない仕組みを作る。</div>
                        <span class="rl-project-tag rl-project-tag--active">稼働中</span>
                    </div>
                    <span class="rl-project-arrow" aria-hidden="true">→</span>
                </a>
                <a class="rl-project-card rl-animate" href="<?php echo esc_url($url['revohat']); ?>" title="レボハット — 日本ならではのハット文化創造とものづくり" target="_blank" rel="noopener" style="--rl-delay:0.24s">
                    <span class="rl-project-icon">🏭</span>
                    <div class="rl-project-content">
                        <div class="rl-project-name">レボハット</div>
                        <div class="rl-project-desc">日本ならではのハット文化創造、ものづくり、次世代ファッションへの入り口。帽子をきっかけにした文化醸成。</div>
                        <span class="rl-project-tag rl-project-tag--soon">準備中</span>
                    </div>
                    <span class="rl-project-arrow" aria-hidden="true">→</span>
                </a>
                <a class="rl-project-card rl-animate" href="<?php echo esc_url($url['revolink']); ?>" title="Revolink — 消費を社会貢献型広告収入に変える仕組み" rel="noopener" style="--rl-delay:0.30s">
                    <span class="rl-project-icon">🔗</span>
                    <div class="rl-project-content">
                        <div class="rl-project-name">Revolink（このページ）</div>
                        <div class="rl-project-desc">日常生活の消費を社会貢献型の広告収入に変え、補助金に頼らない持続可能な活動基盤をつくる。</div>
                        <span class="rl-project-tag rl-project-tag--active">稼働中</span>
                    </div>
                    <span class="rl-project-arrow" aria-hidden="true">→</span>
                </a>
            </div>
            <div class="rl-section-footer" style="max-width:100%;">
                <a href="#rl-contents">▲ 目次に戻る</a>
                <a href="#revolink-form-bottom" class="rl-footer-next">参加申込みへ <span aria-hidden="true">↓</span></a>
            </div>
        </div>
    </section>

    <!-- ===== Ecosystem ===== -->
    <section class="rl-ecosystem" id="rl-ecosystem" data-nav-id="ecosystem">
        <div class="rl-ecosystem-inner">
            <div>
                <p class="rl-section-eyebrow">レボリストLab エコシステム</p>
                <h2 class="rl-section-h2">6つのプロジェクトが<br>ひとつの根でつながる</h2>
                <p class="rl-section-lead">
                    おのくんを中心に、防災・アート・ファッション・音楽・ファンディングが
                    循環する仕組みが「レボリストLab」です。
                    Revolinkはその全体を支える、収益の基盤となっています。
                </p>
                <div class="rl-ecosystem-list">
                    <a class="rl-eco-item" href="<?php echo esc_url($url['bousai']); ?>" title="防災×帽祭 — 楽しみながら防災体験を積む" target="_blank" rel="noopener">
                        <span class="rl-eco-item-icon">🎩</span>
                        <div class="rl-eco-item-text">
                            <div class="rl-eco-item-name">防災×帽祭</div>
                            <div class="rl-eco-item-desc">楽しみながら防災を学ぶ体験の場。1500万人リーチを目指す</div>
                        </div>
                        <span class="rl-eco-item-arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="rl-eco-item" href="<?php echo esc_url($url['revoart']); ?>" title="レボアート — アート力で被災地・課題地を支援" target="_blank" rel="noopener">
                        <span class="rl-eco-item-icon">🖼️</span>
                        <div class="rl-eco-item-text">
                            <div class="rl-eco-item-name">レボアート</div>
                            <div class="rl-eco-item-desc">廃業ペンキ活用など、被災地・課題地をアート力で支援</div>
                        </div>
                        <span class="rl-eco-item-arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="rl-eco-item" href="<?php echo esc_url($url['revosong']); ?>" title="REVOSONG — 想いを音楽にして共有しあうプラットフォーム" target="_blank" rel="noopener">
                        <span class="rl-eco-item-icon">🎵</span>
                        <div class="rl-eco-item-text">
                            <div class="rl-eco-item-name">REVOSONG</div>
                            <div class="rl-eco-item-desc">想いを音楽にして共有しあうプラットフォーム</div>
                        </div>
                        <span class="rl-eco-item-arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="rl-eco-item" href="<?php echo esc_url($url['revofund']); ?>" title="レボファンディング — 循環させ続ける次世代型クラウドファンディング" target="_blank" rel="noopener">
                        <span class="rl-eco-item-icon">💰</span>
                        <div class="rl-eco-item-text">
                            <div class="rl-eco-item-name">レボファンディング</div>
                            <div class="rl-eco-item-desc">「循環」させ続ける次世代型クラウドファンディング</div>
                        </div>
                        <span class="rl-eco-item-arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="rl-eco-item" href="<?php echo esc_url($url['revohat']); ?>" title="レボハット — 日本のハット文化創造・次世代ファッションへの入り口" target="_blank" rel="noopener">
                        <span class="rl-eco-item-icon">🏭</span>
                        <div class="rl-eco-item-text">
                            <div class="rl-eco-item-name">レボハット</div>
                            <div class="rl-eco-item-desc">日本のハット文化創造。次世代ファッションへの入り口</div>
                        </div>
                        <span class="rl-eco-item-arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="rl-eco-item" href="<?php echo esc_url($url['revolink']); ?>" title="Revolink — 日常消費を社会貢献収入に変える持続可能な基盤">
                        <span class="rl-eco-item-icon">🔗</span>
                        <div class="rl-eco-item-text">
                            <div class="rl-eco-item-name">Revolink</div>
                            <div class="rl-eco-item-desc">日常消費を社会貢献収入に変える、持続可能な基盤</div>
                        </div>
                        <span class="rl-eco-item-arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
            <div class="rl-ecosystem-img rl-animate" style="--rl-delay:0.15s">
                <img src="<?php echo esc_url($img_url . 'revolist-lab.png'); ?>"
                     alt="レボリストLab エコシステム図 — 防災×帽祭・レボアート・REVOSONG・レボファンディング・レボハット・Revolinkの6プロジェクトが連携する全体像"
                     width="560" height="420"
                     loading="lazy"
                     decoding="async">
            </div>
        </div>
    </section>

    <!-- ===== フォームセクション ===== -->
    <section id="revolink-form-bottom" data-nav-id="form" class="rl-form-section">
        <div class="rl-form-inner">
            <img src="<?php echo esc_url($img_url . 'chara-a.jpeg'); ?>"
                 alt="おのくん — Revolink参加申込みフォームでコミュニティに加わろう"
                 class="rl-form-chara"
                 width="200" height="240"
                 loading="lazy"
                 decoding="async">
            <p class="rl-section-eyebrow">参加申込み</p>
            <h2 class="rl-section-h2" style="text-align:center;">
                📋 Revolinkに<span>参加する</span>
            </h2>
            <p style="font-size:17px;color:#666;margin-top:12px;line-height:1.75;">
                登録は無料です。あなたの「できる形」で
                おのくんとつながりましょう。
            </p>
            <?php echo do_shortcode('[satoya_form]'); ?>
            <a href="#rl-what" class="rl-back-to-top">▲ ページ上部に戻る</a>
        </div>
    </section>

    <!-- ===== 関連ページリンク（SEO内部リンク） ===== -->
    <section class="rl-related-links" aria-label="関連ページ">
        <div class="rl-related-links-inner">
            <p class="rl-section-eyebrow" style="text-align:center;">関連する取り組み</p>
            <h2 class="rl-related-links-title">Revolinkとつながる活動</h2>
            <p class="rl-related-links-desc">
                Revolinkは、おのくんの活動だけでなく、防災・音楽・ファンディング・里親コミュニティなど、
                さまざまな取り組みとつながっています。
            </p>
            <ul class="rl-related-list">
                <li>
                    <a href="<?php echo esc_url($url['map']); ?>" target="_blank" rel="noopener">
                        🗺️ 里親コミュニティMAPを見る
                        <span>全国・世界のおのくん里親さんの活動をマップで確認</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($url['bousai']); ?>" target="_blank" rel="noopener">
                        🎩 防災×帽祭の取り組みについて
                        <span>楽しみながら防災体験を積むエンタメの場。1500万人リーチを目指す</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($url['revosong']); ?>" target="_blank" rel="noopener">
                        🎵 REVOSONG / Music Charts について
                        <span>想いを音楽にしながら共有しあうプラットフォーム</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($url['revofund']); ?>" target="_blank" rel="noopener">
                        💰 Revo Funding（レボファンディング）について
                        <span>資金を集めながら「循環」させ続ける次世代型クラウドファンディング</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($url['revoart']); ?>" target="_blank" rel="noopener">
                        🖼️ レボアートについて
                        <span>廃業ペンキの利活用など、被災地・課題地をアート力で支援する活動</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($url['onokun']); ?>" target="_blank" rel="noopener">
                        🐙 おのくん公式サイト（onokun.com）
                        <span>東日本大震災から生まれた里親キャラクターおのくんの活動拠点</span>
                    </a>
                </li>
            </ul>
        </div>
    </section>

    <!-- ===== Floating CTA ===== -->
    <a href="#revolink-form-bottom" id="rl-float-cta" class="rl-float-cta" aria-label="今すぐ参加申込む">
        🐙 今すぐ参加申込む
    </a>

    <script>
    (function() {
        'use strict';

        function initScrollAnimations() {
            if (!window.IntersectionObserver) {
                document.querySelectorAll('.rl-animate').forEach(function(el) { el.classList.add('is-visible'); });
                return;
            }
            var io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -20px 0px' });
            document.querySelectorAll('.rl-animate').forEach(function(el) { io.observe(el); });
        }

        function initActiveNav() {
            if (!window.IntersectionObserver) return;
            var sections = document.querySelectorAll('[data-nav-id]');
            var navItems = document.querySelectorAll('.rl-lp-nav-item[data-for]');
            if (!sections.length || !navItems.length) return;
            var io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        var id = e.target.dataset.navId;
                        navItems.forEach(function(n) { n.classList.toggle('is-active', n.dataset.for === id); });
                    }
                });
            }, { threshold: 0.25 });
            sections.forEach(function(s) { io.observe(s); });
        }

        function initFloatCta() {
            var cta  = document.getElementById('rl-float-cta');
            var hero = document.getElementById('rl-hero');
            if (!cta || !hero || !window.IntersectionObserver) return;
            new IntersectionObserver(function(entries) {
                cta.classList.toggle('is-visible', !entries[0].isIntersecting);
            }, { threshold: 0.1 }).observe(hero);
        }

        function initSmoothScroll() {
            var nav    = document.getElementById('rl-lp-nav');
            var offset = nav ? nav.offsetHeight + 8 : 60;
            document.querySelectorAll('a[href^="#"]').forEach(function(a) {
                a.addEventListener('click', function(e) {
                    var target = document.querySelector(this.getAttribute('href'));
                    if (!target) return;
                    e.preventDefault();
                    window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - offset, behavior: 'smooth' });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // JSが動いている場合のみアニメーションを有効化
            document.body.classList.add('rl-js-ready');
            initScrollAnimations();
            initActiveNav();
            initFloatCta();
            initSmoothScroll();
        });
    })();
    </script>

    <?php
    return ob_get_clean();
}
