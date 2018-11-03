<?php
/**
 Template Name: lesson-scheduler
 *
 * lesson scheduler page template
 *
 */

?>
<?php get_header(); ?>

<div class="lesson_scheduler">
  <form method="POST" action= "#">
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
                $lessonsinfo = lesson_scheduler_getAllLessonStatusJSON();
                foreach($lessonsinfo as $id=>$lesson){
                        
                    echo "<tr>";
                    
                    //送信ボタンが押されたかつ、その時のIDと同一ならば登録
                    if ($_POST['syuketu'.$id] != '' && strcmp( $_POST['id'.$id], $id) == 0 ) {
                        delete_post_meta( $id,  $cu->user_login ); 
                        update_post_meta( $id,  $cu->user_login, $_POST['syuketu'.$id]);
                        delete_post_meta( $id,  $cu->user_login."1" ); 
                        update_post_meta( $id,  $cu->user_login."1", $_POST['comment'.$id]);
                    }

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
            ?>
            </tbody>
        </table>
        <?php if(  is_user_logged_in() ) : ?>
        <input type="submit" value="返信"/>
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
       <?php endif; ?>
   </div>    
   </form>
</div>    

<?php get_footer(); ?>
