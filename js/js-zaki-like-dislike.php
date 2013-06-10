<?php

/* JS Zaki Like Dislike Comments Plugin */

function ZakiLikeDislike_JqueryCheck() {
	wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts','ZakiLikeDislike_JqueryCheck');

function ZakiLikeDislike_Js() {
    if(!is_admin()) :
        ?>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready(function() {
                jQuery('div.zaki_like_dislike').each(function() {
                    jQuery(this).click(function() {
                        var thisBtn = jQuery(this);
                        jQuery('div.zaki_like_dislike').fadeTo('fast',0.5);
                        if(!thisBtn.hasClass('in_action')) {
                            jQuery('div.zaki_like_dislike').addClass('in_action');
                            jQuery.post(
                                "<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php",
                                { 
                                    'action' : 'zaki_like_dislike_ajax',
                                    'postid' : parseInt(thisBtn.data('postid')),
                                    'ratetype' : thisBtn.data('ratetype') 
                                }, 
                                function(response) {
                                    <?php if(ZakiLikeDislike::getMode() == 'compact') : ?>
                                        var thisCount = thisBtn.parent().find('div.zaki_like_dislike_count');
                                        thisCount
                                            .removeClass('zaki_like_dislike_count_positive')
                                            .removeClass('zaki_like_dislike_count_negative')
                                            .removeClass('zaki_like_dislike_count_neutral');
                                        if(parseInt(response) == 0) thisCount.addClass('zaki_like_dislike_count_neutral');
                                        if(parseInt(response) > 0) thisCount.addClass('zaki_like_dislike_count_positive');
                                        if(parseInt(response) < 0) thisCount.addClass('zaki_like_dislike_count_negative');
                                        thisCount.empty().text('' + parseInt(response) + '');
                                    <?php else : ?>
                                        var newval = response.split('#');                              
                                        thisBtn.parent().find('div.zaki_like_dislike_like > span').empty().text('' + parseInt(newval[0]) + '');
                                        thisBtn.parent().find('div.zaki_like_dislike_dislike > span').empty().text('' + parseInt(newval[1]) + '');
                                    <?php endif; ?>
                                    jQuery('div.zaki_like_dislike').fadeTo('fast',1);
                                    jQuery('div.zaki_like_dislike').removeClass('in_action');
                                }
                            );  
                        }                 
                    });
                });
            });
            //]]>
        </script>
        <?php
    endif;
}
add_action('wp_head','ZakiLikeDislike_Js');


