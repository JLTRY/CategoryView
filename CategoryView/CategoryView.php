<?php

require_once(dirname(__FILE__) . "/../../../weave/favoritessync/readsync.body.php");
$wgHooks['ArticleFromTitle'][] = 'myCategoryTreeArticleFromTitle';



class myCategoryPage extends CategoryPage {
	protected $mCategoryViewerClass = 'myCategoryViewer';
}

class myCategoryViewer extends CategoryViewer {
	public $htmlroot;
	
	function clearCategoryState() {
		parent::clearCategoryState();		
							
	}
	
	function settitle() {
		//parent::clearCategoryState();		
							
	}
	/**
	 * Add a subcategory to the internal lists, using a Category object
	 * @param Category $cat
	 * @param string $sortkey
	 * @param int $pageLength
	 */
	function addSubcategoryObject( Category $cat, $sortkey, $pageLength ) {		
	}	
	
	function addmembercategory($dir, $cat)
	{		
		if (is_object($cat))
		{
			$it = $cat->getMembers();
			while ($it->valid())
			{
				$key = $it->key();
				$title = $it->current();
				if ( $title->getNamespace() == NS_CATEGORY ) 
				{
					$newdir = new htmldirectory($dir,
									array('title' => $title->getText(),
										 'bmkUri' => $title->getlinkUrl()));
					$newcat = Category::newFromTitle( $title );
					$this->addmembercategory($newdir, $newcat);
				}	
				else	
				{
					if ($title->userCan( 'read' ))
						new htmlfile($dir, array('title' => $title->getText(),
											'bmkUri' => $title->getlinkUrl()));
				}							
				$it->next();
			}	
		}	
	}
	
	
	/**
	 * Add a miscellaneous page
	 * @param Title $title
	 * @param string $sortkey
	 * @param int $pageLength
	 * @param bool $isRedirect
	 */
	function addPage( MediaWiki\Page\PageReference $page, string $sortkey, int $pageLength, bool $isRedirect = false ): void {
								
	}
	
	function getSubcategorySectionoptions($options) {
		$s = '';		
		$title = $this->title;
		$cat = Category::newFromTitle($title);
		$htmlroot = new htmlroot("", Null, $cat->getName(), '');	
		$this->addmembercategory($htmlroot, $cat);
		
		$opts = array('short' =>'false', 'maxlevel' => 9999, 'showkeyword' => 'false', 
					'openlevel' => 9999, 'minlevel' => 0, 'type' => 'all', 'div' => 'grebox-jck', 'showtitle' => 'true');
		foreach ($opts as $opt => $key)
			if (array_key_exists($opt, $options))
				${$opt} = $options[$opt];		
			else
				$$opt = $key;
		if (count($htmlroot->getchildren()))
		{		
			$s .= '<link rel="stylesheet" type="text/css" href="/favorites/css/css3treeview.css">';
			$s .= '<div class="' . $div . '">';			
			if ($showtitle != 'false')
			{				
				if ($htmlroot->getlevel()> 1)
					$s .= '<h2> Sous Catégories </h2>';
				else
					$s .= '<h2> Pages de ' . $this->title .  '</h2>';
			}		
			$s .= '<div class="css-treeview">';
			$htmlroot->writecss3treeview(0, $s, ($short == 'true'), $maxlevel, ($showkeyword == 'true'), $openlevel, $minlevel, $type);			
			$s .= '</div>';
			$s .='</div>';
		}
		return $s;		
	}
	
	function getSubcategorySection()
	{
		return $this->getSubcategorySectionoptions(array());
	}	
	
	function getPagesSection() {		
		return parent::getPagesSection();
	}
	
}

/**
 * ArticleFromTitle hook, override category page handling
 *
 * @param $title Title
 * @param $article Article
 * @return bool
 */
function myCategoryTreeArticleFromTitle( $title, &$article ) {
	if ( $title->getNamespace() == NS_CATEGORY ) {
		$article = new myCategoryPage( $title );			
	}	
	return true;
}
?>