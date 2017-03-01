<?php

# Initialization phase
define('FORM_ID','CONTACT_US');
$errors=Array();

$fields = array('first_name'=>Array('required'=>'*','type'=>'text','label'=>'First Name','validation'=>'first_name','value'=>'')
				,'last_name'=>Array('required'=>'*','type'=>'text','label'=>'Last Name','validation'=>'last_name','value'=>'')
				,'email'=>Array('required'=>'*','type'=>'text','label'=>'Email','validation'=>'email','value'=>'')
				,'phone'=>Array('required'=>'','type'=>'text','label'=>'Telephone','validation'=>'us_phone','value'=>'')
				,'category'=>Array('required'=>'*','type'=>'select','label'=>'Category','validation'=>'category','value'=>'Please make a selection', 'options'=>Array('Science'=>'option1','Math'=>'option2','Arts'=>'option3','Others'=>'option4','Please make a selection'=>''))
				,'questions'=>Array('required'=>'','type'=>'textarea','label'=>'Questions','validation'=>'','value'=>'Please type your questions here.','rows'=>'5', 'cols'=>'70')
			);
$scriptname = $_SERVER['SCRIPT_NAME'];
//$fields = getProfileData();//returns an assoc array of fields with actual data


# Initialization error checks
if( count($errors)>0 )
{
	handleError(FORM_ID,$errors,$fields);
}
else
{
	# Was form Posted?
	if( false===formPosted(FORM_ID) )
	{
		//null;# do nothing so that blank form shows up.
		headerhtml();
		showForm($fields,"version1");
		footer();
		
	}
	elseif(array_key_exists('Submit',$_POST) && 'CANCEL'==strtoupper($_POST['Submit']))
	{
			handleCancel($fields);
	}
	else# form was posted
	{
		# update the 'value' attribute of each field definition with whatever was POSTed
		foreach($fields as $k=>$v)
		{
			if( array_key_exists($k,$_POST) ) 
			{
				$fields[$k]['value'] = $_POST[$k];
			}
		}
				
		# do validation
		$errors = doValidation( $fields );
			
		# errors?
		if( count($errors)> 0 )
		{
			handleError(FORM_ID,$errors,$fields);			
		}
		else
		{
			$action= strtoupper($_POST['Submit']);
			if('SUBMIT'==$action)
			{
				headerhtml();
				showForm($fields,"version2");
				footer();
				exit;
			}
			elseif('EDIT'==$action)
			{
				headerhtml();
				showForm($fields,"version1.1");
				footer();
			exit;
			}
			elseif('FINISH'==$action)
			{
				processData($fields);			
			}
			else
			{
				//default case
				headerhtml();
				showform($fields,'');
				footer();
			}
		}
	}
}

function headerhtml()
{
	echo <<<htmldoc
		<!doctype html>
		<html>
			<head>
				<title> Contact Us </title>
				<!-- Latest compiled and minified CSS -->
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

				<!-- Optional theme -->
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

				<!-- Including jQuery javascipt -->
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

				<!-- Latest compiled and minified JavaScript -->
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
		
				<style type="text/css">
					label{width:7.5em;text-align:right;}
					div.textarea>label{display:block;float:left;}
					div.row{margin-bottom:1em;}
					div.row>label::after{content:" : ";}
					.col-md-3{background-color:rgba(231, 190, 84, 0.9);height:1000px;}
					legend{background-color:rgba(231, 190, 84, 0.1);color:black;display:block;margin-top:2em;font-weight:bolder;}
					h1 {
						position:center; color:black bold; font-style:normal; font-family:Georgia; font-size:40px; font-weight:bolder;
						}
					.mandatory {color:red; font-size:20px;}
				</style>
			</head>
			<body>
			<div class="col-md-12">
			<div class="col-md-3"> </div>
			<form class="col-md-6" method="post" action="{$_SERVER['SCRIPT_NAME']}">
			
htmldoc;
}

function footer()
{
	echo <<<htmlfooter
		<div class="col-md-3"> </div>
		</div>
		</body>
	</html>
htmlfooter;
}
					
