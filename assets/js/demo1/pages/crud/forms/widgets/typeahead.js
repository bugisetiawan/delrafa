// Class definition
var KTTypeahead = function() {
    var demo2 = function() {
        // constructs the suggestion engine
        var code = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace("value"),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"transaction/Purchase/product",
            remote:{
                url:"transaction/Purchase/product/%QUERY%/code/product",
                wildcard:"%QUERY%"
            }
        });

        var name = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace("value"),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"transaction/Purchase/product",
            remote:{
                url:"transaction/Purchase/product/%QUERY%/name/product",
                wildcard:"%QUERY%"
            }
        });

        $( "body" ).on( "click", ".typeahead-form", function() {
            if( $(this).data('autocomple_ready') != 'ok' ){
                $(this).attr('id',Math.round(new Date().getTime() +(Math.random() * 100)));
                var assigned_id=$(this).attr('id');
                $('#'+assigned_id).typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 1
                },
                {
                    name: 'code',
                    source: code,
                    templates: {
                        empty: [
                            '<div class="empty-message" style="padding: 10px 15px; text-align: center;">',
                                'Empty Data',
                            '</div>'
                        ].join('\n'),
                        suggestion: Handlebars.compile('<div>{{code}}</div>')
                    }
                }); 

                $('#kt_typeahead_name').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 1
                },
                {
                    name: 'name',
                    source: name,
                    templates: {
                        empty: [
                            '<div class="empty-message" style="padding: 10px 15px; text-align: center;">',
                                'Empty Data',
                            '</div>'
                        ].join('\n'),
                        suggestion: Handlebars.compile('<div>{{name}}</div>')
                    }
                }); 
            };
            $(this).data('autocomple_ready','ok');
        });
    }

    return {
        // public functions
        init: function() {
            demo2();
        }
    };
}();

jQuery(document).ready(function() {
    KTTypeahead.init();
    $('#kt_typeahead_code').bind('typeahead:select', function(ev, suggestion) {
        code=suggestion['code'];
        name=suggestion['name'];

        $('#kt_typeahead_code').typeahead('val', code);
        $('#kt_typeahead_name').typeahead('val', name);
    });

    $('#kt_typeahead_name').bind('typeahead:select', function(ev, suggestion) {
        code=suggestion['code'];
        name=suggestion['name'];
        
        $('#kt_typeahead_code').typeahead('val', code);
        $('#kt_typeahead_name').typeahead('val', name);
    });
});


$( "body" ).on( "click", ".typeahead", function() {
    if( $(this).data('autocomple_ready') != 'ok' ){
        $(this).attr('id',Math.round(new Date().getTime() +(Math.random() * 100)));
        var assigned_id=$(this).attr('id');
        $('#'+assigned_id).typeahead({
            hint: true,
            highlight: true,
            minLength: 4,
            delay:500,
            source: function (query, process) {
                return $.post('put_your_url_here',{ 'query': query },
                    function (data) {
                        return process(data);
                    },'json');
            },
            displayText: function(item){ return item.name;}
  
        });  
      };
      $(this).data('autocomple_ready','ok');
    });