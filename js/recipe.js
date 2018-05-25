//
//
// Core API class
//
//
class Base {
    constructor(version) { this.version = version; }
    get     (uri, obj, done, fail) { this.call(uri, obj, 'GET'    , done, fail); }
    put     (uri, obj, done, fail) { this.call(uri, obj, 'PUT'    , done, fail); }
    post    (uri, obj, done, fail) { this.call(uri, obj, 'POST'   , done, fail); }
    head    (uri, obj, done, fail) { this.call(uri, obj, 'HEAD'   , done, fail); }
    delete  (uri, obj, done, fail) { this.call(uri, obj, 'DELETE' , done, fail); }
    options (uri, obj, done, fail) { this.call(uri, obj, 'OPTIONS', done, fail); }
}
class API extends Base { constructor(version) { super(version); } call (uri, obj, action, done, fail) { var call = { type: action, url: '/api/v' + this.version + uri, data: obj } ; $.ajax(call).fail(function(responce) { fail(responce); }).done(function(responce) { done(responce); }); } }
class UI  extends Base { constructor(version) { super(version); } call (uri, obj, action, done, fail) { var call = { type: action, url: '/ui/v'  + this.version + uri, data: obj } ; $.ajax(call).fail(function(responce) { fail(responce); }).done(function(responce) { done(responce); }); } redirect(uri, obj) { window.location.href = '/ui/v' + this.version + uri + "?" + $.param( obj ); } }

//
//
// Tools
//
//
function getEntryObject(entry_id) {
    var entry_text_content = $("#entry_text").val();
    var entry_name_content = $("#entry_name").val();
    return { entry_id: entry_id, entry_name: entry_name_content, entry_text: entry_text_content };
}


//
//
// UI calls
//
//
function empty   (responce) { }
function show    (responce) { 
    var obj = $.parseJSON(responce);
    if (obj.result == 1) {
		alert(responce); 
    }
}
function reload  (responce) { 
    var obj = $.parseJSON(responce);
    if (obj.result == 1) {
        window.location.reload(true); 
    }
}
function hideAssigTagsForm(responce){
    $('#assignTagsModal').modal('hide'); 
	reload(responce);
}
function displayFoundTags(responce){
    var obj = $.parseJSON(responce);
    if (obj.result == 1) {
        $.each( obj.data.tags, function( key, value ) {
			$('#tags option[value=' + value.tag_id + ']').remove();
			$('#tags').append($('<option>', { value: value.tag_id, text : value.tag_name }));
		});
		$('#tags').attr('size', obj.length);
    }
}
function newTagEdit() { $('#newTagModal').modal('show'); return false; }
function assignTags() { 
    $('#tags_text').val('');
    $('#tags').find('option').remove().end();
    $('#assignTagsModal').modal('show');
    return false;
}
function edit(responce) { 
    var obj = $.parseJSON(responce);
    if (obj.result == 1) {
        if(obj.data.entry_id) {
            site.ui.redirect( '/entries/' + obj.data.entry_id );
        };
    }
}

//
//
// API calls
//
//

//
// Tags API calls
//
function addTag( entry_id, tag_id ) { 
    var path = site.url.path(); 
    var obj = { entry_id: entry_id, tag_id: tag_id, href: path };
    site.api.post( '/tags', obj, reload, show );  return false; 
}
function delTag( entry_id, tag_id ) { 
    var path = site.url.path(); 
    var obj = { entry_id: entry_id, tag_id: tag_id, href: path };
    site.api.delete( '/tags', obj, reload, show );  return false; 
}
function assingFoundTags(entry_id){
    $('#tags option').prop('selected', true);
    var obj = { entry_id: entry_id, tag_ids: $('#tags').val() };
    site.api.post( '/tags/assign', obj, hideAssigTagsForm, empty );  return false; 
}
function findTags(entry_id){
    $('#tags option').prop('selected', true);
    var obj = { entry_id: entry_id, tag_ids: $('#tags').val() };
    site.api.post( '/tags/assign', obj, hideAssigTagsForm, empty );  return false; 
}
function findTags() {
    var obj = { tags_text: $('#tags_text').val() };
    site.api.post( '/tags/find', obj, displayFoundTags, empty );  return false; 
}

//
// Tag API calls
//
function selectTag( site, tag_id, tag_name )   { 
    var obj = { tag_id: tag_id, tag_name: tag_name };
    site.api.put( '/tag', obj, reload, show ); return false; 
}
function unselectTag( site, tag_id, tag_name )   { 
    var obj = { tag_id: tag_id, tag_name: tag_name };
    site.api.get( '/tag', obj, reload, show );  return false; 
}
function newTag() { 
    $('#newTagModal').modal('hide');
    var obj = { href: site.url.path(), tag_name: $('#new_tag_name').val(), tag_text: $('#new_tag_text').val(), tag_group_id: $('#new_tag_group_id').val() };
    site.api.post( '/tag', obj, reload, empty );  return false; 
}

//
// Entries API calls
//
function saveNewEntry() {
    var obj = getEntryObject('');
    site.api.post( '/entries', obj, edit, show );  return false; 
}
function saveEntry( entry_id ) {
    var obj = getEntryObject(entry_id);
    site.api.put( '/entries', obj, show, show );  return false; 
}
function runEntrySearch() {
    var obj = {q: $('#q').val()};
    site.ui.redirect( '/entries/search', obj ); 
}

