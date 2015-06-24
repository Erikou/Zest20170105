$(document).ready(function() {

    initCollection();

   $(document).on('click', '#add[data-target]', function(e) {
      addElement($(this));
      e && e.preventDefault(); 
      return false;
    });

    $(document).on('click', '#rmv[data-target]', function(e) {
      removeElement($(this));
      e && e.preventDefault();
      return false;
    });

    function addElement($element){
      var $prototypeHolder = $('#' + $element.attr('data-target'));
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
        addSubElement($activitiesPrototypeHolder);
        $item.find('#add').attr('data-target', $activitiesPrototypeHolder.attr('id'));

        //increment
        $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);
      }
      else{
        addSubElement($prototypeHolder);
      }

    }

    function removeElement($element){
      target = $element.attr('data-target');
      $('*[data-content="'+target+'"]').remove();
    }

    function definePrototype($collectionHolder, level){
      if (!$collectionHolder.attr('data-counter')) {
        $collectionHolder.attr('data-counter', $collectionHolder.children().length);
      }

      var $prototype = $collectionHolder.attr('data-prototype');
      var type = (level) ? "entry_name" : "activity_name";
      var re = new RegExp(type,"g")
      var $form = $prototype.replace(re, $collectionHolder.attr('data-counter'));
       
      return $form;

    }

    function addSubElement($prototypeHolder){
      var $element = definePrototype($prototypeHolder, false);
      $prototypeHolder.append($element);
      var $sub = $prototypeHolder.children().last();
      var content = $prototypeHolder.attr('id')+'_'+$prototypeHolder.attr('data-counter');
      attachData($sub, content);
      $sub.find('.transfer').attr('data-sub-target', $prototypeHolder.attr('id'));
      var parentContent = $prototypeHolder.closest('tr').attr('data-content');
      $sub.find('.transfer').attr('data-target', parentContent);
      $sub.find('.transfer').attr('data-disabled', 0);
      $prototypeHolder.attr('data-counter', Number($prototypeHolder.attr('data-counter')) + 1);
    }

    function attachData($item, content){
      if (!$item.attr('data-content')) {
          $item.attr('data-content', content);
        }
      $item.find('#rmv:last').attr('data-target', content);
    }

    function dynamicList(){
    //delete all current field

    //reset data counter

    //add good nb fields

    //set default value
    }

    function initCollection(){
      //list length
      var count = 0;
      $('.input-shift').val(1);
      for(var i = 0; i < employeesData.length; ++i){
          if(employeesData[i][2] == $('.input-team').val() && employeesData[i][3] == $('.input-shift').val()){
            count++;
          }
      }

      console.log('team', $('.input-team').val(), 'shift', $('.input-shift').val(), 'count', count);
    }

});