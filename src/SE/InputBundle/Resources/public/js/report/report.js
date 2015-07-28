$(document).ready(function() {

// Load the fonts
Highcharts.createElement('link', {
   href: '//fonts.googleapis.com/css?family=Dosis:400,600',
   rel: 'stylesheet',
   type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null,
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '13px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },


   // General
   background2: '#F0F0EA'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

    var gaugeOptions = {

        chart: {
            type: 'solidgauge'
        },

        title: null,

        pane: {
            center: ['50%', '50%'],
            size: '100%',
            startAngle: 0,
            endAngle: 360,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: false
        },

        // the value axis
        yAxis: {
            stops: [
                [0.1, '#DF5353'], // red
                [0.5, '#DDDF0D'], // yellow
                [0.9, '#55BF3B'] // green
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 400,
            tickWidth: 0,
            title: {
                y: 140
            },
            labels: {
                y: 16
            }
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    // The speed gauge
    $('#container-prod').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: {
            min: 0,
            max: 10,
            title: {
                text: ''
            }
        },

        credits: {
            enabled: false
        },

        series: [{
            name: 'Productivity',
            data: [jsonHub[jsonHub.length-1]],
            dataLabels: {
                format: '<div style="text-align:center; margin-top:-70px;"><span style="font-size:90px;color:' +
                    ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                       '<span style="font-size:12px;color:silver">to line/h</span></div>'
            },
            tooltip: {
                valueSuffix: 'to line/h'
            }
        }]

    }));

   var optionsDaily = {
        title: {
            text: 'Daily-to-date',
            x: -20 //center
        },
        xAxis: {
            categories: jsonCategories,
            title: {
                    text: null
                }
        },
        yAxis: {
            title: {
                text: ''
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        credits: {
            enabled: false
        },
        tooltip: {
            valueSuffix: 'to line/h'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'HubAsia',
            data: jsonHub
        }, {
            name: 'Outbound 4',
            data: jsonOut4
        }, {
            name: 'Inbound 4',
            data: jsonIn4
        }, {
            name: 'Outbound 3',
            data: jsonOut3
        }, {
            name: 'Inbound 3',
            data: jsonIn3
        }]
    };

  $('#container-daily').highcharts(optionsDaily);   
   
	$('#filters a').click(function(){
  	$(this).siblings().removeClass('label-primary').addClass('label-default');
  	$(this).removeClass('label-default').addClass('label-primary');
    if ($(this).parent().hasClass('teams')){
      if($(this).attr('id') == "allT" || $(this).attr('id') == "Outbound3" || $(this).attr('id') == "Inbound3"){
        if(!$('#filters .shifts').hasClass('hide')){
          $('#filters .shifts').addClass('hide');
        }
      }
      else if ($('#filters .shifts').hasClass('hide')){
        $('#filters .shifts').removeClass('hide');
      }
      $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
      $('#filters .shifts').find('#allS ').removeClass('label-default').addClass('label-primary');
    }

    //update charts data
    var containerDaily = $('#container-daily').highcharts();
    var containerProd = $('#container-prod').highcharts();

    if($(this).attr('id') == "allT"){
      while (containerDaily.series.length > 0) {
            containerDaily.series[0].remove();
      }

      containerDaily.addSeries({
          name: 'HubAsia',
          data: jsonHub
      });
      containerDaily.addSeries({
          name : 'Outbound 4',
          data: jsonOut4
      });
      containerDaily.addSeries({
          name : 'Inbound 4',
          data: jsonIn4
      });
      containerDaily.addSeries({
          name : 'Outbound 3',
          data: jsonOut3
      });
      containerDaily.addSeries({
          name : 'Inbound 3',
          data: jsonIn3
      });

      containerProd.series[0].setData([jsonHub[jsonHub.length-1]]);

    }else if($(this).attr('id') == "Outbound4"){
      while (containerDaily.series.length > 0) {
            containerDaily.series[0].remove();
      }

      containerDaily.addSeries({
          name : 'Outbound 4',
          data: jsonOut4
      });
      containerDaily.addSeries({
          name : 'Shift 1',
          data: jsonOut4s1
      });
      containerDaily.addSeries({
          name : 'Shift 2',
          data: jsonOut4s2
      });
      containerDaily.addSeries({
          name : 'Shift 3',
          data: jsonOut4s3
      });

    containerProd.series[0].setData([jsonOut4[jsonOut4.length-1]]);

    }else if($(this).attr('id') == "Inbound4"){
      while (containerDaily.series.length > 0) {
            containerDaily.series[0].remove();
      }

      containerDaily.addSeries({
          name : 'Inbound 4',
          data: jsonIn4
      });
      containerDaily.addSeries({
          name : 'Shift 1',
          data: jsonIn4s1
      });
      containerDaily.addSeries({
          name : 'Shift 2',
          data: jsonIn4s2
      });
      containerDaily.addSeries({
          name : 'Shift 3',
          data: jsonIn4s3
      });

      containerProd.series[0].setData([jsonIn4[jsonIn4.length-1]]);

    }else if($(this).attr('id') == "Outbound3"){
      while (containerDaily.series.length > 0) {
            containerDaily.series[0].remove();
      }

      containerDaily.addSeries({
          name : 'Outbound 3',
          data: jsonOut3
      });
    }else if($(this).attr('id') == "Inbound3"){
      while (containerDaily.series.length > 0) {
            containerDaily.series[0].remove();
      }

      containerDaily.addSeries({
          name : 'Inbound 3',
          data: jsonIn3
      });
    }

  });
});