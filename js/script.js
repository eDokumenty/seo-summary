function findOnPage(findUrl, inUrl) {
    var color = prompt("Podaj kod koloru podświetlenia linku/ów HEX", '#c0392b');
    var url = location.origin + ajaxurl;
            
    var data = {  
        action: 'findOnPage',
        bgColor: color,
        url : findUrl,
        inUrl : inUrl
    };
    jQuery(function($) {
        $.post(url, data, function( response){  
           newUrl = response.slice(0, -1); 
            if (inUrl === newUrl) { 
                //if (confirm("Czy chcesz kolejny link do podświetlenia") === false) {
                    window.open(newUrl + '?seo_summary', '_blank'); 
                //}
            } else {
                alert("Nie można było otworzyć strony!");
            }
            
        });
    });
}
    
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
        $( "#crawl-now" ).click(function(event){
             var self = $( this );

            var loaderContainer = $( '<span/>', {
                'class': 'loader-image-container'
            }).insertAfter( self );

            var loader = $( '<img/>', {
                src: location.origin + ajaxurl.replace('admin-ajax.php','images/loading.gif'),
                'class': 'loader-image'
            }).appendTo( loaderContainer );
            
            var url = location.origin + ajaxurl;
            
            var data = {  
                action: 'crawl_now'
            };
            $.post(url, data, function( response){  
               $( "#seo-summary" ).empty(); 
               $( "#seo-summary" ).html(response.slice(0, -1));
               alert('Wykonano!');
            });
        });       
        
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
               $info.html("<h1 style='line-height: 30px;'>" + title + "</h1>" + response.slice(0, -1));
                //alert(response)
            });
        });
    });
    
});


jQuery(function($){
    var thead = $('#thead'); // element zawierający menu
    if (thead.position() === undefined) {
        return;
    }
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

jQuery(function($) {
    if ( $('#sortTable').length ) { //check exists
        $("#sortTable").tablesorter();
    }
});