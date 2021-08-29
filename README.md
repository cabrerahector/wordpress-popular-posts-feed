# Popular Posts RSS feed for WordPress

Adds a Popular Posts RSS feed to your WordPress site.

----
## Table of contents

* [Description](https://github.com/cabrerahector/wordpress-popular-posts-feed#description)
* [Requirements](https://github.com/cabrerahector/wordpress-popular-posts-feed#requirements)
* [Installation](https://github.com/cabrerahector/wordpress-popular-posts-feed#installation)
* [Customizing the feed](https://github.com/cabrerahector/wordpress-popular-posts-feed#customization)
* [Contributing](https://github.com/cabrerahector/wordpress-popular-posts-feed#contributing)

## Description

Popular Posts Feed is a [WordPress](http://wordpress.org/) plugin that adds a popular posts feed to your WordPress-powered web site.

## Requirements

* WordPress 4.7 or above.
* PHP 5.3+ or above.
* [WordPress Popular Posts](https://github.com/cabrerahector/wordpress-popular-posts/) 4.1 or above.

## Installation

1. Git clone this repository into your `/wp-content/plugins` directory. Alternatively, you can also [download the most recent release](https://github.com/cabrerahector/wordpress-popular-posts-feed/releases) and follow the [manual plugin installation instructions](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).
2. Go to **Plugins**, find the **Popular Posts Feed** plugin and click on **Activate** to enable it.
3. Go to **Settings > Permalinks** and click on **Save Changes** to flush WordPress' permalinks rules and have it detect the new feed.
4. Visit &lt;your domain name&gt;/feed/popular-posts/ using your browser. If everything went OK, you'll be able to see the popular posts feed right away.

## Customization

The plugin includes various filter hooks that you can use to customize your popular posts RSS feed. Generally speaking though, the one you'll likely want to use is `popular_posts_feed_args`.

Hook into `popular_posts_feed_args` to:

- Change the number of items displayed in the feed.
- Change the post type (default is `post`).
- Have the feed return popular posts from a given taxonomy (`category`, `post_tag`, or even a custom taxonomy!)
- Change the Time Range (default is most popular posts from the past 7 days).
- Have the feed return popular posts from a given author.
- etcetera.

The `popular_posts_feed_args` accepts pretty much the same parameters used by the [wpp_get_mostpopular() template tag](https://github.com/cabrerahector/wordpress-popular-posts/wiki/2.-Template-tags#wpp_get_mostpopular) from the [WordPress Popular Posts plugin](https://wordpress.org/plugins/wordpress-popular-posts/).

Here's an example:

```php
/**
 * Have the WPP feed display the 10 most popular posts
 * from category ID 7 from the past 30 days.
 */
function wp3951_my_popular_feed_options( $args ){
    $args['range'] = 'last30days';
    $args['limit'] = 10;
    $args['taxonomy'] = 'category';
    $args['term_id'] = 7;

    return $args;
}
add_filter('popular_posts_feed_args', 'wp3951_my_popular_feed_options', 10);
```

For more filter hooks, have a look at the `feed.php` file included with the plugin.

## Contributing

* If you have any ideas/suggestions/bug reports, and if there's not an issue filed for it already (see [issue tracker](https://github.com/cabrerahector/wordpress-popular-posts-feed/issues)), please [create an issue](https://github.com/cabrerahector/wordpress-popular-posts-feed/issues/new) so I can keep track of it.
* Developers can send [pull requests](https://help.github.com/articles/using-pull-requests) to suggest fixes / improvements to the source.
