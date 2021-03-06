<?php

class BlockCest {


	protected function _before( FunctionalTester $I ) {
		wp_cache_flush();
		global $wpdb;
		$wpdb->db_connect();
	}

	protected function _after( FunctionalTester $I ) {

	}

	public function testCreateResponse( FunctionalTester $I ) {
		$id    = wp_insert_post( [ 'post_type' => Brizy_Admin_Blocks_Main::CP_GLOBAL, 'post_title' => 'Test' ] );
		$block = Brizy_Editor_Block::get( $id );
		$data  = $block->createResponse();

		$I->assertArrayHasKey( 'uid', $data, "It should contain key 'uid'    " );
		$I->assertArrayHasKey( 'status', $data, "It should contain key 'status'    " );
		$I->assertArrayHasKey( 'data', $data, "It should contain key 'data'  " );
		$I->assertArrayHasKey( 'position', $data, "It should contain key 'position'  " );
		$I->assertArrayHasKey( 'rules', $data, "It should contain key 'rules'  " );
		$I->assertArrayHasKey( 'dataVersion', $data, "It should contain key 'dataVersion'  " );
	}

	/**
	 * @param FunctionalTester $I
	 *
	 * @throws Exception
	 */
	public function saveTest( FunctionalTester $I ) {

		$data         = base64_encode( 'test' );
		$data_decoded = 'test';

		$id = wp_insert_post( [ 'post_type' => Brizy_Admin_Blocks_Main::CP_GLOBAL, 'post_title' => 'Test' ] );

		$block = new Brizy_Editor_Block( $id );
		$block->setPosition( new Brizy_Editor_BlockPosition( "left", 1 ) );
		$block->set_editor_data( $data );
		$block->set_needs_compile( true );
		$block->set_uses_editor( false );
		$block->set_compiler_version( '1' );
		$block->set_editor_version( '2' );
		$block->setDataVersion( 1 );
		$block->save();

		$I->assertTrue( $block->uses_editor(), 'Block should always return true for uses_editor' );

		unset( $block );

		$block = new Brizy_Editor_Block( $id );

		$I->assertInstanceOf( "Brizy_Editor_BlockPosition", $block->getPosition(), "setPosition should return a Brizy_Editor_BlockPosition instance " );
		$I->assertEquals( $data_decoded, $block->get_editor_data(), "It should return decoded data" );
		$I->assertEquals( "1", $block->get_compiler_version() );
		$I->assertEquals( "2", $block->get_editor_version() );
	}

	/**
	 * @param FunctionalTester $I
	 *
	 * @throws Exception
	 */
	public function saveWrongDataVersionTest( FunctionalTester $I ) {

		$data         = base64_encode( 'test' );
		$data_decoded = 'test';

		$id = wp_insert_post( [ 'post_type' => Brizy_Admin_Blocks_Main::CP_GLOBAL, 'post_title' => 'Test' ] );

		$block = new Brizy_Editor_Block( $id );
		$block->setPosition( new Brizy_Editor_BlockPosition( "left", 1 ) );
		$block->set_editor_data( $data );
		$block->set_needs_compile( true );
		$block->set_uses_editor( false );
		$block->set_compiler_version( '1' );
		$block->set_editor_version( '2' );
		$block->setDataVersion( 4 );

		$I->expectThrowable( Brizy_Editor_Exceptions_DataVersionMismatch::class ,function() use($block) {
			$block->save();
		});
	}


	/**
	 * @param FunctionalTester $I
	 *
	 * @throws Exception
	 */
	public function autoSaveTest( FunctionalTester $I ) {

		$data         = base64_encode( 'test' );
		$data_decoded = 'test';

		$id    = wp_insert_post( [ 'post_type' => Brizy_Admin_Blocks_Main::CP_GLOBAL, 'post_title' => 'Test' ] );
		$block = new Brizy_Editor_Block( $id );
		$block->setPosition( new Brizy_Editor_BlockPosition( "left", 1 ) );
		$block->set_editor_data( $data );
		$block->set_compiler_version( '1' );
		$block->set_editor_version( '2' );
		$block->setDataVersion( 1 );
		$block->save( 1 );

		unset( $block );

		$block = new Brizy_Editor_Block( $id );

		$I->assertNotInstanceOf( "Brizy_Editor_BlockPosition", $block->getPosition(), "setPosition should return a Brizy_Editor_BlockPosition instance " );
		$I->assertNotEquals( $data_decoded, $block->get_editor_data(), "It should return decoded data" );
		$I->assertNotEquals( "1", $block->get_compiler_version() );
		$I->assertNotEquals( "2", $block->get_editor_version() );
	}

	/**
	 * @param FunctionalTester $I
	 *
	 * @throws Exception
	 */
	public function jsonSerializeTest( FunctionalTester $I ) {

		$data         = base64_encode( 'test' );
		$data_decoded = 'test';

		$id = wp_insert_post( [ 'post_type' => Brizy_Admin_Blocks_Main::CP_GLOBAL, 'post_title' => 'Test 2' ] );

		$block = new Brizy_Editor_Block( $id );
		$block->setPosition( new Brizy_Editor_BlockPosition( "left", 1 ) );
		$block->set_editor_data( $data );
		$block->setDataVersion( 1 );
		$block->save();

		unset( $block );

		$block = new Brizy_Editor_Block( $id );

		// getting data that is going to be in json string
		$json_serialize = $block->jsonSerialize();

		$I->assertTrue( isset( $json_serialize['rules'] ), 'Rule data is not returned in json serialize data' );
		$I->assertTrue( isset( $json_serialize['position'] ), 'Position data is not returned in json serialize data' );
		$I->assertTrue( isset( $json_serialize['editor_data'] ), 'Editor data is not returned in json serialize data' );
		$I->assertEquals( $json_serialize['editor_data'], $data_decoded, 'Editor data is not decoded' );
	}

}
