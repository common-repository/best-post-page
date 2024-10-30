<?php
/*
Plugin Name: Best Post Page 
Plugin URI: http://kelohe.com/bestpostpage/
Description: This plugin creates a page of your best posts based on views and comments.  You should have <a href='http://wordpress.org/extend/plugins/wp-shortstat2/'>WP-ShortStat</a> installed in order to get the most accurate results. 
Author: Mike Fisher
Version: 1.3
Author URI: http://kelohe.com/
*/

/*  Copyright 2009  Mike Fisher  (email : mfisher5kavika@yahoo.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


set_error_handler("my_warning_handler", E_WARNING);

function my_warning_handler($errno, $errstr) {
// do something
}; //function


// +----------------------------------------------------------------------+
// | Initialization functions                                             |
// +----------------------------------------------------------------------+

if(!function_exists('ss_installed')) {
  function ss_installed () {
    global $wpdb;
    $plugin_option1_name = 'bestpostpage_ss_installed';
    $table_name = $wpdb->prefix . "ss_stats";


    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	if(get_option($plugin_option1_name)) update_option($plugin_option1_name, 'FALSE'); 
	else add_option($plugin_option1_name, 'FALSE', '', 'yes');
    }
    else  {
        if(get_option($plugin_option1_name)) update_option($plugin_option1_name, 'TRUE');
        else add_option($plugin_option1_name, 'TRUE', '', 'yes');
    } // if / else
    insert_page();

  }; // function
}; // if function exists


if(!function_exists('insert_page')) {
  function insert_page () {

    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $post_data = array(
      'post_status' => 'publish',
      'post_type' => 'page',
      'post_author' => 1,
      'post_title' => 'BEST OF POSTS',
      'post_name' => 'best-of-posts',
      'post_content' => 'This is a list of the best posts.'
    );

    $page_exist = FALSE;
    $pages = get_pages();
    foreach ($pages as $page) {
      if ($page->post_name == 'best-of-posts') $page_exist = TRUE;
    }

    if (!$page_exist) {
      $result = wp_insert_post( $post_data );
      if ($result == 0) {
        deactivate_plugins(basename(__FILE__)); //Deactivate plugin
        wp_die("Sorry, could not create Best Of Post page. ");
      }
    }


  }; //function 
}; // if function


if(!function_exists('bestpostpage_activation')) {
function bestpostpage_activation() {
    $plugin_version_name = 'bestpostpage_data'; //plugin_version
    $plugin_version = '1.1';
    $pluginOptions = get_option($plugin_version_name);

    if ( false === $pluginOptions ) {
        // Install plugin
        ss_installed();
        add_option($plugin_version_name, $plugin_version, '', 'yes');

    } else if ( $pluginOptions != $plugin_version ) {
        // Upgrade plugin
        ss_installed();
        update_option($plugin_version_name, $plugin_version);
    }

  };// function
};// if function


if(!function_exists('bestpostpage_deactivation')) {
  function bestpostpage_deactivation() {
    $plugin_version_name = 'bestpostpage_data'; //plugin version
    $plugin_version = '1.1';
    $plugin_option1_name = 'bestpostpage_ss_installed'; //check if wp_shortstats is installed
    $plugin_option2_name = 'bestpostpage_return_number'; //number of post on page
    $plugin_option3_name = 'bestpostpage_learning_rate'; //percentage of posts used to learn

    $pluginOptions = get_option($plugin_version_name);

    if ( $pluginOptions == $plugin_version ) {
       $pages = get_pages();
       foreach ($pages as $page) {
          if ($page->post_name == 'best-of-posts')
             wp_delete_post($page->ID);
       } // foreach

        delete_option($plugin_version_name);
        delete_option($plugin_option1_name);
        delete_option($plugin_option2_name);
        delete_option($plugin_option3_name);
    } // if same version

  }; // function
}; // if function


// +----------------------------------------------------------------------+
// | Admin page functions                                                 |
// +----------------------------------------------------------------------+

if(!function_exists('printAdminPage')) {
  function printAdminPage() {
    $plugin_option2_name = 'bestpostpage_return_number'; //number of post on page
    $plugin_option3_name = 'bestpostpage_learning_rate'; //percentage of posts used to learn
    $pattern = '/%/';

     if (isset($_POST['update_BestPostPageSettings'])) {
        if (isset($_POST[$plugin_option2_name]) && ($_POST[$plugin_option2_name]<=100) && ($_POST[$plugin_option2_name]>=2) ) {
	    $pluginOptions = get_option($plugin_option2_name);
	    $post_option2_value = preg_replace($pattern,"",$_POST[$plugin_option2_name]);
	    if ( false === $pluginOptions ) add_option($plugin_option2_name,$post_option2_value,'','yes');
	    else update_option($plugin_option2_name,$post_option2_value);
         }   

         if (isset($_POST[$plugin_option3_name])&& ($_POST[$plugin_option3_name]<=50) && ($_POST[$plugin_option3_name]>=0) ) {
            $post_option3_value = preg_replace($pattern,"",$_POST[$plugin_option3_name]);
            $pluginOptions = get_option($plugin_option3_name);
            if ( false === $pluginOptions ) add_option($plugin_option3_name,$post_option3_value,'','yes');
            else update_option($plugin_option3_name,$post_option3_value);
         }   
      ?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "BestPostPageSettings");?></strong></p></div>
      <?php
      } ?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2>Best Post Page Plugin Settings</h2>
<h3>How many posts should appear on the 'Best of Posts' page?</h3>
<p>Enter a number between 2 and 100.</p>
<p>
<label for="bestpostpage_return_number">
<input type="text" name="bestpostpage_return_number" 
<?php $plugin_option2_value = get_option($plugin_option2_name);
echo "value=\"".$plugin_option2_value."\"";
?> /></label>    
</p>

<h3>What percentage of the posts on the 'Best of Posts' page should be random?</h3>
<p>Enter a learning rate between 0 and 50.</p>
<p>
<label for="bestpostpage_learning_rate">
<input type="text" name="bestpostpage_learning_rate"
<?php $plugin_option3_value = get_option($plugin_option3_name);
echo "value=\"".$plugin_option3_value."\"";
?> /></label>
</p>

 
<div class="submit">
<input type="submit" name="update_BestPostPageSettings" value="<?php _e('Update Settings', 'BestPostPageSettings') ?>" /></div>
</form>
 </div>
                    <?php
   }//function printAdminPage()
} // if function

if(!function_exists('BestPostPageAdmin')) {
   function BestPostPageAdmin() {
      add_options_page('Best Post Page Plugin Options', 'Best Post Page', 8, basename(__FILE__), 'printAdminPage');
   } // funciton
} // if function

// +----------------------------------------------------------------------+
// | Filter functions for 'Best Of Post' page                             |
// +----------------------------------------------------------------------+

if(!function_exists('qry_posts')) {
 function qry_posts() {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $plugin_option1_name = 'bestpostpage_ss_installed';

	$stats_table_name = $wpdb->prefix . "ss_stats";
        $posts_table_name = $wpdb->prefix . "posts";
        $comments_table_name = $wpdb->prefix . "comments";

//$qry_ss = "SELECT res_id, view_count, count(c.comment_ID) as comment_count"
//        . " from ( "
 //       . " SELECT p.ID as res_id, count(s.remote_ip) as view_count "
//        . " FROM $stats_table_name as s, $posts_table_name as p "
//        . " where " // resource like '%200%' and "
//        . " right(p.guid,15) = right(s.resource,15) "
//	. " and p.post_type <> 'page' "
//        . " group by s.resource order by count(s.remote_ip) desc "
//        . " limit 50 )as sub left outer join $comments_table_name as c on "
//        . " sub.res_id = c.comment_post_ID group by sub.res_id";

$qry_ss = "SELECT res_id, view_count, count(c.comment_ID) as comment_count "
	. " from (select sub_p.res_id, count(s.remote_ip) as view_count "
  	. "FROM wp_ss_stats as s, ( "
    	. "SELECT p.ID as res_id, right(p.guid,15) as pguid "
    	. "FROM wp_posts as p "
    	. "where p.post_type <> 'page' limit 50 ) as sub_p "
  	. "where pguid = right(s.resource,15) "
  	. "group by s.resource  order by count(s.remote_ip) desc ) "
	. "as sub left outer join wp_comments as c on "
	. "sub.res_id = c.comment_post_ID group by sub.res_id ";


$qry_no_ss = "select comment_post_id as res_id, 1 as view_count, "
	. "count(comment_id) as comment_count FROM $comments_table_name "
	. "GROUP BY res_id ORDER BY res_id DESC LIMIT 50";

//this tests the actual value of the option NOT whether it exists
 	if(get_option($plugin_option1_name) == 'FALSE') $sql = $qry_no_ss;
 	else $sql = $qry_ss;

        $qry_results = $wpdb->get_results($sql);
	$i = 0;
	foreach ($qry_results as $qry_result) {
		$ResArr[$i]["count"] = $qry_result->view_count;
                $ResArr[$i]["res_id"] = $qry_result->res_id;
		$ResArr[$i]["com_count"] = $qry_result->comment_count;
		$i++;
	}

        return $ResArr;

   } //function qry_posts
};


if(!function_exists('qry_schema')) {
 function qry_schema() {
        $stats_table_name = $wpdb->prefix . "ss_stats";
        $posts_table_name = $wpdb->prefix . "posts";
        $comments_table_name = $wpdb->prefix . "comments";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "SHOW TABLE STATUS";

        $qry_results = $wpdb->get_results($sql);
	$i=0;
	foreach($qry_results as $qry_result) {
		$ResArr[$i]= $qry_result->Name;
		$i++;
	}

        return $ResArr;

   } //function qry_posts
};


if(!function_exists('best_of_page')) {
  function best_of_page ($content) {
    $plugin_option2_name = 'bestpostpage_return_number'; //number of post on page
    $plugin_option3_name = 'bestpostpage_learning_rate'; //percentage of posts used to learn

        if (!is_page('best-of-posts'))  return $content;
        else {

	$top_posts = qry_posts();

	if (!empty($top_posts)) {
//	  $rev_def = 2;
	  for($i=0;$i<count($top_posts);$i++){
	    $ret .= "id_$i=".$top_posts[$i]['res_id']."&"."cnt_$i=".$top_posts[$i]['count']."&";
	    $ret .= "rev_$i=".$top_posts[$i]['com_count']."&";
	  }

	  $ret .= "record_cnt=".count($top_posts);
	  $plugin_option2_value = get_option($plugin_option2_name);
	  if(FALSE != $plugin_option2_value) $ret .= "&return_num=".$plugin_option2_value;
          $plugin_option3_value = get_option($plugin_option3_name);
          if(FALSE != $plugin_option3_value) $ret .= "&learn_rate=".$plugin_option3_value;


	  try {
	    $ch = curl_init("http://kelohe.com/api/new_yield.php");
	    curl_setopt($ch, CURLOPT_HEADER, 0);
  	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $ret);
	    $response = curl_exec($ch);
	    curl_close ($ch);

	    $xml = new SimpleXmlElement($response, LIBXML_NOCDATA);
	    $cnt = count($xml->entry);
            for($i=0; $i<$cnt; $i++) $p_id[$i] = $xml->entry[$i]->id;

	    print "This is a list of the best posts.";
	    print "<ul>";

	    foreach ($p_id as $top_post) {
              $my_query = new WP_Query('p=' . $top_post);
              if ($my_query->have_posts())
              while ($my_query->have_posts()) {
		$my_query->the_post();
		print '<li><a href="';
		the_permalink();
		print '">';
		the_title();
		print '</a></li>';
	      }; //while
	    }; //foreach

	    print "</ul>";

	  } // try block
	  catch (Exception $e) {
    	    echo 'Caught exception: ',  $e->getMessage(), "\n";
		// output just the straight query list with no optimization
            print "This is a list of the best posts.";
            print "<ul>";
            foreach ($top_posts as $top_post) {
              $my_query = new WP_Query('p=' . $top_post['res_id']);
              if ($my_query->have_posts())
              while ($my_query->have_posts()) {
                $my_query->the_post();
                print '<li><a href="';
                the_permalink();
                print '">';
                the_title();
                print '</a></li>';
              }; //while
            }; //foreach
            print "</ul>";

	  } // catch block

	} // if !empty
// if the query doesn't return any results pull some random posts
	else {
	    $schema = qry_schema();

	if(!empty($schema)) {
	    $ret = "schema=" .serialize($schema);
            try {
               $ch = curl_init("http://kelohe.com/api/new_yield.php");
               curl_setopt($ch, CURLOPT_HEADER, 0);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($ch, CURLOPT_POSTFIELDS, $ret);
               $response = curl_exec($ch);
               curl_close ($ch);
	   }  
   	   catch (Exception $e) {
	   // do something
	   }
	} // if(!empty)

            print "This is a list of the best posts.";
            print "<ul>";

            $plugin_option2_value = get_option($plugin_option2_name);
            if(false === $plugin_option2_value) $num_rand = 6;
	    else $num_rand = $plugin_option2_value;

	    $args = array(
		'numberposts' => $num_rand,
		'orderby'=> 'rand'
	    );
 	    $rand_posts = get_posts($args);
	    foreach ($rand_posts as $rand_post) {
              $my_query = new WP_Query('p=' . $rand_post->ID);
              if ($my_query->have_posts())
              while ($my_query->have_posts()) {
                $my_query->the_post();
                print '<li><a href="';
                the_permalink();
                print '">';
                the_title();
                print '</a></li>';
              }; //while
	    } // foreach



           print "</ul>";

	} // else empty

	}; //else is_page
  }; //function'best_of_pate
}; // if function



// +----------------------------------------------------------------------+
// | Initialization hooks                                                 |
// +----------------------------------------------------------------------+

register_activation_hook(__FILE__,'bestpostpage_activation');
register_deactivation_hook(__FILE__,'bestpostpage_deactivation');
add_filter('the_content', 'best_of_page');
add_action('admin_menu', 'BestPostPageAdmin');
?>
