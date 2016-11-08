$(document).ready(function() {

    initialize();

    var table = $('#table').DataTable();

    $('#formModal').on('show.bs.modal', function (event) {
      getForm($(event.relatedTarget).data('type'), $(event.relatedTarget).data('id'));
      var modal = $(this);
    });

    $('#formModal').on('hide.bs.modal', function (event) {
        table.columns([7,8,9]).visible(true);
        table.column(10).visible(false);
        formResetter();
    });
});

function createChart(json){
	$('#container-activities').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Activities Usage'
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: json['cat'],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'hours'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} h</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Activities',
            data: json['data']
        }]
    });
}

function createGauge(){

  var gaugeOptions = {

    chart: {
        type: 'solidgauge'
    },

    title: {
            text: 'Productivity'
        },

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
  $('#container-productivity').highcharts(Highcharts.merge(gaugeOptions, {
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
        data: [prod],
        dataLabels: {
            format: '<div style="text-align:center; margin-top:-45px;"><span style="font-size:50px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                   '<span style="font-size:12px;color:black">to line/h</span></div>'
        },
        tooltip: {
            valueSuffix: 'to line/h'
        }
    }]
  }));
}

function getForm(type, entryId){
    if( type == "@new" ){
        entryId = null;
        $('.modal-title').text('New Entry');
        $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning alert-danger').addClass('alert-info');
        $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Missing Employee ?</strong><br>You can add his details here and validate. It will be added to the input after review. Productivity will be recalculated.');
        $('#se_inputbundle_editorentry_employee').val(null);
        $('#se_inputbundle_editorentry_sesa').val(null);
        $('#se_inputbundle_editorentry_editorType').val(1);
    }else{
        $('#entry-details input, #entry-details select').prop('disabled', true);
        //new
        $('#presence-container').attr('disabled','disabled');
        if( type == "@edit" ){
            $('.modal-title').text('Edit Entry');
            $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning alert-danger').addClass('alert-warning');
            $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Editing an Employee entry?</strong><br>You can edit the details here and validate. The input Productivity will be updated after review.');
            $('#se_inputbundle_editorentry_editorType').val(2);
        } else if ( type == "@delete"){
            $('.modal-title').text('Delete Entry');
            $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning alert-danger').addClass('alert-danger');
            $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Delete Employee ?</strong><br>If you delete this entry, you have to justify why the employee was not at work. Please submit to continue.');
            $('#se_inputbundle_editorentry_editorType').val(3);
        }
        $.get(
          ajaxEditPopulate,               
          {id: entryId}, 
          function(r){
            $('#se_inputbundle_editorentry_employee').val(r.entry[0]['employee']);
            $('#se_inputbundle_editorentry_sesa').val(r.entry[0]['sesa']);
            $('#se_inputbundle_editorentry_present').val($('#se_inputbundle_editorentry_present').prop('checked', r.entry[0]['present']));
            $('#se_inputbundle_editorentry_halfday').val($('#se_inputbundle_editorentry_halfday').prop('checked', r.entry[0]['halfday']));
            $('#se_inputbundle_editorentry_absence_reason').val(r.entry[0]['absence']);
            $('#se_inputbundle_editorentry_comments').val(r.entry[0]['comments']);

            updatePresenceToggler(r.entry[0]['present'], r.entry[0]['halfday']);

            if(r.request[0] != null){$('.modal-body #override-alert').removeClass('hide');}
            if(r.entry[0]['comments'] != null || type == "@delete"){$('.modal-body .txtarea-sm').removeClass('hide');}

            for (var i = 0; i < r.entry.length; i++) {
                if(i>0){addSubElement($('#activities-prototype'));}
                if(r.entry[i]['activity']){
                    $('#activities-prototype').children('div').last().find('.input-activity').val(r.entry[i]['activity']);
                    $('#activities-prototype').children('div').last().find('.input-regular-hours').val(r.entry[i]['regularHours']);
                    $('#activities-prototype').children('div').last().find('.input-overtime').val(r.entry[i]['otHours']);
                }else{
                    $('#activities-prototype').children('div').last().remove();
                }
            };
            //relieve form
            if( type == "@edit"){
                $('#entry-details input, #entry-details select').prop('disabled', false);
                $('#presence-container').removeAttr('disabled');
            }
            if( type == "@delete"){
                $('#entry-details input, #entry-details select').prop('disabled', true);
                $('#presence-container').attr('disabled','disabled');
            }
          },
          "json")
        ; 
    }

    $('#se_inputbundle_editorentry_inputEntry').val(entryId);

    $('body').on('submit', '.ajaxForm', function (e) {
 
        e.preventDefault();
        $('.modal-footer button[type="submit"]').prop('disabled', true);
        $('#entry-details input, #entry-details select').prop('disabled', false);
        $('#presence-container').removeAttr('disabled');

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $('.modal-body #information-alert').removeClass('alert-info alert-success alert-warning').addClass('alert-success');
                $('.modal-body #override-alert').addClass('hide');
                $('.modal-body #information-alert').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Entry saved successfully!</strong><br>The input will be updated as soon as the changes will be reviewed.');
                $('.modal-body form').addClass('hide');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $('#ajaxForm').html(jqXHR.responseJSON.form);
                }
                $('form #errors').html("lol").parent(".col-md-12").removeClass('hide');
                $('.modal-footer button[type="submit"]').prop('disabled', false);
 
            } else {
                alert(errorThrown);
            }
 
        });
    });
}

