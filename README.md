# Canvasflow for WordPress

 This out-of-the-box connector provides a quick and simple way to push your WordPress blog content directly to an existing Canvasflow publication with no programming knowledge required.

#### Features

* One click publishing to your Canvasflow publication.
* Automatic WordPress to Canvasflow format conversion.
* Multi-publication support.
* Set the default style for published posts with per article override.
* Manage individual post eligibility with publish and update options.
* Support for merging adjacent paragraphs to a single Canvasflow component.
* WordPress Shortcode support.
* Supports article and issue based Canvasflow Publications (with automatic issue detection).
* WordPress Feature image support.
* Custom post type support.
* Article title override.
* Support for all HTML5 tags.

To enable content from your WordPress blog to be published to your Canvasflow publication, you must obtain and enter a valid API key.

For information on how to obtain your Canvasflow API credentials, please contact [support@canvasflow.io](mailto:support@canvasflow.io) or learn more at [https://canvasflow.io](https://canvasflow.io)

#### Prerequisite Knowledge

Basic experience of WordPress and Canvasflow is required. This plugin assumes you have access to an active, API enabled Canvasflow account.

#### Requirements
* WordPress 5.2.2 +
* PHP 5.4 or higher (7.x recommended)
* MySQL 5.0 or higher 
* InnoDB DB storage engine
* Apache or nginx recommended

#### Installation

This section describes how to install and configure the plugin.

##### Install

1. Upload the plugin files to the `/wp-content/plugins/canvasflow directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

##### Config

1. Access Canvasflow --> Settings screen
2. Enter your Canvasflow API credentials and click save to validate.
3. Select the publication you wish to publish articles to.
4. Select the publish channel you wish to connect to.
5. If connected to an issue-based publication, select a default issue.
6. Set the default Canvasflow style that will be applied to published articles.
7. Choose how adjacent paragraphs are managed by Canvasflow.
8. Add required custom post types (if applicable).
9. Click save to commit the changes. 

#### Frequently Asked Questions

###### Does this plugin work with WordPress MU (multi-user)?
We haven't tested or explicitly built the plugin to work with WordPress MU. That's not to say it won't work, only that our initial release did not target a multi-user environment.

###### Is this plugin compatible with issue-based Canvasflow publications? =
Yes, the plugin will detect the Canvasflow Publication type connected and adapt the options available accordingly. 

###### I'm unable to activate the plugin, I get an error about MyISAM storage engine =
It's very likely that your MySQL storage engine is configured to use MyISAM not InnoDB. <a href="https://docs.canvasflow.io/article/199-unable-to-activate-cf-wp-plugin" target_"blank" rel=noopener">click here</a> to learn more about MySQL storage engines and how to convert to InnoDB.</a>

###### Where can I learn more about this plugin?
Please check out the plugin <a href="https://docs.canvasflow.io/article/122-wordpress-plugin" target_"blank">support documentation</a> or contact <a href="mailto:support@canvasflow.io">support@canvasflow.io</a> for more information.

#### Screenshots

1. Manage all of your posts in Canvasflow from your WordPress dashboard
2. Easily select which posts are eligible for upload to your Canvasflow publication
3. Override the style an article inherits on a per upload basis.
4. Real time sync status manages and notifies you of all content changes 
5. Customise the plugin to fit your workflow.


#### Changelog
##### 1.5.5
* Support for Wordpress `v5.4.1`

##### 1.5.4
* Replace prefix + users with `wpdb->users`

##### 1.5.3
* Added support for new API syntax

##### 1.5.0
* Added settings support for feature image

##### 1.4.0
* Added support for Category Meta data
* Plugin is now compatible with Custom fields (requires CF Smart template)

##### 1.3.0
* Added Auto Publish feature for supported distribution platforms
* Added support for `<hr>`
* Added support for Author meta data
* Added support for Published date meta data
* Improved MyISAM engine detection warning information
* Improved inline styling support
* Fixed issue that prevented publishing to a publication that did not have a connected channel
* Fixed issue which could cause MyISAM engine detection to fail

##### 1.2.0
* Added support for publishing directly to a Twixl collection
* Improved metabox publishing indicator
* Security and stability improvements

##### 1.1.0
* Performance improvements
* Added support for custom post types to be added on demand
* Added ability to force WP article title to be included in published content
* Improved handling of nested images
* Support added to process anchor links and caption data around images
* Improved support for Infogram embeds
* Added Podbean podcast support
* Improved support for unknown `<iframe>` content

##### 1.0.0
* Added publish channel option support
* Improved support for large number of WP articles in Post Manager
* API key settings now indicates connected endpoint

##### 0.13.1
* Added MyISAM engine detection and warning

##### 0.13.0
* Added ability to sort post order in Post Manager

##### 0.12.5
* Security improvements 
* JSON response added for metabox publishing
* Improved support for non wp_ prefix databases 

##### 0.12.4
* Internal refactor for optimize API and DB handling

##### 0.12.3
* Add loading icon when publishing an article in metabox

##### 0.12.2
* Replace alert message with html message

##### 0.12.1
* Fix bug that deletes the selected issue in settings.

##### 0.12.0
* 'Target' issue menu is now only displayed if connected to an issue-based publication.
* Added link to plugin support documentation.

##### 0.11.1
* Display publish state in Canvasflow metabox

##### 0.11.0
* Add support for metabox in post editor
* Add support for issue in settings

##### 0.10.0
* Added wider support for WordPress shortcodes

##### 0.9.8
* Fixed bug that could prevent user uploading an article

##### 0.9.7
* Add support for WP database prefix

##### 0.9.6
* Replace [] with array()

##### 0.9.5
* Add error message when there are no styles in a publication

##### 0.9.4
* Change plugin base_url
* Upload pages to Canvasflow

##### 0.9.3
* Use $wpdb for MySQL queries instead of the native driver.

##### 0.9.2
* Non-supported short codes are automatically stripped from post content.

##### 0.9.1
* Minor UI improvements.

##### 0.9.0
* Initial release. Recommended for production.


#### Upgrade Notice
##### 1.5.5
* Support for Wordpress `v5.4.1`

##### 1.5.4
* Replace prefix + users with `wpdb->users`

##### 1.5.3
* Added support for new API syntax

##### 1.5.0
* Added settings support for feature image

##### 1.4.0
* Added support for Category Meta data
* Plugin is now compatible with Custom fields (requires CF Smart template)

##### 1.3.0
* Added Auto Publish feature for supported distribution platforms
* Added support for `<hr>`
* Added support for Author meta data
* Added support for Published date meta data
* Improved MyISAM engine detection warning information
* Improved inline styling support
* Fixed issue that prevented publishing to a publication that did not have a connected channel
* Fixed issue which could cause MyISAM engine detection to fail

##### 1.2.0
* Added support for publishing directly to a Twixl collection
* Improved metabox publishing indicator
* Security and stability improvements

##### 1.1.0
* Performance improvements
* Added support for custom post types to be added on demand
* Added ability to force WP article title to be included in published content
* Improved handling of nested images
* Support added to process anchor links and caption data around images
* Improved support for Infogram embeds
* Added Podbean podcast support
* Improved support for unknown `<iframe>` content

##### 1.0.0
* Added publish channel option support
* Improved support for large number of WP articles in Post Manager
* API key settings now indicates connected endpoint
* Added Infogram support

##### 0.13.1
* Added MyISAM engine detection and warning

##### 0.13.0
* Added ability to sort post order in Post Manager

##### 0.12.5
* Security improvements - Upgrade immediately
* JSON response added for metabox publishing
* Improved support for non wp_ prefix databases 

##### 0.12.4
* Internal refactor for optimize API and DB handling

##### 0.12.3
* Add loading icon when publishing an article in metabox

##### 0.12.2
* Replace alert message with html message

##### 0.12.1
* Fix bug that deletes the selected issue in settings.

##### 0.12.0
* Target issue menu is now only displayed if connected to an issue-based publication.
* Added link to plugin support documentation.

##### 0.11.1
* Display publish state in Canvasflow metabox

##### 0.11.0
* Support for publish to Canvasflow as metabox in post editor
* Articles are added automatically to upload manager
* Users can now select a default issue
* Users can specify to which issue an post is going to be published

##### 0.10.0
* Added support for WordPress Shortcodes

##### 0.9.8
* Fixed bug that could prevent user uploading an article

##### 0.9.8
* Add support for WP database prefix for users

##### 0.9.7
* Add support for WP database prefix

##### 0.9.6
* Add support for PHP 5.4 and below

##### 0.9.5
* Add a message when there are no styles in a publication

##### 0.9.4
* This version let the user post pages as article in Canvasflow

##### 0.9.3 
* This version updates the API host and only retrieve article base publications

##### 0.9.3 
* This version fixes a security related bug.  Upgrade immediately

##### 0.9
* Initial release. Recommended for production.