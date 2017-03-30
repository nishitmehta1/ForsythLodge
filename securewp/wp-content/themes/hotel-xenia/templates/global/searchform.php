<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Search form(s) template part ( all cases, except 404 )
*/
Plethora_Theme::dev_comment('Start >>> Search Form Section', 'layout');
?>
  <form method="get" name="s" id="s" action="<?php echo esc_url(home_url( '/' )); ?>">
       <div class="row">
         <div class="col-lg-12">
             <div class="input-group">
                 <input name="s" id="search" class="form-control" type="text" placeholder="<?php echo esc_attr( esc_html__('Search', 'hotel-xenia') );  ?>">
                 <span class="input-group-btn">
                   <button class="btn btn-inv btn-default" type="submit"><?php echo esc_html__('Go!', 'hotel-xenia'); ?></button>
                 </span>
             </div>
         </div>
       </div>
  </form>
<?php 
Plethora_Theme::dev_comment('End <<< Search Form Section', 'layout');