function initialize(){

    Highcharts.createElement('link',{href:'//fonts.googleapis.com/css?family=Dosis:400,600',rel:'stylesheet',type:'text/css'},null,document.getElementsByTagName('head')[0]);Highcharts.theme={colors:["#7cb5ec","#f7a35c","#90ee7e","#7798BF","#aaeeee","#ff0066","#eeaaee","#55BF3B","#DF5353","#7798BF","#aaeeee"],chart:{backgroundColor:null,style:{fontFamily:"Dosis, sans-serif"}},title:{style:{fontSize:'16px',fontWeight:'bold',textTransform:'uppercase'}},tooltip:{borderWidth:0,backgroundColor:'rgba(219,219,216,0.8)',shadow:false},legend:{itemStyle:{fontWeight:'bold',fontSize:'13px'}},xAxis:{gridLineWidth:1,labels:{style:{fontSize:'12px'}}},yAxis:{minorTickInterval:'auto',title:{style:{textTransform:'uppercase'}},labels:{style:{fontSize:'12px'}}},plotOptions:{candlestick:{lineColor:'#404048'}},background2:'#F0F0EA'};Highcharts.setOptions(Highcharts.theme);
    createGauge();
    var table = $('#table').DataTable( {
        paging: false,
        "dom": 'lrtip',
        "info": false,
        buttons:[{
            text: "<i class='fa fa-edit' title='Edit this input'></i>",
            action: function ( e, dt, node, config ) {
               dt.columns([7,8,9]).visible(false);
               dt.column(10).visible(true);
            },
            className: 'btn-info'
        },{
            extend: 'excelHtml5',
            text: "<i class='fa fa-file-excel-o' title='Export in Excel'></i>",
            className: 'btn-success'
        },{
            extend: 'pdfHtml5',
            text: "<i class='fa fa-file-pdf-o' title='Export in PDF'></i>",
            className: 'btn-danger'
        }],
        "columnDefs": [{
            "targets": [ 10 ],
            "visible": false,
        }]
    });

    table.buttons().container().prependTo($('#input-details .panel-heading')).addClass('pull-right');
    $('div.dt-buttons a').each(function(){
        $(this).attr('data-toggle','tooltip').attr('title',$(this).find('i').attr('title'));
    });

    $("*[data-toggle='tooltip']").tooltip({container: 'body'});

    $.get(
      ajaxActivities,               
      {id: id}, 
      function(response){
        createChart(response.jsonActivities);
      },
      "json"); 
}

function formResetter(){
    $('.modal-body .toggling').addClass('hide');
    $('#activities-prototype').children().remove();
    addSubElement($('#activities-prototype'));
    $('.modal-body form').removeClass('hide');
    $('.modal-footer button[type="submit"]').prop('disabled', false);
    $('form #errors').html("").parent(".col-md-12").addClass('hide');
    $('form').find('.has-error').removeClass('has-error');
    $('#entry-details input, #entry-details select').prop('disabled', false);
    //new
    $('#presence-container').removeAttr('disabled');
    updatePresenceToggler(true, false);
    $('.modal-body #override-alert').addClass('hide');
}

function updatePresenceToggler(p,h){
    if( p && !h ){//present
        $('.presence-gauge').css('top','0px');
        $('#presence-container').attr('data-state', 'Present').attr('data-original-title','Present');
         $('.toggling').addClass('hide').find('.input-reason').val(0);
    }else if( !p && !h ){//absent
        $('.presence-gauge').css('top','15px');
        $('#presence-container').attr('data-state', 'Absent').attr('data-original-title','Absent');
        $('.toggling').removeClass('hide');
    }else if( p && h ){//half
        $('.presence-gauge').css('top','7px');
        $('#presence-container').attr('data-state', 'Halfday').attr('data-original-title','Halfday');
        $('.toggling').removeClass('hide');
    }
}