function handleError($formId, array $errors, $fields){
	# do the error handling here
	headerhtml();
	foreach($errors as $error)
	{
		//echo "<pre>".print_r($error)."</pre>";
		echo sprintf('<div class="alert alert-danger" role="alert"><strong>Error!</strong> %s</div>',$error);
	}
	showform($fields,'version1');
	footer();	
}

function formPosted($form_id){
	//returns a boolean
	switch($form_id)
	{
		case 'CONTACT_US':
			return array_key_exists('Submit',$_POST);			
	}
return false;
}


function showForm(array $fields,$version)
{
	if($version==='version2')
	{
		//confirmation form with hidden fields
		echo <<<editview2
			<form class="col-md-6" method="post" action="{$_SERVER['SCRIPT_NAME']}">
				<fieldset><legend> Contact Us </legend>
				<div class="row"><Strong>&nbsp;&nbsp;&nbsp;&nbsp;NOTE:</strong>
					<ul>
						<li> Review the form details. Once done kindly click on Finish to complete or Edit to modify the details or you can exit anytime by clicking on Cancel.</li>
					</ul>
				</div>
editview2;

		foreach($fields as $fieldName=>$options)
		{
			switch($options['type'])
			{
				case 'text':
					echo sprintf('<div class="row"><label for="%s"><span class="mandatory">%s</span>%s</label><span>%s</span><input type="hidden" name="%s" id="%s" value="%s"/></div>',$fieldName, $options['required'], $options['label'], $options['value'], $fieldName, $fieldName, $options['value']);
					break;
					
				case 'textarea':
					echo sprintf('<div class="row"><label for="%s"><span class="mandatory">%s</span>%s</label><span>%s</span><input type="hidden" name="%s" id="%s" value="%s"/></div>',$fieldName, $options['required'], $options['label'], $options['value'], $fieldName, $fieldName, $options['value']);
					break;

				case 'select':
				echo sprintf('<div class="row"><label for="%s"><span class="mandatory">%s</span>%s</label> <span>%s</span><input type="hidden" name="%s" id="%s" value="%s"/></div>',$fieldName , $options['required'], $options['label'], $options['value'], $fieldName, $fieldName, $options['value'] );							
				break;
			}
			
		}	
		echo '<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="Submit" value="Edit"/>&nbsp;<input type="submit" name="Submit" value="Finish"/>&nbsp;<input type="submit" name="Submit" value="Cancel"/></div>';
	}
	else
	{
		echo <<<contactus
			<fieldset><legend> Contact Us </legend>
			<div class="row"><Strong>&nbsp;&nbsp;&nbsp;&nbsp;NOTE:</strong>
				<ul>
					<li> Enter the form details. Once done kindly click on Submit to move forward or Cancel to exit</li>
				</ul>
			</div>
contactus;
		foreach($fields as $fieldName=>$options)
		{
			switch($options['type'])
			{
				case 'text':
					echo sprintf('<div class="row"><label for="%s"><span class="mandatory">%s</span>%s</label><input type="text" name="%s" id="%s" value="%s"/></div>',$fieldName, $options['required'], $options['label'], $fieldName, $fieldName, $options['value']);
					break;
					
				case 'textarea':
					echo sprintf('<div class="row textarea"><label for="%s"><span class="mandatory">%s</span>%s</label><textarea name="%s" id="%s" rows="%d" cols="%d" placeholder="%s"></textarea></div>',$fieldName, $options['required'], $options['label'], $fieldName, $fieldName, $options['rows'], $options['cols'], $options['value']);
					break;

				case 'select':
					echo sprintf('<div class="row"><label for="%s"><span class="mandatory">%s</span>%s</label><select name="%s" id="%s">',$fieldName, $options['required'], $options['label'], $fieldName, $fieldName );
					foreach($options['options'] as $optionsname=>$optionsvalue)
					{
						if($optionsname==$options['value'])
						{
							echo sprintf('<option value="%s" selected>%s</option>', $optionsname, $optionsname);
						}
						else
						{
							echo sprintf('<option value="%s">%s</option>', $optionsname, $optionsname);
						}
					}						
					echo sprintf('</select></div>');
					break;
					
			}
			
		}
		echo '<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="Submit" value="Submit"/>&nbsp;<input type="submit" name="Submit" value="Cancel"/></div>';
		
	}
	echo '</fieldset></form>';
}

