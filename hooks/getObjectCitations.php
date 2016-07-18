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
function ciniki_citations_hooks_getObjectCitations($ciniki, $business_id, $args) {

    //
    // Check object was passed, object_id is optional
    //
    if( !isset($args['object']) ) {
        return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2693', 'msg'=>'No object specified'));
    }

    //
    // Load the business intl settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
    $rc = ciniki_businesses_intlSettings($ciniki, $business_id);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);

    //
    // Get the list of citations
    //
    $strsql = "SELECT id, object, object_id, "
        . "citation_type, author, title, source_name, "
        . "pages, "
        . "DATE_FORMAT(published_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS published_date, "
        . "url, "
        . "DATE_FORMAT(date_accessed, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS date_accessed, "
        . "notes "
        . "FROM ciniki_citations "
        . "WHERE ciniki_citations.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND ciniki_citations.object = '" . ciniki_core_dbQuote($ciniki, $args['object']) . "' "
        . "";
    if( isset($args['object_id']) ) {
        $strsql .= "AND ciniki_citations.object_id = '" . ciniki_core_dbQuote($ciniki, $args['object_id']) . "' ";
    }
    $strsql .= "ORDER BY source_name, title, author "
        . "";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.citations', array(
        array('container'=>'citations', 'fname'=>'id', 
            'fields'=>array('id', 'object', 'object_id', 'citation_type',
                'author', 'title', 'source_name', 'pages', 'published_date', 'url', 'date_accessed', 'notes')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['citations']) ) {
        return array('stat'=>'ok', 'citations'=>array());
    }
    ciniki_core_loadMethod($ciniki, 'ciniki', 'citations', 'private', 'formatCitations');
    $rc = ciniki_citations_formatCitations($ciniki, $business_id, $rc['citations']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    return array('stat'=>'ok', 'citations'=>$rc['citations']);
}
?>
