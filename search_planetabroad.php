<?php
/*
 * Plugin Name: Search Planetabroad Widget
 * Description: Search Planetabroad Widget
 * Author: Alexandr Dorogikh
 * Author URI: mailto:dorogikh.alexander@gmail.com
 */
?>
<?php
class SearchPlanetabroadWidget extends WP_Widget {
    private $languages = array(
        'en' => 'English',
        'ru' => 'Russian',
        'ch' => 'Chinese'
    );

    function SearchPlanetabroadWidget () {
        $widget_ops = array('description' => __( 'Search Planetabroad Widget' ) );
        $this->WP_Widget(false, __('Search Planetabroad Widget'), $widget_ops);
        wp_enqueue_script("jquery");
    }

    public function widget($args, $instance) {
        extract($args);
        $title = $instance['title'];
        $show_title = $instance['show_title'] ? 1 : 0;
        $language = $instance['language'];

        echo $before_widget;
        if ( $title && $show_title ) echo $before_title . $title . $after_title;
        ?>
        <script type="text/javascript" src="http://www.malsup.com/jquery/block/jquery.blockUI.js?v2.26"></script>
        <script type="text/javascript">

            jQuery(document).ready(function(){

                function SearchPlanetabroadWidget_search(){

                    var url = jQuery(this).parents('form:first').attr('action');
                    var search_str = jQuery('#<?php echo $this->get_field_id('search_string');?>').val();
                    var destination = jQuery('#<?php echo $this->get_field_id('destination');?>').val();
                    if(search_str.length == 0) return;
		    var URL_search_str = search_str + "%2C+" + destination;
		    document.getElementById("resulttable_link").href = "http://www.planetabroad.com/result_table?query=" + URL_search_str;

                    url = url +
                        '?str=' + encodeURIComponent(search_str) +
                        '&dest=' + encodeURIComponent(destination) +
                        '&lang=' + <?php echo "'$language'";?> +
// allow caching:                       '&cache=' + (new Date).getTime() + // prevent caching
                        '&callback=?';
                    jQuery('#<?php echo $this->get_field_id('search_container');?>').parent().block({ message: null });

                    jQuery.getJSON(url, function(data){
                        data = data.result;
                        if((data != 'false') && (data.length > 0)){
                            var el = jQuery('#<?php echo $this->get_field_id('results')?>');
                            el.css('text-align', 'left');
                            jQuery('#<?php echo $this->get_field_id('search_container');?>').hide('fast');
                            if(data == 'false'){
				alert(data);
				alert(status);
                                el.html('Sorry, no results found - try again [<a href="#" class="<?php echo $this->get_field_id('to_back'); ?>" onclick="return false;">back</a>] with queries like "party, diving", "adventure, culture"');
                            }
                            else{
                                el.html(data)
                            }
                            jQuery('#<?php echo $this->get_field_id('result_container');?>').show('fast');
                            SearchPlanetabroadWidget_init_back_links();
                        }
                        else{
                            var el = jQuery('#<?php echo $this->get_field_id('results')?>');
                            el.css('text-align', 'center');
                            jQuery('#<?php echo $this->get_field_id('search_container');?>').hide('fast');
                            el.html('Sorry, no results found - try again [<a href="#" class="<?php echo $this->get_field_id('to_back'); ?>" onclick="return false;">back</a>] with queries like "party, diving", "adventure, culture"');
                            jQuery('#<?php echo $this->get_field_id('result_container');?>').show('fast');
                            SearchPlanetabroadWidget_init_back_links();
                        }
                    jQuery('#<?php echo $this->get_field_id('search_container');?>').parent().unblock();
                    });
                }

                jQuery('#<?php echo $this->get_field_id('submit');?>').click(SearchPlanetabroadWidget_search);
                jQuery('#<?php echo $this->get_field_id('submit');?>').parents('form:first').submit(function(){
                    jQuery('#<?php echo $this->get_field_id('submit');?>').click();
                });

                function SearchPlanetabroadWidget_init_back_links(){
                    jQuery('.<?php echo $this->get_field_id('to_back');?>').click(function(){
                        jQuery('#<?php echo $this->get_field_id('result_container');?>').hide('fast');
                        jQuery('#<?php echo $this->get_field_id('search_container');?>').show('fast');
                    });
                }

            });
        </script>
        <div style="padding:0px 5px;" id="<?php echo $this->get_field_id('search_container');?>">
            <center>What kind of destination are you looking for?</center>
	    <br />
            <hr/>
            <form method="POST" action="http://blog.planetabroad.com/wp-content/plugins/search_planetabroad/ajax.php" name="SearchPlanetabroadWidget_form" onsubmit="return false;">
                <input style="width:97%;" type="text" value="beach, party, adventure" id="<?php echo $this->get_field_id('search_string');?>" name="<?php echo $this->get_field_name('search_string');?>"/><br/><br />
                <select style="width:100%;" id="<?php echo $this->get_field_id('destination');?>" name="">
                    <option value="">Global</option>
                    <option value="asia">Asia</option>
                    <option value="australia">Australia</option>
                    <option value="europe">Europe</option>
                    <option value="africa">Africa</option>
                    <option value="north america">North America</option>
                    <option value="south america">South America</option>
                </select>
		<br />
                <center><br /><a href="#" id="<?php echo $this->get_field_id('submit');?>" type="submit" onclick="return false;">inspire me!</a></center><br />
            </form>
        </div>
        <div style="padding:0px 5px; display:none;" id="<?php echo $this->get_field_id('result_container');?>">
            <center><b>Results</b></center><hr/><br />
            <div style="width:100%; height:80px; padding:0px 26px;" id="<?php echo $this->get_field_id('results'); ?>" >
            </div>
            <hr/>
	    <center>
	    <div style="width: 60%">
            <a style="float: left"; href="www.spiegel.de" id="resulttable_link" target="_blank">more</a>
            <a style="float: right"; href="#" class="<?php echo $this->get_field_id('to_back'); ?>" onclick="return false;">back</a><br />
	    </div>
	    </center>
	    <hr />
        </div>
        <center style="font-size:10px; color:gray;">by <a href="http://www.planetabroad.com" target="_blank">www.planetabroad.com</a></center>
        <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['show_title'] = $new_instance['show_title'] ? 1 : 0;
        $instance['language'] = $new_instance['language'];
        return $instance;
    }

    function form( $instance ) {
    //Defaults
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'show_title' => '0', 'language'=>'en') );
        $title = esc_attr( $instance['title'] );
        $show_title = (bool) $instance['show_title'];
        $language = $instance['language'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>"<?php checked( $show_title ); ?> />
        <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e( 'Show title' ); ?></label><br />

        <?php if(!empty($this->languages)): ?>
        <br />
        <p><label for="<?php echo $this->get_field_id('language'); ?>"><?php _e( 'Language:' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('language'); ?>" name="<?php echo $this->get_field_name('language'); ?>">
                            <?php foreach($this->languages as $k=>$v): ?>
                <option value="<?php echo $k; ?>" <?php selected($k, $language); ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
            </select>
        </p><br />
        <?php endif; ?>

        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("SearchPlanetabroadWidget");'));