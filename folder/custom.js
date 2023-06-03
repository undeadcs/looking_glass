//
var g_bLoading = false;

function ShowLoading( ) {
    if ( g_bLoading ) {
	if ( $( '.got_result' ).length == 0 ) {
	    setTimeout( 'ShowLoading( )', 1000 );
	} else {
	    $( "#loading" ).hide( );
	}
    } else {
	if ( $( '.got_result' ).length == 0 ) {
    		g_bLoading = true;
    		$( "#loading" ).show( );
    		setTimeout( 'ShowLoading( )', 1000 );
	}
    }
}

$( document ).ready( function( ) {
    	$( window ).load( function( ) {
    	    if ( g_bLoading ) {
    		$( "#loading" ).hide( );
    	    }
    	} );
	$( "input", "#rad_list" ).click( function( ) {
		var tmp = $( this ).val( );
		if ( tmp == "whois" ) {
			if ( !sel.disabled ) {
				$( "#agent" ).attr( "disabled", "disabled" );
				sel.disabled = true;
			}
		} else {
			if ( sel.disabled ) {
				$( "#agent" ).removeAttr( "disabled" );
				sel.disabled = false;
			}
		}
		if ( tmp == "bgp" ) {
			if ( sel.disabled2 ) {
				$( "#flt" ).removeAttr( "disabled" );
				$( "#reg" ).removeAttr( "disabled" );
				$( "#msk" ).removeAttr( "disabled" );
				sel.disabled2 = false;
			}
		} else {
			if ( !sel.disabled2 ) {
				$( "#flt" ).attr( "disabled", "disabled" );
				$( "#reg" ).attr( "disabled", "disabled" );
				$( "#msk" ).attr( "disabled", "disabled" );
				sel.disabled2 = true;
			}
		}
		return true;
	} );
	$( "#date1" ).date_input( prepareDateInput( ) );
} );

function prepareDateInput( ) {
	DateInput.prototype.stringToDate = function( string ) {
		var matches;
		//if (matches = string.match(/^(\d{1,2})-([^\s]+)-(\d{4,4})$/)) return new Date(matches[3], this.shortMonthNum(matches[2]), matches[1]);
		if (matches = string.match(/^(\d{4,4})-([^\s]+)-(\d{1,2})$/)) return new Date(matches[1], this.shortMonthNum(matches[2]), matches[3]);
		else return null;
	};
	  
	DateInput.prototype.dateToString = function(date) {
		var d = date.getDate()+'';
		if (d.length == 1) d = '0'+d;
		return date.getFullYear() + "-" + this.short_month_names[date.getMonth()] + "-" + d ;
	};
	
	var opts = { short_month_names: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"] };
	opts.month_names = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
	opts.short_day_names = ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"];
	return opts;
}
