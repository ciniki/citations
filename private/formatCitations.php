<?php
//
// Description
// -----------
// This method will format citations based on their citation type.
//
// Arguments
// ---------
// ciniki:
// business_id:			The business ID to check the session user against.
// method:				The requested method.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_citations_formatCitations($ciniki, $business_id, $citations) {

    foreach($citations as $cid => $citation) {
        $citations[$cid]['citation_text'] = '';
        //
        // Book
        //
        if( $citation['citation_type'] == '10' ) {
            if( $citation['title'] != '' ) {
                $citations[$cid]['citation_text'] .= $citation['title'];
            }
            if( $citation['author'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['author'];
            }
            if( $citation['published_date'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['published_date'];
            }
            if( $citation['pages'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . 'pg: ' . $citation['pages'];
            }
        }

        //
        // Journal
        //
        elseif( $citation['citation_type'] == '30' ) {
            if( $citation['source_name'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['source_name'];
            }
            if( $citation['title'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['title'];
            }
            if( $citation['author'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['author'];
            }
            if( $citation['published_date'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['published_date'];
            }
        }

        // 
        // Website
        //
        elseif( $citation['citation_type'] == '70' ) {
            if( $citation['source_name'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['source_name'];
            }
            if( $citation['author'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['author'];
            }
            if( $citation['url'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['url'];
            }
        }


        // 
        // Person
        //
        elseif( $citation['citation_type'] == '200' ) {
            if( $citation['source_name'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['source_name'];
            }
            if( $citation['date_accessed'] != '' ) {
                $citations[$cid]['citation_text'] .= ($citations[$cid]['citation_text']!=''?', ':'') . $citation['date_accessed'];
            }
        }
    }

    return array('stat'=>'ok', 'citations'=>$citations);
}
?>
