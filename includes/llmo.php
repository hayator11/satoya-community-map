<?php
/**
 * 里親コミュニティMAP — LLMO（LLM最適化）JSON-LD
 */
if (!defined('ABSPATH')) exit;

add_action('wp_head', function () {
    global $post;
    if (!is_a($post, 'WP_Post')) return;
    if (!has_shortcode($post->post_content, 'satoya_map')) return;

    $page_url = get_permalink($post->ID);

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'  => 'WebSite',
                '@id'    => 'https://onokun.com/#website',
                'url'    => 'https://onokun.com/',
                'name'   => 'おのくん公式サイト | レボリストLab',
                'inLanguage' => 'ja',
                'publisher'  => ['@id' => 'https://onokun.com/#organization'],
            ],
            [
                '@type'       => 'Organization',
                '@id'         => 'https://onokun.com/#organization',
                'name'        => 'レボリストLab / おのくん公式',
                'alternateName' => ['おのくん', 'REVOLIST Lab', '里親コミュニティMAP'],
                'url'         => 'https://onokun.com/',
                'description' => '東日本大震災から生まれた里親キャラクター「おのくん」を軸に、全国・世界の里親コミュニティをつなぐマップサービスを運営。',
                'foundingDate' => '2011',
                'areaServed'  => ['JP', 'WORLD'],
                'sameAs'      => [
                    'https://twitter.com/hayator',
                    'https://www.instagram.com/hayator/',
                    'https://onokun.com/',
                ],
            ],
            [
                '@type'       => 'Person',
                '@id'         => 'https://onokun.com/#founder',
                'name'        => '早冨彩子（はやとみ さやこ）',
                'alternateName' => 'hayator',
                'url'         => 'https://onokun.com/about/',
                'worksFor'    => ['@id' => 'https://onokun.com/#organization'],
                'jobTitle'    => 'おのくん活動代表 / レボリストLab ファウンダー',
            ],
            [
                '@type'       => 'WebPage',
                '@id'         => esc_url($page_url) . '#webpage',
                'url'         => esc_url($page_url),
                'name'        => '里親コミュニティMAP — おのくん里親さんの活動場所を地図で探す',
                'description' => '全国・世界のおのくん里親さんの活動場所をマップで可視化。里親カフェ・お店・イベント会場を地図から探せます。東日本大震災から生まれたおのくんの里親コミュニティを支えるプラットフォーム。',
                'inLanguage'  => 'ja',
                'isPartOf'    => ['@id' => 'https://onokun.com/#website'],
                'author'      => ['@id' => 'https://onokun.com/#founder'],
                'publisher'   => ['@id' => 'https://onokun.com/#organization'],
                'speakable'   => ['@type' => 'SpeakableSpecification', 'cssSelector' => ['h1', 'h2', '.satoya-map-title']],
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'おのくん公式サイト', 'item' => 'https://onokun.com/'],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => '里親コミュニティMAP', 'item' => esc_url($page_url)],
                ],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => [
                    [
                        '@type' => 'Question',
                        'name'  => '里親コミュニティMAPとは何ですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => '全国・世界のおのくん里親さんの活動場所をマップで可視化したサービスです。里親カフェ・お店・イベント会場を地図から探せます。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => 'おのくんとは何ですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'おのくんは2011年の東日本大震災後に宮城県東松島市で生まれた靴下のキャラクターです。販売ではなく里親募集という形で世界中に33万人以上の家族を持ち、人と人のつながりを大切にしながら活動しています。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => '里親カフェとは何ですか？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'おのくんの里親さんが運営するカフェ・お店のことです。全国各地にあり、里親コミュニティMAPから近くの里親カフェを探すことができます。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => '里親コミュニティMAPに登録するには？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'ページ内の登録フォームからお申込みいただけます。おのくんの里親さんであれば、活動場所をマップに掲載できます。'],
                    ],
                    [
                        '@type' => 'Question',
                        'name'  => 'おのくんの里親になるには？',
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'おのくん公式サイト（https://onokun.com/）からお申込みいただけます。販売ではなく里親という形で、あなたの家族に迎えることができます。'],
                    ],
                ],
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 5);
