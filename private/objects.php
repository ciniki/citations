<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_citations_objects($ciniki) {
    
    $objects = array();
    $objects['citation'] = array(
        'name'=>'Citation',
        'sync'=>'yes',
        'table'=>'ciniki_citations',
        'fields'=>array(
            'object'=>array(),
            'object_id'=>array(),
            'citation_type'=>array(),
            'author'=>array('default'=>''),
            'title'=>array('default'=>''),
            'source_name'=>array('default'=>''),
            'pages'=>array('default'=>''),
            'published_date'=>array('default'=>''),
            'url'=>array('default'=>''),
            'date_accessed'=>array('default'=>''),
            'notes'=>array('default'=>''),
            ),
        'history_table'=>'ciniki_citation_history',
        );

    return array('stat'=>'ok', 'objects'=>$objects);
}
?>
