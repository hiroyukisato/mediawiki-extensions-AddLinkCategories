<?php
/**
 * Class AddLinkCategories
 *
 *
 */
class AddLinkCategories{

    public static function AddClass( $dummy, $target, &$html, &$attribs, &$query ) {
        if ($query) return true;
        error_log(print_r('hhh',true));
        $adc = new self();
        $category = array_keys($target->getParentCategories());
        $filter =  $adc->getWhitelist();
        if (isset($category)) {
            $class = (isset($attribs['class'])) ? $attribs['class'] . ' ' : '';
            $attribs['class'] = $class . str_replace(array(':', '/'), '-', implode(' ',array_intersect($category,$filter)));
        }
        return true;
    }


    /**
     * ホワイトリストを取得
     *
     * @return array
     */
    private function getWhitelist(){
        global $wgMemc;
        static $list = false;

        if ( $list !== false ) {
            return $list;
        }

        $key = wfMemcKey( 'AddLinkCategories', 'whitelist' );
        $list = $wgMemc->get( $key );

        if ( $list !== false ) {
            return $list;
        }


        $list = [];
        $list = $this->getPagelinks('MediaWiki:AddLinkCategories-Whitelist');
        $wgMemc->set( $key, $list, 60 * 60 );

        return $list;
    }


    /**
     * ページからリンクしているカテゴリページを取得
     *
     * @param $page string ページ名
     *
     * @return array []名前空間付きカテゴリページ名
     *
     */
    private function getPagelinks($page ) {
        global $wgContLang;
        $dbr = wfGetDB( DB_SLAVE, array(), false);
        $title = Title::newFromText( $page );
        $list = [];
        $id = $dbr->selectField(
            'page',
            'page_id',
            array( 'page_namespace' => $title->getNamespace(), 'page_title' => $title->getDBkey() ),
            __METHOD__
        );

        if ( $id ) {

            $res = $dbr->select( 'pagelinks',
                'pl_title',
                array( 'pl_from' => $id, 'pl_namespace' => NS_CATEGORY),
                __METHOD__
            );

            foreach ($res as $row) {
                $list[] = $wgContLang->getNsText(NS_CATEGORY) . ':' . $row->pl_title;
            }

        }
        return $list;
    }
}