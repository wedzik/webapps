/* form-validation.js v2.0 */

// errorMessages in different languages
var errorMessages = {
	en: {
		errorPreText:		'There were some invalid elements in your form:',
		errorPostText:		'Make the necessary corrections in order to proceed.',

		emptyRequired:		'This field is required!',
		emptyRequiredList:	'The following fields are required:',

		tooShort:		'The input should be at least {1} characters long.',
		tooShortList:		'The following fields are too short:',

		tooLong:		'The input should be at most {1} characters long.',
		tooLongList:		'The following fields are too long:',

		notANumber:		'This field requires a numerical value!',
		notANumberList:		'The following fields require a numerical value:',

		tooSmall:		'This number should be at least {1}.',
		tooSmallList:		'The following fields require a bigger value:',

		tooBig:			'This number should be at most {2}.',
		tooBigList:		'The following fields require a smaller value:',

		notADate:		'This field should represent a date value!',
		notADateList:		'The following fields should represent a date:',
                
                notAEmail:		'Wrong email adresse {1}.',
		notAEmailList:		'The following fields should represent a email:',                
	},
	nl: {
		errorPreText:		'Het ingevulde formulier bevat de volgende ongewenste waardes:',
		errorPostText:		'Maak de gewenste correcties om door te gaan.',

		emptyRequired:		'Dit veld is verplicht!',
		emptyRequiredList:	'De volgende velden zijn verplicht:',

		tooShort:		'Dit veld moet minimaal {1} karakters lang zijn.',
		tooShortList:		'De volgende veld(en) bevatten te weinig karakters:',

		tooLong:		'Dit veld mag maximaal {1} karakters lang zijn.',
		tooLongList:		'De volgende veld(en) bevatten te veel karakters:',

		notANumber:		'Dit veld moet een nummer zijn.',
		notANumberList:		'De volgende velden moeten nummers zijn:',

		tooSmall:		'Dit nummer moet minimaal {1} zijn.',
		tooSmallList:		'De volgende velden moeten een hogere waarden hebben:',

		tooBig:			'Dit nummer moet minimaal {2} zijn.',
		tooBigList:		'De volgende velden vereisen een lagere ingevulde waarde:',

		notADate:		'De waarde in dit veld wordt niet herkend als een datum.',
		notADateList:		'De volgende velden moeten datums zijn:',
                
        notAEmail:		'Verkeerde e-adresse {1}.',
		notAEmailList:		'De volgende velden moeten email zijn:',                                
	}
};

// A utility function that returns true if a string contains only
// whitespace characters.
function isblank(s)
{
	return (!s || s.trim() == "");
}

function isRequired(e)
{
	return e.name.match(/\*$/) ? true : false;
}

function getRadioValue( radiogroup )
{
	// radiogroup is the array of radio objects
	for( var i=0; i<radiogroup.length; i++ ) {
		if( radiogroup[i].checked )
			return radiogroup[i].value; // return selected option's value.
	}
	return "none"; // none were selected.
}

function getSelectValue( select )
{
	var val = select.value || '';

	if (val == '' || val == '*')
		return 'none';

	return val;
}

function getFieldName( f )
{
	if (f.FriendlyName) return f.FriendlyName;
        if (f.title != "")
            return f.title.replace(/([A-Z])/g, ' $1').replace(/\*$/, '');
        else
            return f.name.replace(/([A-Z])/g, ' $1').replace(/\*$/, '');            
}

