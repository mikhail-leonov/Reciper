jQuery(document).ready(function () {

    var iconPickerOpt = {cols: 5,  footer: false};
        var options = {
            hintCss: {'border': '1px dashed #13981D'},
            placeholderCss: {'background-color': 'gray'},
            ignoreClass: 'btn',
            opener: {
                active: true,
                as: 'html',
                close: '<i class="fa fa-minus"></i>',
                open: '<i class="fa fa-plus"></i>',
                openerCss: {'margin-right': '10px'},
                openerClass: 'btn btn-success btn-sm'
            }
    };
    menuEditor('groups_tree', {listOptions: options, iconPicker: iconPickerOpt, labelEdit: 'Edit', labelRemove: 'x'});
    menuEditor('tags_tree'  , {listOptions: options, iconPicker: iconPickerOpt, labelEdit: 'Edit', labelRemove: 'x'});

});