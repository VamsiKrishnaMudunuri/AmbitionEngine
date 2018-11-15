var vertex = {

    stats: function(action, vertex_id, stats){

        var $body = $('body');
        var $figures = $body.find(sprintf('[data-vertex-id="figure-%s"]', vertex_id));
        var $members = $body.find(sprintf('[data-vertex-id="member-%s"]', vertex_id));

        $figures.each(function(){

            var $figure = $(this);

            var layout = $figure.data('vertex-layout');
            var text = (stats['text'] && stats['text'][layout]) ? stats['text'][layout] : '';


            if(layout == 'simple_row') {

                var $figureDom = $figure.children('.figure');
                var $wordDom = $figure.children('.word');


                if(text){
                    $figureDom.html(text.count);
                    $wordDom.html(text.word);
                }

            }else{

                var $a = $figure.children('a');
                var $dom = ($a.length <= 0) ? $figure : $a;

                $dom .attr('title', text);
                $dom .html(text);
            }

            if(stats.count > 0){

                $figure.removeClass('hide');

            }else{

                $figure.addClass('hide');

            }

        })

        $members.each(function(){

            var $member = $(this);
            var $remaining = $member.find('.remaining');
            var $existingMember = $member.find(sprintf('.member[data-id="%s"]', stats.member_key));


            if(action){

                $member.prepend(stats.member_view);

            }else{

                if($existingMember.length > 0){

                    $existingMember.remove();

                }else{

                    if($remaining.length > 0){
                        var $a = $remaining.children('a');
                        var count = $a.data('count');
                        var symbol = $a.data('symbol');
                        if(action){
                            count++;
                        }else{
                            count--;
                        }
                        count = Math.max(1, count);
                        $a.data('count', count);
                        $a.html(sprintf('%s %s', symbol, count));

                    }

                }



            }

        })

    }
}