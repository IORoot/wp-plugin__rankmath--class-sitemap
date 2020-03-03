<?php

/**
 * Filter to add extra URLs to the XML sitemap by type.
 *
 * Make sure that this runs AFTER Wordpress has loaded because otherwise Teambooking would not have loaded
 * and the objects / classes would not be available. Samee for RankMath.
 * 
 * http://rachievee.com/the-wordpress-hooks-firing-sequence/
 * 
 * The REST API version is at the bottom and would basically do this call:
 * http://dev.londonparkour.com/wp-admin/admin-ajax.php?action=teambooking_rest_api&auth_token=[INSERT_TOKEN_HERE]&operation=get_slots&service_id=parkour-class
 *
 * @param string $content String content to add, defaults to empty.
 */


/**
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │                                                                         │
 * │ Update Option                                                           │
 * │                                                                         │
 * │ This will get all of the events for the next month and save them to the │
 * │ option 'tb_rm_sitemap_urls' for use on the sitemap generation.          │
 * │                                                                         │
 * └─────────────────────────────────────────────────────────────────────────┘
 */
add_filter('wp_loaded', 'tb_rm_add_to_sitemap');

function tb_rm_add_to_sitemap(){

    // Only work on the sitemap page.
    if (!preg_match('/classschema-sitemap\.xml/', $_SERVER['REQUEST_URI'])) { return; }

    // Instantiate a new TeamBooking Calendar and grab all slots from it.
    // This was a basic copy from the REST method.
    $calendar = new \TeamBooking\Calendar();

    /**
     * Get all the slots from TeamBooking.
     * Look at the team-booking/src/TeamBooking/Calendar.php->getSlots method.
     * 
     * getSlots(array $services, array $coworker_ids, $min_get_time = NULL, $max_get_time = NULL, $just_parse = FALSE, $timezone = NULL, $limit = 0)
     * 
     */
    $service_ids = array('parkour-class', 'beginner-outdoor', 'youth-class-8-12', 'parkour-free-class', 'commandotemple-indoor');
    $coworker_id = array();
    $max_get_time = (new DateTime("+3 months"))->format(DATE_ATOM);
    $slots = $calendar->getSlots($service_ids, $coworker_id, NULL, $max_get_time)->getAllSlots();

    /**
     * Convert slot objects to normal arrays for access.
     */
    foreach ($slots as $slot) {
        $results[] = $slot->getApiResource();
    }

    /**
     * Iterate over array to create lines for sitemap.
     */
    $urls = '';
    foreach ($results as $slot){

        /**
         * Get the image for the class
         */
        $image = tb_rm_event_images($slot['serviceID']);
        $tbk_date = (new DateTime($slot['start']))->format("Y-m-d");
        $schema_line = '<url><loc>'.WP_SITEURL.'/classes/class/?tbk_date='.$tbk_date.'&amp;tbk_service='.$slot['serviceID'].'</loc><lastmod>'.$slot['start'].'</lastmod>'.$image.'</url>';
        $urls .= $schema_line;
    }

    /**
     * Save the value as an option
     */
    update_option('tb_rm_sitemap_urls', $urls );

    return;
}






/**
 * Check for Image in ACF. If present, add to sitemap.
 */
function tb_rm_event_images($service_id){

    // Check if there are entries in ACF for the images.
    if( have_rows( 'schema_item', 'option') ) {

        while( have_rows('schema_item', 'option') ): the_row();

            if (get_sub_field('service_id') != $service_id){ continue; }
            $image_array = get_sub_field('image');
            $image = '<image:image>
                <image:loc>'.$image_array['url'].'</image:loc>
                <image:title>'.$image_array['alt'].'</image:title>
                <image:caption>'.$image_array['alt'].'</image:caption>
            </image:image>';

        endwhile;
    }

    // If not empty, use the URL of image.
    if (!empty($image)){ return $image; }

    // Fallback
    return '<image:image><image:loc>https://londonparkour.com/wp-content/uploads/2018/05/Eliza_LDNPK_Classes_1920x1920.jpg</image:loc></image:image>';

}



/**
 * This is a way of accessing through the REST API for TeamBooking.
 */

// $_REQUEST = array(
//     'action' => 'teambooking_rest_api',
//     'auth_token' => 'fptTKrR1kcR9KAl7xDlDAEMjNdOrNtwA',
//     'operation' => 'get_slots',
//     'show_past' => FALSE,
//     'service_id' => ['parkour-class', 'beginner-outdoor', 'youth-class-8-12', 'parkour-free-class', 'commandotemple-indoor'],
//     // 'service_id' => 'parkour-class',
// );
// $api_call = \TeamBooking\API\REST::call('GET', $_REQUEST);