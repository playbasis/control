<?php
require_once(__DIR__.'/../CITest.php');

class ContentTest extends CITestCase
{
    public function setUp()
    {

    }

    public function testCreateContent()
    {
        $content_model = new Content_model();

        $clientSite = $this->getClientSite();

        $data['client_id'] = $clientSite['client_id'];
        $data['site_id'] = $clientSite['site_id'];
        $data['node_id'] = new MongoId();
        $data['title'] = 'Test Title';
        $data['summary'] = 'Test Summary';
        $data['detail'] = 'Test Detail';
        $data['date_start'] = null;
        $data['date_end'] = null;
        $data['image'] = 'img.jpg';
        $data['category'] = 'dummy Category';
        $data['status'] = false;
        $data['pin'] = '1234';
        $data['tags'] = null;

        $content_id = $content_model->createContent($data);
        $this->assertObjectHasAttribute('$id', $content_id);

        $content_attributes = array(
            'dataset' => $data,
            'content_id' => $content_id,
        );

        return $content_attributes;
    }

    /**
     * @depends testCreateContent
     */
    public function testRetrieveContent($content_attributes)
    {
        $content_model = new Content_model();

        $clientSite = $this->getClientSite();

        $data['client_id'] = $clientSite['client_id'];
        $data['site_id'] = $clientSite['site_id'];


        $content = $content_model->retrieveContent($content_attributes['content_id']);

        // Count to verify number of keys
        $this->assertEquals(count($content_attributes['dataset']) + 4, count($content));

        // Unset non-comparable field
        unset($content['_id']);
        unset($content['deleted']);
        unset($content['date_added']);
        unset($content['date_modified']);

        $this->assertEquals($content_attributes['dataset'], $content);
    }

    /**
     * @depends testCreateContent
     */
    public function testUpdateContent($content_attributes)
    {
        $content_model = new Content_model();
        $updatedString = 'Title Update';

        $clientSite = $this->getClientSite();

        $data['client_id'] = $clientSite['client_id'];
        $data['site_id'] = $clientSite['site_id'];
        $data['_id'] = $content_attributes['content_id'];
        $data['title'] = $updatedString;

        $content = $content_model->updateContent($data);

        // Verify successful update
        $this->assertEquals(true, $content);

        $content = $content_model->retrieveContent($content_attributes['content_id']);

        // Count to verify number of keys
        $this->assertEquals(count($content_attributes['dataset']) + 4, count($content));

        // Unset non-comparable field
        unset($content['_id']);
        unset($content['deleted']);
        unset($content['date_added']);
        unset($content['date_modified']);
        $content_attributes['dataset']['title'] = $updatedString;

        $this->assertEquals($content_attributes['dataset'], $content);
    }
}