<?php

use PHPUnit\Framework\TestCase;

/**
 * General tests for the boolean input element.
 **/
class DocumentTest extends TestCase
{
    protected $testXml = '<?xml version="1.0" encoding="UTF-8"?><root xmlns:db="http://db.com"
><child db:attr1="value1" attr1="value2">test</child></root>';

    // {{{ testSerialize()
    public function testSerialize(): void
    {
        $doc = new \Depage\Xml\Document();
        $doc->loadXML($this->testXml);

        $serialized = serialize($doc);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(\Depage\Xml\Document::class, $unserialized);
        $this->assertEquals($doc->saveXML(), $unserialized->saveXML());
    }
    // }}}
    // {{{ testSerializeEmpty()
    public function testSerializeEmpty(): void
    {
        $doc = new \Depage\Xml\Document();

        $serialized = serialize($doc);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(\Depage\Xml\Document::class, $unserialized);
        $this->assertEquals($doc->saveXML(), $unserialized->saveXML());
    }
    // }}}
    // {{{ testGetDocAndNode()
    public function testGetDocAndNode(): void
    {
        $doc = new \Depage\Xml\Document();
        $doc->loadXML($this->testXml);

        $child = $doc->getElementsByTagName('child')->item(0);

        [$docFromNode, $node] = \Depage\Xml\Document::getDocAndNode($child);

        $this->assertSame($doc, $docFromNode);
        $this->assertSame($child, $node);
    }
    // }}}
    // {{{ testGetDocAndNodeWithDocument()
    public function testGetDocAndNodeWithDocument(): void
    {
        $doc = new \Depage\Xml\Document();
        $doc->loadXML($this->testXml);

        [$docFromDoc, $node] = \Depage\Xml\Document::getDocAndNode($doc);

        $this->assertSame($doc, $docFromDoc);
        $this->assertSame($doc->documentElement, $node);
    }
    // }}}
    // {{{ testReplaceAttributeNames()
    public function testReplaceAttributeNamesString(): void
    {
        $doc = new \Depage\Xml\Document();
        $doc->loadXML($this->testXml);

        $child = $doc->getElementsByTagName('child')->item(0);
        $this->assertTrue($child->hasAttribute('attr1'));

        \Depage\Xml\Document::replaceAttributeNames($child, 'attr1', 'attr2');

        $this->assertFalse($child->hasAttribute('attr1'));
        $this->assertTrue($child->hasAttribute('attr2'));
        $this->assertEquals('value2', $child->getAttribute('attr2'));
    }
    // }}}
    // {{{ testReplaceAttributeNamesArray()
    public function testReplaceAttributeNamesArray(): void
    {
        $doc = new \Depage\Xml\Document();
        $doc->loadXML($this->testXml);

        $child = $doc->getElementsByTagName('child')->item(0);
        $this->assertTrue($child->hasAttributeNS('http://db.com', 'attr1'));

        \Depage\Xml\Document::replaceAttributeNames($child, [
            'http://db.com', 'db:attr1',
        ], [
            'http://db.com', 'db:attr2',
        ]);

        $this->assertFalse($child->hasAttributeNS('http://db.com', 'attr1'));
        $this->assertTrue($child->hasAttributeNS('http://db.com', 'attr2'));
        $this->assertEquals('value1', $child->getAttributeNS('http://db.com', 'attr2'));
    }
    // }}}
    // {{{ testFromDomDocument()
    public function testFromDomDocument(): void
    {
        $dom = new \DOMDocument();
        $dom->loadXML($this->testXml);

        $doc = \Depage\Xml\Document::fromDomDocument($dom);

        $this->assertInstanceOf(\Depage\Xml\Document::class, $doc);
        $this->assertEquals($dom->saveXML(), $doc->saveXML());
    }
    // }}}
    // {{{ testToString()
    public function testToString(): void
    {
        $doc = new \Depage\Xml\Document();
        $doc->loadXML($this->testXml);

        $this->assertEquals($doc->saveXML(), (string) $doc);
    }
    // }}}
}
