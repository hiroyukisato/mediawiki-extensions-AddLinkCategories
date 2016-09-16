<?php

if ( function_exists( 'wfLoadExtension' ) ) {
    wfLoadExtension( 'AddLinkCategories' );

    $wgMessagesDirs['AddLinkCategories'] = __DIR__ . '/i18n';

    return;
} else {
    die( 'This version of the AddLinkCategories extension requires MediaWiki 1.25+' );
}