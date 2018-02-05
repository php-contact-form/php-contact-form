<?php

require dirname(__FILE__) . '/../src/Contact.php';
 
class ContactTests extends PHPUnit_Framework_TestCase
{
    private $contact;
 
    protected function setUp()
    {
        $this->contact = new Contact();
    }
 
    protected function tearDown()
    {
        $this->contact = NULL;
    }
 
    public function testRender()
    {
        $result = $this->contact->render();
        $this->assertContains('Contact Guy Smiley', $result);
    }
} 
