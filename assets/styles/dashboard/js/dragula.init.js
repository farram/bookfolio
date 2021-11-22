var container = document.getElementById('tb');
var folderId = container.dataset.folder;
var rows = container.children;
var nodeListForEach = function (array, callback, scope) {
    for (var i = 0; i < array.length; i++) {
        callback.call(scope, i, array[i]);
        
    }
};
var sortableTable = dragula([container]);
var pingu='';
sortableTable.on('dragend', function() {
    nodeListForEach(rows, function (index, row) {
        pingu = pingu+row.id+',';
    });
    var sortedIDs= pingu;
    
    if (sortedIDs) {
        //alert(sortedIDs);
        $.ajax({
            type: 'GET',
            url: Routing.generate('reorder_images', {id: folderId, list: sortedIDs}),
            success: function (data) {
            }
            
            
        });
    } else {
    }
    
    
    
    
    
    
    
});