function doValidation(array $fields){
	$errors=Array();
	
	foreach($fields as $k=>$v)
	{
		switch($v['validation'])
		{
			case 'first_name':
				//here do actual check for valid person name
				$regex = "/^[A-Za-z ]*$/";
				
				if(empty($v['value']))
				{
					array_push($errors,'First Name is a required field');
				}
				elseif((preg_match($regex,$v['value']))==false)
				{
					array_push($errors,'First Name can contain only letters.');
				}
				break;
			
			case 'last_name':
				//here we check for valid last name		
				$regex = "/^[A-Za-z ]*$/";
				
				if(empty($v['value']))
				{
					array_push($errors,'Last Name is a required field');
				}
				elseif((preg_match($regex,$v['value']))==false)
				{
					array_push($errors,'Last Name can contain only letters.');
				}				
				break;
				
			case 'us_phone':
				//here do actual phone check
				$regex = "/^[1-9]{1}[0-9]{9}$/";
				
				if(empty($v['value']))
				{
					array_push($errors,'Phone is a required field');
				}
				elseif((preg_match($regex,$v['value']))==false)
				{
					array_push($errors,'Enter a valid phone number');
				}		
				break;
			
			case 'email':
				//here do the actual check for email
								
				$regex = "/^([a-zA-Z0-9_\-\.]+)@([a-z]*\.[a-z]{2,})(\.[a-z]{2})?$/";
				
				if(empty($v['value']))
				{
					array_push($errors,'Email is a required field');
				}
				elseif((preg_match($regex,$v['value']))==false)
				{
					array_push($errors,'Enter a valid email id');
				}
				break;
				
			case 'category':
				//here we do the actual check for category
				
				if($v['value']=='Please make a selection')
				{
					array_push($errors,'Please select a category');
				}
				break;
				
		}
		
	}
return $errors;
}

function handleCancel($fields)
{
	//redirect it to the initial form
	header('Location: /ContactUs/contactuswith1selection.php');
	exit;
	
}

function processData(array $fields)
{
	//Show thank you page
	$message = 'First Name: '.$fields['first_name']['value'].PHP_EOL .'Last Name: '.$fields['last_name']['value'].PHP_EOL .'Email: '.$fields['email']['value'].PHP_EOL .'Phone: '.$fields['phone']['value'].PHP_EOL .'Category: '.$fields['category']['value'].PHP_EOL .'Questions: '.$fields['questions']['value'];
	headerhtml();
	$headers = 'Reply-To: '.$_POST['email']."\r\n";
	$headers = 'From: Contact Us Form <reply.contactus.com>'."\r\n";
	if( mail("samare4@uic.edu", "ContactUsFormData", $message, $headers) )
	{
		echo <<<finishbody
		<div class="col-md-6">
			<h1 align="center">Thank you</h1>
		</div>
finishbody;

	}
	else
	{
		//Display technical difficulties page, mention admin address to inform him about the problem or store to a file.
		$headers = 'Reply-To: '.$_POST['email']."\r\n";
		$headers = 'From: Contact Us Form <reply.contactus.com>'."\r\n";
		$message = 'First Name: '.$fields['first_name']['value'].PHP_EOL .'Last Name: '.$fields['last_name']['value'].PHP_EOL .'Email: '.$fields['email']['value'].PHP_EOL .'Phone: '.$fields['phone']['value'].PHP_EOL .'Category: '.$fields['category']['value'].PHP_EOL .'Questions: '.$fields['questions']['value'];
		mail("samare4@uic.edu", "Technical Difficulty : ContactUsFormData", $message, $headers);
		
		echo <<<finishbody
			<div class="col-md-6">
				<h1 align="center">We are facing TECHNICAL DIFFICULTIES.<br> Sorry for the inconvenience. Please try again after sometime. </h1>
			</div>
finishbody;
	}
	footer();
}