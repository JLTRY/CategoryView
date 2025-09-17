<?php

namespace MediaWiki\Extension\CategoryView\Hooks\Handlers;
require_once(dirname(__FILE__) . "/../../../weave/favoritessync/mediawiki.php");

use MediaWiki\Page\CategoryPage;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MediaWiki\Category\CategoryViewer as baseCategoryViewer;
use MediaWiki\Category\Category;
use MediaWiki\Page\Hook\ArticleFromTitleHook;



class myCategoryPage extends CategoryPage {
	protected $mCategoryViewerClass = 'MediaWiki\Extension\CategoryView\Hooks\Handlers\CategoryViewer';
}

class CategoryViewer extends baseCategoryViewer {
	public $htmlroot;
	
	function clearCategoryState() {
		parent::clearCategoryState();
	}

	function settitle() {
		//parent::clearCategoryState();
		print("settitle");
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
					$newdir = new \htmldirectory($dir,
									array('title' => $title->getText(),
										 'bmkUri' => $title->getlinkUrl()));
					$newcat = Category::newFromTitle( $title );
					$this->addmembercategory($newdir, $newcat);
				}
				else
				{
					$services = MediaWikiServices::getInstance();
					$restrictionStore = $services->getRestrictionStore();
					if (! $restrictionStore->getRestrictions( $title,  'read' ))
						new \htmlfile($dir, array('title' => $title->getText(),
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
	function addPage( \MediaWiki\Page\PageReference $page, string $sortkey, int $pageLength, bool $isRedirect = false ): void {

	}

	function getSubcategorySectionoptions($options) {
		$s = '';
		$title = Title::newFromPageIdentity( $this->page );
		$cat = Category::newFromTitle($title);
		$htmlroot = new \htmlroot("", Null, $cat->getName(), '');
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
					$s .= '<h2> Pages de ' . $title .  '</h2>';
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
class ArticleFromTitle implements ArticleFromTitleHook {
    function onArticleFromTitle( $title, &$article, $context ) {
        if ( $title->getNamespace() == NS_CATEGORY ) {
            $article = new myCategoryPage( $title );
        }
	return true;
    }
}
?>