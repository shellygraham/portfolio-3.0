=== Ajax Load More: Filters ===

Contributors: dcooney
Author: Darren Cooney
Author URI: https://connekthq.com/
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/filters/
Requires at least: 4.0
Tested up to: 5.0.3
Stable tag: trunk
Homepage: https://connekthq.com/
Version: 1.6.4


== Copyright ==
Copyright 2019 Darren Cooney

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.


== Description ==

= The Filters add-on provides front-end and admin functionality for building and managing Ajax filters. =

Create custom Ajax Load More filters in seconds.

http://connekthq.com/plugins/ajax-load-more/add-ons/filters/

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-filters.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-filters.zip`.
2. Extract the `ajax-load-more-filters` directory to your computer.
3. Upload the `ajax-load-more-filters` directory to the `/wp-content/plugins/` directory.
4. Ensure Ajax Load More is installed prior to activating the plugin.
5. Activate the plugin in the WP plugin dashboard.


== Changelog ==

= 1.6.3 - February 4, 2019 =
* FIX - Fixed issue with `alm_filters_{id}_{key}_default` & `alm_filters_{id}_{key}_selected` filters not triggering correctly with Taxonomy and Meta Query


= 1.6.3 - December 28, 2018 =
* FIX - Fixed issue with custom taxonomy term values not being selected on page load.
* FIX - Fixed with saving of filter data in WordPress admin. On some servers the data being passed was being rejected by the REST API as the data was not being sent as JSON.


= 1.6.2 - December 3, 2018 =
* FIX - I accidentally left `print_r()` function in the deployed 1.6.1 release. Sorry about that :)


= 1.6.1 - December 6, 2018 =
* FIX - Fixed a bug with parsing the URL of `category` and `category__and` querystring parameters.
* FIX - Fixed issue where filters would remain disabled after zero posts are returned from Ajax Load More - You must update to core Ajax Load More v4.1.0 for this to be resolved.


= 1.6 - November 3, 2018 =
* NEW - Added support for category__and and tag__and queries.
* NEW - Better success and error notifications in WP Admin.
* UPDATE - Improved drag and drop admin for filter groups.
* FIX - Fixed PHP warning messaqge for undefined $alt_key variable.
* FIX - Fixed issue where `almFiltersClear` public JS function was not working with `<select>` elements - https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#almFiltersClear.
* FIX - Fixed issue search term filtering on default `search.php` template.
* FIX - Fixed bug where switching a filter key from Taxonomy or Custom Field wouldn't clear json data causing issues when filtering.


= 1.5 - August 21, 2018 =
* NEW - Adding Created and Modified dates to filters.
* NEW - Added import and export functionality.
* UPDATED - Updated Filters admin interface for UI/UX improvements.
* UPDATED - Better code commenting and organization.
* FIX - Fixed issue with querystring parameters that are not part of filters parsing as custom field values.
* UPDATED - Better code commenting and organization.


= 1.4.1 - July 9, 2018 =
* NEW - Added new Default Value (fallback) parameter which allows for a fallback/default to be set on each filter group.
* NEW - Added controls to move/re-arrange Custom Values in admin.
* NEW - Added controls for collapsing filter groups for better readability.
* UPDATE - Enhanced filter drag and drop functionality.
* UPDATE - Security fix to remove special characters from querystring prior to being parsed.
* UPDATE - Various admin UI/UX improvements


= 1.4 - May 22, 2018 =
* NEW - Adding interactive selected filters display [View example](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/).
* BUG - Fixed issue in filters admin where filters would become unresponsive if a new filter was created and then drag and dropped into a new order


= 1.3 - May 8, 2018 =
* NEW - Adding drag and drop to allow for re-ordering of filters in admin.
* NEW - Adding support for search filter on default WP search template e.g. ?s={term}.
* NEW - Adding callback functions dispatched at various intervals throughout the filter process. See the [docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#callback-functions).

= 1.2 - March 20, 2018 =
* NEW - Added `Selected Value` parameter that allows for setting a default, pre-selected value of a filter. 
* NEW - Added public JS function (`almFiltersClear`) that allows for the complete resetting/clearing of a filter group. 
* FIX - Fixed issue with missing quotes causing issues with filter submit in some browsers.
* FIX - Removed `ALM_FILTERS_EXCLUDE_ARRAY` variable as it was causing issues in PHP version < 7.
* FIX - Fixed issue with filters clearing after popstate event when sharing a filtered URL.

= 1.1 - February 22, 2018 =
* UPGRADE NOTICE - Updated Ajax Load More shortcode to accept the filter ID (as a target) to help with querystring parsing on page load. `[ajax_load_more filters="true" target="{filter_id}"]`.
* UPDATE - Added new `target` shortcode parameter to link the Ajax Load More instance to the filters.
* UPDATE - Temporary removal of paged URLs due to integration issues with other add-ons - Paged URLs will return soon. e.g. `?pg=3`
* UPDATE - Added support for Preloaded + Filters add-on.
* FIX - Fixed multiple compatibility issues with Filters & Paging add-ons.
* FIX - Added a fix for incorrect selected Taxonomy Operator in Filters admin.
* FIX - Fixed string to array error in PHP 7.1.
* FIX - Updated CSS of form properties to help with cross browser compatibility issues.


= 1.0 - February 13, 2018 =
* Initial Release.

