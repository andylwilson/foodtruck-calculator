<?php
/**
 * foodtruck.php, based on demo_postback_nohtml.php, is a single page web application that allows us to request
 * items from a food truck, select quantities, and displays an order total
 *
 * Any number of additional steps or processes can be added by adding keywords to the switch 
 * statement and identifying a hidden form field in the previous step's form:
 *
 *<code>
 * <input type="hidden" name="act" value="next" />
 *</code>
 * 
 * The above live of code shows the parameter "act" being loaded with the value "next" which would be the 
 * unique identifier for the next step of a multi-step process
 *
 * @package ITC250
 * @author Andy Wilson, David Young, Philip Zhu, Ian Beard
 * @version 1.0 2/20/2019
 * @link http://www.andycodes.net/itc250-app/foodtruck/foodtruck.php
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 */

require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
include 'items.php'; 
/*
$config->metaDescription = 'Web Database ITC281 class website.'; #Fills <meta> tags.
$config->metaKeywords = 'SCCC,Seattle Central,ITC281,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->loadhead = ''; #load page specific JS
$config->banner = ''; #goes inside header
$config->copyright = ''; #goes inside footer
$config->sidebar1 = ''; #goes inside left side of page
$config->sidebar2 = ''; #goes inside right side of page
$config->nav1["page.php"] = "New Page!"; #add a new page to end of nav1 (viewable this page only)!!
$config->nav1 = array("page.php"=>"New Page!") + $config->nav1; #add a new page to beginning of nav1 (viewable this page only)!!
*/
//END CONFIG AREA ----------------------------------------------------------

# Read the value of 'action' whether it is passed via $_POST or $_GET with $_REQUEST
if(isset($_REQUEST['act'])){$myAction = (trim($_REQUEST['act']));}else{$myAction = "";}

switch ($myAction) 
{//check 'act' for type of process
	case "display": # 2)Display user's name!
	 	showData();
	 	break;
	default: # 1)Ask user to enter their name 
	 	showForm();
}

function showForm()
{# shows form so user can enter their name.  Initial scenario
	global $config;
    get_header(); #defaults to header_inc.php	
	
	echo 
	'<script type="text/javascript" src="' . VIRTUAL_PATH . 'include/util.js"></script>
	<script type="text/javascript">
		function checkForm(thisForm)
		{//check form data for valid info
			if(empty(thisForm.YourName,"Please Enter Amount Of Order")){return false;}
			return true;//if all is passed, submit!
		}
	</script>
	<h3 align="center">' . smartTitle() . '</h3>
	<p align="center">Welcome to Big Dog\'s Hot Dogs! What would you like to order?</p> 
	<form action="' . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">
    ';
      
	foreach($config->items as $item)
    {            
        echo '<p><b>' . $item->Name . '</b> <input type="text" name="item_' . $item->ID . '" /> ($' . $item->Price . ')<br/>' . $item->Description . '</p>';     
    }       
        echo '
			    <p>
					<input type="submit" value="Please Enter Amount Of Order"><em> (<font color="red"><b>*</b> required field</font>)</em>
				</p>
		        <input type="hidden" name="act" value="display" />
	         </form>';
	get_footer(); #defaults to footer_inc.php
}

function showData()
{#form submits here we show entered name
	
    get_header(); #defaults to footer_inc.php
		
	echo '<h3 align="center">' . smartTitle() . '</h3>';
	
    $grandSubTotal = 0;
    
	foreach($_POST as $name => $value)
    {//loop the form elements
        
        //if form name attribute starts with 'item_', process it
        if(substr($name,0,5)=='item_')
        {
            //explode the string into an array on the "_"
            $name_array = explode('_',$name);

            //id is the second element of the array
			//forcibly cast to an int in the process
            $id = (int)$name_array[1];
            
            $thisItem = getItem($id);
            
            $subTotal = $value * $thisItem->Price;
            $grandSubTotal += $subTotal;
            
            echo "<p>You ordered $value $thisItem->Name(s) which is $subTotal!</p>";           
        }             
    }
    $tax = $grandSubTotal * .11;
    $grandTotal = $grandSubTotal + $tax;
    echo "Your subtotal comes to $$grandSubTotal plus tax ($" . number_format($tax,2) . ") for a grand total of $" . number_format($grandTotal,2);
    /*
    $totalTaxed = $grandSubTotal * .11;
    setlocale(LC_MONETARY,"en_US");
    echo money_format("The price is %i", $totalTaxed);*/
				
	echo '<p align="center"><a href="' . THIS_PAGE . '">RESET</a></p>';
	get_footer(); #defaults to footer_inc.php    
}

function getItem($id)
{
    global $config;
    
    /*
    we need to loop the items
    if we find the item with an id that matches
    the argument, return the object        
    */

    foreach($config->items as $item)
    {
    //echo "<p>ID:$item->ID  Name:$item->Name</p>"; 
    //echo '<p>Taco <input type="text" name="item_1" /></p>';
        if((int)$id == (int)$item->ID)
        {// bingo! this is our item, return it
            return $item;            
        }

    }     
}//end of foreach
?>