<script type="text/javascript" src="{theme}/js/jquery.1.7.2.js"></script>
<script type="text/javascript" src="{theme}/js/jquery.graph.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  var graphData = [{
    data: [{r_unik}],
    color: '#7796b7'
  }];
  $.plot($('#graph-lines'), graphData, {
    series: {
      points: {
        show: true,
        radius: 4
      },
      lines: {
        show: true
      },
      shadowSize: 0
    },
    grid: {
      color: '#7796b7',
      borderColor: 'transparent',
      borderWidth: 20,
      hoverable: true
    },
    xaxis: {
      tickColor: 'transparent',
      tickDecimals: 2,
      tickSize: 3
    },
    yaxis: {
      tickSize: {tickSize}
    }
  });

  $('#graph-bars').hide();

  $('#lines').on('click', function (e) {
    $('#bars').removeClass('active');
    $('#graph-bars').fadeOut();
    $(this).addClass('active');
    $('#graph-lines').fadeIn();
    e.preventDefault();
  });

  $('#bars').on('click', function (e) {
    $('#lines').removeClass('active');
    $('#graph-lines').fadeOut();
    $(this).addClass('active');
    $('#graph-bars').fadeIn().removeClass('hidden');
    e.preventDefault();
  });

  function showTooltip(x, y, contents) {
    $('<div id="tooltip_1">' + contents + '</div>').css({
      top: y - 16,
      left: x + 20
    }).appendTo('body').fadeIn();
  }
  var previousPoint = null;
  $('#graph-lines, #graph-bars').bind('plothover', function (event, pos, item){
    if(item){
      item.dataIndex++;
        if(previousPoint != item.dataIndex){
            previousPoint = item.dataIndex;
            $('#tooltip_1').remove();
            var x = item.datapoint[0], y = item.datapoint[1];
            if(!y)
              showTooltip(item.pageX, item.pageY, '<b>Не было посетителей</b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');
            else
              showTooltip(item.pageX, item.pageY, '<b>' + y + ' <span id="gram_num'+item.dataIndex+'">уникальных посетителей</span></b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');

			langNumric( 'gram_num'+item.dataIndex, y, 'уникальный посетитель', 'уникальных посетителя', 'уникальных посетителей', 'уникальных посетителей' );  
        }
    } else {
        $('#tooltip_1').remove();
        previousPoint = null;
    }
  });
  var graphData_2 = [{
    data: [{r_moneys}],
    color: '#bf68a6'
  }];
  $.plot($('#graph-lines-2'), graphData_2, {
    series: {
      points: {
        show: true,
        radius: 4
      },
      lines: {
        show: true
      },
      shadowSize: 0
    },
    grid: {
      color: '#bf68a6',
      borderColor: 'transparent',
      borderWidth: 20,
      hoverable: true
    },
    xaxis: {
      tickColor: 'transparent',
      tickDecimals: 2,
      tickSize: 3
    },
    yaxis: {
      tickSize: {tickSize_moneys}
    }
  });
  $('#graph-bars-2').hide();
  $('#lines-2').on('click', function (e) {
    $('#bars-2').removeClass('active');
    $('#graph-bars-2').fadeOut();
    $(this).addClass('active');
    $('#graph-lines-2').fadeIn();
    e.preventDefault();
  });
  $('#bars-2').on('click', function (e) {
    $('#lines-2').removeClass('active');
    $('#graph-lines-2').fadeOut();
    $(this).addClass('active');
    $('#graph-bars-2').fadeIn().removeClass('hidden');
    e.preventDefault();
  });
  var previousPoint2 = null;
  $('#graph-lines-2, #graph-bars-2').bind('plothover', function (event, pos, item){
    if(item){
      item.dataIndex++;
        if(previousPoint2 != item.dataIndex){
          previousPoint2 = item.dataIndex;
          $('#tooltip_1').remove();
          var x = item.datapoint[0], y = item.datapoint[1];
          if(!y)
            showTooltip(item.pageX, item.pageY, '<b>Не было просмотров</b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');
          else
            showTooltip(item.pageX, item.pageY, '<b>' + y + ' <span id="1gram_num'+item.dataIndex+'">просмотров</span></b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');

          langNumric( '1gram_num'+item.dataIndex, y, 'просмотр', 'просмотра', 'просмотров', 'просмотров' ); 
        }
    } else {
        $('#tooltip_1').remove();
        previousPoint2 = null;
    }
  });
});
</script>
<div class="search_form_tab" style="margin-top:-9px">
 <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
  <a href="/u{uid}"><div><b>К моей странице</b></div></a>
  <div class="buttonsprofileSec"><a href="/my_stats"><div><b>Статистика страницы</b></div></a></div>
  <div class="fl_r">
   <select class="inpst fl_l" style="margin-right:5px;padding:3px" id="month">{months}</select>
   <select class="inpst fl_l" style="margin-right:5px;padding:3px" id="year">{year}</select>
   <div class="button_div fl_l"><button onClick="location.href = '/?go=my_stats&m='+$('#month').val()+'&y='+$('#year').val()">Просмотреть</button></div>
  </div>
 </div>
</div>
<div class="clear"></div>
<input type="hidden" id="tek_month" value="{t-date}" />
<div class="margin_top_10"></div><div class="allbar_title" style="font-size:13px">Уникальные посетители</div>
<div id="graph-wrapper">
 <div class="graph-container">
  <div id="graph-lines"></div>
  <div id="graph-bars"></div>
 </div>
</div>
<div class="margin_top_10"></div><div class="allbar_title" style="font-size:13px;color:#bf68a6">Просмотры</div>
<div id="graph-wrapper">
 <div class="graph-container">
  <div id="graph-lines-2"></div>
  <div id="graph-bars-2"></div>
 </div>
</div>