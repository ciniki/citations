<?php
//
// Description
// -----------
// This method will search a field for the search string provided.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to search.
// field:			The field to search.  Possible fields available to search are:
//
// start_needle:	The search string to search the field for.
//
// limit:			(optional) Limit the number of results to be returned. 
//					If the limit is not specified, the default is 25.
// 
// Returns
// -------
// <results>
//		<result name="Landscape" />
//		<result name="Portrait" />
// </results>
//
function ciniki_citations_citationSearchField($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'citation_type'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Type'),
		'field'=>array('required'=>'yes', 'blank'=>'no', 'validlist'=>array('author', 'title', 'source_name'), 'name'=>'Field'),
        'start_needle'=>array('required'=>'yes', 'blank'=>'yes', 'name'=>'Search'), 
        'limit'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Limit'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'citations', 'private', 'checkAccess');
    $rc = ciniki_citations_checkAccess($ciniki, $args['business_id'], 'ciniki.citations.searchField', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Load the business intl settings
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
	$rc = ciniki_businesses_intlSettings($ciniki, $args['business_id']);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$intl_timezone = $rc['settings']['intl-default-timezone'];

	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');

    //
    // Search the citations
    //
	$date_format = ciniki_users_dateFormat($ciniki);
    $strsql = "SELECT DISTINCT CONCAT_WS('-', author, title, source_name) as uid, "
        . "author, "
        . "title, "
        . "source_name, "
        . "DATE_FORMAT(published_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS published_date, "
        . "url, "
        . "DATE_FORMAT(date_accessed, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS date_accessed, "
        . "notes "
        . "FROM ciniki_citations "
		. "WHERE ciniki_citations.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ciniki_citations.citation_type = '" . ciniki_core_dbQuote($ciniki, $args['citation_type']) . "' "
		. "AND (" . $args['field']  . " LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. "AND " . $args['field'] . " <> '' "
			. ") "
        . "GROUP BY uid "
		. "ORDER BY " . $args['field'] . " COLLATE latin1_general_cs ";
	if( isset($args['limit']) && $args['limit'] != '' && $args['limit'] > 0 ) {
		$strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";
	} else {
		$strsql .= "LIMIT 25 ";
	}
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
	$rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.citations', array(
		array('container'=>'results', 'fname'=>'title', 'fields'=>array('author', 'title', 'source_name', 'published_date', 'url', 'date_accessed', 'notes')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['results']) || !is_array($rc['results']) ) {
		return array('stat'=>'ok', 'results'=>array());
	}
	return array('stat'=>'ok', 'results'=>$rc['results']);
}
?>
