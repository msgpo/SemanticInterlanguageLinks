<?php

namespace SIL\Tests;

use SIL\HookRegistry;
use Title;

/**
 * @covers \SIL\HookRegistry
 * @group semantic-interlanguage-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit_Framework_TestCase {

	private $cache;
	private $store;

	protected function setUp() {
		parent::setUp();

		$this->store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->cache = $this->getMockBuilder( '\Onoi\Cache\Cache' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SIL\HookRegistry',
			new HookRegistry( $this->store, $this->cache, 'foo' )
		);
	}

	public function testRegister() {

		$title = Title::newFromText( __METHOD__ );

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new HookRegistry( $this->store, $this->cache, 'foo' );
		$instance->register();

		$this->doTestParserFirstCallInit( $instance, $parser );
		$this->doTestNewRevisionFromEditComplete( $instance );
		$this->doTestSkinTemplateGetLanguageLink( $instance );
		$this->doTestPageContentLanguage( $instance );
		$this->doTestArticleFromTitle( $instance );
		$this->doTestParserAfterTidy( $instance, $parser );

		$this->doTestInitProperties( $instance );
		$this->doTestSQLStoreBeforeDeleteSubjectCompletes( $instance );
		$this->doTestSQLStoreBeforeChangeTitleComplete( $instance );

		$this->doTestSpecialSearchProfiles( $instance );
		$this->doTestSpecialSearchProfileForm( $instance );
		$this->doTestSpecialSearchResults( $instance );
		$this->doTestSpecialSearchPowerBox( $instance );
	}

	public function doTestParserFirstCallInit( $instance, $parser ) {

		$handler = 'ParserFirstCallInit';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$parser )
		);
	}

	public function doTestNewRevisionFromEditComplete( $instance ) {

		$handler = 'NewRevisionFromEditComplete';

		$title = Title::newFromText( __METHOD__ );

		$wikipage = $this->getMockBuilder( '\WikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$wikipage->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $wikipage )
		);
	}

	public function doTestSkinTemplateGetLanguageLink( $instance ) {

		$handler = 'SkinTemplateGetLanguageLink';

		$title = Title::newFromText( __METHOD__ );
		$languageLink = array();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$languageLink, $title, $title )
		);
	}

	public function doTestPageContentLanguage( $instance ) {

		$handler = 'PageContentLanguage';
		$pageLang = '';

		$title = Title::newFromText( __METHOD__ );

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $title, &$pageLang )
		);
	}

	public function doTestArticleFromTitle( $instance ) {

		$handler = 'ArticleFromTitle';

		$title = Title::newFromText( __METHOD__ );
		$page = '';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $title, &$page )
		);
	}

	public function doTestParserAfterTidy( $instance, $parser ) {

		$handler = 'ParserAfterTidy';
		$text = '';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$parser, &$text )
		);
	}

	public function doTestInitProperties( $instance ) {

		$handler = 'SMW::Property::initProperties';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array()
		);
	}

	public function doTestSQLStoreBeforeDeleteSubjectCompletes( $instance ) {

		$handler = 'SMW::SQLStore::BeforeDeleteSubjectComplete';
		$title = Title::newFromText( __METHOD__ );

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $this->store, $title )
		);
	}

	public function doTestSQLStoreBeforeChangeTitleComplete( $instance ) {

		$handler = 'SMW::SQLStore::BeforeChangeTitleComplete';
		$title = Title::newFromText( __METHOD__ );

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $this->store, $title, $title, 0, 0 )
		);
	}

	public function doTestSpecialSearchProfiles( $instance ) {

		$handler = 'SpecialSearchProfiles';
		$profiles = array();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$profiles )
		);
	}

	public function doTestSpecialSearchProfileForm( $instance ) {

		$handler = 'SpecialSearchProfileForm';

		$search = $this->getMockBuilder( '\SpecialSearch' )
			->disableOriginalConstructor()
			->getMock();

		$form = '';
		$profile = '';
		$term = '';
		$opts = array();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $search, &$form, $profile, $term, $opts )
		);
	}

	public function doTestSpecialSearchResults( $instance ) {

		$handler = 'SpecialSearchResults';

		$search = $this->getMockBuilder( '\SpecialSearch' )
			->disableOriginalConstructor()
			->getMock();

		$titleMatches = false;
		$textMatches = false;

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $search, &$titleMatches, &$textMatches )
		);
	}

	public function doTestSpecialSearchPowerBox( $instance ) {

		$handler = 'SpecialSearchPowerBox';
		$showSections = array();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$showSections, '', array() )
		);
	}

	private function assertThatHookIsExcutable( \Closure $handler, $arguments ) {
		$this->assertInternalType(
			'boolean',
			call_user_func_array( $handler, $arguments )
		);
	}

}
