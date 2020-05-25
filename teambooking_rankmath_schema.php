<?php

/*
Plugin Name: _ANDYP - Team Booking - RankMath Schema Sitemap
Plugin URI: https://londonparkour.com
Description: <strong>🌍SITEMAP</strong> | <em>Posts > Class Schema</em> | This will create a new taxonomy called classschema that can be used as a sitemap for RankMath. This sitemap then has all classes added to it from TeamBooking
Version: 1.0.1
Author: Andy Pearson
Author URI: https://londonparkour.com
*/

// Add generated URLs to Sitemap through RankMath
include "teambooking/retrieve_classes.php";

// Add generated URLs to Sitemap through RankMath
include "sitemap/rankmath_sitemap.php";