<?php
final class ZakiLikeDislike {

    public static function getTableName() {
        global $wpdb;
        return trim($wpdb->prefix . 'zaki_like_dislike_comments');
    }

    public static function getMode() {
        $settings = get_option('zaki_like_dislike_options');
        if(isset($settings['display_type'])) return $settings['display_type'];
        return false;
    }
    
    public function checkRow($post_id = NULL) {
        global $wpdb;
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
        
        $table_name = self::getTableName();
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE comment_id = ".$post_id);
        
        if(!$row) :
            
            // Creo un record vuoto
            return $wpdb->insert($table_name,array( 
                'comment_id' => $post_id
            ));
        
        endif; 
        
        return true;
    }
    
    public static function getLikeCount($post_id = NULL) {
        global $wpdb;
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
	            
        // Query like
        $table_name = self::getTableName();
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE comment_id = ".$post_id);
        
        if($row) :
            $result = $row->rate_like_value; 
        else :
            $result = 0;
        endif;
          
        return $result;
    }
    
    public static function getDislikeCount($post_id = NULL) {
        global $wpdb;
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
	            
        // Query dislike
        $table_name = self::getTableName();
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE comment_id = ".$post_id);        
        
        if($row) :
            $result = $row->rate_dislike_value; 
        else :
            $result = 0;
        endif;

        return $result;
    }
    
    public static function getLikeIpList($post_id = NULL) {
        global $wpdb;
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
	            
        // Query like
        $table_name = self::getTableName();
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE comment_id = ".$post_id);
        
        if($row) : 
            $list = (array) json_decode($row->rate_like_ip,true);
        else:
            $list = array();
        endif;
           
        return $list;
    }
    
    public static function getDislikeIpList($post_id = NULL) {
        global $wpdb;
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
	            
        // Query dislike
        $table_name = self::getTableName();
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE comment_id = ".$post_id);        
        
        if($row) : 
            $list = (array) json_decode($row->rate_dislike_ip,true);
        else:
            $list = array();
        endif;
           
        return $list;
    }
    
    public static function getLikeDislikeCountDiff() {
        return intval(self::getLikeCount() - self::getDislikeCount());
    }
    
    public static function getLikeCountHtml() {
        ?><span><?=self::getLikeCount($post_id)?></span><?php
    }
    
    public static function getDislikeCountHtml() {
        ?><span><?=self::getDislikeCount($post_id)?></span><?php
    }
    
    public static function getLikeDislikeCountDiffHtml() {
        $diff = self::getLikeDislikeCountDiff();
        if($diff > 0) {
            ?>
            <div class="zaki_like_dislike_count zaki_like_dislike_count_positive">
                <?=$diff?>
            </div>
            <?php
        } else if($diff < 0) {
            ?>
            <div class="zaki_like_dislike_count zaki_like_dislike_count_negative">
                <?=$diff?>
            </div>
            <?php
        } else {
            ?>
            <div class="zaki_like_dislike_count zaki_like_dislike_count_neutral">
                <?=$diff?>
            </div>
            <?php
        }
    }
    
    public static function getLikeBtn($post_id = NULL , $classes = array()) {
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
        $new_classes = implode(' ',array_merge(array('zaki_like_dislike','zaki_like_dislike_like'),$classes));
        ?>
        <div class="<?=$new_classes?>" data-postid="<?=$post_id?>" data-ratetype="like">
            <img src="<?=plugins_url( 'images/up.png' , ZAKI_LIKE_DISLIKE_FILE )?>" />
            <?php if(self::getMode() == 'splitted') self::getLikeCountHtml(); ?>
        </div>
        <?php
    }
    
    public static function getDislikeBtn($post_id = NULL , $classes = array()) {
        global $comment;
	    if(empty($post_id))	$post_id = get_comment_ID();
        $new_classes = implode(' ',array_merge(array('zaki_like_dislike','zaki_like_dislike_dislike'),$classes));
        ?>
        <div class="<?=$new_classes?>" data-postid="<?=$post_id?>" data-ratetype="dislike" >
            <img src="<?=plugins_url( 'images/down.png' , ZAKI_LIKE_DISLIKE_FILE )?>" />
            <?php if(self::getMode() == 'splitted') self::getDislikeCountHtml(); ?>
        </div>
        <?php
    }
    
    public static function getLikeDislikeHtml() {
        ?>
        <div class="zaki_like_dislike_box">
            <?php        
            if(self::getMode() == 'compact') :
                self::getLikeBtn();
                self::getDislikeBtn();
                self::getLikeDislikeCountDiffHtml();
            else :
                self::getLikeBtn();
                self::getDislikeBtn();
            endif;
            ?>
            <div style="clear:left;"></div>
        </div>
        <?php
    }
    
}

