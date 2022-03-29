    var gm = 1.61803399,
        vktext = '#45688e',
        vkgray = '#777',
        vkfonts = 'tahoma, arial, verdana, sans-serif, \'Lucida Sans\'',
        data = [],
        angle = [],
        colors = [],
        options = {
          sepR: 5,
          showLegend: true,
          columns: ['']
        };

    function recalc() {
      var sum = 0,
          i, j;

      for (i = 0; i < data.length; i++) {
        if (!('id' in data[i])) {
          data[i].id = i;
        }
        if ('p' in data[i]) {
          data[i].p = Number(data[i].p);
        }
        if ('q' in data[i]) {
          data[i].q = Number(data[i].q);
        }
      }
      for (i = 0; i < data.length; i++) {
        for (var j = 0; j < data.length - 1; j++) {
        if (('p' in data[j] && 'p' in data[j + 1] && data[j].p > data[j + 1].p) || data[j].q > data[j + 1].q) {
            var tmp = data[j];
            data[j] = data[j + 1];
            data[j + 1] = tmp;
          }
        }
      }

      for (i in data) {
        sum += data[i].q;
      }
      angle = [];
      for (i in data) {
        if (!('p' in data[i])) {
          data[i].p = data[i].q / sum * 100;
        }
        angle[i] = 2 * Math.PI * data[i].p / 100.0;
      }

      var MINI = Math.PI / 60;
      for (i = 0; i < data.length; i++) {
        if (i + 1 != data.length && angle[i] < MINI) {
          var diff = (MINI - angle[i]) / (data.length - i - 1);
          for (j = i + 1; j < data.length; j++) {
            angle[j] -= diff;
          }
          angle[i] = MINI;
        }
      }
    }

    function render() {
      while (root.firstChild) {
        root.removeChild(root.firstChild);
      }

      if (options.showLegend) {
        root.setAttribute('viewBox', '0 0 200 100');
      } else {
        root.setAttribute('viewBox', '0 0 100 100');
      }

      var i;
      var size = 0;
      for (i in angle) size++;

      var legendH = 100 - 50 / gm,
          legendX = 120,
          legendY = size > 1 ? (100 - legendH) / 2 : 50,
          legendGap = size > 1 ? legendH / (size - 1) : 20,
          legendItemH = legendGap / gm;

      var r = 50 - options.sepR,
          curAng = 0,
          curLegendY = legendY;

      for (i in angle) {
        var nextAng = curAng + angle[i],
            midAng = 0.5 * (curAng + nextAng),
            shiftX = options.sepR * Math.cos(midAng),
            shiftY = options.sepR * Math.sin(midAng),
            cx = 50 + shiftX,
            cy = 50 + shiftY;

        var color = (data[i].color ? [data[i].color] : getIthColor(data[i].id));
        colors[i] = color;

        var pieSlice;
        if (angle[i] >= 2 * Math.PI - 1e-3) {
          pieSlice = ce('circle', {
              'id': 'slice_' + _svgUid + '_' + i,
              'cx': 50,
              'cy': 50,
              'r': r
            });
        } else {
          pieSlice = ce('path', {
              'id': 'slice_' + _svgUid + '_' + i,
              'd': [
                  'M ', cx, ' ', cy, ' ',
                  'l ', r * Math.cos(curAng), ' ', r * Math.sin(curAng), ' ',
                  'A ', r, ',', r, ' 0 ', (angle[i] >= Math.PI ? '1': '0'), ',1 ', cx + r * Math.cos(nextAng), ',', cy + r * Math.sin(nextAng), ' ',
                  'L ', cx, ' ', cy, ' z'
                ].join('')
            });
        }

        extendAttr(pieSlice, {
            'fill': color[0],
            'onmouseover': 'onHover(evt, 1)',
            'onmouseout': 'onHover(evt, 0)'
//            'onclick': 'onPieClick(evt)'
          });

        root.appendChild(pieSlice);

        var legendSq = ce('rect', {
            'id': 'lsquare_' + _svgUid + '_' + i,
            'x': legendX,
            'y': curLegendY - legendItemH / 2,
            'width': legendItemH,
            'height': legendItemH,
            'fill': color[0],
            'onmouseover': 'onHover(evt, 1)',
            'onmouseout': 'onHover(evt, 0)'

          });
        var legendText = ce('text', {
            'id': 'ltext_' + _svgUid + '_' + i,
            'x': legendX + 2 * legendItemH,
            'y': curLegendY + legendItemH * 0.4,
            'font-size': '2pt',
            'font-family': vkfonts,
            'font-weight': 600,
//            'textLength': 2 - legendX - legendItemH - 0.2,
            'fill': color[0],
            'onmouseover': 'onHover(evt, 1)',
            'onmouseout': 'onHover(evt, 0)'

          });
        legendText.appendChild(document.createTextNode(data[i].l));

        root.appendChild(legendSq);
        root.appendChild(legendText);

        curAng = nextAng;
        curLegendY += legendGap;
      }

    }

    function onHover(evt, isIn) {
      var id = evt.currentTarget.id,
          eltype = id.substring(0, id.indexOf('_'));
      id = id.substring(id.lastIndexOf('_') + 1);

      var el;
      var maxId = 0;
      for (var i in {'slice':1, 'lsquare':1}) {
        for (var j in angle) {
          el = ge(i + '_' + _svgUid + '_' + j);
          fadeTo(el, 200, isIn ? 0.5 : 1);
          maxId = Math.max(maxId, data[j].id);
        }
        el = ge(i + '_' + _svgUid + '_' + id);
        fadeTo(el, 200, 1);
      }

      parentInvoke('cur.highlightChartRowList', [_svgUid, data[id].id, maxId + 1, isIn]);

      if (eltype == 'slice') {
        parentInvoke('cur.togglePiechartTooltip', [data[id], isIn]);
      }
    }

    function onMousemove(evt) {
      var passEvt = {};
      each (['clientX', 'clientY', 'pageX', 'pageY'], function(i, v) {
        passEvt[v] = evt[v];
      });
      parentInvoke('cur.onPiechartMousemove', [passEvt, _svgUid]);
    }

    function processMessage(inData) {
      switch (inData.act) {
        case 'loadData':
        ret.loadData.apply({}, inData.data);
          break;
        case 'setOptions':
        ret.setOptions.apply({}, inData.data);
          break;
        case 'highlightSlice':
        ret.highlightSlice.apply({}, inData.data);
          break;
      }
    }

    ret = {
      loadData: function(newData) {
        data = newData;
        recalc();
        render();
      },
      setOptions: function(newOptions) {
        extend(options, newOptions);
      },
      highlightSlice: function(id, isIn) {
        for (var i in data) {
          if (data[i].id == id) {
            onHover({currentTarget: {id: 'ltext_' + _svgUid + '_' + i}}, isIn);
          }
        }
      }
    }
