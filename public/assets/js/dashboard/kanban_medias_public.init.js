!function(n){
    "use strict";

    function t(){
        this.$body=n("body")
    }t.prototype.init=function(){
        n(".tasklist").each(function(){
            Sortable.create(sortableMediaspublic,{
                group:"shared",
                animation:150,
                dataIdAttr: 'data-id',
                ghostClass:"bg-ghost",
                store: {
                    get: function (sortable) {
                        var order = localStorage.getItem('sortableMediaspublic');
                        return order ? order.split(',') : [];
                    },
                    set: function (sortable) {
                        var order = sortable.toArray();
                        localStorage.setItem('sortableMediaspublic', order.join(','));
                        $.post('/dashboard/ordermediaspublic/',{list:order});
                    }
                },
            })
        })
    },n.KanbanBoard=new t,n.KanbanBoard.Constructor=t}(window.jQuery),function(){"use strict";window.jQuery.KanbanBoard.init()}();