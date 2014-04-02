<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * Articles Module Html View
 *
 * @author  Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package Component\Articles
 */
class ArticlesModuleArticlesHtml extends PagesModuleDefaultHtml
{
    protected function _fetchData(Library\ViewContext $context)
    {
        $params = $this->module->getParameters();

        // Preparing the sort and direction model states.
        switch ($params->get('sort_by', 'newest'))
        {
            default:
            case 'newest':
                $sort      = 'created_on';
                $direction = 'DESC';
                break;
            case 'oldest':
                $sort      = 'created_on';
                $direction = 'ASC';
                break;
            case 'ordering':
                $sort      = 'ordering';
                $direction = 'ASC';
                break;
        }

        // Prepare category state.
        $category = str_replace(' ', '', $params->get('category', ''));
        if ($category) {
            $category = explode(',', $category);
        }

        // Prepare section state.
        $section = str_replace(' ', '', $params->get('section', ''));
        if ($section) {
            $section = explode(',', $section);
        }

        // Get access id.
        $user = $this->getObject('user');

        $articles = $this->getObject('com:articles.model.articles')
            ->set(array(
            'access'    => $user->isAuthentic(),
            'published' => 1,
            'limit'     => $params->get('count', 5),
            'sort'      => $sort,
            'direction' => $direction,
            'section'   => $section,
            'category'  => $category))
            ->fetch();

        $context->data->articles = $articles;

        // Set layout based on params.
        $this->setLayout($params->get('show_content', 0) ? 'articles' : 'links');

        parent::_fetchData($context);
    }
}