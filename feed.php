<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */

// File is being accessed directly, abort.
if ( ! defined('WPINC') ) {
    die;
}

header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

$time_value = 1;
$time_unit = 'minute';
$current_datetime = current_datetime();
$last_build_date = $current_datetime->format('r');

$args = apply_filters('popular_posts_feed_args', [
    'range' => 'last7days',
    'limit' => get_option('posts_per_rss')
]);
$args['is_feed'] = 1;

$key = 'wpp_' . md5(json_encode($args));
$popular_posts = \WordPressPopularPosts\Cache::get($key);

if ( false === $popular_posts ) {
    $popular_posts = new \WordPressPopularPosts\Query($args);

    \WordPressPopularPosts\Cache::set(
        $key,
        $popular_posts,
        $time_value,
        $time_unit
    );
}

echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';

/**
 * Fires between the xml and rss tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action('rss_tag_pre', 'rss2');
?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php
    /**
     * Fires at the end of the RSS root to add namespaces.
     *
     * @since 2.0.0
     */
    do_action('rss2_ns');
    ?>
>

<channel>
    <title><?php wp_title_rss(); ?></title>
    <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
    <link><?php bloginfo_rss('url') ?></link>
    <description><?php bloginfo_rss("description") ?></description>
    <lastBuildDate><?php echo $last_build_date; ?></lastBuildDate>
    <language><?php bloginfo_rss('language'); ?></language>
    <sy:updatePeriod><?php
        $duration = 'hourly';

        /**
         * Filters how often to update the RSS feed.
         *
         * @since 2.1.0
         *
         * @param string $duration The update period. Accepts 'hourly', 'daily', 'weekly', 'monthly',
         *                         'yearly'. Default 'hourly'.
         */
        echo apply_filters('rss_update_period', $duration);
    ?></sy:updatePeriod>
    <sy:updateFrequency><?php
        $frequency = '1';

        /**
         * Filters the RSS update frequency.
         *
         * @since 2.1.0
         *
         * @param string $frequency An integer passed as a string representing the frequency
         *                          of RSS updates within the update period. Default '1'.
         */
        echo apply_filters('rss_update_frequency', $frequency);
    ?></sy:updateFrequency>
    <?php
    /**
     * Fires at the end of the RSS2 Feed Header.
     *
     * @since 2.0.0
     */
    do_action('rss2_head');

    if ( $popular_posts->get_posts() ) :
        foreach( $popular_posts->get_posts() as $popular_post ) :
            ?>
            <item>
                <title><?php echo apply_filters('the_title_rss', $popular_post->title); ?></title>
                <link><?php echo esc_url( apply_filters('the_permalink_rss', get_permalink($popular_post->id)) ); ?></link>
                <?php if ( get_comments_number($popular_post->id) || comments_open($popular_post->id) ) : ?>
                    <comments><?php echo esc_url( apply_filters('comments_link_feed', get_comments_link($popular_post->id)) ); ?></comments>
                <?php endif; ?>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true, $popular_post->id), false); ?></pubDate>
                <dc:creator><![CDATA[<?php echo get_the_author_meta('display_name', $popular_post->uid); ?>]]></dc:creator>
                <?php
                $categories = get_the_category($popular_post->id);
                $cat_names = array();

                if ( ! empty($categories) ) {
                    foreach ( (array) $categories as $category ) {
                        $cat_names[] = sanitize_term_field('name', $category->name, $category->term_id, 'category', 'rss');
                    }

                    foreach ( $cat_names as $cat_name ) {
                        echo "\t\t<category><![CDATA[" . @html_entity_decode($cat_name, ENT_COMPAT, get_option('blog_charset')) . "]]></category>\n";
                    }
                }
                ?>
                <guid isPermaLink="false"><?php the_guid($popular_post->id); ?></guid>
                <?php $post = get_post($popular_post->id ); setup_postdata($post); ?>
                <description><![CDATA[<?php echo apply_filters('the_excerpt_rss', get_the_excerpt($popular_post->id)); ?>]]></description>
                <?php
                if ( ! get_option('rss_use_excerpt') ) :
                    $content = apply_filters('the_content', $post->post_content);
                    $content = str_replace(']]>', ']]&gt;', $content);
                    $content = apply_filters('the_content_feed', $content, 'rss2');

                    if ( strlen( $content ) > 0 ) :
                        ?>
                        <content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
                        <?php
                    else :
                        ?>
                        <content:encoded><![CDATA[<?php echo apply_filters('the_excerpt_rss', get_the_excerpt($popular_post->id)); ?>]]></content:encoded>
                        <?php
                    endif;
                endif;

                if ( get_comments_number($popular_post->id) || comments_open($popular_post->id) ) :
                    ?>
                    <wfw:commentRss><?php echo esc_url( get_post_comments_feed_link($popular_post->id, 'rss2') ); ?></wfw:commentRss>
                    <slash:comments><?php echo get_comments_number($popular_post->id); ?></slash:comments>
                    <?php
                endif;

                rss_enclosure();

                /**
                 * Fires at the end of each RSS2 feed item.
                 *
                 * @since 2.0.0
                 */
                do_action('rss2_item');
                ?>
            </item>
            <?php
        endforeach;
    endif;
    ?>
</channel>
</rss>
