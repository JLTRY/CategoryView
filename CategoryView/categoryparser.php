<?php
/**
 * 
 */

require_once(dirname(__FILE__) . "/../../../weave/favoritessync/readsync.body.php");
require_once(dirname(__FILE__) . "/CategoryView.php");

// Take credit for your work.
$wgExtensionCredits['parserhook'][] = array(
 
   // The full path and filename of the file. This allows MediaWiki
   // to display the Subversion revision number on Special:Version.
   'path' => __FILE__,
 
   // The name of the extension, which will appear on Special:Version.
   'name' => 'Category Parser Function',
 
   // A description of the extension, which will appear on Special:Version.
   'description' => 'Category parser',
 
   // Alternatively, you can specify a message key for the description.
   //'descriptionmsg' => 'exampleextension-desc',
 
   // The version of the extension, which will appear on Special:Version.
   // This can be a number or a string.
   'version' => 1, 
 
   // Your name, which will appear on Special:Version.
   'author' => 'Me',
 
   // The URL to a wiki page/web page with information about the extension,
   // which will appear on Special:Version.
   'url' => 'http://www.jltryoen.fr',
 
);


$wgHooks['ParserFirstCallInit'][] = "CategoryParserSetup";
function CategoryParserSetup(Parser &$parser) {
   	$parser->setFunctionHook( 'category', 'categoryviewRenderFunction' );
	return true;
}

// Allow translation of the parser function name
$wgExtensionMessagesFiles['category'] = dirname( __FILE__ ) . '/categoryparser.i18n.php';
 

function categoryviewrender($context, $options, &$input)
{		
	$cw = new myCategoryViewer( Title::newFromText( $options['title'], NS_CATEGORY ) , $context);
	$cw->settitle($options['title']);
	$cw->clearCategoryState();
	$s = $cw->getSubcategorySectionoptions($options); 
	$input = $s;	
}

function categoryviewRenderFunction( $parser ) {
	//Suppose the user invoked the parser function like so:
	//{{#myparserfunction:foo=bar|apple=orange}}	
	$opts = array();
	// Argument 0 is $parser, so begin iterating at 1
	for ( $i = 1; $i < func_num_args(); $i++ ) {
			$opts[] = func_get_arg( $i );
	}
	//The $opts array now looks like this:
	//      [0] => 'foo=bar'
	//      [1] => 'apple=orange'

	//Now we need to transform $opts into a more useful form...
	$options = extractOptions( $opts );
    
	if (!array_key_exists('output', $options))	
		$options['output'] = 'css3treeview';	
	if (!array_key_exists('title', $options))	
		$options['title'] = 'Informatique';	
		
	categoryviewrender(  RequestContext::getMain(), $options, $input);
	if (($options['output'] == 'mediawiki')
		||($options['output'] == 'link')
		|| ($options['output'] == 'title')
		|| ($options['output'] == 'wikiimagelink'))
		return array( $input, 'noparse' => false, 'isHTML' => false);    	
	else	
		return array( $input, 'noparse' => true, 'isHTML' => true ,"markerType" => 'nowiki');    	
}
?>