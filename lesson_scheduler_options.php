<?php

   // 管理メニューに追加するフック
    add_action('admin_menu', 'lesson_scheduler_add_menu');

    // 上のフックに対するaction関数
    function lesson_scheduler_add_menu() {
        // 設定メニュー下にサブメニューを追加:
        add_submenu_page('options-general.php','Lesson Scheduler', 'Lesson Scheduler', 8, __FILE__, 'lesson_scheduler_option_page' );
    }

    
/*  プラグイン用設定画面
-----------------------------------------------------------*/
    function lesson_scheduler_option_page() {
        
?>
<div class="wrap">
    <h2>lesson scheduler</h2>

    <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    
    <!--練習場所の設定画面-->
    <table class="form-table" >
        <th scope="row"><?php _e('set lesson place','lesson-scheduler'); ?></th>
        <?php for( $i=1; $i<=lesson_scheduler_option_max; $i++ ){ ?>
            <tr><td><input type="text" name="lesson_scheduler_place_<?php echo $i; ?>" value="<?php echo get_option('lesson_scheduler_place_'.$i); ?>" /></td></tr>
        <?php } ?>
    </span>
    </table>
    
    <!--練習時間の設定画面-->
    <table class="form-table">
        <th scope="row"><?php _e('set lesson time','lesson-scheduler'); ?></th>
        <?php for( $i=1; $i<=lesson_scheduler_option_max; $i++ ){ ?>
            <tr><td><input type="text" name="lesson_scheduler_time_<?php echo $i; ?>" value="<?php echo get_option('lesson_scheduler_time_'.$i); ?>" /></td></tr>
        <?php } ?>
    </tr>
    </table>

    <table class="form-table" >
        <th scope="row"><?php _e('set option','lesson-scheduler') ?></th>
        <?php if( get_option('lesson_scheduler_cb_1') == '1' ) : ?>
            <tr><td><input type="checkbox" name="lesson_scheduler_cb_1" value="1" checked="checked"/><?php _e( 'auto title','lesson-scheduler') ?></td></tr>
        <?php else : ?>
            <tr><td><input type="checkbox" name="lesson_scheduler_cb_1" value="1" /><?php _e( 'auto title','lesson-scheduler') ?></td></tr>
        <?php endif; ?>

        <?php if( get_option('lesson_scheduler_cb_2') == '1' ) : ?>
            <tr><td><input type="checkbox" name="lesson_scheduler_cb_2" value="1" checked="checked"/><?php _e( 'print past schedules','lesson-scheduler') ?></td></tr>
        <?php else : ?>
            <tr><td><input type="checkbox" name="lesson_scheduler_cb_2" value="1" /><?php _e( 'print past schedules','lesson-scheduler') ?></td></tr>
        <?php endif; ?>

        <?php if( get_option('lesson_scheduler_cb_3') == '1' ) : ?>
            <tr><td><input type="checkbox" name="lesson_scheduler_cb_3" value="1" checked="checked"/><?php _e( 'use the mobile phone mode','lesson-scheduler') ?></td></tr>
        <?php else : ?>
            <tr><td><input type="checkbox" name="lesson_scheduler_cb_3" value="1" /><?php _e( 'use the mobile phone mode','lesson-scheduler') ?></td></tr>
        <?php endif; ?>
    </table>       

    <table class="form-table" >
        <th scope="row"><?php _e('set display & sort columns','lesson-scheduler') ?></th>
        <?php
        //表示＆ソートカラム指定用コンボボックス生成
        for($i=1; $i<=lesson_scheduler_option_dispcolnum; $i++){
            lesson_scheduler_userfields_select($i);
        }
        ?>
  </table>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="<?php lesson_scheduler_get_alloption(lesson_scheduler_option_max);?>"/>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes','lesson-scheduler'); ?>" />
    </p>

    </form>
</div>
<?php }
//オプション用変数をすべてつなげる
function lesson_scheduler_get_alloption($max)
{
    for( $i=1; $i<=$max; $i++ ){
        $str = $str."lesson_scheduler_place_".$i.",";
    }
    
    for( $i=1; $i<=$max; $i++ ){
        $str = $str."lesson_scheduler_time_".$i.",";
    }
    
    $str = $str."lesson_scheduler_cb_1,lesson_scheduler_cb_2,lesson_scheduler_cb_3";
    
    for($i=1; $i<=lesson_scheduler_option_dispcolnum; $i++){

        $str = $str.",lesson_scheduler_disp_".$i;
        $str = $str.",lesson_scheduler_sort_".$i;
    }
        
    echo $str;
}

//ソート用ユーザーカラム選択コンボを作成
function lesson_scheduler_userfields_select($num)
{
    //デフォルトの表示用カラム
    $default_cols = array(
        "not_disp"=>"なし",
        "first_name"=>"名字",
        "last_name"=>"名前",
        "nickname"=>"ニックネーム"
    );
    
    //表示カラムをコンボボックス出力
    $chkname = "lesson_scheduler_disp_".$num;
    $optvalue = get_option($chkname);
    
    $str = '<tr><td><select name="'.$chkname.'">';
    foreach( $default_cols as $key=>$dispname){
        if( $optvalue == $key ){
            $selected = "selected";
        }
        else{
            $selected = "";
        }
        $str = $str.'<option value="'.$key.'" '.$selected.'>'.$dispname.'</option>';
    }
    
    //ユーザー登録プロフィールを表示カラムコンボボックスへ追加
    $orgFields =  lesson_scheduler_get_userfields();
    if (count($orgFields) > 0) {
        foreach ($orgFields as $key=>$dispname) {
            if( $optvalue == $key ){
                $selected = "selected";
            }
            else{
                $selected = "";
            }
            $str = $str.'<option value="'.$key.'" '.$selected.'>'.$dispname.'</option>';
        }
    }

    //cimyプラグインを使っている場合はそのカラムも表示する        
    if(function_exists(get_cimyFields)){
        //全てのカラムを取得
        $allFields = get_cimyFields();
        foreach ($allFields as $field) {
            //コンボボックスかテキストの場合に対象とする
            if( $field['TYPE'] == 'dropdown' || $field['TYPE'] == 'text' ){
                $namefield = cimy_uef_sanitize_content($field['NAME']);
                if( $optvalue == "cimy:".$namefield ){
                    $selected = "selected";
                }
                else{
                    $selected = "";
                }
                $str = $str.'<option value="cimy:'.$namefield.'" '.$selected.'>CimyField:'.$namefield.'</option>';
           }
        }
    }
   
    //ソート用コンボボックス生成
    $chkname = "lesson_scheduler_sort_".$num;
    $optvalue = get_option($chkname);
    $str = $str.'</select></td>';
    
    $str = $str.'<td><select name="lesson_scheduler_sort_'.$num.'" >';
    $selected = ($optvalue == "not_sort" ) ? "selected" : "";
    $str = $str.'<option value="not_sort" ' .$selected . '>ソートなし</option>';
    $selected = ($optvalue =="asc") ? "selected" : "" ;
    $str = $str.'<option value="asc" ' .$selected. '>昇順</option>';
    $selected = ($optvalue =="desc") ? "selected" : "";
    $str = $str.'<option value="desc" ' .$selected . '>降順</option>';
    $str = $str.'</select></td></tr>';
    
   
    echo $str;        
      
}

//user_contactmethodsで登録したフィールドを返却
function lesson_scheduler_get_userfields(){

   if(function_exists(wp_get_user_contact_methods)){
       $fields = wp_get_user_contact_methods(null);
   }
   else{
       $fields = _wp_get_user_contactmethods(null);
   }
  
   return $fields;

}

?>
