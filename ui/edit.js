//
// The app to add/edit citations 
//
function ciniki_citations_edit() {
    this.flags = {
        '1':{'name':'Favourite'},
        };
    this.init = function() {
        //
        // The panel to display the edit form
        //
        this.citation = new M.panel('Note',
            'ciniki_citations_edit', 'citation',
            'mc', 'medium', 'sectioned', 'ciniki.citations.edit.citation');
        this.citation.default_data = {};
        this.citation.data = {};
        this.citation.citation_id = 0;
        this.citation.object = '';
        this.citation.object_id = '';
        this.citation.formtab = null;
        this.citation.formtabs = {'label':'', 'field':'citation_type', 'tabs':{
            'book':{'label':'Book', 'field_id':10},
            'journal':{'label':'Journal', 'field_id':30},
            'website':{'label':'Website', 'field_id':70},
            'person':{'label':'Person', 'field_id':200},
            }};
        this.citation.forms = {};
        this.citation.forms.book = {
            'info':{'label':'', 'fields':{
                'title':{'label':'Title', 'type':'text', 'livesearch':'yes'},
                'author':{'label':'Author', 'type':'text', 'livesearch':'yes'},
                'published_date':{'label':'Published Date', 'type':'date'},
                'pages':{'label':'Pages', 'type':'text'},
            }},
            '_notes':{'label':'Notes', 'type':'simpleform', 'fields':{
                'notes':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
            }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_citations_edit.citationSave();'},
                'delete':{'label':'Delete', 'visible':function() { return (M.ciniki_citations_edit.citation.citation_id>0?'yes':'no'); }, 'fn':'M.ciniki_citations_edit.citationDelete();'},
            }},
        };
        this.citation.forms.journal = {
            'info':{'label':'', 'fields':{
                'source_name':{'label':'Name', 'type':'text', 'livesearch':'yes'},
                'title':{'label':'Title', 'type':'text', 'livesearch':'yes'},
                'author':{'label':'Author', 'type':'text', 'livesearch':'yes'},
                'published_date':{'label':'Published Date', 'type':'date'},
            }},
            '_notes':{'label':'Notes', 'type':'simpleform', 'fields':{
                'notes':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
            }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_citations_edit.citationSave();'},
                'delete':{'label':'Delete', 'visible':function() { return (M.ciniki_citations_edit.citation.citation_id>0?'yes':'no'); }, 'fn':'M.ciniki_citations_edit.citationDelete();'},
            }},
        };
        this.citation.forms.website = {
            'info':{'label':'', 'fields':{
                'source_name':{'label':'Website', 'type':'text', 'livesearch':'yes'},
                'author':{'label':'Author', 'type':'text', 'livesearch':'yes'},
                'url':{'label':'URL', 'type':'date'},
                'date_accessed':{'label':'Date Accessed', 'type':'date'},
            }},
            '_notes':{'label':'Notes', 'type':'simpleform', 'fields':{
                'notes':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
            }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_citations_edit.citationSave();'},
                'delete':{'label':'Delete', 'visible':function() { return (M.ciniki_citations_edit.citation.citation_id>0?'yes':'no'); }, 'fn':'M.ciniki_citations_edit.citationDelete();'},
            }},
        };
        this.citation.forms.person = {
            'info':{'label':'', 'fields':{
                'source_name':{'label':'Name', 'type':'text', 'livesearch':'yes'},
                'date_accessed':{'label':'Date Accessed', 'type':'date'},
            }},
            '_notes':{'label':'Notes', 'type':'simpleform', 'fields':{
                'notes':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
            }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_citations_edit.citationSave();'},
                'delete':{'label':'Delete', 'visible':function() { return (M.ciniki_citations_edit.citation.citation_id>0?'yes':'no'); }, 'fn':'M.ciniki_citations_edit.citationDelete();'},
            }},
        };
        this.citation.sections = this.citation.forms.book;
        this.citation.liveSearchCb = function(s, i, value) {
            if( i == 'author' || i == 'title' || i == 'source_name' ) {
                M.api.getJSONBgCb('ciniki.citations.citationSearchField', {'tnid':M.curTenantID, 'citation_type':this.formtabs.tabs[this.formtab].field_id, 'field':i, 'start_needle':value, 'limit':15},
                    function(rsp) {
                        M.ciniki_citations_edit.citation.liveSearchShow(s, i, M.gE(M.ciniki_citations_edit.citation.panelUID + '_' + i), rsp.results);
                    });
            }
        };
        this.citation.liveSearchResultValue = function(s, f, i, j, d) {
            if( (f == 'author' || f == 'title' || f == 'source_name' ) && d != null ) { 
                var name = '';
                if( d.source_name != '' ) { name += (name!=''?', ':'') + d.source_name; }
                if( d.title != '' ) { name += (name!=''?', ':'') + d.title; }
                if( d.author != '' ) { name += (name!=''?', ':'') + d.author; }
                return name; 
            }
            return '';
        };
        this.citation.liveSearchResultRowFn = function(s, f, i, j, d) { 
            if( (f == 'author' || f == 'title' || f == 'source_name' ) && d != null ) {
                return 'M.ciniki_citations_edit.citation.updateFields(\'' + s + '\',\'' + f + '\',\'' + escape(d.author) + '\',\''+escape(d.title)+'\',\''+escape(d.source_name)+'\',\''+escape(d.published_date)+'\',\''+escape(d.url)+'\',\''+escape(d.date_accessed)+'\');';
            }
        };
        this.citation.updateFields = function(s, fid, author, title, source_name, published_date, url, date_accessed) {
            if( M.gE(this.panelUID + '_author') ) { M.gE(this.panelUID + '_author').value = unescape(author); }
            if( M.gE(this.panelUID + '_title') ) { M.gE(this.panelUID + '_title').value = unescape(title); }
            if( M.gE(this.panelUID + '_source_name') ) { M.gE(this.panelUID + '_source_name').value = unescape(source_name); }
            if( M.gE(this.panelUID + '_published_date') ) { M.gE(this.panelUID + '_published_date').value = unescape(published_date); }
            if( M.gE(this.panelUID + '_url') ) { M.gE(this.panelUID + '_url').value = unescape(url); }
            if( M.gE(this.panelUID + '_date_accessed') ) { M.gE(this.panelUID + '_date_accessed').value = unescape(date_accessed); }
            this.removeLiveSearch(s, fid);
        };
        this.citation.fieldValue = function(s, i, d) { 
            if( this.data[i] != null ) {
                return this.data[i]; 
            } 
            return ''; 
        };
        this.citation.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.citations.citationHistory', 'args':{'tnid':M.curTenantID, 
                'citation_id':this.citation_id, 'field':i}};
        };
        this.citation.addButton('save', 'Save', 'M.ciniki_citations_edit.citationSave();');
        this.citation.addClose('Cancel');
    };

    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) { args = eval(aG); }

        //
        // Create container
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_citations_edit', 'yes');
        if( appContainer == null ) {
            M.alert('App Error');
            return false;
        }

        this.citationEdit(cb, args.object, args.object_id, (args.citation_id==null?0:args.citation_id));
    }

    this.citationEdit = function(cb, object, object_id, cid) {
        if( object != null ) { this.citation.object = object; }
        if( object_id != null ) { this.citation.object_id = object_id; }
        if( cid != null ) { this.citation.citation_id = cid; }
        M.api.getJSONCb('ciniki.citations.citationGet', {'tnid':M.curTenantID, 'citation_id':this.citation.citation_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_citations_edit.citation;
            p.data = rsp.citation;
            p.refresh();
            p.show(cb);
        });
    };

    this.citationSave = function() {
        if( this.citation.citation_id > 0 ) {
            var c = this.citation.serializeForm('no');
            if( c != null ) {
                var rsp = M.api.postJSONCb('ciniki.citations.citationUpdate', {'tnid':M.curTenantID, 'citation_id':this.citation.citation_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } else {
                            M.ciniki_citations_edit.citation.close();
                        }
                    });
            } else {
                this.citation.close();
            }
        } else {
            var c = this.citation.serializeForm('yes');
            M.api.postJSONCb('ciniki.citations.citationAdd', {'tnid':M.curTenantID, 
                'object':this.citation.object, 'object_id':this.citation.object_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } else {
                        M.ciniki_citations_edit.citation.close();
                    }
                });
        }
    };

    this.citationDelete = function() {
        M.confirm('Are you sure you want to delete this citation?',null,function() {
            M.api.getJSONCb('ciniki.citations.citationDelete', {'tnid':M.curTenantID, 
                'citation_id':M.ciniki_citations_edit.citation.citation_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_citations_edit.citation.close();
                });
        });
    };
}
