$(document).ready(function() {

    initCollection();
    $('form').preventDoubleSubmission();

    $('.input-team, .input-shift input[type=radio]').change(function(e) {
      var valueSelected = $('.input-shift input[type=radio]:checked').val();
      dynamicList(valueSelected);
    });

  /*  $('.input-ot-time input').change(function(e) {
      var startTime = $(this).closest('.input-ot-time').find('input:first').val();
      var endTime = $(this).closest('.input-ot-time').find('input:last').val();
      var $overTime = $(this).closest('.row').find('.input-overtime input');
      displayOvertime(startTime, endTime, $overTime);
    });
*/
    $('.saver').prepend( "<i class='glyphicon glyphicon-send'> </i> " );
    $('.saver').hide();
    $('.saver').prop('disabled', true);
    $(document).on('click', '.confirmation',  function(e){
      alert("Please review carefully your manhours input, then click save");
      $('.confirmation').hide();
      $('.saver').show();
      $('.saver').prop('disabled', false);
    });

    $(document).on('change', 'form',  function(e){
        $('.saver').hide();
        $('.saver').prop('disabled', true);
        $('.confirmation').show();
    });

    $(document).on('change', '.input-employee select', function(e){
     //input sesa automatic...
     var $this =$(this)
     var id = $this.val(); 
     $.get(
      ajaxPopulate,               
      {idEmployee: id}, 
      function(response){
        if(response.code == 100 && response.success){
         $this.parent().siblings('.input-sesa').find('input').val(response.sesa);
         $this.closest('td').siblings('#activities').find('.input-activity select').val(response.activity);

        }
      },
      "json");    
    });


   $(document).on('click', '#add[data-target]', function(e) {
      var $proto = $('#' + $(this).attr('data-target'));
      defaultValues = [0, 1,"", 0, 0, 0];
      addElement($proto, defaultValues);
      e && e.preventDefault(); 
      return false;
    });

    $(document).on('click', '#rmv[data-target]', function(e) {
      removeElement($(this));
      e && e.preventDefault();
      return false;
    });

    $(document).on('click', '#presence-container, presence-container *', function(e) {
      presenceToggler($(this).closest("#presence-container"));
      e && e.preventDefault();
      return false;
    });

  $(document).on('click', '#comment', function(e) {
    $(this).parent().siblings('.txtarea-sm').toggleClass('hide');
    window.alert("HERE");
  });

  $( "div" ).find( ".input-transfer-team" ).hide();
  
  $('.input-activity select').change(function(){
      if ($(this).val() == "13") {
    	  $(this).parent().siblings('.input-transfer-team').show();
      } else {
    	  $(this).parent().siblings('.input-transfer-team').hide();
      }
  });

});

function addElement($prototypeHolder, values){
  if($prototypeHolder.is("#entries-prototype")){
    var $element = definePrototype($prototypeHolder, true);
    $('tbody#entries').append($element);
    var $item = $('tbody#entries').children().last();

    //attach data-target on rmv btn and remove btn on item appended
    var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');
    attachData($item, content);

    //get activity prototype
    var $activitiesPrototypeHolder = $item.find('#activities-prototype');
    $activitiesPrototypeHolder.attr('id',content+'_activities-prototype');

    //add first activity
    var $sub = addSubElement($activitiesPrototypeHolder);
    $item.find('#add').attr('data-target', $activitiesPrototypeHolder.attr('id'));

    //increment
    $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);

    $item.find('.input-employee select').val(values[0]);
    $item.find('.input-sesa input').val(values[2]);
    $item.find('.input-reason').val(0);
    $item.find("*[data-toggle='tooltip']").tooltip();
  }
  else{
    var $sub = addSubElement($prototypeHolder);
    $sub.find("*[data-toggle='tooltip']").tooltip();
  }
   //set default values
   $sub.find('.input-activity select').val(values[5]);
}

function removeElement($element){
  target = $element.attr('data-target');
  $proto = $('*[data-content="'+target+'"]').parent();
  $('*[data-content="'+target+'"]').remove();
  preventNullActivity($proto);
}

function definePrototype($collectionHolder, level){
  if (!$collectionHolder.attr('data-counter')) {
    $collectionHolder.attr('data-counter', $collectionHolder.children().length);
  }

  var $prototype = $collectionHolder.attr('data-prototype');
  var type = (level) ? "entry_name" : "activity_name";
  var re = new RegExp(type,"g")
  var $form = $prototype.replace(re, $collectionHolder.attr('data-counter')).replace(/time_name/g, parseInt($collectionHolder.attr('data-counter'))+1);

  return $form;

}

