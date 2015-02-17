/*
 * Post Filters by Digitize jQuery functions
 *
 * Built for use with the jQuery library
 * http://jquery.com
 *
 * Version 1.0.1
 */

// <![CDATA[

jQuery(document).ready(function() {
    jQuery("#start_date").datepicker({ 
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        onClose: function(selectedDate){
            jQuery("#end_date").datepicker("option", "minDate", selectedDate);
        }
    });
    jQuery("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        onClose: function(selectedDate){
            jQuery("#start_date").datepicker("option", "maxDate", selectedDate);
        }
    });
});
// ]]>