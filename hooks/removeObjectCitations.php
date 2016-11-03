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
function ciniki_citations_hooks_removeObjectCitations($ciniki, $business_id, $args) {

    //
    // Check object was passed, object_id is optional
    //
    if( !isset($args['object']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.citations.2', 'msg'=>'No object specified'));
    }
    if( !isset($args['object_id']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.citations.3', 'msg'=>'No object specified'));
    }

    $strsql = "SELECT id, uuid "
        . "FROM ciniki_citations "
        . "WHERE ciniki_citations.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND ciniki_citations.object = '" . ciniki_core_dbQuote($ciniki, $args['object']) . "' "
        . "AND ciniki_citations.object_id = '" . ciniki_core_dbQuote($ciniki, $args['object_id']) . "' "
        . "";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectDelete');
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.citations', 'item');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['rows']) ) {
        $citations = $rc['rows'];
        foreach($citations as $citation) {
            $rc = ciniki_core_objectDelete($ciniki, $business_id, 'ciniki.citations.citation', $citation['id'], $citation['uuid'], 0x04);
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
        }
    }

    return array('stat'=>'ok');
}
?>
