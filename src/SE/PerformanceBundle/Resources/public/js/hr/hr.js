$(document).ready(function() {

Highcharts.createElement('link',{href:'//fonts.googleapis.com/css?family=Dosis:400,600',rel:'stylesheet',type:'text/css'},null,document.getElementsByTagName('head')[0]);Highcharts.theme={colors:["#7cb5ec","#f7a35c","#90ee7e","#7798BF","#aaeeee","#ff0066","#eeaaee","#55BF3B","#DF5353","#7798BF","#aaeeee"],chart:{backgroundColor:null,style:{fontFamily:"Dosis, sans-serif"}},title:{style:{fontSize:'16px',fontWeight:'bold',textTransform:'uppercase'}},tooltip:{borderWidth:0,backgroundColor:'rgba(219,219,216,0.8)',shadow:false},legend:{itemStyle:{fontWeight:'bold',fontSize:'13px'}},xAxis:{gridLineWidth:1,labels:{style:{fontSize:'12px'}}},yAxis:{minorTickInterval:'auto',title:{style:{textTransform:'uppercase'}},labels:{style:{fontSize:'12px'}}},plotOptions:{candlestick:{lineColor:'#404048'}},background2:'#F0F0EA'};Highcharts.setOptions(Highcharts.theme);

var attJson;
var monthVal;
var yearVal;
var attendanceTable;
initData();

monthVal = parseInt($('.monthpick').val()) + 1;
yearVal = $('.yearpick').val();
$('form select').addClass('form-control');

$.get(
    ajaxAttendance,               
    {
        month: monthVal,
        year: yearVal
    }, 
    function(r){
        attJson = r.template;
        createTable(attJson, r.headers, r.daysNb);
        createCharts(r.jsonData, 0, 0, r.headers, r.daysNb);
        $('#filters a').click(function(){
          $this = $(this);
          filterData($this, attJson, r.jsonData);
        });
    },
    "json"); 

$('.close').click(function(){$('.table-panel').toggleClass('col-md-12 col-md-9').siblings('.entry-panel').addClass('hide');});

$(document).on('change', '.month select', function(e){
  monthVal = parseInt($('.monthpick').val()) + 1;
  yearVal = $('.yearpick').val();
  $.get(
    ajaxAttendance,               
    {
      month: monthVal,
      year: yearVal
    }, 
    function(r){
      $('#filters .teams').html(r.filters);//update filters
      attJson = r.template; 
      $('#filters a').click(function(){
        $this = $(this);
        filterData($this, attJson, r.jsonData);
      });
        var attendanceTable = $('#attendance').DataTable();
        attendanceTable.clear();
        attendanceTable.destroy();
        $('#attendance thead .day-h').remove();
        createTable(attJson, r.headers, r.daysNb);
        createCharts(r.jsonData, 0, 0, r.headers, r.daysNb);
    },
    "json");
});

});

function initData(){
    var d = new Date();
    $('#monthpicker').monthpicker({'minYear' : 2015, 'maxYear' : 2016});
    $('.monthpick').val(d.getMonth());
    $('.yearpick').val(d.getFullYear());
}

