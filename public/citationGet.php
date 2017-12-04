<?php
//
// Description
// ===========
// This method will return all information for a citation.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to get the citation from.
// citation_id:         The ID of the citation to get.
// 
// Returns
// -------
//
function ciniki_citations_citationGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'citation_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Citation'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'citations', 'private', 'checkAccess');
    $rc = ciniki_citations_checkAccess($ciniki, $args['tnid'], 'ciniki.citations.citationGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Load the tenant intl settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'intlSettings');
    $rc = ciniki_tenants_intlSettings($ciniki, $args['tnid']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');

    //
    // Setup the default citation
    //
    if( $args['citation_id'] == 0 ) {
        $dt = new DateTime('now', new DateTimeZone($intl_timezone));
        $date_format = ciniki_users_dateFormat($ciniki, 'php');
        return array('stat'=>'ok', 'citation'=>array(
            'id'=>0,
            'citation_type'=>'0',
            'author'=>'',
            'title'=>'',
            'source_name'=>'',
            'pages'=>'',
            'published_date'=>'',
            'url'=>'',
            'date_accessed'=>$dt->format($date_format),
            'notes'=>'',
            ));
    }

    //
    // Get the citation
    //
    $date_format = ciniki_users_dateFormat($ciniki);
    $strsql = "SELECT ciniki_citations.id, "
        . "ciniki_citations.object, "
        . "ciniki_citations.object_id, "
        . "ciniki_citations.citation_type, "
        . "ciniki_citations.author, "
        . "ciniki_citations.title, "
        . "ciniki_citations.source_name, "
        . "ciniki_citations.pages, "
        . "DATE_FORMAT(published_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS published_date, "
        . "ciniki_citations.url, "
        . "DATE_FORMAT(date_accessed, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS date_accessed, "
        . "ciniki_citations.notes "
        . "FROM ciniki_citations "
        . "WHERE ciniki_citations.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND ciniki_citations.id = '" . ciniki_core_dbQuote($ciniki, $args['citation_id']) . "' "
        . "";

    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.citations', array(
        array('container'=>'citations', 'fname'=>'id', 'name'=>'citation',
            'fields'=>array('id', 'object', 'object_id', 'citation_type', 'author', 'title', 'source_name', 
                'pages', 'published_date', 'url', 'date_accessed', 'notes')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['citations']) ) {
        return array('stat'=>'ok', 'err'=>array('code'=>'ciniki.citations.8', 'msg'=>'Unable to find citation'));
    }
    $citation = $rc['citations'][0]['citation'];

    return array('stat'=>'ok', 'citation'=>$citation);
}
?>