function addSubElement($prototypeHolder){
  var $element = definePrototype($prototypeHolder, false);
  $prototypeHolder.append($element);
  var $sub = $prototypeHolder.children().last();
  var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');
  attachData($sub, content);
  var parentContent = $prototypeHolder.closest('tr').attr('data-content');
  var $transfer = $sub.find('.transfer').attr({
    'data-sub-target': $prototypeHolder.attr('id'),
    'data-target': parentContent,
    'data-disabled': 0});
  $sub.find('.input-regular-hours input').val(8);
  $sub.find('.input-overtime input').val(0);
  //$sub.find('.input-zone select').val(0);
  $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);

  preventNullActivity($prototypeHolder);

  return $sub;
}

function attachData($item, content){
  if (!$item.attr('data-content')) {
      $item.attr('data-content', content);
    }
  $item.find('#rmv:last').attr('data-target', content);
}

function dynamicList(valueSelect){
//delete all current field
$('#entries').children().remove();

//reset data counter
$('#entries-prototype').attr('data-counter', 0);

//set new overtime default
switch(valueSelect){
  case '2':
    $('.input-ot-time input:first').val("13:00");
    $('.input-ot-time input:last').val("01:00");
    break;
  case '3':
    $('.input-ot-time input:first').val("19:30");
    $('.input-ot-time input:last').val("07:30");
    break;
  default:
    $('.input-ot-time input:first').val("07:30");
    $('.input-ot-time input:last').val("19:30");
}

//add good nb fields
for(var i = 0; i < employeesData.length; ++i){
      if(employeesData[i][3] == $('.input-team').val() && employeesData[i][4] == valueSelect){
        addElement($('#entries-prototype'), employeesData[i]);
      }
  }
}

function initCollection(){
  if($.trim( $('#errors').html() ).length ) {
    $('#errors').parent().toggleClass('hide');
    alert('There are errors in this input, please check.');
    }
  else{
    $('.input-ot-time input:first').val('07:30');
    $('.input-ot-time input:last').val('19:00');
    $('.input-shift').val(1);
    $(".input-shift input[value=1]").attr('checked', 'checked');
    for(var i = 0; i < employeesData.length; ++i){
      if(employeesData[i][3] == $('.input-team').val() && employeesData[i][4] == $('.input-shift').val()){
        addElement($('#entries-prototype'), employeesData[i]);
      }
    } 
    $('form').children('div').last().hide();
  }
  $('.clockpicker').clockpicker(); 
  $("*[data-toggle='tooltip']").tooltip();
}

function displayOvertime(start, end, $overt){
  end = end.split(/:/);
  start = start.split(/:/);
  var diff = Math.round((end[0] * 3600 + end[1] * 60 + 43200 - (start[0] * 3600 + start[1] * 60 + 43200))/36)/100;
  if (diff<0){
    diff+=24;
  }
  $overt.val(diff);
}

jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return this;
};

function presenceToggler($this){
  var $main = $this.closest('#presence');
  var cb1 =  $main.find('.input-present');
  var cb2 =  $main.find('.input-halfday');
  var $actProto = $main.closest('td').siblings('#activities').find('div:first');

  if($this.attr('data-state') == 'Present'){
    $this.find('.presence-gauge').animate({top: "+=15"}, 500);
    cb1.val(cb1.prop('checked', false));
    $this.attr('data-state','Absent').attr('data-original-title','Absent');

    $this.closest('td').toggleClass('expand-cell');
    $main.find('.toggling').toggleClass('hide').find('.input-reason').val(1);
    $actProto.children().remove();

  }else if($this.attr('data-state') == 'Absent'){
    $this.find('.presence-gauge').animate({top: "-=8"}, 500);
    cb1.val(cb1.prop('checked', true));
    cb2.val(cb2.prop('checked', true));
    $this.attr('data-state','Halfday').attr('data-original-title','Halfday');

    addSubElement($actProto);
    $actProto.children().last().find('.input-regular-hours input').val(4);
     
  }else{
    $this.find('.presence-gauge').animate({top: "-=7"}, 500);
    cb2.val(cb2.prop('checked', false));
    $this.attr('data-state', 'Present').attr('data-original-title','Present');

    $this.closest('td').toggleClass('expand-cell');
    $main.find('.toggling').toggleClass('hide');
    $main.find('.input-reason').val(0);
    $actProto.children().last().find('.input-regular-hours input').val(8);
  
  }
}

function preventNullActivity($proto){
  //test to disable deleting the last activity from user (if not absent)
  if ($proto.children().length == 1){
    $proto.find('#rmv').attr('disabled','disabled');
  }else{
    $proto.find('#rmv').removeAttr('disabled');
  }
}