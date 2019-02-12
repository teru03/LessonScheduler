<?php
/**
 Template Name: lesson-scheduler
 *
 * lesson scheduler page template
 *
 */
?>
<?php get_header(); ?>
<?php 
    //show_admin_bar(false);
    $mobileflg = lesson_scheduler_chk_mobile() ? "active" : "none"; 
    $pcflg = lesson_scheduler_chk_mobile() ? "none" : "active"; 
    //echo "mobile = ".$mobileflg;
    //echo " pc = ".$pcflg;
?>

<div id="lesson_scheduler_dialog_main" title="練習">
    <p id="lesson_scheduler_dialog"></p>
</div>

<div class="wrap">
    <div class="lesson_scheduler" style="display:<?php echo $mobileflg; ?>;" >
        <form action="#" method="POST">
        <!-- タイトルの表示 -->
        <h3><?php _e('schedule','lesson-scheduler') ?></h3>
        <hr>
        <!-- 練習ループ -->
        <?php
            $lessonsinfo = lesson_scheduler_getAllLessonStatusJSON($_POST);
            foreach($lessonsinfo as $id=>$lesson){
                echo "<div class='lesson_scheduler_mobile'  data-id='".$id."' data-path='".get_bloginfo('url')."'>";
                echo $lesson["lesson_date"].'<BR>'; 
                echo $lesson["lesson_place"].'<BR>'; 
                echo $lesson["lesson_time"].'<BR>'; 
                echo $lesson["lesson_remarks"].'<BR></div>'; 
                echo '<div class="lesson_scheduler_mobile_input">';
                if( is_user_logged_in() ){ 
                    lesson_scheduler_selectReplyByValue( $id, $lesson["status"] );
                    echo '<input type="hidden" readonly="readonly" name="id'.$id.'" value="'.$id.'" />';
                    echo '<input type="text" name="comment'.$id.'" value="'.$lesson["comment"].' " />';
                }
                
                //出席人数表示
                echo '<BR>';
                lesson_scheduler_dispAttendUser($id);
                echo '<hr></div>';
            }
        ?>
        <?php if(  is_user_logged_in() ) : ?>
        <div><input type="submit" value="返信"/></div>
        <?php endif; ?>
        </form>
    </div>    
    
    <div class="lesson_scheduler" style="display:<?php echo $pcflg; ?>" >
       <form action="#" method="POST">
       <div class="tablelesson-2">
            <h2>あなたの出欠</h2>
            <table class="lesson_scheduler_table">
                <!-- タイトル行の表示 -->
                <thead>
                <tr><th>練習日</th><th>練習場所</th><th>練習時間</th><th>備考</th>
                <?php if(  is_user_logged_in() ) : ?>
                <th>出欠</th><th>コメント</th>
                <?php endif; ?>
                </tr></thead>
                <tbody>
    
                <?php
                    $cu = wp_get_current_user();
                    $lessonsinfo = lesson_scheduler_getAllLessonStatusJSON($_POST);
                    foreach($lessonsinfo as $id=>$lesson){
                            
                        echo "<tr>";
                        
                        foreach($lesson as $key=>$val){ 
                            if( $key == "status"  ){ 
                                if( is_user_logged_in() ){ 
                                    echo '<td>';
                                    lesson_scheduler_selectReplyByValue( $id, $val );
                                    echo '</td>';
                                    echo '<input type="hidden" readonly="readonly" name="id'.$id.'" value="'.$id.'" />';
                                }
    
                            }
                            else if( $key == "comment" ){
                                if( is_user_logged_in() ){
                                    echo '<td><input type="text" name="comment'.$id.'" value="'.$val.' " /></td>';
                                }
                            }
                            else{
                                echo "<td>".$val."</td>";
                            }
                        }
                        echo "</tr>";
                    }
                ?> </tbody>
            </table>
            <?php if(  is_user_logged_in() ) : ?>
            <input type="submit" value="返信"/>
            <?php endif; ?>
            <BR><BR><h2>みんなの出欠</h2>
            <table class="lesson_scheduler_table">
                <thead><tr>
                <?php
    
                    $usersinfo = lesson_scheduler_getAllDispJSON();
                    foreach($usersinfo["header"] as $label){
                        echo "<th>".$label."</th>";        
                    }
                ?>
                </tr></thead>
                <tbody>
                <?php
    
                    foreach($usersinfo["body"] as $user){
                        echo "<tr>";
                        foreach($user as $key=>$value){
                            if( !strstr($key,"lesson_date")  ){
                                echo "<td>".$value."</td>";
                                continue;
                            }
                            echo "<td>";
                            if( strcmp($value,"attend") == 0 ){
                                echo '●';    //出席
                            }elseif( strcmp($value,"absence") == 0 ){
                                echo '×';    //欠席
                            }elseif( strcmp($value,"late") == 0 ){
                                echo '△';    //遅刻
                            }elseif( strcmp($value,"early") == 0 ){
                                echo '□';    //早退
                            }elseif( strcmp($value,"undecided") == 0 ){
                                echo '？';    //未定
                            }else{
                                echo '-----';    //未選択
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
    
                ?>
                </tbody>
            </table>
        </div>
        </form>
    </div>    
</div>
<!-- 前の記事と後の記事へのリンクを表示 -->
<?php  if (  $wp_query->max_num_pages > 1 ) : ?>
    <br>
    <div id="nav-below" class="navigation">
        <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Newer lessons' ,'lesson-scheduler' ) ); ?></div>
        <div class="nav-next"><?php previous_posts_link( __( 'Older lessons <span class="meta-nav">&rarr;</span>' ,'lesson-scheduler' ) ); ?></div>
    </div><!-- #nav-below -->
    <br>
<?php endif; ?>

<?php get_footer(); ?>
