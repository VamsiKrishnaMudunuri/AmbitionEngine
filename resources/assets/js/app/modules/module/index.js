$(function() {

    $module = $('.module-index');

    $module.find('.toggle-checkbox').change(function(){

        $this = $(this);
        var $toggleContainer = $this.parents('.toggle');
        var toggle = 'bs.toggle';
        var url = $this.data('url');
        var status = $this.prop('checked');
        var $parent = $module.find('[data-module-id="' + $this.data('module-id') +'"]');
        var $child = $module.find('[data-module-parent="' + $this.data('module-id') +'"]');

        if($child.length > 0) {

            $child.each(function(){
                $thisChild = $(this);
                if(status){
                    $thisChild.data(toggle).enable(true);
                    $thisChild.data(toggle).on(true);
                }else{
                    $thisChild.data(toggle).off(true);
                    $thisChild.data(toggle).disable(true);
                }
            })

        }

        widget.ajax.post($toggleContainer, $this, null, {'url' : url}, function(data, textStatus, jqXHR){

        }, function(jqXHR, textStatus, errorThrown){

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, isError){

            if(isError){

                if(status){
                    $parent.data(toggle).off(true);
                }else{
                    $parent.data(toggle).on(true);
                }

                if($child.length > 0) {
                    $child.each(function(){
                        $thisChild = $(this);
                        if(status){
                            $thisChild.data(toggle).off(true);
                            $thisChild.data(toggle).disable(true);
                        }else{
                            $thisChild.data(toggle).enable(true);
                            $thisChild.data(toggle).on(true);
                        }
                    })
                }

            }

        });

    })

});