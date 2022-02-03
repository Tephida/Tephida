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
    data: [{r_hits}],
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
      tickSize: {tickSize_hits}
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
  var graphData_3 = [{
    data: [{r_new_users}],
    color: '#78b27c'
  }];
  $.plot($('#graph-lines-3'), graphData_3, {
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
      color: '#78b27c',
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
      tickSize: {tickSize_new_users}
    }
  });
  $('#graph-bars-3').hide();
  $('#lines-3').on('click', function (e) {
    $('#bars-3').removeClass('active');
    $('#graph-bars-3').fadeOut();
    $(this).addClass('active');
    $('#graph-lines-3').fadeIn();
    e.preventDefault();
  });
  $('#bars-3').on('click', function (e) {
    $('#lines-3').removeClass('active');
    $('#graph-lines-3').fadeOut();
    $(this).addClass('active');
    $('#graph-bars-3').fadeIn().removeClass('hidden');
    e.preventDefault();
  });
  var previousPoint3 = null;
  $('#graph-lines-3, #graph-bars-3').bind('plothover', function (event, pos, item){
    if(item){
      item.dataIndex++;
        if(previousPoint3 != item.dataIndex){
          previousPoint3 = item.dataIndex;
          $('#tooltip_1').remove();
          var x = item.datapoint[0], y = item.datapoint[1];
          if(!y)
            showTooltip(item.pageX, item.pageY, '<b>Не было новых участников</b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');
          else
            showTooltip(item.pageX, item.pageY, '<b>' + y + ' <span id="2gram_num'+item.dataIndex+'">участник</span></b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');

          langNumric( '2gram_num'+item.dataIndex, y, 'новый участник', 'новых участника', 'новых участников', 'новых участников' );  
        }
    } else {
        $('#tooltip_1').remove();
        previousPoint3 = null;
    }
  });
  var graphData_4 = [{
    data: [{r_exit_users}],
    color: '#82a2cd'
  }];
  $.plot($('#graph-lines-4'), graphData_4, {
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
      color: '#82a2cd',
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
      tickSize: {tickSize_exit_users}
    }
  });
  $('#graph-bars-4').hide();
  $('#lines-4').on('click', function (e) {
    $('#bars-4').removeClass('active');
    $('#graph-bars-4').fadeOut();
    $(this).addClass('active');
    $('#graph-lines-4').fadeIn();
    e.preventDefault();
  });
  $('#bars-4').on('click', function (e) {
    $('#lines-4').removeClass('active');
    $('#graph-lines-4').fadeOut();
    $(this).addClass('active');
    $('#graph-bars-4').fadeIn().removeClass('hidden');
    e.preventDefault();
  });
  var previousPoint4 = null;
  $('#graph-lines-4, #graph-bars-4').bind('plothover', function (event, pos, item){
    if(item){
      item.dataIndex++;
        if(previousPoint4 != item.dataIndex){
          previousPoint4 = item.dataIndex;
          $('#tooltip_1').remove();
          var x = item.datapoint[0], y = item.datapoint[1];
          if(!y)
            showTooltip(item.pageX, item.pageY, '<b>Не было вышедших участников</b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');
          else
            showTooltip(item.pageX, item.pageY, '<b>' + y + ' <span id="3gram_num'+item.dataIndex+'">вышедших участников</span></b><br /><small>' + item.dataIndex + ' ' + $('#tek_month').val() +'</small>');

          langNumric( '3gram_num'+item.dataIndex, y, 'вышедший участник', 'вышедших участника', 'вышедших участников', 'вышедших участников' );
        }
    } else {
        $('#tooltip_1').remove();
        previousPoint4 = null;
    }
  });
});
</script>
<div class="search_form_tab" style="margin-top:-9px">
 <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
  <a href="/public{gid}"><div><b>К сообществу</b></div></a>
  <div class="buttonsprofileSec"><a href="/stats?gid={gid}"><div><b>Статистика страницы</b></div></a></div>
  <div class="fl_r">
   <select class="inpst fl_l" style="margin-right:5px;padding:5px" id="month">{months}</select>
   <select class="inpst fl_l" style="margin-right:5px;padding:5px" id="year">{year}</select>
   <div class="button_div fl_l"><button onClick="location.href = '/stats?gid={gid}&m='+$('#month').val()+'&y='+$('#year').val()">Просмотреть</button></div>
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
<div class="margin_top_10"></div><div class="allbar_title" style="font-size:13px;color:#78b27c">Новые участники</div>
<div id="graph-wrapper">
 <div class="graph-container">
  <div id="graph-lines-3"></div>
  <div id="graph-bars-3"></div>
 </div>
</div>
<div class="margin_top_10"></div><div class="allbar_title" style="font-size:13px;color:#82a2cd">Вышедшие участники</div>
<div id="graph-wrapper">
 <div class="graph-container">
  <div id="graph-lines-4"></div>
  <div id="graph-bars-4"></div>
 </div>
</div>