// This is the function that performs form verification. It will be invoked
// from the onSubmit() event handler. The handler should return whatever
// value this function returns.
function verify(frm, form_id){
	var f;
        for(cur_form in document.forms) {
            if (form_id == document.forms[cur_form].id) {
                f = document.forms[cur_form];
            }
        }
	var msg;
	var lng = f.getAttribute('lang') || 'en';
	var empty_fields = "";
	var errors = "";
	var focusfield = null;
	var errorFields = {
		emptyRequired: [],
		tooShort:	[],
		tooLong:	[],
		notANumber: [],
		tooSmall:	[],
		tooBig:		[],
		notADate:	[],
		notAEmail:	[]                
	};

	var warn_focusfield = null;
	var empty_warn_count = 0;
	var error_count = 0;
	// Loop through the elements of the form, looking for all
	// text and textarea elements that have a "required=true" property
	// defined. Then, check for fields that are empty and make a list of them.
	// Also, if any of these elements have a "min" or a "max" property defined,
	// then verify that they are numbers and that they are in the right range.
	// Put together error messages for fields that are wrong.
	for(var i = 0; i < f.elements.length; i++) {
		var e = f.elements[i];
		var fieldError = false
		if ( (e.type == "text") || (e.type == "textarea") || (e.type == "password")) {
			if( isRequired(e) ) {
				// first check if the field is empty
				if (isblank(e.value)) {
					error_count++;
					fieldError = errorMessages[lng].emptyRequired;

					// a required field
					errorFields.emptyRequired.push(getFieldName(e));
					// mark this field to receive focus if we haven't already picked a field.
					focusfield = focusfield || e;
				}
			}

			// Check for minimum length requirements
			if( !fieldError && e.getAttribute('minlength') && e.value.length < e.getAttribute('minlength') ) {
				error_count++;
				fieldError = errorMessages[lng].tooShort
						.replace('{0}', getFieldName(e))
						.replace('{1}', e.getAttribute('minlength'));

				errorFields.tooShort.push(getFieldName(e) +" (min. "+ e.getAttribute("minlength") +")");

				// mark this field to receive focus if we haven't already picked a field.
				focusfield = focusfield || e;
			}

			// Check for maximum length requirements
			if( !fieldError && e.getAttribute('maxlength') && e.value.length > e.getAttribute('maxlength') ) {
				error_count++;
				fieldError = errorMessages[lng].tooLong
								.replace('{0}', getFieldName(e))
								.replace('{1}', e.getAttribute('maxlength'));

				errorFields.tooShort.push(getFieldName(e) +" (max. "+ e.getAttribute("maxlength") +")");

				// mark this field to receive focus if we haven't already picked a field.
				focusfield = focusfield || e;
			}

			// Now check for fields that are supposed to be numeric.
			if ( !fieldError && (e.getAttribute('numeric') || e.getAttribute('min') || e.getAttribute('max')) ) {
				var v = parseFloat(e.value);
				if (isNaN(v)) {
					fieldError = errorMessages[lng].notANumber;
					errorFields.notANumber.push(getFieldName(e));
				} else if (e.getAttribute('min') && (v < e.getAttribute('min'))) {
					fieldError = errorMessages[lng].tooSmall;
					errorFields.tooSmall.push(getFieldName(e) +" (min. "+ e.getAttribute("min") +")");
				} else if (e.getAttribute('max') && (v > e.getAttribute('max'))) {
					fieldError = errorMessages[lng].tooBig;
					errorFields.tooBig.push(getFieldName(e) +" (max. "+ e.getAttribute("max") +")");
				}
				if (fieldError) {
					error_count++;
					fieldError = fieldError.replace('{0}', getFieldName(e))
									.replace('{1}', e.getAttribute('min'))
									.replace('{2}', e.getAttribute('max'));

					focusfield = focusfield || e;
				}
			}

			// Now check for fields that are supposed to be dates.
			if ( !fieldError && e.getAttribute('calendardate')) {
				var v = Date.parse(e.value);
				if( !isblank(e.value) && isNaN(v) ) {
					error_count++;
					fieldError = errorMessages[lng].notADate.replace('{0}', getFieldName(e));
					errorFields.notADate.push(getFieldName(e));

					// mark this field to receive focus if we haven't already picked a field.
					focusfield = focusfield || e;
				}
			}
            if ( !fieldError && e.getAttribute('email')) {
				var v = Date.parse(e.value);
                                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if( !re.test(e.value)) {
					error_count++;
					fieldError = errorMessages[lng].notAEmail.replace('{0}', getFieldName(e));
					errorFields.notAEmail.push(getFieldName(e));

					// mark this field to receive focus if we haven't already picked a field.
					focusfield = focusfield || e;
				}
			}                        
		}

		if ( !fieldError && isRequired(e) && (e.type == "radio") && f.elements[e.name][0] == e ) {
			// check to see if any radio option has been selected
			if (getRadioValue( f.elements[e.name] ) == "none") {
				error_count++;
				fieldError = errorMessages[lng].emptyRequired.replace('{0}', getFieldName(e));

				errorFields.emptyRequired.push(getFieldName(e));


				// mark this field to receive focus if we haven't already picked a field.
				focusfield = focusfield || e;
			}
		}

		if ( !fieldError && isRequired(e) && (e.nodeName.toLowerCase() == "select")) {
			// check to see if any radio option has been selected
			if (getSelectValue( f.elements[e.name] ) == "none") {
				error_count++;
				fieldError = errorMessages[lng].emptyRequired.replace('{0}', getFieldName(e));

				errorFields.emptyRequired.push(getFieldName(e));


				// mark this field to receive focus if we haven't already picked a field.
				focusfield = focusfield || e;
			}
		}

		//e.title = fieldError || "";
		classname = e.getAttribute('class') || '';
		e.className = classname.replace(/\s?error$/,'');
		var errorBox = document.getElementById(e.name+"errorBox");
		if (errorBox) {
			errorBox.parentNode.removeChild(errorBox);
		}
		if (fieldError) {
			e.className = e.className + " error";
			errorBox = document.createElement("span");
			errorBox.id = e.name+"errorBox";
			errorBox.innerHTML = "<a href=\"#\" onclick='alert(\""+fieldError+"\");return false;' title='"+fieldError+"' class=\"form-error\">* [?]</a>";
			e.parentNode.appendChild(errorBox);
		}
	}

	// Now, if there were any errors, display the messages, and
	// return false to prevent the form from being submitted.
	// Otherwise return true.
	if (!empty_warn_count && !error_count) return true;

	msg = "";
	if( error_count ) { // don't display this msg if only optional fields were left blank
		msg += errorMessages[lng].errorPreText+"\n";
		msg += "__________________________________________________\n\n";
	}

	for (type in errorFields) {
		if (errorFields[type].length) {
			msg += errorMessages[lng][type+"List"] + "\n";
			for (i in errorFields[type]) {
				msg += "- " + errorFields[type][i] + "\n";
			}
			msg += "\n";
		}
	}

	if(focusfield) focusfield.focus();
	else if(warn_focusfield) warn_focusfield.focus();

	// If the user provided answers that were invalid, force him to go back.
	// But if the user merely left answers blank, allow him to continue if he wants.
	if( error_count ) { // If there were invalid answers or required blanks
		msg += "__________________________________________________\n\n";
		msg += errorMessages[lng].errorPostText;
		alert(msg);
		return false;
	}
}

function domSubmit(source, form_id) {
    return verify(source, form_id);
}
// This next line intentionally left as a comment with no line break at the end
//