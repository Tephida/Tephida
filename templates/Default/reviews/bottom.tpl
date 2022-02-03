<script type="text/javascript">
var page_cnt_reviews = 1;
function reviewsPage(){
  if($('#load_reviews_but').text() == 'Показать больше отзывов'){
    textLoad('load_reviews_but');
    $.post('/index.php?go=reviews', {page_cnt: page_cnt_reviews}, function(d){
      page_cnt_reviews++;
      $('#preLoadedReviews').append(d);
      $('#load_reviews_but').text('Показать больше отзывов');
      if(!d) $('#reviews_but').remove();
    });
  }
}
$(document).ready(function(){
	$(window).scroll(function(){
		if($(document).height() - $(window).height() <= $(window).scrollTop()+($(document).height()/2-250)){
			reviewsPage();
		}
	});
});
</script>
<div id="preLoadedReviews"></div>
<div class="clear"></div>
<div class="rate_alluser cursor_pointer" style="margin:12px -12px -15px -12px" onClick="reviewsPage()" id="reviews_but"><div id="load_reviews_but">Показать больше отзывов</div></div>