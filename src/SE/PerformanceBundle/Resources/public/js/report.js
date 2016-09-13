$(document).ready(function() {

Highcharts.createElement('link',{href:'//fonts.googleapis.com/css?family=Dosis:400,600',rel:'stylesheet',type:'text/css'},null,document.getElementsByTagName('head')[0]);Highcharts.theme={colors:["#7cb5ec","#f7a35c","#90ee7e","#7798BF","#aaeeee","#ff0066","#eeaaee","#55BF3B","#DF5353","#7798BF","#aaeeee"],chart:{backgroundColor:null,style:{fontFamily:"Dosis, sans-serif"}},title:{style:{fontSize:'16px',fontWeight:'bold',textTransform:'uppercase'}},tooltip:{borderWidth:0,backgroundColor:'rgba(219,219,216,0.8)',shadow:false},legend:{itemStyle:{fontWeight:'bold',fontSize:'13px'}},xAxis:{gridLineWidth:1,labels:{style:{fontSize:'12px'}}},yAxis:{minorTickInterval:'auto',title:{style:{textTransform:'uppercase'}},labels:{style:{fontSize:'12px'}}},plotOptions:{candlestick:{lineColor:'#404048'}},background2:'#F0F0EA'};Highcharts.setOptions(Highcharts.theme);

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
        data: [jsonTotalData['hub'][2][jsonTotalData['hub'][2].length-1]],
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
          categories: jsonTotalData['dates'],
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
          data: jsonTotalData['hub'][2]
      }, {
          name: 'Outbound 4',
          data: jsonTotalData['out4'][2]
      }, {
          name: 'Inbound 4',
          data: jsonTotalData['in4'][2]
      }, {
          name: 'Outbound 3',
          data: jsonTotalData['out3'][2]
      }, {
          name: 'Inbound 3',
          data: jsonTotalData['in3'][2]
      }, {
          name : 'Adaptation',
          data: jsonTotalData['ada'][2]
      }]
  };

  $('#container-daily').highcharts(optionsDaily);  
  replaceTotalData('hub'); 
  var yesterdayDate = new Date();
  yesterdayDate.setDate(yesterdayDate.getDate() - 1);
  $('#yesterday').append(yesterdayDate.toDateString());
   
	$('#filters a').click(function(){
  	$(this).siblings().removeClass('label-primary').addClass('label-default');
  	$(this).removeClass('label-default').addClass('label-primary');

    //update charts data
    var containerDaily = $('#container-daily').highcharts();
    var containerProd = $('#container-prod').highcharts();

    if ($(this).parent().attr('id') == 1){
      if($(this).attr('id') == "allT" || $(this).attr('id') == "Outbound3" || $(this).attr('id') == "Inbound3"){
        if(!$('#filters .shifts').hasClass('hide')){
          $('#filters .shifts').addClass('hide');
        }
      }else if ($(this).attr('id') == "Adaptation" || $(this).attr('id') == "Taskforce" || $(this).attr('id') == "Releasing"){
       $('#filters .shifts').removeClass('hide');
       if(!$('#filters .shifts #Shift3').hasClass('hide')){
          $('#filters .shifts #Shift3').addClass('hide');
        } 
      }else{
        $('#filters .shifts').removeClass('hide');
        $('#filters .shifts #Shift3').removeClass('hide');
      }
      $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
      $('#filters .shifts #allS').removeClass('label-default').addClass('label-primary');

      
      if($(this).attr('id') == "allT"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name: 'HubAsia',
            data: jsonTotalData['hub'][2]
        });
        containerDaily.addSeries({
            name : 'Outbound 4',
            data: jsonTotalData['out4'][2]
        });
        containerDaily.addSeries({
            name : 'Inbound 4',
            data: jsonTotalData['in4'][2]
        });
        containerDaily.addSeries({
            name : 'Outbound 3',
            data: jsonTotalData['out3'][2]
        });
        containerDaily.addSeries({
            name : 'Inbound 3',
            data: jsonTotalData['in3'][2]
        });
        containerDaily.addSeries({
            name : 'Adaptation',
            data: jsonTotalData['ada'][2]
        });

        containerProd.series[0].setData([jsonTotalData['hub'][2][jsonTotalData['hub'][2].length-1]]);
        replaceTotalData('hub');

      }else if($(this).attr('id') == "Outbound4"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Outbound 4',
            data: jsonTotalData['out4'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonTotalData['out4s1'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonTotalData['out4s2'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 3',
            data: jsonTotalData['out4s3'][2]
        });

        containerProd.series[0].setData([jsonTotalData['out4'][2][jsonTotalData['out4'][2].length-1]]);
        replaceTotalData('out4');

      }else if($(this).attr('id') == "Inbound4"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Inbound 4',
            data: jsonTotalData['in4'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonTotalData['in4s1'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonTotalData['in4s2'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 3',
            data: jsonTotalData['in4s3'][2]
        });

        containerProd.series[0].setData([jsonTotalData['in4'][2][jsonTotalData['in4'][2].length-1]]);
        replaceTotalData('in4');

      }else if($(this).attr('id') == "Outbound3"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Outbound 3',
            data: jsonTotalData['out3'][2]
        });
        containerProd.series[0].setData([jsonTotalData['out3'][2][jsonTotalData['out3'][2].length-1]]);
        replaceTotalData('out3');

      }else if($(this).attr('id') == "Inbound3"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Inbound 3',
            data: jsonTotalData['in3'][2]
        });
        containerProd.series[0].setData([jsonTotalData['in3'][2][jsonTotalData['in3'][2].length-1]]);
        replaceTotalData('in3');

      }else if($(this).attr('id') == "Releasing"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Releasing',
            data: jsonTotalData['rel'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonTotalData['rels1'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonTotalData['rels2'][2]
        });

        containerProd.series[0].setData([jsonTotalData['rel'][2][jsonTotalData['rel'][2].length-1]]);
        replaceTotalData('rel');

      }else if($(this).attr('id') == "Adaptation"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Adaptation',
            data: jsonTotalData['ada'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonTotalData['adas1'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonTotalData['adas2'][2]
        });

        containerProd.series[0].setData([jsonTotalData['ada'][2][jsonTotalData['ada'][2].length-1]]);
        replaceTotalData('ada');

      }else if($(this).attr('id') == "Taskforce"){
        while (containerDaily.series.length > 0) {
              containerDaily.series[0].remove();
        }

        containerDaily.addSeries({
            name : 'Taskforce 4',
            data: jsonTotalData['tas'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 1',
            data: jsonTotalData['tass1'][2]
        });
        containerDaily.addSeries({
            name : 'Shift 2',
            data: jsonTotalData['tass2'][2]
        });

        containerProd.series[0].setData([jsonTotalData['tas'][2][jsonTotalData['tas'][2].length-1]]);
        replaceTotalData('tas');

      }
    }
    else{
      //shift filters
      if($('#filters a#Outbound4').hasClass('label-primary')){
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonTotalData['out4s1'][2][jsonTotalData['out4s1'][2].length-1]]);
          replaceTotalData('out4s1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonTotalData['out4s2'][2][jsonTotalData['out4s2'][2].length-1]]);
          replaceTotalData('out4s2');
        }else if($(this).attr('id') == "Shift3"){
          containerProd.series[0].setData([jsonTotalData['out4s3'][2][jsonTotalData['out4s3'][2].length-1]]);
          replaceTotalData('out4s3');
        }else{
          containerProd.series[0].setData([jsonTotalData['out4'][2][jsonTotalData['out4'][2].length-1]]);
          replaceTotalData('out4');
        }
      }else if($('#filters a#Inbound4').hasClass('label-primary')){
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonTotalData['in4s1'][2][jsonTotalData['in4s1'][2].length-1]]);
          replaceTotalData('in4s1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonTotalData['in4s2'][2][jsonTotalData['in4s2'][2].length-1]]);
          replaceTotalData('in4s2');
        }else if($(this).attr('id') == "Shift3"){
          containerProd.series[0].setData([jsonTotalData['in4s3'][2][jsonTotalData['in4s3'][2].length-1]]);
          replaceTotalData('in4s3');
        }else{
          containerProd.series[0].setData([jsonTotalData['in4'][2][jsonTotalData['in4'][2].length-1]]);
          replaceTotalData('in4');
        }
      }else if($('#filters a#Releasing').hasClass('label-primary')){
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonTotalData['rels1'][2][jsonTotalData['rels1'][2].length-1]]);
          replaceTotalData('rels1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonTotalData['rels2'][2][jsonTotalData['rels2'][2].length-1]]);
          replaceTotalData('rels2');
        }else{
          containerProd.series[0].setData([jsonTotalData['rel'][2][jsonTotalData['rel'][2].length-1]]);
          replaceTotalData('rel');
        }
      }else if($('#filters a#Adaptation').hasClass('label-primary')){
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonTotalData['adas1'][2][jsonTotalData['adas1'][2].length-1]]);
          replaceTotalData('adas1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonTotalData['adas2'][2][jsonTotalData['adas2'][2].length-1]]);
          replaceTotalData('adas2');
        }else{
          containerProd.series[0].setData([jsonTotalData['ada'][2][jsonTotalData['ada'][2].length-1]]);
          replaceTotalData('ada');
        }
      }else if($('#filters a#Taskforce').hasClass('label-primary')){
        if($(this).attr('id') == "Shift1"){
          containerProd.series[0].setData([jsonTotalData['tass1'][2][jsonTotalData['tass1'][2].length-1]]);
          replaceTotalData('tass1');
        }else if($(this).attr('id') == "Shift2"){
          containerProd.series[0].setData([jsonTotalData['tass2'][2][jsonTotalData['tass2'][2].length-1]]);
          replaceTotalData('tass2');
        }else{
          containerProd.series[0].setData([jsonTotalData['tas'][2][jsonTotalData['tas'][2].length-1]]);
          replaceTotalData('tas');
        }
      }
    }
  }); 

});

