<?php

/**
 * @file    document.php
 *
 * @author    Frank Hellenkamp [jonas@depage.net]
 */

namespace Depage\Xml;

/**
 * @brief DOMDocument
 *
 * Serializable subclass of DOMDocument with helper methods especially
 * for html-content, and for removing up unwanted tags from html.
 */
class Document extends \DOMDocument
{
    public $contentType = "text/xml";
    public $charset = "UTF-8";

    // {{{ constructor()
    /**
     * @brief   htmldom class constructor
     *
     * @param   $version (string)
     * @param   $encoding (string)
     *
     * @return  (Depage::HtmlForm::Abstracts::HtmlDom) htmlDOM
     **/
    public function __construct($version = null, $encoding = null)
    {
        if (is_null($version)) {
            $version = "1.0";
        }
        if (is_null($encoding)) {
            $encoding = "UTF-8";
        }
        parent::__construct($version, $encoding);
    }
    // }}}
    // {{{ __serialize()
    /**
     * @brief   serializes htmldom into string
     *
     * @return  array containing the XML string
     **/
    public function __serialize(): array
    {
        if ($this->documentElement === null) {
            return [
                'xml' => '',
            ];
        }

        return [
            'xml' => (string) $this,
        ];
    }
    // }}}
    // {{{ __unserialize()
    /**
     * @brief   unserializes htmldom-objects
     *
     * @param   array $data containing the XML string
     *
     * @return  (void)
     **/
    public function __unserialize(array $data): void
    {
        if (empty($data['xml'])) {
            $this->loadXML('<root/>');
            $this->version = "1.0";
            $this->encoding = "UTF-8";

            $this->removeChild($this->documentElement);

            return;
        }

        $this->loadXML($data['xml']);
    }
    // }}}

    // {{{ getDocAndNode()
    /**
     * @brief   helper function to get node and owner-document by document or node
     *
     * @param   $docOrNode \DOMNode
     *
     * @return  array($doc, $node)
     **/
    public static function getDocAndNode(\DOMNode $docOrNode): array
    {
        if ($docOrNode->nodeType == XML_DOCUMENT_NODE) {
            $doc = $docOrNode;
            $node = $doc->documentElement;
        } else {
            $doc = $docOrNode->ownerDocument;
            $node = $docOrNode;
        }

        return [$doc, $node];
    }
    // }}}
    // {{{ fromDomDocument()
    /**
     * @brief fromDomDocument
     *
     * @param mixed
     * @return void
     **/
    public static function fromDomDocument(\DOMDocument $dom): self
    {
        $xml = new Document();
        $rootNode = $dom->documentElement;
        $rootNode = $xml->importNode($rootNode, true);
        $xml->appendChild($rootNode);

        return $xml;
    }
    // }}}
    // {{{ replaceAttributeNames()
    /**
     * @brief replaceAttributeNames
     *
     * @param mixed $node
     * @param mixed $search
     * @param mixed $replace
     * @return void
     **/
    public static function replaceAttributeNames(\DOMNode $node, string|array $search, string|array $replace): void
    {
        $xpath = new \DOMXPath($node->ownerDocument);
        if (is_string($search)) {
            $attrName = $search;

            $query = ".//@$attrName";
        } elseif (is_array($search)) {
            $attrNS = $search[0];
            $attrName = $search[1];

            $prefix = explode(":", $attrName)[0];

            $xpath->registerNamespace($prefix, $attrNS);

            $query = ".//@$attrName";
        }
        $nodes = $xpath->query($query, $node);

        for ($i = $nodes->length - 1; $i >= 0; $i--) {
            $n = $nodes->item($i);
            $parent = $n->parentNode;

            if (is_string($replace)) {
                $parent->setAttribute($replace, $n->nodeValue);
            } elseif (is_array($replace)) {
                $parent->setAttributeNS($replace[0], $replace[1], $n->nodeValue);
            }
            $parent->removeAttributeNode($n);
        }
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   converts the document to a string by saving it as XML
     *
     * @return  string
     **/
    public function __toString(): string
    {
        return $this->saveXML();
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
