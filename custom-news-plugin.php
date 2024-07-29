<?php
/*
Plugin Name: Mitteilungsfelder Plugin
Description: Mit diesem Plugin können benuzerdefinierte Mitteliungsfelder mit dem Shortcode '[display_news]' anzeigen lassen.
Version: 2.1.3 (18.03.2024)
Author: LordSteini
*/

// Shortcode für die Anzeige von News
function display_news_shortcode() {
    // Hole alle News mit einem Enddatum größer oder gleich dem aktuellen Datum
    $current_date = date('Y-m-d');
    $args = array(
        'post_type' => 'news',
        'meta_query' => array(
            array(
                'key' => 'end_date',
                'value' => $current_date,
                'compare' => '>=',
                'type' => 'DATE',
            ),
        ),
    );
    $news_query = new WP_Query($args);

    // Wenn es News gibt, zeige sie an
    if ($news_query->have_posts()) {
        while ($news_query->have_posts()) {
            $news_query->the_post();
            echo '<div class="news-item">';
            echo '<h2>' . get_the_title() . '</h2>';
            echo '<p>' . get_the_content() . '</p>';
            echo '</div>';
        }
    } else {
        // Wenn es keine News gibt, zeige eine Meldung an
        echo 'Keine aktuellen Mitteilungen';
    }

    // Setze die Post-Daten zurück
    wp_reset_postdata();
}
add_shortcode('display_news', 'display_news_shortcode');

// Registriere den News Custom Post Type
function register_news_post_type() {
    $labels = array(
        'name' => 'News',
        'singular_name' => 'News',
        'menu_name' => 'News',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
    );

    register_post_type('news', $args);
}
add_action('init', 'register_news_post_type');

// Füge ein benutzerdefiniertes Meta-Feld für das Enddatum hinzu
function add_end_date_meta_box() {
    add_meta_box(
        'end_date_meta_box',
        'Enddatum',
        'display_end_date_meta_box',
        'news',
        'side'
    );
}
add_action('add_meta_boxes', 'add_end_date_meta_box');

function display_end_date_meta_box($post) {
    $end_date = get_post_meta($post->ID, 'end_date', true);
    ?>
    <label for="end_date">Enddatum:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
    <?php
}

// Speichere das benutzerdefinierte Meta-Feld
function save_end_date_meta_data($post_id) {
    if (array_key_exists('end_date', $_POST)) {
        update_post_meta(
            $post_id,
            'end_date',
            sanitize_text_field($_POST['end_date'])
        );
    }
}
add_action('save_post', 'save_end_date_meta_data');
?>