function createCharts(json, t, s, h, n){

  $('#summary-panel #mar').html(json[t][s]['report']['attrate']);
  $('#summary-panel #hr').html(json[t][s]['report']['totalhr']);
  $('#summary-panel #ot').html(json[t][s]['report']['totalothr']);
  $('#summary-panel #otn').html(json[t][s]['report']['wdot']);
  $('#summary-panel #phot').html(json[t][s]['report']['weot']);

  $('#summary-panel #high').html($('#inject-employee .orange').length);
  $('#summary-panel #low').html($('#inject-employee .yellow').length);
  $('#summary-panel #miss').html($('#inject-employee .marron').length);
  $('#summary-panel #leaves').html($('#inject-employee .blue').length);

  var categories = [];
  for (var i=1; i <= n; i++) {
        if(typeof(h[i]) != "undefined" && h[i] !== null) {categories.push(h[i][0]['id']);}
    };
      
  $('#container-attendance').highcharts({
    chart: {
            type: 'area'
        },
    title: {
        text: 'Monthly attendance rate & Overtime Usage',
        x: -20 //center
    },
    xAxis: {
        categories: categories,
        title: {
                text: null
            }
    },
    yAxis: [{
        title: {
            text: 'Attendance Rate %'
        },
        plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
        }],
        min: 0,
        max:100
    },{
        min: 0,
        title: {
            text: 'Overtime Hours'
        },
        opposite: true
    }],
    credits: {
        enabled: false
    },
    tooltip: {
      shared: false,
      formatter: function() {
        var text = '';
        if(this.series.name.match("^Overtime")) {
            text = '<span style="color:' + this.series.color + '">\u25CF</span><b> ' + this.x + ' - ' + this.series.name + '<br> Overtime: <b>' + Highcharts.numberFormat(this.y, 0) + 'h </b><br>  Headcount: <b>' + this.point.tip[1] + '</b><br> Overtime rate: <b>' + this.point.tip[2] + '%</b>';
        } else {
            text = this.x + ' <br>' + '<span style="color:' + this.series.color + '">\u25CF</span> ' + this.series.name + ': <b>' + Highcharts.numberFormat(this.y, 0) + '%</b>';
        }
        return text;
      }
    },
    legend: {
        layout: 'horizontal',
        align: 'right',
        verticalAlign: 'center',
        borderWidth: 0
    },
    series: [{
        name: 'Attendance - ' + $('#filters .teams').find('.label-primary').text(),
        data: json[0][0]['attrate']['data'],
    },{
        name: 'Overtime - ' + $('#filters .teams').find('.label-primary').text(),
        data: json[0][0]['otconso']['data'],
        yAxis: 1    
    }]
  });

  $('#container-daily-ot').highcharts({
    chart: {
            type: 'column'
        },
    title: {
        text: 'Overtime over the week',
        x: -20 //center
    },
    xAxis: {
        categories: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
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
        valueSuffix: 'h'
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'center',
        borderWidth: 0
    },
    series: [{
        name: $('#filters .teams').find('.label-primary').text(),
        data: json[0][0]['dailyot']['data']
    }]
  });
}

function filterData($this, json, data){
  $this.siblings().removeClass('label-primary').addClass('label-default');
  $this.removeClass('label-default').addClass('label-primary');

  //update charts data
  var containerAttendance = $('#container-attendance').highcharts();
  var containerDailyOt = $('#container-daily-ot').highcharts();
    
  if ($this.parent().attr('id') == 1){//team
    if($this.attr('data-max-shift') == 1){
      if(!$('#filters .shifts').hasClass('hide')){
        $('#filters .shifts').addClass('hide');
      }
    }else if ($this.attr('data-max-shift') == 2){
     $('#filters .shifts').removeClass('hide');
     if(!$('#filters .shifts #3').hasClass('hide')){
        $('#filters .shifts #3').addClass('hide');
      } 
    }else{
      $('#filters .shifts').removeClass('hide');
      $('#filters .shifts #3').removeClass('hide');
    }
    $('#filters .shifts a').removeClass('label-primary').addClass('label-default');
    $('#filters .shifts #0').removeClass('label-default').addClass('label-primary');

    if($this.attr('id') != 0){
      $('#attendance').DataTable().column(1).search($this.text()).draw();  
    }else{
      $('#attendance').DataTable().column(1).search("").draw();  
    }
    //if filter by team, reset filter shift to all
    $('#attendance').DataTable().column(2).search("").draw(); 

    chartsFilter(data, $this.attr('id'), 0, containerAttendance, containerDailyOt);
  }else{
   //shift
   if($this.attr('id') != 0){
      $('#attendance').DataTable().column(2).search($this.attr('id')).draw();  
    }else{
      $('#attendance').DataTable().column(2).search("").draw();  
    }
    chartsFilter(data, $('#filters #1').find('.label-primary').attr('id'), $this.attr('id'), containerAttendance, containerDailyOt);
  }
}

function createTable(j, h, n){

    for (var i=1; i <= n; i++) {
        if(typeof(h[i]) != "undefined" && h[i] !== null) {
            $("<th id='"+i+"' class='day-h'>"+h[i][0]['id']+"</th>").insertBefore('#total');
            if( h[i][0]['hd'] ){$('#attendance thead #'+i).addClass('holiday');}
            else if( !h[i][0]['wd'] ){$('#attendance thead #'+i).addClass('weekend');}
        }
    };

    attendanceTable = $('#attendance').DataTable( {
        scrollY:        "480px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   true,
        retrieve: true,
        "dom": 'lfrtip',
        "info": false,
        "columnDefs": [
            {
                "targets": [ 1 ],
                "visible": false,
            },
            {
                "targets": [ 2 ],
                "visible": false
            }
        ]
    });
    attendanceTable.rows.add(j).draw();

    $("*[data-toggle='tooltip']").tooltip({html :'true',container: 'body'});
    $('.e-day-ok').closest('td').addClass('green box');
    $('.e-day-low').closest('td').addClass('yellow box');
    $('.e-day-high').closest('td').addClass('orange box');
    $('.e-day-absent').closest('td').addClass('grey box');
    $('.e-day-leave').closest('td').addClass('blue box');
    $('.e-day-half').closest('td').addClass('bluegreen box');
    $('.e-day-missing').closest('td').addClass('marron text-center');
    $('#attendance .box').not('.grey').append("<div class='overlay'><i class='glyphicon glyphicon-info-sign'></i></div>");

    $('#attendance .box').not('.grey').click(function(){
          $this = $(this);
          if($('.table-panel').hasClass('col-md-12')){
              $('.table-panel').toggleClass('col-md-12 col-md-9').siblings('.entry-panel').removeClass('hide');
          }
          displayDetails($this);
    });

    $('#attendance_filter').prependTo('.table-panel .panel-heading').addClass('pull-right').html($('#attendance_filter').html().split("Search:").join("")).find('input').attr('placeholder', 'Search...');
    $('.panel-body #attendance_filter').addClass('hide');
    $('#attendance_filter input').keyup(function(){
      attendanceTable.search($(this).val()).draw();
    })
}

