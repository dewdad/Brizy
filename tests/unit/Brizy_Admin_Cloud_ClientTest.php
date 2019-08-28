<?php

use \Prophecy\Argument;

class Brizy_Admin_Cloud_ClientTest extends \Codeception\Test\Unit {


	/**
	 * @return \Prophecy\Prophecy\ObjectProphecy
	 */
	private function getProjectObjserver() {
		$projectObserver = $this->prophesize( Brizy_Editor_Project::class );
		$projectObserver->getMetaValue( 'brizy-cloud-token' )->willReturn( 'brizy-cloud-token-hash' );

		return $projectObserver;
	}

	/**
	 * @return \Prophecy\Prophecy\ObjectProphecy
	 */
	private function getHttpObjserver() {
		return $httpObserver = $this->prophesize( WP_Http::class );;
	}

	/**
	 * @throws Exception
	 */
	public function testConstructor() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$this->assertSame( $client->getBrizyProject(), $projectObserver->reveal(), 'The getBrizyProject should return the valid instance' );
		$this->assertSame( $client->getHttp(), $httpObserver->reveal(), 'The getHttp should return the valid instance' );
	}

	public function testSetters() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$client->setHttp( $http = new WP_Http() );
		$this->assertSame( $client->getHttp(), $http, 'The getHttp should return the valid instance' );

		$project = $this->prophesize( Brizy_Editor_Project::class )->reveal();
		$client->setBrizyProject( $project );
		$this->assertSame( $client->getBrizyProject(), $project, 'The getBrizyProject should return the valid instance' );
	}

	/**
	 * @throws Exception
	 */
	public function testGetLibraries() {

		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();
		$client          = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_LIBRARY;
		$httpObserver->get( $apiEndpointUrl, Argument::containing( [
			'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
			'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash'
		] ) )->shouldBeCalled();

		$client->getLibraries();
	}


	public function testSignIn() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );


		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_SIGNIN;
		$httpObserver->post( $apiEndpointUrl, Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
				'Content-type'      => 'application/x-www-form-urlencoded'
			],
			'body'    => [
				'email'    => 'username@email.com',
				'password' => 'password'
			]
		] ) )->shouldBeCalled();

		$client->signIn( 'username@email.com', 'password' );
	}

	public function testSignUp() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );


		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_SIGNUP;
		$httpObserver->post( $apiEndpointUrl, Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
				'Content-type'      => 'application/x-www-form-urlencoded'
			],
			'body'    => [

				'first_name'       => 'first_name',
				'last_name'        => 'last_name',
				'email'            => 'email@email.com',
				'new_password'     => 'password',
				'confirm_password' => 'password'
			]
		] ) )->shouldBeCalled();

		$client->signUp( 'first_name',
			'last_name',
			'email@email.com',
			'password',
			'password' );
	}

	public function testResetPassword() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );


		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_RESET_PASSWORD;
		$httpObserver->post( $apiEndpointUrl, Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
				'Content-type'      => 'application/x-www-form-urlencoded'
			],
			'body'    => [
				'email' => 'email@email.com',
			]
		] ) )->shouldBeCalled();

		$client->resetPassword( 'email@email.com' );
	}

	public function testGetContainers() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_CONTAINERS;
		$httpObserver->get( $apiEndpointUrl, Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			]
		] ) )->shouldBeCalled();

		$client->getContainers();
	}

	public function testGetProjects() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$filters        = [ 'key' => 'val' ];
		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_PROJECTS . '?' . http_build_query( $filters );
		$httpObserver->get( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			]
		] ) )->shouldBeCalled();

		$client->getProjects( $filters );
	}

	public function testGetProject() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = sprintf( Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_PROJECTS . "/%d", 100 );

		$httpObserver->get( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			]
		] ) )->shouldBeCalled();

		$client->getProject( 100 );
	}

	public function testCreateProject() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_PROJECTS;

		$httpObserver->post( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
			'body'    => [
				'name'      => 'name',
				'container' => 'container',
				'globals'   => null,
				'site_id'   => null
			]
		] ) )->shouldBeCalled();

		$client->createProject( 'container', 'name' );
	}


	public function testCreateBlock() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();
		$client          = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );


		// set up reuqest for library list
		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_LIBRARY;
		$httpObserver->get( $apiEndpointUrl, Argument::containing( [
			'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
			'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash'
		] ) )->willReturn(
			[
				'body'     => '[{"id":4,"name":"My Personal Projects"},{"id":58930,"name":"fsfaf"}]',
				'response' =>
					array(
						'code'    => 200,
						'message' => 'OK',
					),
				'cookies'  =>
					array(),
				'filename' => null,
			]
		);


		$blockObserver = $this->prophesize( Brizy_Editor_Block::class );
		$blockObserver->get_editor_data()->willReturn( "SOME_ENCODED_JSON" );
		$blockObserver->get_uid()->willReturn( "SOME_UID" );
		$blockObserver->getCloudId()->willReturn( null );
		$block = $blockObserver->reveal();

		$cloudBlockData = array(
			'library'     => 4,
			'meta'        => array(),
			'data'        => $block->get_editor_data(),
			'is_autosave' => 0,
			'uid'         => $block->get_uid()
		);


		$httpObserver->post( Argument::exact( Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_SAVEDBLOCKS ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
			'body'    => $cloudBlockData
		] ) )->shouldBeCalled();


		$client->createOrUpdateBlock( $block );
	}


	public function testUpdateBlock() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();
		$client          = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );


		// set up reuqest for library list
		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_LIBRARY;
		$httpObserver->get( $apiEndpointUrl, Argument::containing( [
			'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
			'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash'
		] ) )->willReturn(
			[
				'body'     => '[{"id":4,"name":"My Personal Projects"},{"id":58930,"name":"fsfaf"}]',
				'response' =>
					array(
						'code'    => 200,
						'message' => 'OK',
					),
				'cookies'  =>
					array(),
				'filename' => null,
			]
		);


		$blockObserver = $this->prophesize( Brizy_Editor_Block::class );
		$blockObserver->get_editor_data()->willReturn( "SOME_ENCODED_JSON" );
		$blockObserver->get_uid()->willReturn( "SOME_UID" );
		$blockObserver->getCloudId()->willReturn( "CLOUD_UID" );
		$block = $blockObserver->reveal();

		$cloudBlockData = array(
			'library'     => 4,
			'meta'        => array(),
			'data'        => $block->get_editor_data(),
			'is_autosave' => 0,
			'uid'         => $block->get_uid()
		);


		$httpObserver->request( Argument::exact( Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_SAVEDBLOCKS ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
			'method'  => 'PUT',
			'body'    => $cloudBlockData
		] ) )->shouldBeCalled();


		$client->createOrUpdateBlock( $block );
	}

	public function testDeleteBlock() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_SAVEDBLOCKS . "/100";

		$httpObserver->request( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
			'method'  => 'DELETE'
		] ) )->shouldBeCalled();

		$client->deleteBlock( 100 );
	}


	public function testIsMediaUploaded() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_MEDIA . '?name=MEDIA_UID';

		$httpObserver->get( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
		] ) )->shouldBeCalled();

		$client->isMediaUploaded( 'MEDIA_UID' );
	}

	public function testUploadMedia() {

		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();
		$uid             = md5( time() );

		$filePath = tempnam( '/tmp', 'test' );
		file_put_contents( $filePath, 'content' );


		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_MEDIA;

		$httpObserver->post( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
			'body'    => array(
				'attachment' => base64_encode( 'content' ),
				'name'       => $uid,
				'filename'   => basename( $filePath )
			),
		] ) )->shouldBeCalled();

		$client->uploadMedia( $uid, $filePath );
	}


	public function testUploadFonts() {
		$projectObserver = $this->getProjectObjserver();
		$httpObserver    = $this->getHttpObjserver();

		$client = new Brizy_Admin_Cloud_Client( $projectObserver->reveal(), $httpObserver->reveal() );

		$apiEndpointUrl = Brizy_Config::CLOUD_ENDPOINT . Brizy_Config::CLOUD_FONTS;

		$fontToCreate = [
			'id'      => 'askdalskdlaksd',
			'family'  => 'proxima-nova',
			'type'    => 'uploaded',
			'weights' => [
				'400' => [
					'ttf'   => codecept_data_dir( 'fonts/pn-regular-webfont.ttf' ),
					'eot'   => codecept_data_dir( 'fonts/pn-regular-webfont.eot' ),
					'woff'  => codecept_data_dir( 'fonts/pn-regular-webfont.woff' ),
					'woff2' => codecept_data_dir( 'fonts/pn-regular-webfont.woff2' ),
				],
				'500' => [
					'eot'   => codecept_data_dir( 'fonts/pn-medium-webfont.eot' ),
					'woff'  => codecept_data_dir( 'fonts/pn-medium-webfont.woff' ),
					'woff2' => codecept_data_dir( 'fonts/pn-medium-webfont.woff2' ),
				],
				'700' => [
					'eot'   => codecept_data_dir( 'fonts/pn-bold-webfont.eot' ),
					'woff'  => codecept_data_dir( 'fonts/pn-bold-webfont.woff' ),
					'woff2' => codecept_data_dir( 'fonts/pn-bold-webfont.woff2' ),
				],
			]
		];

		$httpObserver->post( Argument::exact( $apiEndpointUrl ), Argument::exact( [
			'headers' => [
				'X-AUTH-APP-TOKEN'  => Brizy_Config::CLOUD_APP_KEY,
				'X-AUTH-USER-TOKEN' => 'brizy-cloud-token-hash',
			],
			'body'    => array(
				'uid'    => $fontToCreate['id'],
				'family' => $fontToCreate['family'],
				'files'  => $fontToCreate['weights']
			),
		] ) )->shouldBeCalled();


		$client->createFont( $fontToCreate );

	}
}