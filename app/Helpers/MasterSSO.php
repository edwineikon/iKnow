<?php

namespace App\Helpers;

include __DIR__.'/../libraries/VivOAuthIMAP/VivOAuthIMAP.php';
include __DIR__.'/../libraries/VivOAuthIMAP/lib/mime_parser.php';
include __DIR__.'/../libraries/VivOAuthIMAP/lib/rfc822_addresses.php';
include __DIR__.'/../libraries/xmlseclibs/xmlseclibs_helper.php';

class MasterSSO
{
    // fungsi untuk authentikasi user menggunakan IMAP
	public function authenticationImap(array $prop)
    {
		$imap           = new VivOAuthIMAP();
        $imap->host     = 'ssl://imap.gmail.com';
        $imap->port     = 993;
        $imap->username = $prop['username'];
        $imap->password = $prop['password'];

        if ($imap->login())
        {
        	return true;
        }
        else
        {
        	return false;
        }
	}

    // Fungsi Untuk mengambil SAMLResponse / token dari Google
    public function getSAMLResponse($SAMLRequest, $loginEmail)
    {
        //get certificate & privatekey from db 
        $certificate  = '-----BEGIN CERTIFICATE-----
                        MIIEBzCCA3CgAwIBAgIJAOOe4Yr2d1YwMA0GCSqGSIb3DQEBBQUAMIG0MQswCQYD
                        VQQGEwJJRDETMBEGA1UECBMKSmF3YSBUaW11cjERMA8GA1UEBxMIU3VyYWJheWEx
                        EjAQBgNVBAoTCVNTTyBFSUtPTjEdMBsGA1UECxMURUlLT04gVGVjaG5vbG9neSwg
                        UFQxFjAUBgNVBAMTDW1hc3RlcnNzby5kZXYxMjAwBgkqhkiG9w0BCQEWI2plcHJp
                        LnN1Z2loYXJ0b0BlaWtvbnRlY2hub2xvZ3kuY29tMB4XDTE1MDgxMDAyMjgxNloX
                        DTE2MDgwOTAyMjgxNlowgbQxCzAJBgNVBAYTAklEMRMwEQYDVQQIEwpKYXdhIFRp
                        bXVyMREwDwYDVQQHEwhTdXJhYmF5YTESMBAGA1UEChMJU1NPIEVJS09OMR0wGwYD
                        VQQLExRFSUtPTiBUZWNobm9sb2d5LCBQVDEWMBQGA1UEAxMNbWFzdGVyc3NvLmRl
                        djEyMDAGCSqGSIb3DQEJARYjamVwcmkuc3VnaWhhcnRvQGVpa29udGVjaG5vbG9n
                        eS5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMukbvcCkTsYaHfKX/Ko
                        yVZ6slGXIoME0yPLmLGdTsfGNQDx96KdHZmDXtDfJtvEz8GXUdrty0zobkdTJ4w3
                        D34evsPerbQImzER0VNhYB5YUyUzKJ6tB8MZ2QxJ4fMPT4LmpCRwlkgg9pnm0z41
                        uk59sO/SYZ3HyJlxaZ9YXn+pAgMBAAGjggEdMIIBGTAdBgNVHQ4EFgQUvjHcwtMG
                        jqsxdaYK3svYiX21voMwgekGA1UdIwSB4TCB3oAUvjHcwtMGjqsxdaYK3svYiX21
                        voOhgbqkgbcwgbQxCzAJBgNVBAYTAklEMRMwEQYDVQQIEwpKYXdhIFRpbXVyMREw
                        DwYDVQQHEwhTdXJhYmF5YTESMBAGA1UEChMJU1NPIEVJS09OMR0wGwYDVQQLExRF
                        SUtPTiBUZWNobm9sb2d5LCBQVDEWMBQGA1UEAxMNbWFzdGVyc3NvLmRldjEyMDAG
                        CSqGSIb3DQEJARYjamVwcmkuc3VnaWhhcnRvQGVpa29udGVjaG5vbG9neS5jb22C
                        CQDjnuGK9ndWMDAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAEYTlXoO
                        2jHj+FcU6W0PagSab8v2yRT//By+ktUZGK8dpfBwajReDvWB6UUpkTrqPRtNXCya
                        c7NW07GkW6xpFlosVYUVdhAl7TqMpcK7jv9O/tsfdCXWKMfHFGaPQFqC8RSKjJrq
                        h0VqYWYkFXvSpQNfTQfwtTvE+azgpxbddGim
                        -----END CERTIFICATE-----';
        $privatekey   = '-----BEGIN RSA PRIVATE KEY-----
                        MIICXgIBAAKBgQDLpG73ApE7GGh3yl/yqMlWerJRlyKDBNMjy5ixnU7HxjUA8fei
                        nR2Zg17Q3ybbxM/Bl1Ha7ctM6G5HUyeMNw9+Hr7D3q20CJsxEdFTYWAeWFMlMyie
                        rQfDGdkMSeHzD0+C5qQkcJZIIPaZ5tM+NbpOfbDv0mGdx8iZcWmfWF5/qQIDAQAB
                        AoGAEzGXZ96tE5XUWt4PNw7jkywTXI9TKGgvAmOxK6R5BWlQG5uyzHfkj1CLgkJW
                        JkahkkyR4YGiNMh/hGd5BcU2x3AxDuve1L5UttC10XHCxYD/mLKo9MXVvFod44Zx
                        IC8DmwTpvMnKr4xCpCjb0EyGMd007dUsiopnJoiO530s/VECQQD2qBVmU8d1HgRy
                        SCZi/6lp0CqEGigNCI/EAXVvPp/lcPIFAmosSQVcFH6xqDwtpLhQ3QboI9vEImEk
                        9KrSwlV1AkEA01s5rwa4bsrAZSgGQsq/9Hm1VlbOiVjrT8gFacJB9V5WLNJFX2L2
                        o3iyVV8DgnK8nloFbJvZBJ2E8NjqknAW5QJBAJLqszRsGpYL9yILD7JQDhzUvT5K
                        RijdPKTHKafFaYBEsiOBuLQAGo0qN/yh9JZLUu33eTG0iiZdQ/e7NDStRDECQQDD
                        CAwrmVz5R3jQH2Xfnm4RL3oI6ON/VCEXprBwDgSFYf7NL186jPygjlCpfJqldjDd
                        Qp58wTc6DgzNnqyeYnaNAkEAk/gKd+bUIQEGUNXTjgzROW7KuZnA0ezWj1sn8g4o
                        uxyDeFV/v7epmLQhyWk9jdwKCY1wBbrx52CbNuhfZQsTHQ==
                        -----END RSA PRIVATE KEY-----';
                        
        $incoming = base64_decode($SAMLRequest);
        if(!$xml_string = gzinflate($incoming))
        {
            $xml_string = $incoming;
        }
        
        $xml = new DOMDocument();
        $xml->loadXML($xml_string);
        if($xml->hasChildNodes() && ($node = $xml->childNodes->item(0)))
        {
            $authnrequest = array();
            foreach($node->attributes as $attr)
            {
                $authnrequest[$attr->name] = $attr->value;
            }
            if($node->hasChildNodes())
            {
                foreach($node->childNodes as $childnode)
                {
                    if($childnode->hasAttributes())
                    {
                        $authnrequest[$childnode->nodeName]=array();
                        foreach($childnode->attributes as $attr)
                        {
                            $authnrequest[$childnode->nodeName][$attr->name] = $attr->value;
                        }
                    }
                    else
                    {
                        $authnrequest[$childnode->nodeName]=$childnode->nodeValue;
                    }
                }
            }
        }
        
        $acs_url = $authnrequest['AssertionConsumerServiceURL'];

        $response_params = array();
        $time = time();
        $response_params['IssueInstant'] = str_replace('+00:00','Z',gmdate("c",$time));
        $response_params['NotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+300));
        $response_params['NotBefore'] = str_replace('+00:00','Z',gmdate("c",$time-30));
        $response_params['AuthnInstant'] = str_replace('+00:00','Z',gmdate("c",$time-120));
        $response_params['SessionNotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+3600*8));

        $response_params['ID'] = $this->generateUniqueID(40);
        $response_params['assertID'] = $this->generateUniqueID(40);

        $response_params['issuer'] = $_SERVER['HTTP_HOST'];//Does not really matter as far as i could tell
        $response_params['email'] = $loginEmail;

        $response_params['x509'] = $certificate;

        $xml = new DOMDocument('1.0','utf-8');
        $resp = $xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:Response');

        $resp->setAttribute('ID',$response_params['ID']);
        $resp->setAttribute('InResponseTo',$authnrequest['ID']);
        $resp->setAttribute('Version','2.0');
        $resp->setAttribute('IssueInstant',$response_params['IssueInstant']);
        $resp->setAttribute('Destination',$authnrequest['AssertionConsumerServiceURL']);
        $xml->appendChild($resp);
        
        $issuer = $xml->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion','samlp:Issuer',$response_params['issuer']);
        $resp->appendChild($issuer);
        
        $status = $xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:Status');
        $resp->appendChild($status);

        $statusCode = $xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:StatusCode');
        $statusCode->setAttribute('Value', 'urn:oasis:names:tc:SAML:2.0:status:Success');
        $status->appendChild($statusCode);

        $assertion = $xml->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion','saml:Assertion');
        $assertion->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
        $assertion->setAttribute('ID',$response_params['assertID']);
        $assertion->setAttribute('IssueInstant',$response_params['IssueInstant']);
        $assertion->setAttribute('Version','2.0');
        $resp->appendChild($assertion);
        
        $assertion->appendChild($xml->createElement('saml:Issuer',$response_params['issuer']));

        $subject = $xml->createElement('saml:Subject');
        $assertion->appendChild($subject);
        
        $nameid = $xml->createElement('saml:NameID',$response_params['email']);
        
        $nameid->setAttribute('Format','urn:oasis:names:tc:SAML:2.0:nameid-format:email');
        $nameid->setAttribute('SPNameQualifier','google.com');
        $subject->appendChild($nameid);
        
        $confirmation = $xml->createElement('saml:SubjectConfirmation');
        $confirmation->setAttribute('Method','urn:oasis:names:tc:SAML:2.0:cm:bearer');
        $subject->appendChild($confirmation);
        
        $confirmationdata = $xml->createElement('saml:SubjectConfirmationData');
        $confirmationdata->setAttribute('InResponseTo',$authnrequest['ID']);
        $confirmationdata->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);
        $confirmationdata->setAttribute('Recipient',$authnrequest['AssertionConsumerServiceURL']);
        $confirmation->appendChild($confirmationdata);
        
        $condition = $xml->createElement('saml:Conditions');
        $condition->setAttribute('NotBefore',$response_params['NotBefore']);
        $condition->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);
        $assertion->appendChild($condition);
        
        $audiencer = $xml->createElement('saml:AudienceRestriction');
        $condition->appendChild($audiencer);
        
        $audience = $xml->createElement('saml:Audience','google.com');
        $audiencer->appendChild($audience);
        
        $authnstat = $xml->createElement('saml:AuthnStatement');
        $authnstat->setAttribute('AuthnInstant',$response_params['AuthnInstant']);
        $authnstat->setAttribute('SessionIndex','_'.$this->generateUniqueID(30));//$response_params['assertID']
        $authnstat->setAttribute('SessionNotOnOrAfter',$response_params['SessionNotOnOrAfter']);
        $assertion->appendChild($authnstat);
        
        $authncontext = $xml->createElement('saml:AuthnContext');
        $authnstat->appendChild($authncontext);
        
        $authncontext_ref = $xml->createElement('saml:AuthnContextClassRef','urn:oasis:names:tc:SAML:2.0:ac:classes:Password');
        $authncontext->appendChild($authncontext_ref);
        
        //Private KEY   
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));
        $objKey->loadKey($privatekey);
        
        //Sign the Assertion
        $objXMLSecDSig = new XMLSecurityDSig();
        $objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objXMLSecDSig->addReferenceList(array($assertion), XMLSecurityDSig::SHA1,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N),array('id_name'=>'ID','overwrite'=>false));
        $objXMLSecDSig->sign($objKey);
        $objXMLSecDSig->add509Cert($response_params['x509']);
        $objXMLSecDSig->insertSignature($assertion,$subject);

        $r = $xml->saveXML();
        $r = str_replace('<?xml version="1.0"?>','',$r);//Don't need that
        $r = base64_encode(stripslashes($r));//We assume post binding, the response is not deflated
        return array('SAMLResponse'=>$r, 'acsUrl'=>$acs_url);
    }

    // Fungsi untuk menggenerate UniqueID
    public function generateUniqueID($length)
    {
        $chars = "abcdef0123456789";
        $chars_len = strlen($chars);
        $uniqueID = "";

        for ($i = 0; $i < $length; $i++) $uniqueID.= substr($chars, rand(0, 15) , 1);
        return 'a' . $uniqueID;
    }
}