function chartsFilter(j, t, s, cAttendance, cDailyOt){
  $('#summary-panel #mar').html(j[t][s]['report']['attrate']);
  $('#summary-panel #hr').html(j[t][s]['report']['totalhr']);
  $('#summary-panel #ot').html(j[t][s]['report']['totalothr']);
  $('#summary-panel #otn').html(j[t][s]['report']['wdot']);
  $('#summary-panel #phot').html(j[t][s]['report']['weot']);
  $('#summary-panel #high').html($('#inject-employee .orange').length);
  $('#summary-panel #low').html($('#inject-employee .yellow').length);
  $('#summary-panel #miss').html($('#inject-employee .marron').length);
  $('#summary-panel #leaves').html($('#inject-employee .blue').length);

  cAttendance.series[0].remove();
  cAttendance.series[0].remove();
  cDailyOt.series[0].remove();

  cAttendance.addSeries({
        name: 'Attendance - ' + $('#filters .teams').find('.label-primary').text(),
        data: j[t][s]['attrate']['data'],
    });
  cAttendance.addSeries({
        name: 'Overtime - ' + $('#filters .teams').find('.label-primary').text(),
        data: j[t][s]['otconso']['data'],
        yAxis: 1    
    });
  cDailyOt.addSeries({
    name : $('#filters .teams').find('.label-primary').text(),
    data: j[t][s]['dailyot']['data']
  });

}

function displayDetails($this){
    $.get(
        ajaxAttDetails,               
    {
      y: $('.yearpick').val(),
      m: parseInt($('.monthpick').val()) + 1,
      d: $this.find('div:first').attr('data-d'),
      e: $this.find('div:first').attr('data-e')
    }, 
    function(r){
      $('#e-name').html(r.det['name']);
      $('#e-date').html(r.det['date']);
      $('#e-tot').html(r.det['tothr']);
      $('#e-mh').html(r.det['totreg']);
      $('#e-ot').html(r.det['totot']);
      $('#e-res').html(r.det['res']);

      var $detTable = $('#input-det-table');
      $('.det-content').find('.clone').remove();
      $('.entry-panel .alert').addClass('hide');

      if(r.det['tab']){
          for (var i = 0; i < r.det['tab'].length; i++) {
            $detTable.find('#inject-employee').children().remove();
            $detTable.find('#input-det').html(r.det['tab'][i]['header']);
            $detTable.find('th a').attr('href', r.det['tab'][i]['link'])
            if( !r.det['tab'][i]['present']){$detTable.find('#inject-employee').html("<tr><td>Absent : "+r.det['tab'][i]['absence']+"</td></tr>");}
            else if(r.det['tab'][i]['row']){
                for (var j = 0; j < r.det['tab'][i]['row'].length; j++) {
                    $detTable.find('#inject-employee').append(r.det['tab'][i]['row'][j]);
                };
            }
            $('.det-content').append($detTable);
            var $detTable = $detTable.clone().addClass('clone');
          };
      }

      if ($this.hasClass('yellow')){
        $('.entry-panel .alert-danger').removeClass('hide');
        $('.entry-panel #error-msg').html("The hours recorded are too low!")
      }else if ($this.hasClass('orange')){
        $('.entry-panel .alert-danger').removeClass('hide');
        $('.entry-panel #error-msg').html("The hours recorded are too high!")
      }else{
        $('.entry-panel .alert-success').removeClass('hide');
      }

      $("*[data-toggle='tooltip']").tooltip({html :'true',container: 'body'});
      
    },
    "json");
}
