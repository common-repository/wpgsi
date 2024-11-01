<?php
/**
 * Define the internationalization functionality.
 * Loads and defines the internationalization files for this plugin
 * @since      1.0.0
 * @package    Wpgsi
 * @subpackage Wpgsi/includes
 * @author     javmah <jaedmah@gmail.com>
 */

class Wpgsi_Admin{
	/**
	 * Events Children titles .
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $eventsAndTitles    Events list.
	 */	
	private $plugin_name;

	/**
	 * Events Children titles .
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $eventsAndTitles    Events list.
	 */	
	private $version;

	/**
	 * Events Children titles.
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $eventsAndTitles    Events list.
	 */	
	public $googleSheet;

	/**
	 * The current Date.
	 * @since    1.0.0
	 * @access   Public
	 * @var      string    $Date    The current version of the plugin.
	 */
	Public $Date = "";

	/**
	 * The current Time.
	 * @since    1.0.0
	 * @access   Public
	 * @var      string    $Time   The current Time.
	 */
	Public $Time = "";

	/**
	 * Events list.
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $events    Events list.
	*/				
	public $events	= array();

	/**
	 * Events Children titles.
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $eventsAndTitles    Events list.
	*/	
	public $eventsAndTitles = array();		# Event Key and Event Title 
	
	/**
	 * WooCommerce Order Statuses.
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $active_plugins     List of active plugins .
	*/	
	public $wooCommerceOrderStatuses  = array();

	/**
	 * List of active plugins.
	 * @since    1.0.0
	 * @access   Public
	 * @var      array    $active_plugins     List of active plugins .
	*/	
	public $active_plugins  = array();

	/**
	 * Common methods used in the all the classes 
	 * @since    3.6.0
	 * @var      object    $version    The current version of this plugin.
	*/	
	public $common;

	# Class Constrictors 
	public function __construct($plugin_name, $version, $googleSheet, $common){
		# Plugin Name
		$this->plugin_name 	= $plugin_name;
		# WPGSI version 
		$this->version 		= $version;
		# Events
		$this->googleSheet 	= $googleSheet;
		# Common Methods
		$this->common 		= $common;
	}

	/**
	 * This Function will create Custom post type for saving wpgsi integration and  save wpgsi_ log
	 * @since    1.0.0
	*/
	public function wpgsi_customPostType(){
		register_post_type('wpgsiIntegration');  		
	}

	# Register the stylesheets for the admin area.
	public function wpgsi_enqueue_styles(){
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpgsi-admin.css', array(), $this->version, 'all');
	}

