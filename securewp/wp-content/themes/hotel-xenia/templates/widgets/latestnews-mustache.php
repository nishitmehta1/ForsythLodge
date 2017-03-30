 <!-- ===================== LATEST NEWS: MUSTACHE ========================-->

 {{{ before_widget }}}

 {{# title }}
 <h4>{{ title }} </h4>
 {{/ title }}

 <div class="pl_latest_news_widget">
 
  {{# posts}}

    <div class="post_listed">

        <a href="{{ permalink }}" class="post_listed_photo" style="background-image:url(' {{ thumbnail_url }} ')"></a> 

        <h5 class="post_listed_title textify_links">
            <a href="{{ permalink }}">{{ title }}</a>
        </h5>
        {{# display_date }}<small> {{ date }}</small> {{/ display_date }}

    </div>

  {{/ posts}}

 </div>  

 {{{ after_widget }}}

 <!-- END================== LATEST NEWS: MUSTACHE ========================-->