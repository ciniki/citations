<?php
//
// Description
// ===========
// This method updates one or more elements of an existing citation.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to the citation is a part of.
// citation_id:		The ID of the citation to update.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_citations_citationUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'citation_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Citation'), 
        'citation_type'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Type'), 
        'author'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Author'), 
        'title'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Title'), 
        'source_name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Name'), 
        'pages'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Pages'), 
        'published_date'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'date', 'name'=>'Published Date'), 
        'url'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'URL'), 
        'date_accessed'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'date', 'name'=>'Date Accessed'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
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
    $rc = ciniki_citations_checkAccess($ciniki, $args['business_id'], 'ciniki.citations.citationUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

	//  
	// Turn off autocommit
	//  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	$rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.citations');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}   

	//
	// Update the citation
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
	$rc = ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.citations.citation', $args['citation_id'], $args);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	//
	// Commit the database changes
	//
    $rc = ciniki_core_dbTransactionCommit($ciniki, 'ciniki.citations');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	//
	// Update the last_change date in the business modules
	// Ignore the result, as we don't want to stop user updates if this fails.
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'updateModuleChangeDate');
	ciniki_businesses_updateModuleChangeDate($ciniki, $args['business_id'], 'ciniki', 'citations');

	return array('stat'=>'ok');
}
?>