	# Register the JavaScript for the admin area.
	public function wpgsi_enqueue_scripts(){
		# ============================= 3.4.0 starts =================================
		# Limit The Code only For WPGSI Page So that It will Not slow the Process
		if(get_current_screen()->id == 'toplevel_page_wpgsi'){
			
			# +++++++++++++++++++++++++++++++ Below code should Fix ++++++++++++++++++++++++++++++++++++++++++++
			# There are come Default function for This, So Why Custom  Thing

			# Set date 
			# Current Date 
			$date_format 	= get_option('date_format');
			$this->Date		= ($date_format) ? current_time($date_format) : current_time('d/m/Y');
			# Current Time 
			$time_format 	= get_option('time_format');
			$this->Time		= ($date_format) ? current_time($time_format) : current_time('g:i a');
			# Active Plugins, Checking Active And Inactive Plugin 
			$this->active_plugins = get_option('active_plugins');		
			
			# ++++++++++++++++++++++++++++++ below Code also Should Change as you see Custom Order Status will not Display +++++++++++++++++++
			# WooCommerce order Statuses 
			if(function_exists("wc_get_order_statuses")){
				$woo_order_statuses =  wc_get_order_statuses();
				# for Woocommerce New orders;
				$this->wooCommerceOrderStatuses['wc-new_order']  =  'WooCommerce New Checkout Page Order';
				# For Default Status
				foreach($woo_order_statuses as $key => $value){
					$this->wooCommerceOrderStatuses[$key]  =  'WooCommerce ' . $value;
				}
			}else{
				# If Function didn't exist do it 
				$this->wooCommerceOrderStatuses = array(
					"wc-new_order"	=> "WooCommerce New Checkout Page Order",
					"wc-pending"	=> "WooCommerce Order Pending payment",
					"wc-processing"	=> "WooCommerce Order Processing",
					"wc-on-hold"	=> "WooCommerce Order On-hold",
					"wc-completed"	=> "WooCommerce Order Completed",
					"wc-cancelled"	=> "WooCommerce Order Cancelled",
					"wc-refunded"	=> "WooCommerce Order Refunded",
					"wc-failed"		=> "WooCommerce Order Failed",
				);
			}

			# User Starts
			# wordpress user events 
			$wordpressUserEvents =  array(
				"wordpress_newUser" 			=> 'Wordpress New User', 
				"wordpress_UserProfileUpdate" 	=> 'Wordpress User Profile Update', 
				"wordpress_deleteUser" 			=> 'Wordpress Delete User',
				"wordpress_userLogin" 			=> 'Wordpress User Login', 
				"wordpress_userLogout" 			=> 'Wordpress User Logout',
			);

			# Inserting User Events to All Events 
			$this->events += $wordpressUserEvents;

			# New Code for User 
			foreach($wordpressUserEvents as $key => $value){
				# This is For Free User 
				$this->eventsAndTitles[$key] = array(
					"userID" 				=> "User ID",
					"userName" 				=> "User Name",
					"firstName" 			=> "User First Name",
					"lastName" 				=> "User Last Name",
					"nickname" 				=> "User Nickname",
					"displayName" 			=> "User Display Name",
					"eventName" 			=> "Event Name",
					"description" 			=> "User Description",
					"userEmail" 			=> "User Email",
					"userRegistrationDate" 	=> "User Registration Date",
					"userRole"				=> "User Role",
					"userPassword"			=> "User Password",
					#
					"site_time"				=> "Site Time",
					"site_date"				=> "Site Date",
				);
				
				# This is For Paid User 
				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						$this->eventsAndTitles[$key] = array(
							"userID" 				=> "User ID",
							"userName" 				=> "User Name",
							"firstName" 			=> "User First Name",
							"lastName" 				=> "User Last Name",
							"nickname" 				=> "User Nickname",
							"displayName" 			=> "User Display Name",
							"eventName" 			=> "Event Name",
							"description" 			=> "User Description",
							"userEmail" 			=> "User Email",
							"userRegistrationDate" 	=> "User Registration Date",
							"userRole"				=> "User Role",
							"userPassword"			=> "User Password",
							#
							"site_time"				=> "Site Time",
							"site_date"				=> "Site Date",
							# New Code Starts From Here 
							#++++++++++++++++++++++++++++++++++++
							"user_date_year" 		=> "Year of the Date",
							"user_date_month"		=> "Month of the Date",
							"user_date_date" 		=> "Date of the Date",
							"user_date_time" 		=> "Time of the Date",
							#+++++++++++++++++++++++++++++++++++++
							# New Code Ends Here 
						);
					}
				}
		
				if($key == 'wordpress_userLogin'){
					$this->eventsAndTitles[$key]["userLogin"] 		= "Logged in ";
					$this->eventsAndTitles[$key]["userLoginTime"] 	= "Logged in Time";
					$this->eventsAndTitles[$key]["userLoginDate"] 	= "Logged in Date";
				}

				if($key == 'wordpress_userLogout'){
					$this->eventsAndTitles[$key]["userLogout"] 		= "User Logout";
					$this->eventsAndTitles[$key]["userLogoutTime"] 	= "Logout Time";
					$this->eventsAndTitles[$key]["userLogoutDate"] 	= "Logout Date";
				}

				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						# For user Meta 
						$usersMeta = $this->wpgsi_users_metaKeys();
						if($usersMeta[0]  && ! empty($usersMeta[1]) && wpgsi_fs()->can_use_premium_code()){
							# Looping comment Meta 
							foreach($usersMeta[1] as $metaKey){
								$this->eventsAndTitles[$key][$metaKey] = "User Meta  " . $metaKey;
							}
						}
					}
				}
			}

			# Post Event array 
			$wordpressPostEvents = array(
				'wordpress_newPost'		  => 'Wordpress New Post',
				'wordpress_editPost'	  => 'Wordpress Edit Post',
				'wordpress_deletePost'	  => 'Wordpress Delete Post',
				'wordpress_page'		  => 'Wordpress Page',
			);

			# Inserting WP Post Events to All Events 
			$this->events += $wordpressPostEvents;

			# post loop 
			foreach($wordpressPostEvents as $key => $value){
				# setting wordpress_page profile update events
				if($key != 'wordpress_page'){
					# This is For Free User 
					$this->eventsAndTitles[$key] = array(
						"postID" 			=> "Post ID",
						"post_authorID"		=> "Post Author ID",
						"authorUserName"	=> "Post Author User name",
						"authorDisplayName"	=> "Post Author Display Name",
						"authorEmail"		=> "Post Author Email",
						"authorRole"		=> "Post Author Role",

						"post_title" 		=> "Post Title",
						"post_date" 		=> "Post Date",
						"post_date_gmt" 	=> "Post Date GMT",
						#
						"site_time"			=> "Site Time",
						"site_date"			=> "Site Date",

						# New Code Starts From Here 
						#++++++++++++++++++++++++++++++++++++
						"post_date_year" 	=> "Post on Year",
						"post_date_month"	=> "Post on Month",
						"post_date_date" 	=> "Post on Date",
						"post_date_time" 	=> "Post on Time",
						#+++++++++++++++++++++++++++++++++++++
						# New Code Ends Here 

						"post_content" 		=> "Post Content",
						"post_excerpt" 		=> "Post Excerpt",
						"post_status" 		=> "Post Status",
						"eventName" 		=> "Event Name",
						"comment_status" 	=> "Comment Status",
						"ping_status" 		=> "Ping Status",
						"post_password" 	=> "Post Password",
						"post_name" 		=> "Post Name",
						"to_ping" 			=> "To Ping",
						"pinged" 			=> "Pinged",
						"post_modified" 	=> "Post Modified Date",
						"post_modified_gmt" => "Post Modified GMT",

						"post_modified_year" 	=> "Post modified Year",
						"post_modified_month"	=> "Post modified Month",
						"post_modified_date" 	=> "Post modified Date",
						"post_modified_time" 	=> "Post modified Time",
						
						"category"          => "Post Categories",
						"post_parent" 		=> "Post Parent",
						"guid" 				=> "Guid",
						"menu_order" 		=> "Menu Order",
						"post_type" 		=> "Post Type",
						"post_mime_type" 	=> "Post Mime Type",
						"comment_count" 	=> "Comment Count",
						"filter" 			=> "Filter",
					);
						
					if(wpgsi_fs()->is__premium_only()){
						if(wpgsi_fs()->can_use_premium_code()){
							# For Post Meta 
							$postsMeta = $this->wpgsi_posts_metaKeys();
							if($postsMeta[0] && ! empty($postsMeta[1]) && wpgsi_fs()->can_use_premium_code()){
								# Looping comment Meta 
								foreach($postsMeta[1] as $metaKey){
									$this->eventsAndTitles[$key][$metaKey] = "Post Meta  " . $metaKey;
								}	
							}
						}
					}
				}

				if($key == 'wordpress_page'){
					
					$this->eventsAndTitles[$key] = array(
						"postID" 				=> "Page ID",
						"post_authorID"			=> "Page Author ID",
						"authorUserName"		=> "Page Author User name",
						"authorDisplayName"		=> "Page Author Display Name",
						"authorEmail"			=> "Page Author Email",
						"authorRole"			=> "Page Author Role",

						"post_title" 			=> "Page Title",
						"post_date" 			=> "Page Date",
						"post_date_gmt" 		=> "Page Date GMT",
						#
						"site_time"				=> "Site Time",
						"site_date"				=> "Site Date",
						
						# New Code Starts From Here 
						#+++++++++++++++++++++++++++++++++++++
						"post_date_year" 		=>	"Page on Year",
						"post_date_month"		=>	"Page on Month",
						"post_date_date" 		=>	"Page on Date",
						"post_date_time" 		=>	"Page on Time",
						#++++++++++++++++++++++++++++++++++++++
						# New Code Ends Here 

						"post_content" 			=> "Page Content",
						"post_excerpt" 			=> "Page Excerpt",
						"post_status" 			=> "Page Status",
						"eventName" 			=> "Event Name",
						"comment_status" 		=> "Comment Status",
						"ping_status" 			=> "Ping Status",
						"post_password" 		=> "Page Password",
						"post_name" 			=> "Page Name",
						"to_ping" 				=> "To Ping",
						"pinged" 				=> "Pinged",
						"post_modified" 		=> "Page Modified",
						"post_modified_gmt" 	=> "Page Modified GMT",

						# New Code Starts From Here 
						#++++++++++++++++++++++++++++++++++++
						"post_modified_year" 	=> "Page modified Year",
						"post_modified_month"	=> "Page modified Month",
						"post_modified_date" 	=> "Page modified Date",
						"post_modified_time" 	=> "Page modified Time",
						#+++++++++++++++++++++++++++++++++++++
						# New Code Ends Here 

						"post_parent" 			=> "Page Parent",
						"guid" 					=> "Guid",
						"menu_order" 			=> "Menu Order",
						"post_type" 			=> "Page Type",
						"post_mime_type" 		=> "Page Mime Type",
						"comment_count" 		=> "Comment Count",
						"filter" 				=> "Filter",
					);

					if(wpgsi_fs()->is__premium_only()){
						if(wpgsi_fs()->can_use_premium_code()){
							# For page Meta 
							$pagesMeta = $this->wpgsi_pages_metaKeys();
							if($pagesMeta[0] && ! empty($pagesMeta[1]) && wpgsi_fs()->can_use_premium_code()){
								# Looping comment Meta 
								foreach($pagesMeta[1] as $metaKey){
									$this->eventsAndTitles[$key][$metaKey] = "Page Meta  " . $metaKey;
								}	
							}
						}
					}
				}
			} # Loop Ends 

			# Comment Starts
			$wordpressCommentEvents = array(
				'wordpress_comment'		  => 'Wordpress Comment',
				'wordpress_edit_comment'  => 'Wordpress Edit Comment',
			);

			# Inserting comment Events to All Events 
			$this->events += $wordpressCommentEvents;

			# setting wordpress comments events
			foreach($wordpressCommentEvents as $key => $value){
				# For Free User 
				
				$this->eventsAndTitles[ $key ]  = array(
					"comment_ID" 				=> "Comment ID",
					"comment_post_ID" 			=> "Comment Post ID",
					"comment_author"			=> "Comment Author",
					"comment_author_email" 		=> "Comment Author Email",
					"comment_author_url" 		=> "Comment Author Url",
					"comment_content" 			=> "Comment Content",
					"comment_type" 				=> "Comment Type",
					"user_ID" 					=> "Comment User ID",
					"comment_author_IP" 		=> "Comment Author IP",
					"comment_agent" 			=> "Comment Agent",
					"comment_date" 				=> "Comment Date",
					"comment_date_gmt" 			=> "Comment Date GMT",
				
					"filtered" 					=> "Filtered",
					"comment_approved" 			=> "Comment Approved",
					#
					"site_time"					=> "Site Time",
					"site_date"					=> "Site Date",
				);
				
				# For Paid User 
				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						$this->eventsAndTitles[ $key ] = array(
							"comment_ID" 			=> "Comment ID",
							"comment_post_ID" 		=> "Comment Post ID",
							"comment_author"		=> "Comment Author",
							"comment_author_email" 	=> "Comment Author Email",
							"comment_author_url" 	=> "Comment Author Url",
							"comment_content" 		=> "Comment Content",
							"comment_type" 			=> "Comment Type",
							"user_ID" 				=> "Comment User ID",
							"comment_author_IP" 	=> "Comment Author IP",
							"comment_agent" 		=> "Comment Agent",
							"comment_date" 			=> "Comment Date",
							"comment_date_gmt" 		=> "Comment Date GMT",
							#
							"site_time"				=> "Site Time",
							"site_date"				=> "Site Date",
							# New Code Starts From Here 
							#+++++++++++++++++++++++++++++
							"year_of_comment" 		=> "Year of the Comment",
							"month_of_comment"		=> "Month of the Comment",
							"date_of_comment" 		=> "Date of the Comment",
							"time_of_comment" 		=> "Time of the Comment",
							#+++++++++++++++++++++++++++++
							# New Code Ends Here 
							"filtered" 				=> "Filtered",
							"comment_approved" 		=> "Comment Approved",
						);
					}
				}
			} # Loop ends Here 

			if(wpgsi_fs()->is__premium_only()){
				if(wpgsi_fs()->can_use_premium_code()){
					# For Comment Meta 
					$commentsMeta = $this->wpgsi_comments_metaKeys();
					if($commentsMeta[0] && ! empty($commentsMeta[1]) && wpgsi_fs()->can_use_premium_code__premium_only()){
						# Looping the comment event 
						foreach($wordpressCommentEvents as $key => $value){
							# Looping comment Meta 
							foreach($commentsMeta[1] as $metaKey){
								$this->eventsAndTitles[ $key ][$metaKey] = "Comment Meta  " . $metaKey;
							}	
						}
					}
				}
			}

			# Woocommerce 
			if(in_array('woocommerce/woocommerce.php' , $this->active_plugins)){
				# Woo product  Starts 
				# WooCommerce Product Event Array 
				$wooCommerceProductEvents 		=  array(
					'wc-new_product'			=> 'WooCommerce New Product',
					'wc-edit_product'			=> 'WooCommerce Update Product',
					'wc-delete_product'			=> 'WooCommerce Delete Product',
				);

				# Inserting WooCommerce product Events to All Events 
				$this->events += $wooCommerceProductEvents;

				# WooCommerce Products 
				foreach($wooCommerceProductEvents as $key => $value){
					# Default fields
					$this->eventsAndTitles[ $key ]	= array(
						"productID"			=> "Product ID",
						"type"				=> "Product Type",
						"post_type"			=> "Post Type",
						"name"				=> "Name",
						"slug"				=> "Slug",
						"date_created"		=> "Date created",
						"date_modified"		=> "Date modified",
						# Get Product Prices
						
						# Get Product Tax, Shipping & Stock
						
						# Get Product Dimensions
						"weight"			=> "Weight",
						"length"			=> "Length",
						"width"				=> "Width",
						"height"			=> "Height",
						"tag_ids"			=> "Tag ids",
						"category_ids"		=> "Category ids",
						"image_id"			=> "Image id",
						"gallery_image_ids"	=> "Gallery image ids",
						#
						"site_time"			=> "Site Time",
						"site_date"			=> "Site Date",
					);
					
					# freemius 
					if(wpgsi_fs()->is__premium_only()){
						if(wpgsi_fs()->can_use_premium_code()){
							$this->eventsAndTitles[ $key ]	= array(
								"productID"					=> "Product ID",
								"type"						=> "Product Type",
								"post_type"					=> "Post Type",
								"name"						=> "Name",
								"slug"						=> "Slug",
								"date_created"				=> "Date created",
								"date_modified"				=> "Date modified",
								
								"date_created_year"	 		=>	"Created on Year",
								"date_created_month" 		=>	"Created on Month",
								"date_created_date"	 		=>	"Created on Date",
								"date_created_time"	 		=>	"Created on Time",
								# 
								"date_modified_year" 		=>	"Modified on Year",
								"date_modified_month"		=>	"Modified on Month",
								"date_modified_date" 		=>	"Modified on Date",
								"date_modified_time" 		=>	"Modified on Time",
								#
								"site_time"			 		=> "Site Time",
								"site_date"			 		=> "Site Date",
								#
								"status"			 		=> "Status",
								"eventName"			 		=> "Event name",
								"featured"			 		=> "Featured",
								"catalog_visibility" 		=> "Catalog visibility",
								"description"		 		=> "Description",
								"short_description"	 		=> "Short description",
								"sku"				 		=> "SKU",
								"menu_order"		 		=> "Menu order",
								"virtual"			 		=> "Virtual",
								"permalink"			 		=> "Permalink",
								# Get Product Prices
								"price"				 		=> "Price",
								"regular_price"		 		=> "Regular price",
								"sale_price"		 		=> "Sale price",
								"date_on_sale_from"	 		=> "Date on sale from",
								"date_on_sale_to"	 		=> "Date on sale to",
								"total_sales"		 		=> "Total sales",
								# Get Product Tax, Shipping & Stock
								"tax_status"		 		=> "Tax status",
								"tax_class"			 		=> "Tax class",
								"manage_stock"		 		=> "Manage stock",
								"stock_quantity"	 		=> "Stock quantity",
								"stock_status"		 		=> "Stock status",
								"backorders"		 		=> "Back orders",
								"sold_individually"	 		=> "Sold individually",
								"purchase_note"		 		=> "Purchase note",
								# Get Product Dimensions
								"shipping_class_id"	 		=> "Shipping class id",
								"weight"			 		=> "Weight",
								"length"			 		=> "Length",
								"width"				 		=> "Width",
								"height"			 		=> "Height",
								"category" 		            => "Categories",
								"category_ids"		 		=> "Category ids",
								"tags"			 			=> "Tags",
								"tag_ids"			 		=> "Tag ids",
								"image_id"			 		=> "Image id",
								"image"				 		=> "Image",
								"gallery_image_ids"	 		=> "Gallery image ids",
								"get_attachment_image_url"	=> "image url",
							);
						}
					}
				}
				#Product status Loop ends here

				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){

						# Wc Product attributes 
						if(function_exists('wc_get_attribute_taxonomies')){
							foreach($wooCommerceProductEvents as $key => $value){
								# Looping comment Meta 
								foreach(wc_get_attribute_taxonomies() as $wc_product_attribute ){
									$this->eventsAndTitles[$key]['pa_' . $wc_product_attribute->attribute_name] = 'Product Attribute : ' . $wc_product_attribute->attribute_label;
								}	
							}
						}

						# For WooCommerce Product Meta to the product  event
						$productsMeta = $this->wpgsi_wooCommerce_product_metaKeys();
						# Check and Balance & Premium Code only 
						if($productsMeta[0] && ! empty($productsMeta[1]) && wpgsi_fs()->can_use_premium_code()){
							# Looping the WooCommerce Product Event
							foreach($wooCommerceProductEvents as $key => $value){
								# Looping comment Meta 
								foreach($productsMeta[1] as $metaKey){
									$this->eventsAndTitles[ $key ][$metaKey] = "Product Meta  " . $metaKey;
								}	
							}
						}
					}
				}
				
				# Inserting WooCommerce Order Events to All Events 
				$this->events += $this->wooCommerceOrderStatuses;

				# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				#(1) Product Meta's
				#(2) Product Info
				#(3) Product Details
				#(4) Empty Product Place Holder 
				# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

				# WooCommerce Orders 
				foreach($this->wooCommerceOrderStatuses as $key => $value){
					# Default fields
					$this->eventsAndTitles[ $key ]	= array(
						"orderID"						=>	"Order ID",
						# Billing Information
						"billing_first_name"			=>	"Billing first name",	
						"billing_last_name"				=>	"Billing last name",
						"billing_company"				=>	"Billing company",
						"billing_address_1"				=>	"Billing address 1",
						"billing_address_2"				=>	"Billing address 2",
						"billing_city"					=>	"Billing city",
						"billing_state"					=>	"Billing state",
						"billing_postcode"				=>	"Billing postcode",
						# Shipping Information
						"shipping_first_name"			=>	"Shipping first name",
						"shipping_last_name"			=>	"Shipping last name",	
						"shipping_company"				=>	"Shipping company",
						"shipping_address_1"			=>	"Shipping address 1",
						"shipping_address_2"			=>	"Shipping address 2",
						"shipping_city"					=>	"Shipping city",
						"shipping_state"				=>	"Shipping state",
						"shipping_postcode"				=>	"Shipping postcode",
						#
						"site_time"			 			=> "Site Time",
						"site_date"			 			=> "Site Date",
						# Developer defined 
						"status"						=>	"Status",	
						"eventName"						=>	"Event name",		
					);

					# freemius 
					if(wpgsi_fs()->is__premium_only()){
						if(wpgsi_fs()->can_use_premium_code()){
							$this->eventsAndTitles[$key]	        = array(
								"orderID"							=>	"Order ID",
								"cart_tax"							=>	"Cart tax",
								"currency"							=>	"Currency",
								"discount_tax"						=>	"Discount tax",
								"discount_total"					=>	"Discount total",
								"fees"								=>	"Fees",
								"shipping_tax"						=>	"Shipping tax",	
								"shipping_total"					=>	"Shipping total",
								"subtotal"							=>	"Subtotal",
								"subtotal_to_display"				=>	"Subtotal to display",
								"tax_totals"						=>	"Tax totals",
								"taxes"								=>	"Taxes",
								"total"								=>	"Total",
								"total_discount"					=>	"Total discount",
								"total_tax"							=>	"Total tax",
								"total_refunded"					=>	"Total refunded",
								"total_tax_refunded"				=>	"Total tax refunded",
								"total_shipping_refunded"			=>	"Total shipping refunded",
								"item_count_refunded"				=>	"Item count refunded",
								"total_qty_refunded"				=>	"Total qty refunded",
								"remaining_refund_amount"			=>	"Remaining refund amount",
								# items Details 
								# ********************************************************************
								"items"								=>	"Items",
								"get_product_id"					=>	"Items id",
								"get_name"							=>	"Items name",
								"get_quantity"						=>	"Items quantity",
								"get_total"							=>	"Items total",
								"get_sku"		 					=>	"Items sku",	
								"get_type"	   						=>	"Items type",
								"get_slug"							=>	"Items slug",
								"get_price"							=>	"Items price",
								"get_regular_price"					=>	"Items regular_price",
								"get_sale_price"					=>	"Items sale_price", 
								"get_virtual" 						=>	"Items virtual",
								"get_permalink"						=>	"Items permalink",
								"get_featured"						=>	"Items featured",
								"get_status"						=>	"Items status",
								"get_tax_status" 					=>	"Items tax_status",
								"get_tax_class"						=>	"Items tax_class",
								"get_manage_stock"					=>	"Items manage_stock",
								"get_stock_quantity"				=>	"Items stock_quantity",
								"get_stock_status"					=>	"Items stock_status",
								"get_backorders"					=>	"Items backorders",
								"get_sold_individually"				=>	"Items sold individually",
								"get_purchase_note"					=>	"Items purchase note",
								"get_shipping_class_id"				=>	"Items shipping class id",
								"get_weight"		 				=>	"Items weight",
								"get_length"	 					=>	"Items length",
								"get_width"	 						=>	"Items width",
								"get_height"		 				=>	"Items height",
								"get_default_attributes"			=>	"Items default attributes",
								"get_category_ids"					=>	"Items category ids",
								"get_tags" 							=>	"Items tags",
								"get_tag_ids" 						=>	"Items tag ids",
								"get_image_id"	 					=>	"Items image id",
								"get_gallery_image_ids"				=>	"Items gallery image ids",
								"get_attachment_image_url"			=>	"Items attachment image url",
								# ********************************************************************
								"item_count"						=>	"Item count",
								"downloadable_items"				=>	"Downloadable items",
								# customer Details
								"customer_id"						=>	"Customer id",
								"user_id"							=>	"User id",	
								"user"								=>	"User",
								"customer_ip_address"				=>	"Customer ip address",
								"customer_user_agent"				=>	"Customer user agent",
								"created_via"						=>	"Created via",
								"customer_note"						=>	"Customer note",
								# Order Date 
								"date_created"						=>	"Date created",
								"date_modified"						=>	"Date modified",
								"date_completed"					=>	"Date completed",
								"date_paid"							=>	"Date paid",
								# New Code Starts  
								# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								"date_created_year"					=>	"Created on year",
								"date_created_month"				=>	"Created on Month",
								"date_created_date"					=>	"Created on date",
								"date_created_time"					=>	"Created on time",
								
								"date_modified_year"				=>	"Modified on year",
								"date_modified_month"				=>	"Modified on Month",
								"date_modified_date"				=>	"Modified on date",
								"date_modified_time"				=>	"Modified on time",
								
								"date_completed_year"				=>	"Completed on year",
								"date_completed_month"				=>	"Completed on Month",
								"date_completed_date"				=>	"Completed on date",
								"date_completed_time"				=>	"Completed on time",

								"date_paid_year"					=>	"Paid on year",
								"date_paid_month"					=>	"Paid on Month",
								"date_paid_date"					=>	"Paid on date",
								"date_paid_time"					=>	"Paid on time",
								# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								# New Code Starts  

								# Billing Information
								"billing_first_name"			=>	"Billing first name",
								"billing_last_name"				=>	"Billing last name",
								"billing_company"				=>	"Billing company",
								"billing_address_1"				=>	"Billing address 1",
								"billing_address_2"				=>	"Billing address 2",
								"billing_city"					=>	"Billing city",
								"billing_state"					=>	"Billing state",
								"billing_postcode"				=>	"Billing postcode",
								"billing_country"				=>	"Billing country",
								"billing_email"					=>	"Billing email",
								"billing_phone"					=>	"Billing phone",
								# Shipping method 
								"shipping_method"				=>	"Shipping method",
								# Shipping Information  
								"shipping_first_name"			=>	"Shipping first name",
								"shipping_last_name"			=>	"Shipping last name",	
								"shipping_company"				=>	"Shipping company",
								"shipping_address_1"			=>	"Shipping address 1",
								"shipping_address_2"			=>	"Shipping address 2",
								"shipping_city"					=>	"Shipping city",
								"shipping_state"				=>	"Shipping state",
								"shipping_postcode"				=>	"Shipping postcode",
								"shipping_country"				=>	"Shipping country",
								"address"						=>	"Address",
								"shipping_address_map_url"		=>	"Shipping address map url",
								"formatted_billing_full_name"	=>	"Formatted billing full name",
								"formatted_shipping_full_name"	=>	"Formatted shipping full name",
								"formatted_billing_address"		=>	"Formatted billing address",	
								"formatted_shipping_address"	=>	"Formatted shipping address",
								# Payment methods
								"payment_method"				=>	"Payment method",
								"payment_method_title"			=>	"Payment method title",
								"transaction_id"				=>	"Transaction id",
								# URLS
								"checkout_payment_url"			=>	"Checkout payment url",
								"checkout_order_received_url"	=>	"Checkout order received url",
								"cancel_order_url"				=>	"Cancel order url",
								"cancel_order_url_raw"			=>	"Cancel order url raw",
								"cancel_endpoint"				=>	"Cancel endpoint",
								"view_order_url"				=>	"View order url",
								"edit_order_url"				=>	"Edit order url",
								# 
								"status"						=>	"Status",	
								"eventName"						=>	"Event name",			
							);
						}
					}
				}
				# main Order status Loop ends here 
				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						# Wc order items attributes 
						if(function_exists('wc_get_attribute_taxonomies')){
							foreach($wooCommerceProductEvents as $key => $value){
								# Looping comment Meta 
								foreach(wc_get_attribute_taxonomies() as $wc_product_attribute ){
									$this->eventsAndTitles[$key]['pa_' . $wc_product_attribute->attribute_name] = 'Product Attribute : ' . $wc_product_attribute->attribute_label;
								}	
							}
						}

						# For WooCommerce order Items Meta
						$itemsMeta = $this->wpgsi_wooCommerce_product_metaKeys();
						if($itemsMeta[0] && ! empty($itemsMeta[1])){
							# Looping the WooCommerce Product Event
							foreach( $this->wooCommerceOrderStatuses as $key => $value){
								# Looping comment Meta 
								foreach($itemsMeta[1] as $metaKey){
									$this->eventsAndTitles[$key][$metaKey] = "Items Meta  " . $metaKey;
								}	
							}
						}

						# For WooCommerce Order Meta Data insert to the order Events
						$ordersMeta = $this->wpgsi_wooCommerce_order_metaKeys();
						# Check and Balance & Premium Code only 
						if($ordersMeta[0] && ! empty($ordersMeta[1]) && wpgsi_fs()->can_use_premium_code__premium_only()){
							# Looping the WooCommerce Product Event
							foreach($this->wooCommerceOrderStatuses as $key => $value){
								# Looping comment Meta s
								foreach($ordersMeta[1] as $metaKey){
									$this->eventsAndTitles[$key][$metaKey] = "Order Meta  " . $metaKey;
								}
							}
						}
					}
				}
			}

			# Below are Contact forms 
			# Contact Form 7
			$cf7 = $this->cf7_forms_and_fields();
			if($cf7[0]){
				foreach($cf7[1] as $form_id => $form_name){
					$this->events[$form_id] =  $form_name;		
				}

				foreach($cf7[2] as $form_id => $fields_array){
					$this->eventsAndTitles[$form_id] = $fields_array; 			
				}
			}

			# For Ninja Form 
			$ninja =  $this->ninja_forms_and_fields();
			if($ninja[0]){
				foreach($ninja[1] as $form_id => $form_name){
					$this->events[$form_id] = $form_name;		
				}

				foreach($ninja[2] as $form_id => $fields_array){
					$this->eventsAndTitles[$form_id] = $fields_array; 			
				}
			}

			# formidable form 
			$formidable =  $this->formidable_forms_and_fields();
			if($formidable[0]){
				foreach($formidable[1] as $form_id => $form_name){
					$this->events[$form_id] = $form_name;		
				}

				foreach($formidable[2] as $form_id => $fields_array){
					$this->eventsAndTitles[$form_id] = $fields_array; 			
				}
			}

			# wpforms-lite/wpforms.php
			$wpforms  =  $this->wpforms_forms_and_fields();
			if($wpforms[0]){
				foreach($wpforms[1] as $form_id => $form_name){
					$this->events[$form_id] = $form_name;		
				}

				foreach($wpforms[2] as $form_id => $fields_array){
					$this->eventsAndTitles[$form_id] = $fields_array; 			
				}
			}

			# weforms/weforms.php
			$weforms  =  $this->weforms_forms_and_fields();
			if($weforms[0]){
				foreach($weforms[1] as $form_id => $form_name){
					$this->events[$form_id ] = $form_name;		
				}

				foreach($weforms[2] as $form_id => $fields_array){
					$this->eventsAndTitles[$form_id] = $fields_array; 			
				}
			}

			# gravity forms/gravity forms.php
			$gravityForms  =  $this->gravity_forms_and_fields();
			if($gravityForms[0]){
				foreach($gravityForms[1] as $form_id => $form_name){
					$this->events[$form_id] = $form_name;		
				}

				foreach($gravityForms[2] as $form_id => $fields_array){
					$this->eventsAndTitles[$form_id] = $fields_array; 			
				}
			}

			if(wpgsi_fs()->is__premium_only()){
				if(wpgsi_fs()->can_use_premium_code()){
					# forminator forminator/forminator.php
					$forminatorForms  =  $this->forminator_forms_and_fields();
					if($forminatorForms[0]){
						foreach($forminatorForms[1] as $form_id => $form_name){
							$this->events[$form_id] = $form_name;		
						}

						foreach($forminatorForms[2] as $form_id => $fields_array){
							$this->eventsAndTitles[$form_id] = $fields_array; 			
						}
					}

					# forminator fluentform/fluentform.php
					$fluentForms  =  $this->fluent_forms_and_fields();
					if($fluentForms[0]){
						foreach($fluentForms[1] as $form_id => $form_name){
							$this->events[$form_id] = $form_name;		
						}

						foreach($fluentForms[2] as $form_id => $fields_array){
							$this->eventsAndTitles[$form_id] = $fields_array; 			
						}
					}

					# Adding CPT Events and Fields 
					$CptEvents = $this->wpgsi_allCptEvents();
					# Check and Balance 
					if($CptEvents[0]){
						# Adding events to main events array 
						$this->events += $CptEvents[2];
						# Looping the Custom post type Event
						foreach($CptEvents[2] as $key => $value){
							# Looping comment Meta 
							foreach($CptEvents[3] as $cptDataFieldID => $cptDataFieldName){
								# Adding event data fields 
								$this->eventsAndTitles[$key][$cptDataFieldID] = $cptDataFieldName;
							}
						}
					}

					# ------------------- New Code -------------------------
					# database tables and columns
					$db_tables_columns  =   $this->database_tables_and_columns();
					if($db_tables_columns[0]){
						foreach($db_tables_columns[1] as $form_id => $form_name){
							$this->events[$form_id ] = $form_name;		
						}

						foreach($db_tables_columns[2] as $form_id => $fields_array){
							$this->eventsAndTitles[$form_id ] = $fields_array; 			
						}
					}
				}
			}

		} # toplevel_page_wpgsi ends Here
		# ============================= 3.4.0 ends ==================================
		
		# Passing the Data To WPGSI Page 
		if(get_current_screen()->id == 'toplevel_page_wpgsi'){
			
			wp_register_script('vue', plugin_dir_url(__FILE__) . 'js/vue.js', '', FALSE, FALSE);
			wp_enqueue_script('wpgsi-admin', plugin_dir_url(__FILE__) . 'js/wpgsi-admin.js', array('vue'), '0.1', TRUE);  
			
			if(isset($_GET["action"], $_GET["id"])){
				# getting the integration
				$Integration = $this->wpgsi_getIntegration(sanitize_text_field($_GET["id"]));
				# if There is a integration
				if($Integration[0]){
					
					$frontEnd = array(
						"ajaxUrl"  				=> admin_url('admin-ajax.php'),
						"CurrentPage" 			=> 'edit',  
						"DataSourceTitles" 		=> json_encode($this->events),
						"DataSourceFields" 		=> json_encode($this->eventsAndTitles),
						"IntegrationTitle"    	=> (isset($Integration[1]["IntegrationTitle"]))   	?  $Integration[1]["IntegrationTitle"] 	 	: '', 
						"DataSource"          	=> (isset($Integration[1]["DataSource"])) 		  	?  $Integration[1]["DataSource"] 			: '',
						"DataSourceID"          => (isset($Integration[1]["DataSourceID"])) 	  	?  $Integration[1]["DataSourceID"] 		 	: '', 
						"Worksheet"           	=> (isset($Integration[1]["Worksheet"])) 		  	?  $Integration[1]["Worksheet"] 			: '', 
						"WorksheetID"         	=> (isset($Integration[1]["WorksheetID"])) 		  	?  $Integration[1]["WorksheetID"] 		 	: '',
						"Spreadsheet"         	=> (isset($Integration[1]["Spreadsheet"])) 		  	?  $Integration[1]["Spreadsheet"] 		 	: '', 
						"SpreadsheetID"       	=> (isset($Integration[1]["SpreadsheetID"])) 	  	?  $Integration[1]["SpreadsheetID"]		 	: '', 
						"WorksheetColumnsTitle" => (isset($Integration[1]["WorksheetColumnsTitle"]))?  $Integration[1]["WorksheetColumnsTitle"]	: '', 
						"Relations"				=> (isset($Integration[1]["Relations"])) 		  	?  $Integration[1]["Relations"] 			: '',
						"GoogleSpreadsheets"	=> json_encode($this->wpgsi_GoogleSpreadsheets()[1]),
						'nonce' 				=> wp_create_nonce('wpgsiProNonce'),
					);
				}
			}else{
				$frontEnd = array(
					"ajaxUrl"  					=> admin_url('admin-ajax.php'),
					"CurrentPage" 				=> 'new',
					"DataSourceTitles" 			=> json_encode($this->events),
					"DataSourceFields" 			=> json_encode($this->eventsAndTitles),
					"GoogleSpreadsheets"		=> json_encode($this->wpgsi_GoogleSpreadsheets()[1]),
					'nonce' 					=> wp_create_nonce('wpgsiProNonce'),
				);
			}

			# Localizing js data to the script
			if(isset($frontEnd) && ! empty($frontEnd)){
				wp_localize_script('wpgsi-admin', 'frontEnd', $frontEnd);   
			}else{
				$this->common->wpgsi_log(get_class($this), __METHOD__,"500","ERROR: frontEnd array is empty ! wp_localize_script has no data to Pass.");
			}
		} 
	}

	/**
	 * Admin menu init
	 * @since    	1.0.0
	 * @return 	   	array    Integrations details  .
	*/
	public function wpgsi_admin_menu(){
		add_menu_page(__('Spreadsheet Integrations', 'wpgsi'), __('Spreadsheet Integrations', 'wpgsi'),'manage_options','wpgsi', array($this,'wpgsi_requestDispatcher'),'dashicons-media-spreadsheet');
		add_submenu_page("wpgsi", "Google Spreadsheet Integrations", "Google Spreadsheet Integrations", "manage_options", "wpgsi",  array($this,'wpgsi_requestDispatcher'));
		// https://wordpress.stackexchange.com/questions/98226/admin-menus-name-menu-different-from-first-submenu
	}

	/**
	 * URL routers for main landing Page 
	 * @since    	1.0.0
	 * @return 	   	array 	Integrations details.
	*/
	public function wpgsi_requestDispatcher(){
		// Check user capabilities, to access the routes user need those capabilities ; publish_posts, publish_pages, edit_posts, edit_others_posts 
		if(function_exists('current_user_can') &&  current_user_can('administrator') && current_user_can('publish_posts') && current_user_can('publish_pages') && current_user_can('edit_posts') && current_user_can('edit_others_posts') ){
			# getting acton
			$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
			# getting IntegrationID
			$IntegrationID     = isset($_GET['id']) ? intval(sanitize_text_field($_GET['id'])) : 0;
			# routing to the Pages
			switch ($action){
				case 'new':
					$this->wpgsi_new_integration();													
					break;
				case 'edit':
					($IntegrationID) ? $this->wpgsi_edit_integration($IntegrationID)  : $this->wpgsi_new_integration();	
					break;
				case 'delete':
					($IntegrationID) ? $this->wpgsi_delete_connection($IntegrationID) : $this->wpgsi_connections();		
					break;
				case 'remoteUpdate':
					($IntegrationID) ? $this->wpgsi_remoteUpdate($IntegrationID) : $this->wpgsi_connections();		
					break;
				default:
					$this->wpgsi_connections();	
				break;
			}
		} else {
			echo"<h3>To Access those endpoints you need, publish_posts, publish_pages, edit_posts, edit_others_posts capabilities & current_user_can func.</h3>";
		}
	}

	# comments;
	public function wpgsi_admin_notices(){
		// echo "<pre>";
		// echo"</pre>";
	}
	
	/**
	 * Third party plugin :
	 * Checkout Field Editor (Checkout Manager) for WooCommerce
	 * BETA testing;
	 * @since    2.0.0
	*/
	public function wpgsi_woo_checkout_field_editor_pro_fields(){
		
		$active_plugins 				= get_option('active_plugins');
		$woo_checkout_field_editor_pro 	= array();

		if(in_array('woo-checkout-field-editor-pro/checkout-form-designer.php', $active_plugins)){
			$a  = get_option("wc_fields_billing");
			$b  = get_option("wc_fields_shipping");
			$c  = get_option("wc_fields_additional");
			
			if($a){
				foreach($a as $key => $field){
					if(isset($field['custom']) &&  $field['custom'] == 1){
						$woo_checkout_field_editor_pro[ $key ]['type']  = $field['type'];
						$woo_checkout_field_editor_pro[ $key ]['name']  = $field['name'];
						$woo_checkout_field_editor_pro[ $key ]['label'] = $field['label'];
					}
				}
			}

			if($b){
				foreach($b as $key => $field){
					if(isset($field['custom']) &&  $field['custom'] == 1){
						$woo_checkout_field_editor_pro[ $key ]['type']  = $field['type'];
						$woo_checkout_field_editor_pro[ $key ]['name']  = $field['name'];
						$woo_checkout_field_editor_pro[ $key ]['label'] = $field['label'];
					}
				}
			}

			if($c){
				foreach($c as $key => $field){
					if(isset($field['custom']) &&  $field['custom'] == 1){
						$woo_checkout_field_editor_pro[ $key ]['type']  = $field['type'];
						$woo_checkout_field_editor_pro[ $key ]['name']  = $field['name'];
						$woo_checkout_field_editor_pro[ $key ]['label'] = $field['label'];
					}
				}
			}

		}else{
			return array(FALSE, "ERROR : Checkout Field Editor aka Checkout Manager for WooCommerce is not INSTALLED.");
		}

		if(empty( $woo_checkout_field_editor_pro)){
			return array(FALSE, "ERROR : Checkout Field Editor aka Checkout Manager for WooCommerce is EMPTY no Custom Field.");
		}else{
			return array(TRUE, $woo_checkout_field_editor_pro);
		}	
	}
	
	/**
	 * Main Landing Page . List of Integrations
	 * @since    	1.0.0
	 * @return 	   	
	*/
	public function wpgsi_connections(){
		# Adding List table
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wpgsi-list-table.php';
		$credential = get_option('wpgsi_google_credential', FALSE);
		# Creating view Page layout 
		echo"<div class='wrap'>";
			# if credentials is empty; Show this message to create credential.
			if(! $credential){
				echo"<div class='notice notice-warning inline'>";
					echo"<p> Please integrate Google APIs & Service Account before creating new connection. Get <code><b><a href=" . admin_url('admin.php?page=wpgsi-settings&action=service-account-help') . " style='text-decoration: none;'> step-by-step</a></b></code> help. This plugin will not work without Google APIs & Service Account. </p>";
				echo"</div>";
			}
			echo "<h1 class='wp-heading-inline'> Integrations </h1>";
			echo "<a href=". admin_url('admin.php?page=wpgsi&action=new') . " class='page-title-action'>Add New Integration</a>";
			# Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions
	        echo"<form id='newIntegration' method='get'>";
           		# For plugins, we also need to ensure that the form posts back to our current page 
            	echo"<input type='hidden' name='page' value='". esc_attr($_REQUEST['page']) ."' />";
            	echo"<input type='hidden' name='wpgsi_nonce' value='" . wp_create_nonce('wpgsi_nonce_bulk_action') . "' />";
	            # Now we can render the completed list table 
				# ++++++++++++++++++++++++++++++++ Working Here ! ++++++++++++++++++++++++++++++++
				$wpgsi_table = new Wpgsi_List_Table($this->eventsAndTitles, $this->common);
				$wpgsi_table->prepare_items();
				$wpgsi_table->display();
			echo"</form>";
		echo"</div>";
		# Caching the integrations 
		$integrations = $this->common->wpgsi_getIntegrations();
		if($integrations[0]){
			# setting or updating the transient;
			set_transient('wpgsi_integrations', $integrations[1]);
		}
	}

	/**
	 * wpgsi Add new Connections  view page 
	 * @since    	1.0.0
	 * @return 	   	array 		Integrations details.
	*/
	public function wpgsi_new_integration(){
		if(@fsockopen('www.google.com', 80)){
			require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/wpgsi-new-integration-display.php';
		}else{
			$this->common->wpgsi_log(get_class($this), __METHOD__,"501","ERROR: No internet connection.");
			echo"<h3> No internet connection. Sorry ! you can't create a integrations now.</h3>";
			return array(FALSE, "ERROR: No internet connection.");
		}
	}

	/**
	 * Edit a Connection view page  
	 * @since    	1.0.0
	 * @return 	   	array 		Integrations details  .
	*/
	public function wpgsi_edit_integration($id=''){
		if(@fsockopen('www.google.com', 80)){
			require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/wpgsi-edit-integration-display.php';
		}else{
			$this->common->wpgsi_log(get_class($this), __METHOD__,"502","ERROR: No internet connection.");
			echo"<h3> No internet connection. Sorry ! you can't edit a integrations now. </h3>";
			return array(FALSE, "ERROR: No internet connection.");
		}
	}

	/**
	 * Getting Google Spreadsheets 
	 * @since    	1.0.0
	 * @return 	   	array    Integrations details.
	*/
	public function wpgsi_GoogleSpreadsheets(){
		# Internet Connection Testing .
		if(! @fsockopen('www.google.com', 80)){
			$this->common->wpgsi_log(get_class($this), __METHOD__, "503", "ERROR: No internet connection !");
			return array(FALSE, "ERROR: No internet connection !");
		}
		# getting spreadsheets and Worksheets
		$r =  $this->googleSheet->wpgsi_spreadsheetsAndWorksheets();
		if(isset($r[0]) && $r[0]){
			return $r;
		}else{
			$this->common->wpgsi_log(get_class($this), __METHOD__, "505", "ERROR: from wpgsi_spreadsheetsAndWorksheets func. ". json_encode($r));
			return array(FALSE, array());
		}
	}

	/**
	 * Change connection status;
	 * @since    	3.7.4
	 * @return 	   	array 		Integrations details  .
	*/
	public function wpgsi_changeIntegrationStatus(){
		#
		if(function_exists('current_user_can') &&  current_user_can('administrator') && current_user_can('publish_posts') && current_user_can('publish_pages') && current_user_can('edit_posts') && current_user_can('edit_others_posts') ){
			# Checking  SpreadsheetID is set or not
			if(! isset($_POST['integrationID']) OR ! is_numeric($_POST['integrationID'])){
				$this->common->wpgsi_log(get_class($this), __METHOD__,"530","ERROR : integrationID is not set.");
				echo"ERROR: integrationID is not set.";
				exit;
			}
			# getting integration ID
			$integrationID 	= sanitize_text_field($_POST['integrationID']);
			# check the Post type status
			if(get_post($integrationID)->post_status == 'publish'){
				$custom_post = array('ID' => $integrationID, 'post_status' => 'pending');
			}else{
				$custom_post = array('ID' => $integrationID, 'post_status' => 'publish');
			}
			# updating post status 
			wp_update_post($custom_post);
			# Keeping Log 
			$this->common->wpgsi_log(get_class($this), __METHOD__, "200", "SUCCESS: ID " . $integrationID . " Integration status  change to .". get_post($integrationID)->post_status);
			# sending success status.
			echo"SUCCESS: ID " . $integrationID . " Integration status  change to .". get_post($integrationID)->post_status;
		} else {
			$this->common->wpgsi_log(get_class($this), __METHOD__,"530","ERROR : User capability is sort for changing the Integration Status.");
			echo"ERROR : User capability is sort for changing the Integration Status.";
			exit;
		}
		exit;
	}

	/**
	 * Change remote Update Status;
	 * @since    	3.7.4
	 * @return 	   	array 		Integrations details  .
	*/
	public function wpgsi_changeRemoteUpdateStatus($id=''){
		#
		if(function_exists('current_user_can') &&  current_user_can('administrator') && current_user_can('publish_posts') && current_user_can('publish_pages') && current_user_can('edit_posts') && current_user_can('edit_others_posts') ){
			# Checking  SpreadsheetID is set or not
			if(! isset($_POST['integrationID']) OR ! is_numeric($_POST['integrationID'])){
				$this->common->wpgsi_log(get_class($this), __METHOD__,"531","ERROR : integrationID is not set.");
				echo"ERROR: integrationID is not set.";
				exit;
			}
			# getting integration ID
			$integrationID 	= sanitize_text_field($_POST['integrationID']);
			# Getting Integrations
			$integration =  get_post($integrationID);
			# Check and Balance 
			if($integration){
				# getting Integration status 
				$remoteUpdateStatus = get_post_meta($integrationID, "remoteUpdateStatus", TRUE);

				if($remoteUpdateStatus){
					# Setting Integration status FALSE
					update_post_meta($integrationID, "remoteUpdateStatus", FALSE);
					# Keeping Log 
					$this->common->wpgsi_log(get_class($this), __METHOD__, "200", "SUCCESS:  ID " . $integrationID . " remote update status to DISABLED");
					# sending success status.
					echo"SUCCESS: ID " . $integrationID . "  remote update status to DISABLED.";
					exit;
				}else{
					# Setting Integration status TRUE
					update_post_meta($integrationID, "remoteUpdateStatus", TRUE);
					# Keeping Log 
					$this->common->wpgsi_log(get_class($this), __METHOD__, "200", "SUCCESS:  ID " . $integrationID . " remote update status to ENABLED.");
					# sending success status.
					echo"SUCCESS: ID " . $integrationID . "  remote update status to ENABLED.";
					exit;
				}
			}else{
				# Keeping Log 
				$this->common->wpgsi_log(get_class($this), __METHOD__, "507", "ERROR: No Integration on  this ID " . $integrationID);
			}
		} else {
			$this->common->wpgsi_log(get_class($this), __METHOD__,"530","ERROR : User capability is sort for changing the Integration Status.");
			echo"ERROR : User capability is sort for changing the Integration Status.";
			exit;
		}
		#
		exit;
	}

	/**
	 * Creating Google sheet Column titles; aka first row with names 
	 * @since    	3.7.7
	 * @return 	   	array 		Integrations details  .
	*/
	public function wpgsi_createSheetColumnTitles(){
		if(function_exists('current_user_can') &&  current_user_can('administrator') && current_user_can('publish_posts') && current_user_can('publish_pages') && current_user_can('edit_posts') && current_user_can('edit_others_posts') ){
			# Checking  SpreadsheetID is set or not
			if(! isset($_POST['integrationID'], $_POST['eventsAndTitles']) OR ! is_numeric($_POST['integrationID'])){
				$this->common->wpgsi_log(get_class($this), __METHOD__,"531","ERROR : integrationID or eventsAndTitles is not set.");
				echo"ERROR: integrationID or eventsAndTitles is not set.";
				exit;
			}
			# converting eventsAndTitles
			$eventsAndTitles =@ json_decode(base64_decode($_POST['eventsAndTitles']), true);
			# check and balance 
			if( empty($eventsAndTitles) OR ! is_array($eventsAndTitles) ){
				$this->common->wpgsi_log(get_class($this), __METHOD__,"531","ERROR : eventsAndTitles is empty or not array.");
				echo"ERROR : eventsAndTitles is empty or not array.";
				exit;
			}
			# getting integration ID
			$integrationID 	= sanitize_text_field($_POST['integrationID']);
			# get the post with Post ID 
			$post = get_post($integrationID);
			# Check & balance if there is a Post
			if($post){
				# Converting to PHP array from JSON
				$post_content = json_decode($post->post_content, TRUE);
				$post_excerpt = json_decode($post->post_excerpt);
				# Replacing Sheet ABC With Event Titles;
				$newArray = array();
				foreach($eventsAndTitles as $key => $value){
					$newArray["{{" . $key . "}}"] = $value;
				}
				# holders
				$FinalArray = array();
				foreach($post_content[1] as $key => $value){
					$FinalArray[ $key ] =  strip_tags(strtr($value, $newArray));
				}
				# 
				$returns = $this->googleSheet->wpgsi_append_row($post_excerpt->SpreadsheetID, $post_excerpt->WorksheetID, $FinalArray);
				# Redirect The User With message 
				if($returns[0]){
					$this->common->wpgsi_log(get_class($this), __METHOD__,"200","SUCCESS: Google spreadsheet column title created, " . json_encode($returns));
					echo"SUCCESS: ID " . $integrationID . "  Google spreadsheet column title created.";
				}else{
					$this->common->wpgsi_log(get_class($this), __METHOD__,"512","ERROR: Google spreadsheet column title didn't created " . json_encode(array("ret"=>$returns, "SpreadsheetID" => $post_excerpt->SpreadsheetID, "WorksheetID" => $post_excerpt->WorksheetID, "FinalArray" => $FinalArray)));
					echo"ERROR: ID " . $integrationID . "  Google spreadsheet column title is not created.";
				}
			}
		} else {
			$this->common->wpgsi_log(get_class($this), __METHOD__,"530","ERROR : User capability is sort for changing the Integration Status.");
			echo"ERROR : User capability is sort for changing the Integration Status.";
			exit;
		}
		#
		exit;
	}

	/**
	 * Adding a settings link at Plugin page after activate deactivate links.
	 * @since    3.7.4
	*/
	public function wpgsi_action_link($links_array, $plugin_file_name){
		# check and balance 
		if( $plugin_file_name  == 'wpgsi/wpgsi.php'){
			$links_array[] = '<a href="'.esc_url(get_admin_url(null, 'admin.php?page=wpgsi')).'">Settings</a>';
		}
		#
		return $links_array;
	}

	/**
	 * Help and Code for remote update. 
	 * This Function Will generate Google App script code For Remote Update.
	 * This Function also Show step-by-step help to stepup google App script 
	 * @since    	3.6.0
	 * @return 	   	array 		Integrations details.
	*/
	public function wpgsi_remoteUpdate($id=''){
		# Getting Integrations
		$Integrations  =  get_post($id);
		# Check and Balance 
		if($Integrations){

			# Remote Update Help. 
			# Check to see  wp_get_current_user() is exist or not;
			if(! function_exists('wp_get_current_user') ){
				echo"ERROR: wp_get_current_user() is not exist.";
				$this->common->wpgsi_log(get_class($this), __METHOD__, "508", "ERROR: wp_get_current_user() is not exist.");
			}

			# Check to see  current_user_can() is exist or not;
			if(! function_exists('current_user_can')){
				echo"ERROR: current_user_can() is not exist.";
				$this->common->wpgsi_log(get_class($this), __METHOD__, "508", "ERROR: current_user_can() is not exist.");
			}

			# is current user is administrator check 
			if(! current_user_can('administrator')){
				echo"ERROR: current user is not administrator.";
				$this->common->wpgsi_log(get_class($this), __METHOD__, "508", "ERROR: current_user_can() is not exist.");
			}

			# getting Current user Details.
			$current_user = wp_get_current_user();
			
			# Check and Balance.
			if(! isset($current_user->data->ID, $current_user->data->user_email) OR empty($current_user->data->user_email)){
				echo"ERROR: user ID or user Email is not set or empty.";
				$this->common->wpgsi_log(get_class($this), __METHOD__, "509", "ERROR: user ID or User Email is not set or empty.");
			}
			# Setting array value.
			$userBase64TokenArr		     = array();
			# Integration ID
			$userBase64TokenArr['ID'] 	 = $id;
			# User ID
			$userBase64TokenArr['UID'] 	 = $current_user->data->ID;
			# User Email
			$userBase64TokenArr['email'] = $current_user->data->user_email;
			# Creating token;
			$userToken = base64_encode(json_encode($userBase64TokenArr));
			# Check and Balance.
			if(!empty($userToken)){
				$sheetData 			=@ json_decode($Integrations->post_excerpt, TRUE);
				$integrationsTitle 	= esc_html($Integrations->post_title);
				$Worksheet 			= esc_html($sheetData['Worksheet']);
				$Spreadsheet 		= esc_html($sheetData['Spreadsheet']);
				$WorksheetID 		= esc_html($sheetData['WorksheetID']);
				$SpreadsheetID 		= esc_html($sheetData['SpreadsheetID']);
				$DataSourceID 		= esc_html($sheetData['DataSourceID']);
				$lock 				= TRUE;
				# Check and Balance for Free and professional version 
				if(in_array($DataSourceID, array('wordpress_newPost','wordpress_editPost','wordpress_deletePost','wordpress_page'))){
					#  including the View File;
					require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/wpgsi-remoteUpdate.php';
				}else{
					# if Professional version
					if(wpgsi_fs()->is__premium_only()){
						if(wpgsi_fs()->can_use_premium_code()){
							$lock = FALSE;
							#  including the View File;
							require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/wpgsi-remoteUpdate.php';
						}
					}
				}

				# weaning message 
				if($lock AND !in_array($DataSourceID, array('wordpress_newPost','wordpress_editPost','wordpress_deletePost','wordpress_page'))){
					echo"<br><b><i>We are very sorry. All default WordPress Posts and Pages remote updates are FREE.<br> WooCommerce and Custom post types are in the Professional version. Hope you understand our situation. Thank you for using the Plugin. </i></b>";
				}

			}else{
				
				echo"ERROR: json_encode or base64_encode error.";
				$this->common->wpgsi_log(get_class($this), __METHOD__, "510", "ERROR: json_encode or base64_encode error.");
			}

		}else{
			# Keeping Log.
			$this->common->wpgsi_log(get_class($this), __METHOD__, "511", "ERROR: No Integration on  this ID " . $id);
			#
			wp_redirect(admin_url('/admin.php?page=wpgsi&rms=fail'));
			#
			exit;
		}
	}

	/**
	 * Delete the Connection;
	 * @since    	1.0.0
	 * @return 	   	array 		Integrations details  .
	*/
	public function wpgsi_delete_connection($id=''){
		if(function_exists('current_user_can') &&  current_user_can('administrator') && current_user_can('publish_posts') && current_user_can('publish_pages') && current_user_can('edit_posts') && current_user_can('edit_others_posts') ){
			# insert log
			$this->common->wpgsi_log(get_class($this), __METHOD__,"200","SUCCESS: Integration Deleted Successfully. ID " . $id);
			# Redirect 
			wp_delete_post($id) ? wp_redirect(admin_url('/admin.php?page=wpgsi&rms=success')) : wp_redirect(admin_url('/admin.php?page=wpgsi&rms=fail'));
			# Reset And Caching the integrations 
			$integrations = $this->common->wpgsi_getIntegrations();
			if($integrations[0]){
				# setting or updating the transient;
				set_transient('wpgsi_integrations', $integrations[1]);
			}
		} else {
			$this->common->wpgsi_log(get_class($this), __METHOD__,"530","ERROR : User capability is sort for delete the Integration Status.");
			echo"ERROR : User capability is sort for delete the Integration Status.";
			exit;
		}
	}

	/**
	 * Save getIntegration Data to Database , New getIntegration and Edit getIntegration use This Function;
	 * @since    	1.0.0
	 * @return 	   	array 		Integrations details.
	*/
	public function wpgsi_save_integration(){
		#
		if(!function_exists('current_user_can') OR !current_user_can('administrator') OR !current_user_can('edit_posts') OR !current_user_can('publish_posts') ){
			# Inserting on log
			$this->common->wpgsi_log(get_class($this), __METHOD__, "524", "ERROR : User capability is short for saving the Integrations.");
			# redirecting
			wp_redirect(admin_url('/admin.php?page=wpgsi&rms=no_capability'));		
			#	
			exit;
		}

		# Setting ERROR status 
		$errorStatus = TRUE;
		// 
		// It Should be removed From $_POST Array ***
		// unset($_POST['SpreadsheetAndWorksheet']);
		// 
		# Check and Balance 
		if(! isset($_POST['IntegrationTitle']) OR empty($_POST['IntegrationTitle'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "513", "ERROR: IntegrationTitle is Empty.");
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_IntegrationTitle'));
		}

		if(! isset($_POST['DataSource']) OR empty($_POST['DataSource'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "516", "ERROR: DataSource name is Empty.");
			wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_DataSource'));
		}

		if(! isset($_POST['DataSourceID']) OR empty($_POST['DataSourceID'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "517", "ERROR: DataSourceID is Empty.");
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_DataSourceID'));
		}

		if(empty($_POST['Worksheet']) OR is_null($_POST['WorksheetID'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "518", "ERROR: Worksheet or WorksheetID is Empty.");
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_Worksheet_worksheetID'));
		}

		if(empty($_POST['Spreadsheet']) OR empty($_POST['Spreadsheet'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "519", "ERROR: Spreadsheet is Empty.");
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_Spreadsheet'));
		}
		
		if(! isset($_POST['SpreadsheetID']) OR empty($_POST['SpreadsheetID'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "520", "ERROR: SpreadsheetID is Empty.");
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_SpreadsheetID'));
		}

		if($_POST['status'] == "edit_Integration"  AND  empty($_POST['ID'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "521", "ERROR: edit_Integration ID is Empty.");
			wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=empty_edit_id'));
		}

		if(empty($_POST['Relation']) OR empty($_POST['Relation'])){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "522", "ERROR: Relations is Empty.");
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_Relation'));
		}

		# Empty integration Platform holder 
		$IntegrationPlatform  = "";
		# for user 
		if(in_array($_POST['DataSourceID'],array('wordpress_newUser','wordpress_UserProfileUpdate','wordpress_deleteUser','wordpress_userLogin','wordpress_userLogout'))){
			$IntegrationPlatform  = "wpUser";
		} 
		# for Post and Page 
		if(in_array($_POST['DataSourceID'],array('wordpress_newPost', 'wordpress_editPost', 'wordpress_deletePost', 'wordpress_page'))){
			$IntegrationPlatform  = "wpPost";
		} 
		# For Comment
		if(in_array($_POST['DataSourceID'],array('wordpress_comment', 'wordpress_edit_comment'))){
			$IntegrationPlatform  = "wpComment";
		} 
		# For WooCommerce product 
		if(in_array($_POST['DataSourceID'],array('wc-new_product', 'wc-edit_product', 'wc-delete_product'))){
			$IntegrationPlatform  = "wcProduct";
		}
		# For WooCommerce Order 
		if(in_array($_POST['DataSourceID'], array("wc-new_order", "wc-pending", "wc-processing", "wc-on-hold", "wc-completed", "wc-cancelled", "wc-refunded", "wc-failed"))){
			$IntegrationPlatform  = "wcOrder";
		} 
		# For Contact form 7
		if($this->cf7_forms_and_fields()[0] AND isset($this->cf7_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "cf7";
		} 
		# For ninja form 
		if($this->ninja_forms_and_fields()[0] AND isset($this->ninja_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "ninjaForm";
		} 
		# For ninja form 
		if($this->formidable_forms_and_fields()[0] AND isset($this->formidable_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "formidableForm";
		} 
		# For WP Form
		if($this->wpforms_forms_and_fields()[0] AND isset($this->wpforms_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "wpForm";
		} 
		# For we form
		if($this->weforms_forms_and_fields()[0] AND isset($this->weforms_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "weForm";
		} 
		# For Gravity Form 
		if($this->gravity_forms_and_fields()[0] AND isset($this->gravity_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "gravityForm";
		} 
		# For Forminator 
		if($this->forminator_forms_and_fields()[0] AND isset($this->forminator_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "forminatorForm";
		} 
		# For  fluent form
		if($this->fluent_forms_and_fields()[0] AND isset($this->fluent_forms_and_fields()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "fluentForm";
		} 
		# For Custom Post type
		if($this->wpgsi_allCptEvents()[0] AND isset($this->wpgsi_allCptEvents()[2][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "customPostType";
		} 
		# For database 
		if($this->database_tables_and_columns()[0] AND isset($this->database_tables_and_columns()[1][$_POST['DataSourceID']])){
			$IntegrationPlatform  = "database";
		} 
		# check the integration platform 
		if(empty($IntegrationPlatform)){
			$errorStatus = FALSE;
			$this->common->wpgsi_log(get_class($this), __METHOD__, "523", "ERROR: There is no Integration Platform. DataSourceID is : " . sanitize_text_field($_POST['DataSourceID']));
		  	wp_redirect(admin_url('/admin.php?page=wpgsi&action=new&rms=fail_empty_IntegrationPlatform'));
		}
		
		# sanitize_text_field 
		$ColumnTitle = array_map('sanitize_text_field', $_POST['ColumnTitle']);
		$Relation 	 = array_map('sanitize_text_field', $_POST['Relation']);

		# empty post ID  holder 
		$post_id = '';
		# Save new integration
		if($_POST['status'] == "new_Integration"  AND  $errorStatus){
			# Preparing Post array for DB insert
			$customPost = array(
				'ID'				=> $IntegrationPlatform,																	// 
				'post_content'  	=> json_encode(array($ColumnTitle, $Relation)), 										    // Used for JSON || ColumnHeaders || OutputsHolder 
				'post_title'    	=> sanitize_text_field($_POST['IntegrationTitle']), 										// used for title
				'post_status'   	=> 'publish',																				// Use for status  || on or off
				'post_excerpt'  	=> json_encode(array(	
														"DataSource"	=> sanitize_text_field($_POST['DataSource']), 
														"DataSourceID"	=> sanitize_text_field($_POST['DataSourceID']),
														"Worksheet"		=> sanitize_text_field($_POST['Worksheet']),
														"WorksheetID"	=> sanitize_text_field($_POST['WorksheetID']),
														"Spreadsheet"	=> sanitize_text_field($_POST['Spreadsheet']),
														"SpreadsheetID"	=> sanitize_text_field($_POST['SpreadsheetID'])) 
													), 
				'post_name'  		=> '',																						//  Use it for  " DataSource " like "cf7_5", "wordpress_newUser"
				'post_type'   		=> 'wpgsiIntegration',																		//  Use || wpgsi_connection OR wpgsi_
				'menu_order'		=> '',																						//  Is used for fields Serializations  || to know what after what
				'post_parent'		=> '',																						//  This Should be the id of New Connection
				# New Code 
				'meta_input'   => array(
					'IntegrationPlatform' 	=> $IntegrationPlatform,
					'ColumnTitle' 			=> json_encode($ColumnTitle),
					'Relation' 				=> json_encode($Relation),
					'DataSource' 			=> sanitize_text_field($_POST['DataSource']),
					'DataSourceID' 			=> sanitize_text_field($_POST['DataSourceID']),
					'Worksheet' 			=> sanitize_text_field($_POST['Worksheet']),
					'WorksheetID' 			=> sanitize_text_field($_POST['WorksheetID']),
					'Spreadsheet' 			=> sanitize_text_field($_POST['Spreadsheet']),
					'SpreadsheetID' 		=> sanitize_text_field($_POST['SpreadsheetID'])
				)
			);
			# Inserting New integration custom Post type 
			$post_id = wp_insert_post($customPost);																			//  Insert the post into the database
		}

		# Save edited Integration
		if(($_POST['status'] == "edit_Integration" AND ! empty($_POST['ID'])) AND $errorStatus){
			# Preparing Post array for status Change 
			$customPost = array(
				'ID'			=> sanitize_text_field($_POST['ID']),														// Edit ID 
				'post_content'  => json_encode(array($ColumnTitle, $Relation)), 										    // Used for JSON ||ColumnHeaders || OutputsHolder 
				'post_title'    => sanitize_text_field($_POST['IntegrationTitle']), 										// used for title
				'post_status'   => 'publish',																				// Use for status  || on or off
				'post_excerpt'  => json_encode(array(	
														"DataSource"	=> sanitize_text_field($_POST['DataSource']), 
														"DataSourceID"	=> sanitize_text_field($_POST['DataSourceID']),
														"Worksheet"		=> sanitize_text_field($_POST['Worksheet']),
														"WorksheetID"	=> sanitize_text_field($_POST['WorksheetID']),
														"Spreadsheet"	=> sanitize_text_field($_POST['Spreadsheet']),
														"SpreadsheetID"	=> sanitize_text_field($_POST['SpreadsheetID']),
													) 
												),
				'post_name'  	=> '',																						//  Use it for  " DataSource " like "cf7_5", "wordpress_newUser"
				'post_type'   	=> 'wpgsiIntegration',																		//  Use || wpgsi_connection OR wpgsi_
				'menu_order'	=> '',																						//  Is used for fields Serializations  || to know what after what
				'post_parent'	=> '',																						//  This Should be the id of New Connection
				# New Code 
				'meta_input'   => array(
					'IntegrationPlatform' 	=> $IntegrationPlatform,
					'ColumnTitle' 			=> json_encode($ColumnTitle),
					'Relation' 				=> json_encode($Relation),
					'DataSource' 			=> sanitize_text_field($_POST['DataSource']),
					'DataSourceID' 			=> sanitize_text_field($_POST['DataSourceID']),
					'Worksheet' 			=> sanitize_text_field($_POST['Worksheet']),
					'WorksheetID' 			=> sanitize_text_field($_POST['WorksheetID']),
					'Spreadsheet' 			=> sanitize_text_field($_POST['Spreadsheet']),
					'SpreadsheetID' 		=> sanitize_text_field($_POST['SpreadsheetID'])
				)
			);
			# Updating Custom Post Type 
			$post_id = wp_update_post($customPost);																			// Insert the post into the database
		}

		# if There is a Post Id , That Means Post is success fully saved
		if($post_id AND $errorStatus){
			# inserting on log
			$this->common->wpgsi_log(get_class($this), __METHOD__, "200", "SUCCESS: Integration saved.");
			# Caching integrations to wp set_transient
			$integrations = $this->common->wpgsi_getIntegrations();
			if($integrations[0]){
				# setting or updating the Options
				set_transient('wpgsi_integrations', $integrations[1]);
			}
			# Redirecting
			wp_redirect(admin_url('/admin.php?page=wpgsi&rms=success'));														// Redirect User With SUCCESS Note is not With ERROR Note 
		}else{
			# Inserting on log
			$this->common->wpgsi_log(get_class($this), __METHOD__, "524", "ERROR: Integration didn't saved. Integration insert fail. ");
			# redirecting
			wp_redirect(admin_url('/admin.php?page=wpgsi&rms=fail_insert'));													// Redirect User With SUCCESS Note is not With ERROR Note 
		}
	}
	
	/**
	 * Get getIntegration Data from Database  by there id
	 * @since    	1.0.0
	 * @param     	int    		Integration id      .
	 * @return 	   	array 		Integrations details  .
	*/
	public function wpgsi_getIntegration($IntegrationID = ''){ 
		# Check IntegrationID is empty or not
		if(empty($IntegrationID)){
			$this->common->wpgsi_log(get_class($this), __METHOD__, "525", "ERROR: IntegrationID id is Empty.");																			// Check Data is Any returns or Not 
			return array(FALSE, "ERROR: IntegrationID id is Empty.");
		}
		# Check IntegrationID is numeric or not 
		if(! is_numeric($IntegrationID)){
			$this->common->wpgsi_log(get_class($this), __METHOD__, "526", "ERROR: IntegrationID id is not numeric.");																			// Check Data is Any returns or Not 
			return array(FALSE, "ERROR: IntegrationID id is not numeric.");
		}
		# getting the integration 
		$post_data = get_post($IntegrationID);																				// Check There is a Data in the Database !
		
		if(empty($post_data)){
			$this->common->wpgsi_log(get_class($this), __METHOD__, "527", "ERROR: Nothing in the Database on this ID or Empty Data or ID is Wrong !");																			// Check Data is Any returns or Not 
			return array(FALSE, "Nothing in the Database on this ID or Empty Data or ID is Wrong !");
		}

		$data		  							= json_decode($post_data->post_excerpt, TRUE); 								// Getting Data from WP server 
		$return_array 							= array();
		$return_array['IntegrationTitle'] 		= sanitize_text_field($post_data->post_title);
		$return_array['DataSource'] 			= sanitize_text_field($data['DataSource']);		
		$return_array['DataSourceID'] 			= sanitize_text_field($data['DataSourceID']);		
		$return_array['Worksheet'] 				= sanitize_text_field($data['Worksheet']);
		$return_array['WorksheetID'] 			= sanitize_text_field($data['WorksheetID']);
		$return_array['Spreadsheet'] 			= sanitize_text_field($data['Spreadsheet']);
		$return_array['SpreadsheetID'] 			= sanitize_text_field($data['SpreadsheetID']);
		
		$post_content 							= json_decode($post_data->post_content, TRUE);
		$return_array['WorksheetColumnsTitle']  = $post_content[0];
		$return_array['Relations'] 				= $post_content[1];
		$return_array['Status'] 				= $post_data->post_status;
		
		return array(TRUE, $return_array);
	}

	/**
	 * AJAX events  function for New integration and edit integration , This will supply worksheet column titles 
	 * @since    	1.0.0
	 * @param     	string    	$SpreadsheetID       The name of this plugin.
	 * @param      	string    	$Worksheet    The version of this plugin.
	 * @return 	   	string 		This will return json string ,of column titles .
	*/
	public function wpgsi_WorksheetColumnsTitle(){
		# Testing security nonce Set and Valid test
		if(! isset($_POST['nonce']) OR ! wp_verify_nonce($_POST['nonce'], 'wpgsiProNonce')){
			$this->common->wpgsi_log(get_class($this), __METHOD__,"528","ERROR : invalid nonce.");
			json_encode(array("status" => FALSE ,"message"=>"ERROR: invalid nonce."), TRUE);
			exit;
		}
		# Checking  Worksheet is set or not
		if(! isset($_POST['Worksheet'])){
			$this->common->wpgsi_log(get_class($this), __METHOD__,"529","ERROR : Worksheet is not set.");
			json_encode(array("status" => FALSE ,"message"=>"ERROR: Worksheet is not set."), TRUE);
			exit;
		}
		# Checking  SpreadsheetID is set or not
		if(! isset($_POST['SpreadsheetID'])){
			$this->common->wpgsi_log(get_class($this), __METHOD__,"530","ERROR : SpreadsheetID is not set.");
			json_encode(array("status" => FALSE ,"message"=>"ERROR: SpreadsheetID is not set."), TRUE);
			exit;
		}
		# Checking  Worksheet is empty or not
		if(empty($_POST['Worksheet'])){
			$this->common->wpgsi_log(get_class($this), __METHOD__,"531","ERROR : Worksheet is empty !");
			json_encode(array("status" => FALSE ,"message"=>"ERROR: Worksheet is empty !"), TRUE);
		}
		# Checking  SpreadsheetID is empty or not
		if(empty($_POST['SpreadsheetID'])){
			$this->common->wpgsi_log(get_class($this), __METHOD__,"532","ERROR : SpreadsheetID is empty !");
			json_encode(array("status" => FALSE ,"message"=>"ERROR: SpreadsheetID is empty !"), TRUE);
		}

		$WorksheetName	= strip_tags($_POST['Worksheet']) ;
		$SpreadsheetID 	= sanitize_text_field($_POST['SpreadsheetID']);
		$google_token 	= get_option('wpgsi_google_token', FALSE);
		$columnTitle 	= $this->googleSheet->wpgsi_columnTitle($WorksheetName, $SpreadsheetID, $google_token);
		# Printing, not returning 
		echo json_encode($columnTitle);
		exit ;
	}

	/**
	 * Using custom hook sending data to Google spreadsheet 
	 * @since    1.0.0
	 * @param    string    	$plugin_name    The name of this plugin.
	 * @param    string    	$version    	The version of this plugin.
	 * @return 	 array 		$columns 		Array of all the list table columns.
	*/
	public function wpgsi_SendToGS($Evt_DataSource, $Evt_DataSourceID, $data_array, $id){
		# Don't do anything if there is No internet , As you know it is a Integration Plugin.
		# This Code Should Be Change | Change Code in WooTrello
		if(! @fsockopen('www.google.com', 80)){
			$this->common->wpgsi_log(get_class($this), __METHOD__,"533","ERROR: No internet connection.");
			return array(FALSE ,"ERROR: No internet connection.");
		}
		# getting all integration.
		$integrations   	 = get_posts(array(
			'post_type'   	 => 'wpgsiIntegration',
			'post_status' 	 => 'publish',
			'posts_per_page' => -1
		));
		# Looping the integrations
		foreach($integrations as  $integration){
			#
			$post_content 	= json_decode($integration->post_content, TRUE);
			$post_excerpt 	= json_decode($integration->post_excerpt, TRUE);
			#
			$DataSource		= $post_excerpt["DataSource"];
			$DataSourceID	= $post_excerpt["DataSourceID"];
			$Worksheet		= $post_excerpt["Worksheet"];
			$WorksheetID	= $post_excerpt["WorksheetID"];
			$Spreadsheet	= $post_excerpt["Spreadsheet"];
			$SpreadsheetID	= $post_excerpt["SpreadsheetID"];
			$ColumnsTitle 	= $post_content[0];
			$relation 		= $post_content[1];
			# Pre-process
			$ArrayKeyAndValue = array();
			foreach($data_array as $relationKey => $relationValue){
				$ArrayKeyAndValue["{{" . $relationKey . "}}"] = $relationValue;
			}
			# Check the value change depends on type 
			$dataWithRelationKey = array();
			foreach($relation as $key => $value){
				if(is_array($value)){
					$dataWithRelationKey[ $key ] = implode(", ", $value);
				}else{
					$dataWithRelationKey[ $key ] =  strtr($value, $ArrayKeyAndValue);
				}
			} 
			# Sending Request;
			if($Evt_DataSourceID == $DataSourceID){
				# getting last data's MD5 hash 
				$wpgsi_lastFired_md5 	= get_post_meta($integration->ID ,'wpgsi_lastFired_md5', TRUE);
				# dualSubmission Prevention 
				# lastFired is set and value is Not grater then 301 seconds
				if($wpgsi_lastFired_md5  AND  $wpgsi_lastFired_md5 == md5(json_encode($dataWithRelationKey).date('i')) ){
					$this->common->wpgsi_log(get_class($this), __METHOD__, "535", "ERROR: Dual submission Prevented of Integration : <b> " . $integration->ID . " </b> " . json_encode($dataWithRelationKey));
				}else{
					# Send the request 
					$ret = $this->googleSheet->wpgsi_append_row($SpreadsheetID, $WorksheetID, $dataWithRelationKey);
					# Check ERROR or SUCCESS 
					if($ret[0]){
						$this->common->wpgsi_log(get_class($this), __METHOD__, "200", "SUCCESS: okay, on the event . " . json_encode($ret));
						# below code will save last data's MD5 hash to the post meta so that dual submission with in 30 second hav not prevent Heigh traffic
						# data + php current minutes 
						update_post_meta($integration->ID, 'wpgsi_lastFired_md5', md5(json_encode($dataWithRelationKey).date('i')));
					}else{
						$this->common->wpgsi_log(get_class($this), __METHOD__, "536", "ERROR: on sending data . " . json_encode(array("SpreadsheetID" => $SpreadsheetID, "WorksheetID" => $WorksheetID,  "dataWithRelationKey" => $dataWithRelationKey ,"Google_response" => $ret)));
					}
				}
			}
		}
	}

	/**
	 * This Function will return [wordPress Pages] Meta keys.
	 * @since      3.3.0
	 * @return     array    This array has two vale First one is Bool and Second one is meta key array.
	*/
	public function wpgsi_pages_metaKeys(){
		# Global Db object 
		global $wpdb;
		# Query 
		$query  =  "SELECT DISTINCT( $wpdb->postmeta.meta_key ) 
					FROM $wpdb->posts 
					LEFT JOIN $wpdb->postmeta 
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
					WHERE $wpdb->posts.post_type = 'page' 
					AND $wpdb->postmeta.meta_key != '' ";
		# execute Query
		$meta_keys = $wpdb->get_col($query);
		# return Depend on the Query result 
		if(empty($meta_keys)){
			return array(FALSE, 'ERROR: Empty! No Meta key exist of the Post type page.');
		}else{
			return array(TRUE, $meta_keys);
		}
	}

	/**
	 * This Function will return [wordPress Posts] Meta keys.
	 * @since      3.3.0
	 * @return     array    This array has two vale First one is Bool and Second one is meta key array.
	*/
	public function wpgsi_posts_metaKeys(){
		# Global Db object 
		global $wpdb;
		# Query 
		$query  =  "SELECT  DISTINCT( $wpdb->postmeta.meta_key ) 
				  	FROM $wpdb->posts 
					LEFT JOIN $wpdb->postmeta 
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
					WHERE $wpdb->posts.post_type = 'post' 
					AND $wpdb->postmeta.meta_key != '' ";
		# execute Query
		$meta_keys = $wpdb->get_col($query);
		# return Depend on the Query result 
		if(empty($meta_keys)){
			return array(FALSE, 'ERROR: Empty! No Meta key exist of the Post.');
		}else{
			return array(TRUE, $meta_keys);
		}
	}

	/**
	 * This Function will return [wordPress Users] Meta keys.
	 * @since      3.3.0
	 * @return     array    This array has two vale First one is Bool and Second one is meta key array.
	*/
	public function wpgsi_users_metaKeys(){
		# Global Db object 
		global $wpdb;
		# Query 
		$query = "SELECT  DISTINCT( $wpdb->usermeta.meta_key ) FROM $wpdb->usermeta ";
		# execute Query
		$meta_keys = $wpdb->get_col($query);
		# return Depend on the Query result 
		if(empty($meta_keys)){
			return array(FALSE, 'ERROR: Empty! No Meta key exist of users.');
		}else{
			return array(TRUE, $meta_keys);
		}
	}

	/**
	 * This Function will return [wordPress Users] Meta keys.
	 * @since      3.3.0
	 * @return     array    This array has two vale First one is Bool and Second one is meta key array.
	*/
	public function wpgsi_comments_metaKeys(){
		# Global Db object 
		global $wpdb;
		# Query 
		$query = "SELECT  DISTINCT( $wpdb->commentmeta.meta_key ) FROM $wpdb->commentmeta ";
		# execute Query
		$meta_keys = $wpdb->get_col($query);
		# return Depend on the Query result 
		if(empty($meta_keys)){
			return array(FALSE, 'ERROR: Empty! No Meta key exist on comment meta.');
		}else{
			return array(TRUE, $meta_keys);
		}
	}

	/**
	 * This Function will return [WooCommerce Order] Meta keys.
	 * @since      3.3.0
	 * @return     array    This array has two vale First one is Bool and Second one is meta key array.
	*/
	public function wpgsi_wooCommerce_order_metaKeys(){
		# Global Db object 
		global $wpdb;
		# Query 
		$query  =  "SELECT  DISTINCT( $wpdb->postmeta.meta_key ) 
					FROM $wpdb->posts 
					LEFT JOIN $wpdb->postmeta 
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
					WHERE $wpdb->posts.post_type = 'shop_order' 
					AND $wpdb->postmeta.meta_key != '' ";
		# execute Query
		$meta_keys = $wpdb->get_col($query);
		# return Depend on the Query result 
		if(empty($meta_keys)){
			return array(FALSE, 'ERROR: Empty! No Meta key exist of the post type WooCommerce Order.');
		}else{
			return array(TRUE, $meta_keys);
		}
	}

	/**
	 * This Function will return [WooCommerce product] Meta keys.
	 * @since      3.3.0
	 * @return     array    This array has two vale First one is Bool and Second one is meta key array.
	*/
	public function wpgsi_wooCommerce_product_metaKeys(){
		# Global Db object 
		global $wpdb;
		# Query 
		$query  =  "SELECT  DISTINCT( $wpdb->postmeta.meta_key ) 
					FROM $wpdb->posts 
					LEFT JOIN $wpdb->postmeta 
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
					WHERE $wpdb->posts.post_type = 'product' 
					AND $wpdb->postmeta.meta_key != '' ";
		# execute Query
		$meta_keys = $wpdb->get_col($query);
		# return Depend on the Query result 
		if(empty($meta_keys)){
			return array(FALSE, 'ERROR: Empty! No Meta key exist of the Post type WooCommerce Product.');
		}else{
			return array(TRUE, $meta_keys);
		}
	}

	/**
	 *  Contact form 7,  form  fields 
	 *  @since    3.1.0
	*/
	public function cf7_forms_and_fields(){
		# is there CF7 
		if(! in_array('contact-form-7/wp-contact-form-7.php', get_option("active_plugins")) OR ! $this->common->wpgsi_dbTableExists('posts')){
			return array(FALSE, "ERROR:  Contact form 7 is Not installed or DB Table is Not Exist  ");
		}

		$cf7forms 		= array();
		$fieldsArray 	= array();	
		global $wpdb;	
		$cf7Forms = $wpdb->get_results("SELECT * FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type = 'wpcf7_contact_form' AND {$wpdb->postmeta}.meta_key = '_form'");
		# Looping the Forms 
		foreach($cf7Forms as $form){	
			# Inserting Fields 																			# Loop the Custom Post ;
			$cf7forms[ "cf7_" . $form->ID ] = "Cf7 - " . $form->post_title;	
			# Getting Fields Meta 
			$formFieldsMeta = get_post_meta($form->ID, '_form', true);
			# Replacing Quoted string 
			$formFieldsMeta = preg_replace('/"((?:""|[^"])*)"/', "", $formFieldsMeta);
			# Removing : txt 
			$formFieldsMeta = preg_replace('/\w+:\w+/', "", $formFieldsMeta);
			# Removing submit
			$formFieldsMeta = preg_replace('/\bsubmit\b/', "", $formFieldsMeta);
			# if txt is Not empty 
			if(! empty($formFieldsMeta)){
				# Getting Only [] txt 
				$bracketTxt = array();
				# Separating bracketed txt and inserting theme to  $bracketTxt array
				preg_match_all('/\[(.*?)\]/', $formFieldsMeta, $bracketTxt);
				# Check is set & not empty
				if(isset($bracketTxt[1]) && !empty($bracketTxt[1])){
					# Field Loop 
					foreach($bracketTxt[1] as $txt){
						# Divide the TXT after every space 
						$tmpArr =  explode(' ', $txt);
						# taking Only the second Element of every array || first one is Field type || Second One is Field key 
						$singleItem =  array_slice($tmpArr, 1, 1);
						# Remove Submit Empty Array || important i am removing submit 
						if(isset($singleItem[0] ) && !empty($singleItem[0])){
							$fieldsArray["cf7_" . $form->ID][$singleItem[0]] = $singleItem[0];
						}
					}
				}
			}
		} # Loop ends 

		# Adding extra fields || like Date and Time || Add more in future  
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				if(! empty($fieldsArray)){
					foreach($fieldsArray as $formID => $formFieldsArray){
						# For Time
						if(! isset($formFieldsArray['wpgsi_submitted_time'])){
							$fieldsArray[$formID]['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
						}

						# for Date 
						if(! isset($formFieldsArray['wpgsi_submitted_date'])){
							$fieldsArray[$formID]['wpgsi_submitted_date'] = "wpgsi Form submitted date";
						}
					}
				}
			}
		}
		return array(TRUE, $cf7forms, $fieldsArray);
	}

	/**
	 *  Ninja  form  fields 
	 *  @param     int     $user_id     username
	 *  @param     int     $old_user_data     username
	 *  @since     1.0.0
	*/
	public function ninja_forms_and_fields(){
		# if ninja form is installed 
		if(! in_array('ninja-forms/ninja-forms.php', get_option("active_plugins")) OR ! $this->common->wpgsi_dbTableExists('nf3_forms')){
			return array(FALSE, "ERROR:  Ninja form 7 is Not Installed " );
		}
		global $wpdb;	
		$FormArray 	 	= array();																								# Empty Array for Value Holder 
		$fieldsArray 	= array();		
		$ninjaForms 	= $wpdb->get_results("SELECT * FROM {$wpdb->prefix}nf3_forms", ARRAY_A);
		
		foreach($ninjaForms as $form){
			$FormArray["ninja_". $form["id"]] = "Ninja - ". $form["title"];	
			$ninjaFields =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}nf3_fields where parent_id = '".$form["id"]."'", ARRAY_A);
			foreach($ninjaFields as $field){
				$field_list = array("textbox", "textarea", "number");
				# freemius 
				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						$field_list = array(
							"firstname",
							"lastname",
							"textbox",
							"textarea",
							"email",
							"phone",
							"number",
							"checkbox",
							"date",
							"listmultiselect",
							"listradio",
							"listselect",
							"liststate",
							"starrating",
							"hidden",
							"address",
							"city",
							"zip",
							"type",
							"confirm",
							"listimage",
						);
					}
				}

				if(in_array($field["type"], $field_list)){
					$fieldsArray["ninja_". $form["id"]] [$field["key"]] = $field["label"];
				}
			}
		}

		# Adding extra fields || like Date and Time || Add more in future  
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				if(! empty($fieldsArray)){
					foreach($fieldsArray as $formID => $formFieldsArray){
						# For Time
						if(! isset($formFieldsArray['wpgsi_submitted_time'])){
							$fieldsArray[$formID]['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
						}
						
						# for Date 
						if(! isset($formFieldsArray['wpgsi_submitted_date'])){
							$fieldsArray[$formID]['wpgsi_submitted_date'] = "wpgsi Form submitted date";
						}
					}
				}
			}
		}

		return array(TRUE, $FormArray, $fieldsArray);
	}
 
	/**
	 *  formidable form  fields 
	 *  @since    1.0.0
	*/
	public function formidable_forms_and_fields(){
		# check and balance 
		if(! in_array('formidable/formidable.php', get_option("active_plugins")) OR ! $this->common->wpgsi_dbTableExists('frm_forms')){
			return array(FALSE, "ERROR: formidable form  is Not Installed OR DB table is Not Exist");
		}
		# Global database object 
		global $wpdb;
		$FormArray 	 = array();																						# Empty Array for Value Holder 
		$fieldsArray = array();																						# Empty Array for Holder 
		$frmForms 	 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}frm_forms");								# Getting  Forms Database 
		
		foreach($frmForms as $form){
			$FormArray["frm_".$form->id] =  "Formidable - " . $form->name ;											# Inserting ARRAY title 
			# Getting Meta Fields || maybe i don't Know ;-D
			$fields = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}frm_fields WHERE form_id = " . $form->id . " ORDER BY field_order"); 	# Getting  Data from Database 
			foreach($fields as $field){
				# Default fields
				$field_list = array("text", "textarea", "number");

				# freemius
				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						$field_list = array(
							"text", 
							"textarea", 
							"number", 
							"email", 
							"phone", 
							"hidden", 
							"url",
							"user_id",
							"select",
							"radio",
							"checkbox",
							"rte",
							"date",
							"time",
							"star",
							"range",
							"password",
							"address",
							"repeater",
							"quantity",
							"credit_card"
						);
					}
				}

				if(in_array($field->type, $field_list )){
					$fieldsArray["frm_".$form->id][$field->id] = $field->name;
				}
			}
		}

		# Adding extra fields || like Date and Time || Add more in future  
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				if(! empty($fieldsArray)){
					foreach($fieldsArray as $formID => $formFieldsArray){
						# For Time
						if(! isset($formFieldsArray['wpgsi_submitted_time'])){
							$fieldsArray[$formID]['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
						}
						
						# for Date 
						if(! isset($formFieldsArray['wpgsi_submitted_date'])){
							$fieldsArray[$formID]['wpgsi_submitted_date'] = "wpgsi Form submitted date";
						}
					}
				}
			}
		}

		return array(TRUE, $FormArray, $fieldsArray);																# Inserting Data to the Main [$eventsAndTitles ] Array 
	}

	/**
	 *  wpforms fields 
	 *  @since    1.0.0
	*/
	public function wpforms_forms_and_fields(){
		# Check and Balance 
		if(! count(array_intersect(get_option("active_plugins"), array('wpforms-lite/wpforms.php', 'wpforms/wpforms.php'))) OR ! $this->common->wpgsi_dbTableExists('posts')){
			return array(FALSE, "ERROR:  wp form is Not Installed OR DB Table is Not Exist  " );
		}
		# Empty holder 
		$FormArray	 = array();
		$fieldsArray = array();	
		# Getting Data from Database 
		global $wpdb;
		$wpforms = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'wpforms'  ");
		
		foreach($wpforms as $wpform){
			$FormArray[ "wpforms_". $wpform->ID ] = "WPforms - ".$wpform->post_title ;	
			$post_content =  json_decode($wpform->post_content);
			
			foreach($post_content->fields as $field){
				# Default fields
				$field_list = array("name", "text", "textarea");

				# freemius
				if(wpgsi_fs()->is__premium_only()){
					if(wpgsi_fs()->can_use_premium_code()){
						$field_list = array(
							"name", 
							"text", 
							"email", 
							"textarea", 
							"number", 
							"number-slider", 
							"phone", 
							"address", 
							"date-time", 
							"url", 
							"password", 
							"hidden", 
							"rating", 
							"checkbox", 
							"radio", 
							"select", 
							"payment-single", 
							"payment-checkbox", 
							"payment-total", 
							"stripe-credit-card"
						);
					}
				}

				if(in_array($field->type, $field_list)){
					$fieldsArray["wpforms_" . $wpform->ID][$field->id] = $field->label;
				}
			}	
		}

		# Adding extra fields || like Date and Time || Add more in future  
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				if(! empty($fieldsArray)){
					foreach($fieldsArray as $formID => $formFieldsArray){
						# For Time
						if(! isset($formFieldsArray['wpgsi_submitted_time'])){
							$fieldsArray[$formID]['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
						}
						
						# for Date 
						if(! isset($formFieldsArray['wpgsi_submitted_date'])){
							$fieldsArray[$formID]['wpgsi_submitted_date'] = "wpgsi Form submitted date";
						}
					}
				}
			}
		}

		return array(TRUE, $FormArray, $fieldsArray);	
	}

	# FIXME:
	# do it after Upload || last off all forms 
	/**
	 *  WE forms fields 
	 *  @since    1.0.0
	*/
	public function weforms_forms_and_fields(){
		# check and balance 
		if(! in_array('weforms/weforms.php', get_option("active_plugins"))  OR  ! $this->common->wpgsi_dbTableExists('posts')){
			return array(FALSE, "ERROR:  weForms  is Not Active  OR DB is not exist.");
		}
		# empty holders
		$FormArray	 	= array();
		$fieldsArray 	= array();
		$fieldTypeArray = array();
		# Global database object 
		global $wpdb;
		$weforms 	 = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'wpuf_contact_form' ");
		$weFields 	 = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'wpuf_input' ");
		# create the list 
		foreach($weforms as $weform){
			if(isset($weform->ID, $weform->post_title)){
				$FormArray[ "we_" . $weform->ID ] = 'weForms - '. $weform->post_title;
			}
		}
		
		foreach($weFields as $Field){
			foreach($FormArray as $weformID => $weformTitle){
				if(isset($Field->post_parent) AND  $weformID  ==  "we_" . $Field->post_parent){
					$content_arr = unserialize( $Field->post_content);
					if(isset($content_arr['name'], $content_arr['label'], $content_arr['template'])){
						$fieldsArray[ $weformID ][ $content_arr['name'] ] 	  =   $content_arr['label'] ;
						$fieldTypeArray[ $weformID ][ $content_arr['name'] ]  =   $content_arr['template'] ;
					}
				}
			}
		}

		# Adding extra fields || like Date and Time || Add more in future  
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				if(! empty($fieldsArray)){
					foreach($fieldsArray as $formID => $formFieldsArray){
						# For Time
						if(! isset($formFieldsArray['wpgsi_submitted_time'])){
							$fieldsArray[$formID]['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
						}
						
						# for Date 
						if(! isset($formFieldsArray['wpgsi_submitted_date'])){
							$fieldsArray[$formID]['wpgsi_submitted_date'] = "wpgsi Form submitted date";
						}
					}
				}
			}
		}
		#
		return array(TRUE, $FormArray, $fieldsArray, $fieldTypeArray);
	}

	/**
	 * 	Under Construction 
	 *  gravity forms fields 
	 *  @since    1.0.0
	*/
	public function gravity_forms_and_fields(){
		# check to see active 
		if(! in_array('gravityforms/gravityforms.php', get_option("active_plugins"))){
			return array(FALSE, "ERROR:  gravity forms  is Not Active  OR DB is not exist");
		}
		# 
		if(! class_exists('GFAPI')){
			return array(FALSE, "ERROR:  gravityForms class GFAPI is not exist");
		}

		$gravityForms = GFAPI::get_forms();
		#check and Test 
		if(! empty($gravityForms)){
			# Empty array holder Declared
			$FormArray 	 	= array();																								# Empty Array for Value Holder 
			$fieldsArray 	= array();	
			$fieldTypeArray = array();	

			if(wpgsi_fs()->is__premium_only()){
				if(wpgsi_fs()->can_use_premium_code()){
					# New Code Loop
					foreach($gravityForms as $form){
						$FormArray[ "gravity_". $form["id"] ] = "Gravity - ". $form["title"];	
						# Form Fields || Check fields are set or Not
						if(isset($form['fields']) AND is_array($form['fields'])){
							foreach($form['fields'] as $field){
								if(empty($field['inputs'])){
									# if there is no subfields
									$fieldsArray[ "gravity_" . $form["id"] ] [ $field["id"] ] 		= $field["label"];
									$fieldTypeArray[ "gravity_" . $form["id"] ] [ $field["id"] ] 	= $field["type"];
								}else{
									# Looping Subfields
									foreach($field["inputs"] as $subField){
										$fieldsArray[ "gravity_". $form["id"] ] [ $subField["id"] ] 	= $field["label"].' ('. $subField["label"] .')';
										$fieldTypeArray[ "gravity_". $form["id"] ] [ $subField["id"] ] 	= $field["type"];
									}
								}
							}
						}
					}
				}
			}
		}else{
			return array(FALSE, array(), array(), array());
		}

		return array(TRUE, $FormArray, $fieldsArray, $fieldTypeArray);
	}

	/**
	 * forminator forms fields 
	 * @since      3.6.0
	 * @return     array   First one is CPS and Second one is CPT's Field source.
	*/
	public function forminator_forms_and_fields(){
		# check to see Plugin is active 
		if(! in_array('forminator/forminator.php', get_option("active_plugins"))){
			return array(FALSE, "ERROR: forminator form  is Not Installed OR no integration Exist");
		}

		$FormArray 	 = array();			# Empty Array for Value Holder 
		$fieldsArray = array();			# Empty Array for Holder 
		
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				# Getting Forminator Fields 
				$forms = Forminator_API::get_forms();
				# Check And Balance 
				if(! empty($forms)){
					# Looping the Forms 
					foreach($forms as $form){
						# inserting Forms 
						$FormArray[ "forminator_". $form->id ] = "forminator - ". $form->name;
						# Getting Fields 
						$fields = get_post_meta($form->id , 'forminator_form_meta');
						# Check & balance 
						if(isset($fields[0]['fields']) AND !empty($fields[0]['fields'])){
							# Looping the Fields 
							foreach($fields[0]['fields'] as $field){
								if(isset($field['id'], $field['field_label'])){
									$fieldsArray["forminator_" . $form->id][$field['id']] = (isset($field['field_label']) AND !empty($field['field_label']))  ?  $field['field_label'] : $field['id'];
								}
							}
							# Date And Time 
							$fieldsArray["forminator_". $form->id]['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
							$fieldsArray["forminator_". $form->id]['wpgsi_submitted_date'] = "wpgsi Form submitted date";
						}
					}
				}
			}
		}
		
		return array(TRUE, $FormArray, $fieldsArray);		
	}

	/**
	 * fluent forms fields 
	 * @since      3.6.0
	 * @return     array   First one is CPS and Second one is CPT's Field source.
	*/
	public function fluent_forms_and_fields(){
		# check to see plugin is active or not 
		if(! in_array('fluentform/fluentform.php', get_option("active_plugins"))){
			return array(FALSE, "ERROR: fluentform form  is Not Installed OR no integration Exist");
		}

		$FormArray 	 = array();			
		$fieldsArray = array();			
		$fluentForms = fluentFormApi('forms')->forms(array('sort_by' => 'DESC'), TRUE);
		
		if(wpgsi_fs()->is__premium_only()){
			if(wpgsi_fs()->can_use_premium_code()){
				# Check and Balance
				if(isset($fluentForms['data']) AND !empty($fluentForms['data'])){
					foreach($fluentForms['data'] as $form){
						if(isset($form->id, $form->title, $form->form_fields)){
							$FormArray[ "fluent_" . $form->id ] = "fluent - " . $form->title;
							# getting Fields
							$fields =  fluentFormApi('forms')->form($formId = $form->id)->fields();
							# Check and Balance
							if(! empty($fields) AND isset($fields['fields'])){
								foreach($fields['fields'] as $field){
									if(isset($field['index'], $field['attributes']['name'])){
										$fieldsArray["fluent_" . $form->id ] [ $field['attributes']['name'] ] = (isset($field['attributes']['placeholder']) AND !empty($field['attributes']['placeholder'])) ? $field['attributes']['placeholder'] : $field['attributes']['name'];
									}
								}
							}
						}
						# Date And Time 
						$fieldsArray["fluent_". $form->id] ['wpgsi_submitted_time'] = "wpgsi Form submitted  time";
						$fieldsArray["fluent_". $form->id] ['wpgsi_submitted_date'] = "wpgsi Form submitted date";
					}
				}
			}
		}
		
		return array(TRUE, $FormArray, $fieldsArray);	
	}

	/**
	 * This Function will All Custom Post types wit associative  data 
	 * This function will check global $wp_post_types;  OR  get_post_types() if not found or not exist then it will return false and error message  
	 * 
	 * @since      3.7.2
	 * @return     array   First one is CPS and Second one is CPT's Field source.
	*/
	################################################################################################################################
	# this function has a error ! structural error, $eventDataFields outsource should not contain event field and meta field on same array 
	# *** this is not a Error, All the meta data function returns  this way. 
	################################################################################################################################
	public function wpgsi_allCptEvents(){
		# declaring global post type 
		global $wp_post_types;
		# Custom post type empty holder 
		$customPostTypes = array();
		# remove array 
		$removeArray   = array(	
								"post",
								"page",
								"wpforms",
								"acf-field-group",
								"acf-field",
								"product",
								"product_variation", 
								"shop_order",
								"shop_order_refund"
							);
		# if global $wp_post_types; is set and not empty 
		if(isset($wp_post_types) AND ! empty($wp_post_types)) {
			foreach($wp_post_types as $postKey => $PostValue){
				# if Post type is Not Default 
				if(isset($PostValue->_builtin) AND ! $PostValue->_builtin){
					# Look is it on remove list, if not insert 
					if(! in_array($postKey, $removeArray)){
						# Pre populate $cpts array 
						if(isset($PostValue->label) AND ! empty($PostValue->label)){
							$customPostTypes[$postKey]  =  $PostValue->label ." (".  $postKey. ")";
						}else{
							$customPostTypes[$postKey]  = $postKey;
						}
					}
				}
			}
			#  if get_post_types() function is exist and not empty 
		} elseif (function_exists('get_post_types') AND ! empty(get_post_types(array('_builtin' => false), 'names', 'and'))) {
			foreach(get_post_types(array('_builtin' => false ), 'names', 'and') as $key => $value){
				if(! in_array($key, $removeArray)){
					$customPostTypes[ $key ] = $value;
				}
			}
		} else {
			# Keeping Log 
			$this->common->wpgsi_log(get_class($this), __METHOD__, "507", "ERROR: global wp_post_types or  get_post_types() are empty.");
			# return
			return array(FALSE, "ERROR: global wp_post_types or  get_post_types() are empty.");
		}
		# check $ customPostTypes is empty or not 
		if(! empty($customPostTypes)){
			# Looping for Creating Extra Events Like Update and Delete 
			foreach($customPostTypes as $key => $value){
				$cptEvents['cpt_new_'.$key] 	=  'CPT New ' . $value;
				$cptEvents['cpt_update_'.$key] 	=  'CPT Update ' . $value;
				$cptEvents['cpt_delete_'.$key] 	=  'CPT Delete ' . $value;
			}
			# Now setting default Event data Source Fields; Those events data source  are common in all WordPress Post type 
			$eventDataFields = array(
								"postID"				=>"ID",
								"post_authorID"			=>"post author_ID",
								"authorUserName"		=>"author User Name",
								"authorDisplayName"		=>"author Display Name",
								"authorEmail"			=>"author Email",
								"authorRole"			=>"author Role",
								#
								"post_title"			=>"post title",
								"post_date"				=>"post date",
								"post_date_gmt"			=>"post date gmt",
								#
								"site_time"				=>"Site Time",
								"site_date"				=>"Site Date",
								#
								"post_content"			=>"post content",
								"post_excerpt"			=>"post excerpt",
								"post_status"			=>"post status",
								"comment_status"		=>"comment status",
								"ping_status"			=>"ping status",
								"post_password"			=>"post password",
								"post_name"				=>"post name",
								"to_ping"				=>"to ping",
								"pinged"				=>"pinged",
								#
								"post_modified"			=>"post modified date",
								"post_modified_gmt"		=>"post modified date GMT",
								"post_parent"			=>"post parent",
								"guid"					=>"guid",
								"menu_order"			=>"menu order",
								"post_type"				=>"post type",
								"post_mime_type"		=>"post mime type",
								"comment_count"			=>"comment count",
								"filter"				=>"filter",
							);
			# Global Db object 
			global $wpdb;
			# Query for getting Meta keys 
			$query  =  "SELECT  DISTINCT( $wpdb->postmeta.meta_key ) 
						FROM $wpdb->posts 
						LEFT JOIN $wpdb->postmeta 
						ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
						WHERE $wpdb->posts.post_type != 'post' 
						AND $wpdb->posts.post_type   != 'page' 
						AND $wpdb->posts.post_type   != 'product' 
						AND $wpdb->posts.post_type   != 'shop_order' 
						AND $wpdb->posts.post_type   != 'shop_order_refund' 
						AND $wpdb->posts.post_type   != 'product_variation' 
						AND $wpdb->posts.post_type 	 != 'wpforms' 
						AND $wpdb->postmeta.meta_key != '' ";
			# execute Query for getting the Post meta key it will use for event data source 
			$meta_keys = $wpdb->get_col($query);
			# Inserting Meta keys to Main $eventDataFields Array;
			if(! empty($meta_keys) AND is_array($meta_keys)){
				foreach($meta_keys as  $value){
					if(! isset($eventDataFields[ $value ])){
						$eventDataFields[ $value ] = "CPT Meta ". $value; 
					}
				}
			}else{
				# insert to the log but don't return
				# ERROR:  Meta keys  are empty;
			}
			# Everything seems ok, Now send the CPT events and Related Data source;
			return array(TRUE, $customPostTypes, $cptEvents, $eventDataFields, $meta_keys);
		}else{
			return array(FALSE, "ERROR: custom Post type Array is Empty.");
		}
	}

	/**
	 * database table and columns 
	 * @since      3.6.0
	 * @return     array   First one is CPS and Second one is CPT's Field source.
	*/
	public function database_tables_and_columns(){
		# Empty holder
		$tables 		= array();
		$tableColumn 	= array();
		# Global database instance 
		global $wpdb;    
		# Database Query 
		$result = $wpdb->get_results("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '" . $wpdb->dbname ."'" , ARRAY_A);
		# if not empty 
		if(isset($result) AND !empty($result)){
			# Looping the Table and Columns
			foreach($result as $row_array){
				$tables[$row_array['TABLE_NAME']] = "Database Table " . $row_array['TABLE_NAME'];
				$tableColumn[$row_array['TABLE_NAME']][$row_array['COLUMN_NAME']] = "Column " . $row_array['COLUMN_NAME'];
			}
			# return true and data 
			return array(TRUE, $tables, $tableColumn);		
		}else{
			# return false and empty array()
			return array(FALSE, array(), array());		
		}
	}

	/**
	 * database migrations 
	 * @since     3.7.0
	 * @return    array   	it will not return  array of relation
	*/
	public function wpgsi_db_migration(){
		
		$current_version = "3.7.0";

		if(! in_array($current_version, array("3.6.0","3.5.0","3.3.0","3.2.0","3.1.0","3.0.0"))){
			# getting all wpgsiIntegration post;
			$listOfConnections   	  =  get_posts(array(
				'post_type'   	 	  => 'wpgsiIntegration',
				'post_status' 		  => array('publish', 'pending'),
				'posts_per_page' 	  => -1
			));
			# Holder array
			$integrationsArray 		  = array();
			# integration loop starts
			foreach($listOfConnections as $key => $value){
				# Compiled to JSON String 
				$post_content =@ json_decode($value->post_content, TRUE);
				$post_excerpt =@ json_decode($value->post_excerpt, TRUE);
				# if JSON Compiled SUCCESSfully 
				if(is_array($post_content) AND is_array($post_excerpt)){
					$integrationsArray[$value->ID]["IntegrationID"] 	= $value->ID;
					$integrationsArray[$value->ID]["IntegrationTitle"] 	= $value->post_title;
					$integrationsArray[$value->ID]["ColumnTitle"] 		= json_encode($post_content[0]);
					$integrationsArray[$value->ID]["Relation"] 			= json_encode($post_content[1]);
					$integrationsArray[$value->ID]["DataSource"] 		= $post_excerpt["DataSource"];
					$integrationsArray[$value->ID]["DataSourceID"] 		= $post_excerpt["DataSourceID"];
					$integrationsArray[$value->ID]["Worksheet"] 		= $post_excerpt["Worksheet"];
					$integrationsArray[$value->ID]["WorksheetID"] 		= $post_excerpt["WorksheetID"];
					$integrationsArray[$value->ID]["Spreadsheet"] 		= $post_excerpt["Spreadsheet"];
					$integrationsArray[$value->ID]["SpreadsheetID"] 	= $post_excerpt["SpreadsheetID"];
					$integrationsArray[$value->ID]["Status"] 			= $value->post_status;	
				}
			}

			# Now Run The Update 
			foreach($integrationsArray as $ID => $valueArray){
				if(isset($valueArray['IntegrationID'], $valueArray['IntegrationTitle'], $valueArray['DataSource'], $valueArray['DataSourceID'])){
					# stor before unset
					$IntegrationID 		= $valueArray['IntegrationID'];
					$IntegrationTitle 	= $valueArray['IntegrationTitle'];
					$DataSource         = $valueArray['DataSource'];
					$DataSourceID       = $valueArray['DataSourceID'];
					# unset unnecessary array items
					unset($valueArray['IntegrationID']);
					unset($valueArray['IntegrationTitle']);
					unset($valueArray['Status']);
					unset($valueArray['DataSource']);
					unset($valueArray['DataSourceID']);
					# Now Run Update Function 
					$customPost = array(
						'ID'				=> $IntegrationID,														
						'post_title'    	=> $IntegrationTitle, 										
						'post_content'  	=> $DataSource, 										
						'post_excerpt'  	=> $DataSourceID, 																				
						'post_type'   		=> 'wpgsiIntegration',																																																									
						# post meta
						'meta_input'		=> $valueArray
					);
					# Updating Custom Post Type 
					// $post_id = wp_update_post($customPost);
					print_r($customPost);
				}
			}

			# Now Run the update 
			// print_r($integrationsArray);
		}
	}

	# END of CLASS Wpgsi_Admin 
}

#==================================  TO DO 3.7.0  ==================================
# change the Plugin with New Data database structure 
# Database Migration  run on Plugin Update 
# change the Database to G sheet Code according to the new Database structure 
# Change All database dependant things 
# Free up memory 
# Code harmony 
# ==================================  Thought  ==================================
# DataSource should be common Like
# database, wordpressPost, wordpressPage, wordpressComment, wordpressUser, customPostType, 
# cf7, formidable, wpForm, ninjaForm, weForm, 

