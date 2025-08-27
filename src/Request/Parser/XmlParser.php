<?php
/**
 * This class is a specific parser implementation for XML data parsing
 */
declare(strict_types=1);

namespace Maleficarum\Request\Parser;

class XmlParser extends \Maleficarum\Request\Parser\AbstractParser {
    /* ------------------------------------ Class Property START --------------------------------------- */

    const DEFAULT_XML_VERSION = '1.0';
    const DEFAULT_XML_ENCODING = 'UTF-8';

    /* ------------------------------------ Class Property END ----------------------------------------- */

    /* ------------------------------------ Class Methods START ---------------------------------------- */

    /**
     * @see \Maleficarum\Request\Parser\AbstractParser::parsePostData()
     */
    public function parsePostData(): array
    {
        try {
            $xml = $this->getXmlRawBody();
            $data = $this->nodeToArray($xml->documentElement);
            $data = $this->sanitizeData($data);
        } catch (\DOMException $e) {
            $data = [];
        }

        return $data;
    }

    /**
     * @see \Maleficarum\Request\Parser\AbstractParser::getRawPostPayload()
     */
    public function getRawPostPayload(): array
    {
        try {
            $xml = $this->getXmlRawBody();
            $data = $this->nodeToArray($xml->documentElement);
        } catch (\DOMException $e) {
            $data = [];
        }

        return $data;
    }

    /**
     * @return \DOMDocument
     *
     * @throws \DOMException
     */
    private function getXmlRawBody(): \DOMDocument
    {
        $raw = $this->getRequest()->getRawBody();
        $raw = preg_replace('/<!DOCTYPE[^>]*>/i', '', $raw);
        $xmlProlog = $this->getXmlProlog($raw);
        $dom = \Maleficarum\Ioc\Container::get('DomDocument', [$xmlProlog['version'], $xmlProlog['encoding']]);
        $result = $dom->loadXML($raw);

        if ($result === false) {
            throw new \DOMException('Unable to parse the XML document');
        }

        return $dom;
    }

    /**
     * @param \DomElement $node
     *
     * @return array[]
     */
    private function nodeToArray(\DomElement $node) : array
    {
        $result = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                $result['@attributes'][$attribute->name] = $attribute->value;
            }
        }

        if ($node->hasChildNodes()) {
            $groups = [];
            foreach ($node->childNodes as $child) {
                if (in_array($child->nodeType, [XML_TEXT_NODE, XML_CDATA_SECTION_NODE])) {
                    trim($child->nodeValue) and $result['@value'] = $child->nodeValue;
                } else {
                    $groups[] = $this->nodeToArray($child);
                }
            }
            $result = array_merge($result, ...$groups);
        }

        return [$node->nodeName => $result];
    }

    /**
     * @param string $raw
     *
     * @return array
     */
    private function getXmlProlog(string $raw) : array
    {
        $version = self::DEFAULT_XML_VERSION;
        $encoding = self::DEFAULT_XML_ENCODING;

        $head = substr($raw, 0, 512);

        if (preg_match('/^\s*<\?xml\s+([^?]+)\?>/i', $head, $m)) {
            $attributes = $m[1];

            if (preg_match('/\bversion\s*=\s*(["\'])(.*?)\1/i', $attributes, $vm)) {
                $version = $vm[2];
            }

            if (preg_match('/\bencoding\s*=\s*(["\'])(.*?)\1/i', $attributes, $em)) {
                $encoding = strtoupper($em[2]);
            }
        }

        return ['version' => $version, 'encoding' => $encoding];
    }

    /* ------------------------------------ Class Methods END ------------------------------------------ */

}
