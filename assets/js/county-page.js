		
		var states = [
    { name: 'ALABAMA', abbreviation: 'AL'},
    { name: 'ALASKA', abbreviation: 'AK'},
    { name: 'AMERICAN SAMOA', abbreviation: 'AS'},
    { name: 'ARIZONA', abbreviation: 'AZ'},
    { name: 'ARKANSAS', abbreviation: 'AR'},
    { name: 'CALIFORNIA', abbreviation: 'CA'},
    { name: 'COLORADO', abbreviation: 'CO'},
    { name: 'CONNECTICUT', abbreviation: 'CT'},
    { name: 'DELAWARE', abbreviation: 'DE'},
    { name: 'DISTRICT OF COLUMBIA', abbreviation: 'DC'},
    { name: 'FEDERATED STATES OF MICRONESIA', abbreviation: 'FM'},
    { name: 'FLORIDA', abbreviation: 'FL'},
    { name: 'GEORGIA', abbreviation: 'GA'},
    { name: 'GUAM', abbreviation: 'GU'},
    { name: 'HAWAII', abbreviation: 'HI'},
    { name: 'IDAHO', abbreviation: 'ID'},
    { name: 'ILLINOIS', abbreviation: 'IL'},
    { name: 'INDIANA', abbreviation: 'IN'},
    { name: 'IOWA', abbreviation: 'IA'},
    { name: 'KANSAS', abbreviation: 'KS'},
    { name: 'KENTUCKY', abbreviation: 'KY'},
    { name: 'LOUISIANA', abbreviation: 'LA'},
    { name: 'MAINE', abbreviation: 'ME'},
    { name: 'MARSHALL ISLANDS', abbreviation: 'MH'},
    { name: 'MARYLAND', abbreviation: 'MD'},
    { name: 'MASSACHUSETTS', abbreviation: 'MA'},
    { name: 'MICHIGAN', abbreviation: 'MI'},
    { name: 'MINNESOTA', abbreviation: 'MN'},
    { name: 'MISSISSIPPI', abbreviation: 'MS'},
    { name: 'MISSOURI', abbreviation: 'MO'},
    { name: 'MONTANA', abbreviation: 'MT'},
    { name: 'NEBRASKA', abbreviation: 'NE'},
    { name: 'NEVADA', abbreviation: 'NV'},
    { name: 'NEW HAMPSHIRE', abbreviation: 'NH'},
    { name: 'NEW JERSEY', abbreviation: 'NJ'},
    { name: 'NEW MEXICO', abbreviation: 'NM'},
    { name: 'NEW YORK', abbreviation: 'NY'},
    { name: 'NORTH CAROLINA', abbreviation: 'NC'},
    { name: 'NORTH DAKOTA', abbreviation: 'ND'},
    { name: 'NORTHERN MARIANA ISLANDS', abbreviation: 'MP'},
    { name: 'OHIO', abbreviation: 'OH'},
    { name: 'OKLAHOMA', abbreviation: 'OK'},
    { name: 'OREGON', abbreviation: 'OR'},
    { name: 'PALAU', abbreviation: 'PW'},
    { name: 'PENNSYLVANIA', abbreviation: 'PA'},
    { name: 'PUERTO RICO', abbreviation: 'PR'},
    { name: 'RHODE ISLAND', abbreviation: 'RI'},
    { name: 'SOUTH CAROLINA', abbreviation: 'SC'},
    { name: 'SOUTH DAKOTA', abbreviation: 'SD'},
    { name: 'TENNESSEE', abbreviation: 'TN'},
    { name: 'TEXAS', abbreviation: 'TX'},
    { name: 'UTAH', abbreviation: 'UT'},
    { name: 'VERMONT', abbreviation: 'VT'},
    { name: 'VIRGIN ISLANDS', abbreviation: 'VI'},
    { name: 'VIRGINIA', abbreviation: 'VA'},
    { name: 'WASHINGTON', abbreviation: 'WA'},
    { name: 'WEST VIRGINIA', abbreviation: 'WV'},
    { name: 'WISCONSIN', abbreviation: 'WI'},
    { name: 'WYOMING', abbreviation: 'WY' }
];
		
		jQuery(function($){
			
			$(document).ready(function(){
			  $('#us-map').usmap({
			    'stateSpecificStyles': {

			    },
			    'stateSpecificHoverStyles': {

			    },

			    'mouseoverState': {
				
			    },
				'mouseover' : function(event,data) {
					var state = data;
					var events= event;
					for(var i=0;i<states.length;i++){
						if(state.name == states[i].abbreviation) $('#temp-state').html(states[i].name);
					}
				},
				'mouseout' :function(event,data) {
					$('#temp-state').html('...');	
				},

			    'click' : function(event, data) {
				   	var state = data;
				   	for(var i=0;i<states.length;i++){
						if(state.name == states[i].abbreviation) $('#state-name').html(states[i].name);
						if(state.name == states[i].abbreviation) $('#county-head').html(states[i].name);
					}
				    var transferdata = {
					    'action' : 'get_state',
					    'name' : state.name,
				    };
				    $.post(wpdata.ajax_url,transferdata,function(response){
				    	//alert(response);
					var data = JSON.parse(response);
					$('#states_counties').html('<div id="spin-load"><img src="'+wpdata.spinner+'"/></div>');
					for(var i=0;i<data.length;i++) {
						var transferdata = {
							'action' : 'get_county_snip',
							'post_id' : data[i].post_id,
							'count' : i+1,
						};
						$('#spin-load').html('<img src="'+wpdata.spinner+'"/>');
						$.post(wpdata.ajax_url,transferdata,function(response){
							$('#states_counties').prepend(response);
							$('#spin-load').html('');
						});
						
					}
					    
				    });
			    }
	  			});
			});
		});