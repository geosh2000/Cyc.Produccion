var status;
function sendRequest(asesor, semana, year, col, newVal){
        $.ajax({
          url: '/json/editableTable_query.php',
          type: 'POST',
          data: {queryIns: "INSERT INTO horarios_position_select (asesor, semana, year, "+col+") VALUES ("+asesor+", "+semana+", "+year+", "+newVal+")", queryUpd: "Update horarios_position_select SET "+col+"="+newVal+" WHERE asesor="+asesor+" AND semana="+semana+" AND year="+year},
          dataType: 'json',
          success: function(array){
            data=array;
            if(data['status']=='OK'){
              tipo_noti='success';
              notif_msg=data['msg'];
            }else{
              tipo_noti='error';
              notif_msg=data['msg'];
            }
            new noty({
                text: notif_msg,
                type: tipo_noti,
                timeout: 5000,
                animation: {
                    open: {height: 'toggle'}, // jQuery animate function property object
                    close: {height: 'toggle'}, // jQuery animate function property object
                    easing: 'swing', // easing
                    speed: 500 // opening & closing animation speed
                }
            });
            
          }
        });
        
    }

$(function(){

    var validation;

     function checkRegexp( o, regexp) {
      if ( !( regexp.test( o ) ) ) {
        return false;
      } else {
        return true;
      }
    }

    $('#tableedit').tablesorter({
        theme: 'jui',
        headerTemplate: '{content}',
        widthFixed: false,
        widgets: [ 'zebra','filter', 'output', 'editable', 'uitheme' ],
        widgetOptions: {

           uitheme: 'jui',
            columns: [
                "primary",
                "secondary",
                "tertiary"
                ],
            columns_tfoot: false,
            columns_thead: true,
            filter_childRows: false,
            filter_columnFilters: true,
            filter_cssFilter: "tablesorter-filter",
            filter_functions: null,
            filter_hideFilters: false,
            filter_ignoreCase: true,
            filter_reset: null,
            filter_searchDelay: 300,
            filter_startsWith: false,
            filter_useParsedData: false,
            resizable: true,
            saveSort: true,
            output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
            output_ignoreColumns : [0],          // columns to ignore [0, 1,... ] (zero-based index)
            output_hiddenColumns : false,       // include hidden columns in the output
            output_includeFooter : true,        // include footer rows in the output
            output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
            output_headerRows    : true,        // output all header rows (multiple rows)
            output_delivery      : 'd',         // (p)opup, (d)ownload
            output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
            output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
            output_replaceQuote  : '\u201c;',   // change quote to left double quote
            output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
            output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
            output_wrapQuotes    : false,       // wrap every cell output in quotes
            output_popupStyle    : 'width=580,height=310',
            output_saveFileName  : 'cuartiles_<?php echo "$year"."_$month"."_$dep";?>.csv',
            // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
            output_encoding      : 'data:application/octet-stream;charset=utf8,',

            editable_columns       : [3,4],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
            editable_autoResort    : false,         // auto resort after the content has changed.
            editable_validate      : function(text, original, columnIndex){ validation=true; return text; },
            editable_focused       : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.addClass('focused');
            },
            editable_blur          : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.removeClass('focused');
            },
            editable_selectAll     : function(txt, columnIndex, $element){
              // note $element is the div inside of the table cell, so use $element.closest('td') to get the cell
              // only select everthing within the element when the content starts with the letter "B"
              return /^b/i.test(txt) && columnIndex === 0;
            },
            editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
            editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
            editable_noEdit        : 'no-edit',     // class name of cell that is not editable
            editable_editComplete  : 'editComplete' // event fired after the table content has been edited

        }
    }).children('tbody').on('editComplete', 'td', function(event, config){
      var $this = $(this),
        newContent = $this.text(),
        asesor = $this.closest('td').attr('asesor'),
        semana = $this.closest('td').attr('semana'),
        year = $this.closest('td').attr('year');
        col = $this.closest('td').attr('col');
        if(validation==true){
        	sendRequest(asesor, semana, year , col, newContent);
        }

      // Do whatever you want here to indicate
      // that the content was updated
      $this.addClass( 'editable_updated' ); // green background + white text
      setTimeout(function(){
        $this.removeClass( 'editable_updated' );
      }, 500);

      /*
      $.post("mysite.php", {
        "row"     : rowIndex,
        "cell"    : cellIndex,
        "content" : newContent
      });
      */
    });

});