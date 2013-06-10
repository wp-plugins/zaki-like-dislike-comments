<?php

/* AJAX Request Zaki Like Dislike Comments Plugin */

add_action('wp_ajax_zaki_like_dislike_ajax', 'ZakiLikeDislike_Ajax');
add_action('wp_ajax_nopriv_zaki_like_dislike_ajax', 'ZakiLikeDislike_Ajax');
function ZakiLikeDislike_Ajax() {
	global $wpdb;
	$table_name = ZakiLikeDislike::getTableName();
	
	// Dati controllo
	$postid = intval( $_POST['postid'] );
	$ratetype = $_POST['ratetype'];
	$userip = $_SERVER['REMOTE_ADDR'];
	
	// Controllo presenza record nel db
	ZakiLikeDislike::checkRow($postid);
	
	// Dati db
	$likes = ZakiLikeDislike::getLikeCount($postid);
	$dislikes = ZakiLikeDislike::getDislikeCount($postid);
	$likes_ov = ZakiLikeDislike::getLikeIpList($postid);
	$dislikes_ov = ZakiLikeDislike::getDislikeIpList($postid);

    switch($ratetype) :
        case 'like' :
            // Controllo ip
            if(!in_array($userip,$likes_ov)) :
                $likes++;
                $likes_ov[] = $userip;
                if(in_array($userip,$dislikes_ov)) :
                    $valuekey = array_search($userip,$dislikes_ov);
                    unset($dislikes_ov[$valuekey]);
                    $dislikes--;
                endif;
                $wpdb->update( 
                    $table_name, 
                    array( 
                        'rate_like_value' => $likes,
                        'rate_dislike_value' => $dislikes,
                        'rate_like_ip' => json_encode($likes_ov),
                        'rate_dislike_ip' => json_encode($dislikes_ov)
                    ), 
                    array( 'comment_id' => $postid )
                );
            endif;
            if(ZakiLikeDislike::getMode() == 'compact') { echo $likes - $dislikes; } else { echo $likes.'#'.$dislikes; }
            break;
        case 'dislike' :
            // Controllo ip
            if(!in_array($userip,$dislikes_ov)) :
                $dislikes++;
                $dislikes_ov[] = $userip;
                if(in_array($userip,$likes_ov)) :
                    $valuekey = array_search($userip,$likes_ov);
                    unset($likes_ov[$valuekey]);
                    $likes--;
                endif;
                $wpdb->update( 
                    $table_name, 
                    array( 
                        'rate_like_value' => $likes,
                        'rate_dislike_value' => $dislikes,
                        'rate_like_ip' => json_encode($likes_ov),
                        'rate_dislike_ip' => json_encode($dislikes_ov)
                    ), 
                    array( 'comment_id' => $postid )
                );
            endif;
            if(ZakiLikeDislike::getMode() == 'compact') { echo $likes - $dislikes; } else { echo $likes.'#'.$dislikes; }
            break;
    endswitch;

	die();
}
