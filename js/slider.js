var fs = fs || {};

fs.signup = fs.signup || new function() {
    var context = this;

    context.selectors = {};
    context.rega = null;

    /* carousel */
    context.mySimpleCarousel = function(){
        var carousel_interval_id = {};
        var carousel_size = {};
        var active_id = {};
        var isAnimated = {};
        var loop_time = {};
        var isStarted = {};
        var me;

        var init = function(scope){
            me = scope;
            me.active_id = $('#carousel-pagination .pager').index($('#carousel-pagination .pager.active'));
            me.isAnimated = false;
            me.carousel_size = $('#carousel-pagination .pager').length;
            me.loop_time = 5000;
            me.isStarted = false;
            me.carousel_interval_id = 0;

            $('#carousel-items .item').each(function(){
                $(this).css('left', 600);
            });
            $('#carousel-items .item').eq($('#carousel-pagination .pager').index($('#carousel-pagination .pager.active'))).addClass('active').css('left',0);

            $('#carousel-pagination .pager').each(function(index){
                $(this).bind('click', function(){
                    if(!$(this).hasClass('active')){
                        animate_to($(this).parent().find('.pager').index($(this)));
                        stop_animation();
                        start_animation();
                    }
                });
            });

            start_animation();
        };
        var animate_to = function(animate_to_marker){
            if(!me.isAnimated){
                me.isAnimated = true;
                me.active_id = (animate_to_marker !== undefined)? animate_to_marker : (me.active_id+1)%me.carousel_size;
                var direction = me.active_id - $('#carousel-pagination .pager').index($('#carousel-pagination .pager.active'));
                $('#carousel-pagination .pager').removeClass('active');
                $('#carousel-pagination .pager').eq(me.active_id).addClass('active');

                if(direction > 0){
                    $('#carousel-items .item').eq(me.active_id);
                    $('#carousel-items .item.active').animate({'left':-600}, 500, function(){
                        $(this).css('left', 600);
                        $(this).removeClass('active');
                        $('#carousel-items .item').eq(me.active_id).addClass('active');
                    });
                    $('#carousel-items .item').eq(me.active_id).animate({'left': 0}, 500, function(){
                        me.isAnimated = false;
                    });
                } else if(direction < 0) {
                    $('#carousel-items .item').eq(me.active_id);
                    $('#carousel-items .item.active').animate({'left':600}, 500, function(){
                        $(this).css('left', 600);
                        $(this).removeClass('active');
                        $('#carousel-items .item').eq(me.active_id).addClass('active');
                    });
                    $('#carousel-items .item').eq(me.active_id).css('left',-600).animate({'left': 0}, 500, function(){
                        me.isAnimated = false;
                    });
                }
            }
        };
        var start_animation = function(){

            if(!me.isStarted){
                me.isStarted = true;
                me.carousel_interval_id = setInterval(function(){
                    animate_to();
                }, me.loop_time);
            }
        };
        var stop_animation = function(){
            clearInterval(me.carousel_interval_id);
            me.isStarted = false;
        };
        $('#carousel-items').unbind('mouseleave').bind('mouseleave', function(){ start_animation(); });
        $('#carousel-items').unbind('mouseenter').bind('mouseenter', function(){ stop_animation(); });

        init(this);
    };

}