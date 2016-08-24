jQuery(function($) {
    var $info = $("#modal-content");
    $info.dialog({
        'dialogClass'   : 'wp-dialog',
        'resizable'     : false,
        'modal'         : true,
        'autoOpen'      : false,
        'closeOnEscape' : true,
        'height'        : 600,
        'width'         : 750,
        'draggable'     : false,
        // not scroll background
        open: function(){
            $("body").css("overflow", "hidden");
        },
        close: function(){
            $("body").css("overflow", "auto");
        }
    });
    $( function() {
        $( "#dialog" ).dialog();
    });
    // When click outside
    $(".ui-widget-overlay").live("click", function (){
        $("div:ui-dialog:visible").dialog("close");
    });
    $(".open-modal").click(function(event) {
        event.preventDefault();
        $info.dialog('open');
        $type = $(this).attr('wp_type');
        if ( $type === 'text' ){
            $info.dialog( "option", "title", "Tekst na stronie" );
        } else if ( $type === 'inLink' ) {
            $info.dialog( "option", "title", "Linki znajdujące się na stronie" );
        } else {
            $info.dialog( "option", "title", "Strony zawierające link do tej strony" );
        }
        
        $info.html('');
    });
    $(document).ready(function(){
        $("td").click(function(event){
        event.stopPropagation();
            var title = $(this).attr('id');
            var data = {  
                action: 'get_' + $(this).attr('wp_type'),
                post_name: $(this).attr('wp_value')
            };  
            $info.html(title);
           
            var url = location.origin + ajaxurl;
            $.post(url, data, function( response){  
               $info.html("<h1>" + title + "</h1>" + response.slice(0, -1));
                //alert(response)
            });
        });
    });
});


jQuery(function($){
    var thead = $('#thead'); // element zawierający menu
    var theadPositionTop = thead.position().top; // sprawdzamy początkową pozycję menu

    $(window).scroll(function () { // przypisujemy funkcję do zdarzenia 'scroll'
        if(parseInt($(window).scrollTop()) > theadPositionTop) { 
            // sprawdzamy czy scroll "przejechał" przez wysokość, na której znajduje się menu. 
            // MUSIMY sprawdzić, czy jest większy, nie da się zrobić porównania if (parseInt($(window).scrollTop()) == menuPositionTop)
            // ponieważ scroll nie przelatuje po wszystkich wartościach po kolei, tylko "przeskakuje" wartości, tym więcej im szybciej machamy scrollbarem ;)

            if (thead.hasClass('static')) { // ten warunek nie jest konieczny, ale gdy go dodamy unikamy niepotrzebnego usuwania i dodawania klasy. to samo można zrobić za pomocą funkcji .css()
                thead.removeClass('static').addClass('fixeed'); // zmieniamy pozycję ze static na fixed zamieniając klasy
            }
        }

        else {
            if (thead.hasClass('fixeed')) { // podobnie jak warunek powyżej. też nie jest konieczny
                thead.removeClass('fixeed').addClass('static');  // zmieniamy pozycję z fixed na static zamieniając klasy
            }
        }
    });

});
