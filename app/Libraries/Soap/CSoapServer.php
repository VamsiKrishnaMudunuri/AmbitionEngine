<?php

namespace App\Libraries\Soap;

use UnexpectedValueException;
use RuntimeException;
use SoapServer;
use DOMDocument;
use DOMXPath;

class CSoapServer extends SoapServer {

    public function __construct($wsdl, $options = array())
    {
        if($wsdl !== null && is_string($wsdl)
            && array_key_exists('soap:address', $options)
            && array_key_exists('soap:address-file', $options))
        {
            if(!file_exists($wsdl) && !touch($wsdl))
                throw new UnexpectedValueException(
                    "cannot read $wsdl");

            $url = parse_url($options['soap:address']);
            $options['soap:address'] = sprintf('%s://%s:%s%s', $url['scheme'], $url['host'], config('soap.service_port'), $url['path']);

            // update if cache is broken
            if($this->_cacheBroken($wsdl, $options) || !file_exists($options['soap:address-file']) )
                $this->_overrideAddress($wsdl, $options);

            $wsdl = $options['soap:address-file'];
        }

        return parent::__construct($wsdl, $options);
    }

    private function _cacheBroken($wsdl, array $options)
    {
        // bail if caching is disabled
        if(!array_key_exists('cache_wsdl', $options)
            || !in_array($options['cache_wsdl'], array(
                WSDL_CACHE_DISK, WSDL_CACHE_MEMORY,
                WSDL_CACHE_BOTH
            ))
            || !ini_get('soap.wsdl_cache_enabled'))
            return false;

        // we'll test the mtime of the file against the cache ttl
        $iCacheTtl = ini_get('soap.wsdl_cache_ttl');
        if(time() - filemtime($wsdl) > $iCacheTtl)
            return true;

        return false;

    }

    private function _overrideAddress($wsdl, array $options)
    {
        $sAddr = $options['soap:address'];
        $sPath = $options['soap:address-file'];
        $sNode = 'soap:address';
        $sAttr = 'location';

        // allow for custom node
        if(array_key_exists('soap:node', $options))
            $sNode = $options['soap:node'];

        // allow for custom attribute
        if(array_key_exists('soap:location', $options))
            $sNode = $options['soap:location'];

        // locate soap attribute
        $oXml = new DOMDocument('1.0', 'iso-8859-1');
        if(!$oXml->loadXml(file_get_contents($wsdl)))
            throw new UnexpectedValueException(
                "couldn't parse $wsdl as XML");

        // replace soap:address value
        $oPath = new DOMXpath($oXml);
        $oList = $oPath->query("//$sNode");
        if($oList->length == 0)
            throw new UnexpectedValueException(
                "soap:address not found in $wsdl"
            );

        $oList->item(0)->setAttribute($sAttr, $sAddr);

        $parts = pathinfo($sPath);
        if(!file_exists($parts['dirname'])){
            mkdir($parts['dirname'],0777, true);
        }
        // save file in $sPath
        if(!file_put_contents($sPath, $oXml->saveXML()))
            throw new RuntimeException(
                "coudn't save overriden wsdl");
    }

}