//
//
// On Doc ready
//
//
$( document ).ready(function() {

    $("#tags").keydown(function(event) {
        if (event.which != 46) { return; }
            var sel = $(this);
            var val = sel.val();
            if (val != "") { sel.find("option[value="+val+"]").remove(); }
    });

    $(".control_list").hide();
    $(".control_list_control_0" ).show();
    $(".control").click(function() {
        var id = $( this ).attr('id'); var visile = $( '.control_list_' + id ).is(":visible"); 
        $(".control_list").hide();
        if (visile){ $( '.control_list_' + id ).hide(); } else { $(".control_list_" + id ).show(); }
    });

});


//
// Site class
//
var Query=function(a){"use strict";var b=function(a){var b=[],c,d,e,f;if(typeof a=="undefined"||a===null||a==="")return b;a.indexOf("?")===0&&(a=a.substring(1)),d=a.toString().split(/[&;]/);for(c=0;c<d.length;c++)e=d[c],f=e.split("="),b.push([f[0],f[1]]);return b},c=b(a),d=function(){var a="",b,d;for(b=0;b<c.length;b++)d=c[b],a.length>0&&(a+="&"),a+=d.join("=");return a.length>0?"?"+a:a},e=function(a){a=decodeURIComponent(a),a=a.replace("+"," ");return a},f=function(a){var b,d;for(d=0;d<c.length;d++){b=c[d];if(e(a)===e(b[0]))return b[1]}},g=function(a){var b=[],d,f;for(d=0;d<c.length;d++)f=c[d],e(a)===e(f[0])&&b.push(f[1]);return b},h=function(a,b){var d=[],f,g,h,i;for(f=0;f<c.length;f++)g=c[f],h=e(g[0])===e(a),i=e(g[1])===e(b),(arguments.length===1&&!h||arguments.length===2&&!h&&!i)&&d.push(g);c=d;return this},i=function(a,b,d){arguments.length===3&&d!==-1?(d=Math.min(d,c.length),c.splice(d,0,[a,b])):arguments.length>0&&c.push([a,b]);return this},j=function(a,b,d){var f=-1,g,j;if(arguments.length===3){for(g=0;g<c.length;g++){j=c[g];if(e(j[0])===e(a)&&decodeURIComponent(j[1])===e(d)){f=g;break}}h(a,d).addParam(a,b,f)}else{for(g=0;g<c.length;g++){j=c[g];if(e(j[0])===e(a)){f=g;break}}h(a),i(a,b,f)}return this};return{getParamValue:f,getParamValues:g,deleteParam:h,addParam:i,replaceParam:j,toString:d}},Uri=function(a){"use strict";var b=!1,c=function(a){var c={strict:/^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,loose:/^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/},d=["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],e={name:"queryKey",parser:/(?:^|&)([^&=]*)=?([^&]*)/g},f=c[b?"strict":"loose"].exec(a),g={},h=14;while(h--)g[d[h]]=f[h]||"";g[e.name]={},g[d[12]].replace(e.parser,function(a,b,c){b&&(g[e.name][b]=c)});return g},d=c(a||""),e=new Query(d.query),f=function(a){typeof a!="undefined"&&(d.protocol=a);return d.protocol},g=null,h=function(a){typeof a!="undefined"&&(g=a);return g===null?d.source.indexOf("//")!==-1:g},i=function(a){typeof a!="undefined"&&(d.userInfo=a);return d.userInfo},j=function(a){typeof a!="undefined"&&(d.host=a);return d.host},k=function(a){typeof a!="undefined"&&(d.port=a);return d.port},l=function(a){typeof a!="undefined"&&(d.path=a);return d.path},m=function(a){typeof a!="undefined"&&(e=new Query(a));return e},n=function(a){typeof a!="undefined"&&(d.anchor=a);return d.anchor},o=function(a){f(a);return this},p=function(a){h(a);return this},q=function(a){i(a);return this},r=function(a){j(a);return this},s=function(a){k(a);return this},t=function(a){l(a);return this},u=function(a){m(a);return this},v=function(a){n(a);return this},w=function(a){return m().getParamValue(a)},x=function(a){return m().getParamValues(a)},y=function(a,b){arguments.length===2?m().deleteParam(a,b):m().deleteParam(a);return this},z=function(a,b,c){arguments.length===3?m().addParam(a,b,c):m().addParam(a,b);return this},A=function(a,b,c){arguments.length===3?m().replaceParam(a,b,c):m().replaceParam(a,b);return this},B=function(){var a="",b=function(a){return a!==null&&a!==""};b(f())?(a+=f(),f().indexOf(":")!==f().length-1&&(a+=":"),a+="//"):h()&&b(j())&&(a+="//"),b(i())&&b(j())&&(a+=i(),i().indexOf("@")!==i().length-1&&(a+="@")),b(j())&&(a+=j(),b(k())&&(a+=":"+k())),b(l())?a+=l():b(j())&&(b(m().toString())||b(n()))&&(a+="/"),b(m().toString())&&(m().toString().indexOf("?")!==0&&(a+="?"),a+=m().toString()),b(n())&&(n().indexOf("#")!==0&&(a+="#"),a+=n());return a},C=function(){return new Uri(B())};return{protocol:f,hasAuthorityPrefix:h,userInfo:i,host:j,port:k,path:l,query:m,anchor:n,setProtocol:o,setHasAuthorityPrefix:p,setUserInfo:q,setHost:r,setPort:s,setPath:t,setQuery:u,setAnchor:v,getQueryParamValue:w,getQueryParamValues:x,deleteQueryParam:y,addQueryParam:z,replaceQueryParam:A,toString:B,clone:C}},jsUri=Uri;

function Site() { 
    this.url  = new Uri (window.location.href); 
    this.api  = new API(1);
    this.ui   = new UI (1);
}
var site = new Site();