function replaceTotalData(team){
    $('#daily-panel #m-to').html(jsonTotalData[team][1]['to']);
    $('#daily-panel #m-ma').html(jsonTotalData[team][1]['mh']);
    $('#daily-panel #m-wh').html(jsonTotalData[team][1]['wh']);
    $('#daily-panel #m-hc').html(jsonTotalData[team][1]['hc']);
    $('#daily-panel #m-ot').html(jsonTotalData[team][1]['ot']);
    $('#daily-panel #m-mto').html(jsonTotalData[team][1]['mto']);
    $('#daily-panel #m-tr').html(jsonTotalData[team][1]['tr']);
    $('#daily-panel #m-ab').html(jsonTotalData[team][1]['ab']);

    if(jsonTotalData[team][1]['wh'] > 0){
      var prod = Math.round( (jsonTotalData[team][1]['to'] / jsonTotalData[team][1]['wh'] ) * 100) / 100;
      $('#daily-panel #m-prod').html(prod+" to lines/h");
    } else {
      $('#daily-panel #m-prod').html('0 to lines/h');
    }

    $('#yesterday-panel #y-to').html(jsonTotalData[team][0]['to']);
    $('#yesterday-panel #y-ma').html(jsonTotalData[team][0]['mh']);
    $('#yesterday-panel #y-wh').html(jsonTotalData[team][0]['wh']);
    $('#yesterday-panel #y-hc').html(jsonTotalData[team][0]['hc']);
    $('#yesterday-panel #y-ot').html(jsonTotalData[team][0]['ot']);
    $('#yesterday-panel #y-mto').html(jsonTotalData[team][0]['mto']);
    $('#yesterday-panel #y-tr').html(jsonTotalData[team][0]['tr']);
    $('#yesterday-panel #y-ab').html(jsonTotalData[team][0]['ab']);
  }