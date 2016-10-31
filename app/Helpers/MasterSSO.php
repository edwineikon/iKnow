<?php

namespace App\Helpers;

use PhpImap\Mailbox as ImapMailbox;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;

class MasterSSO
{
    // fungsi untuk authentikasi user menggunakan IMAP
	public function authenticationImap(array $prop)
    {
        $mailbox = new ImapMailbox('{imap.gmail.com:993/imap/ssl}INBOX', $prop['username'], $prop['password'], __DIR__);

        try
        {
            // Read all messaged into an array:
            $mailsIds = $mailbox->searchMailbox('ALL');
            if(!$mailsIds) {
                die('Mailbox is empty');
            }

            // Get the first message and save its attachment(s) to disk:
            $mail = $mailbox->getMail($mailsIds[0]);
            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
	}

    // Fungsi Untuk mengambil SAMLResponse / token dari Google
    public function getSAMLResponse($SAMLRequest, $loginEmail)
    {
        //get certificate & privatekey from db 
        $certificate  = '
-----BEGIN CERTIFICATE-----
MIIDsjCCAxugAwIBAgIJALE61GLKsN04MA0GCSqGSIb3DQEBBQUAMIGYMQswCQYD
VQQGEwJJRDESMBAGA1UECBMJRWFzdCBKYXZhMREwDwYDVQQHEwhTdXJhYmF5YTEN
MAsGA1UEChMEQlBKUzENMAsGA1UECxMEQlBKUzESMBAGA1UEAxMJbG9jYWxob3N0
MTAwLgYJKoZIhvcNAQkBFiFlZHdpbi5zeWFyaWVmQGVpa29udGVjaG5vbG9neS5j
b20wHhcNMTYxMDMxMDYwNTM1WhcNMTcxMDMxMDYwNTM1WjCBmDELMAkGA1UEBhMC
SUQxEjAQBgNVBAgTCUVhc3QgSmF2YTERMA8GA1UEBxMIU3VyYWJheWExDTALBgNV
BAoTBEJQSlMxDTALBgNVBAsTBEJQSlMxEjAQBgNVBAMTCWxvY2FsaG9zdDEwMC4G
CSqGSIb3DQEJARYhZWR3aW4uc3lhcmllZkBlaWtvbnRlY2hub2xvZ3kuY29tMIGf
MA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDgjli/fPt4+QXXcqjyXStmK2wt33NH
XMJa8Lcu6uOAmLqOBiAZqTbB6sXQjtwPG+YnWsEpfmjzmmccPJ3KguDlyfA7pT6s
cEaglNzF6uY+y61eyBJwG/J1Bw2vknHiP5Er15Rr53A2cW4MRYRKb0MzUp28nkad
sC5jg6QoF9MFMQIDAQABo4IBADCB/TAdBgNVHQ4EFgQUnABDtR+thqFpyyHbzYrB
yghlyMYwgc0GA1UdIwSBxTCBwoAUnABDtR+thqFpyyHbzYrByghlyMahgZ6kgZsw
gZgxCzAJBgNVBAYTAklEMRIwEAYDVQQIEwlFYXN0IEphdmExETAPBgNVBAcTCFN1
cmFiYXlhMQ0wCwYDVQQKEwRCUEpTMQ0wCwYDVQQLEwRCUEpTMRIwEAYDVQQDEwls
b2NhbGhvc3QxMDAuBgkqhkiG9w0BCQEWIWVkd2luLnN5YXJpZWZAZWlrb250ZWNo
bm9sb2d5LmNvbYIJALE61GLKsN04MAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEF
BQADgYEAJpvBuLe4Kxfk4wnPnQiUHzxZjQNONsYpQJji/yPXzSnJ2p/yvGkblAaU
pOJo+RHsxQ0z3qTNLU8AS/bDQjzk1B5yfu9xmw/Bbc3Xs74ZlfKB3x4WahN2+uOV
LgYmyq6zwjENVH8iGfteVhg7NHf/lIbtgm+asROZkZ4odTf98QI=
-----END CERTIFICATE-----';
        $privatekey   = '
-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDgjli/fPt4+QXXcqjyXStmK2wt33NHXMJa8Lcu6uOAmLqOBiAZ
qTbB6sXQjtwPG+YnWsEpfmjzmmccPJ3KguDlyfA7pT6scEaglNzF6uY+y61eyBJw
G/J1Bw2vknHiP5Er15Rr53A2cW4MRYRKb0MzUp28nkadsC5jg6QoF9MFMQIDAQAB
AoGAMPvcMCk7UfBAbfb9E6FvTiI6vub3ZqG9Y8kOrJVgezTVvRVo+zAGWda0wAHp
zwHu+ra5XCvPCRStWWN/qCVUmWL2siE5NG6gA3jZ1lgMDXP2pkuyaWJJW7xeOc6k
wMiXUFpdbljmp5GjWI+8PX49pcWAEEkccK+zmslSCvMzFeECQQD6pKA64En11B8H
WjPZkSKTLGnvhEmJlbIDS6XfYvNrpPkTTIIMZRXGNLkrZwIvu/XhhM2O3llr7Y2N
QRuTtga7AkEA5Vr81ORfL7sZiGamYF4JdCsa6QRjzTEaldhGfxT1wHAe95tGt0hu
4cCpt5xmkv2kPmxZVfLDjDwQUrN7lupDAwJAAN85v3qwRy6pvPjPXV5n5GMvKyom
p1fh+qj0tsY9Wo6EX1DQ0wI3BS2Bx2jgVRIuVM6FeI3Fed6ls2wakHT9qQJBAICK
oAzI+TgNCmBR94km6vF6fxh9Z1nG3XmBvvDWVG1H6XMoSVfLdql7iyLmuu5CzVxW
6TKsXkAoIZXYFbodDPUCQQD2CYtXaewqy5QpJTYb4prQZdO79xHmsoypV+s/zKUf
GlOz0dzLCotJBvnlL2+Q4nfrbZN7GvQQjdI4qs8Q0hv0
-----END RSA PRIVATE KEY-----';
                        
        $incoming = base64_decode($SAMLRequest);
        if(!$xml_string = gzinflate($incoming))
        {
            $xml_string = $incoming;
        }
        
        $xml = new \DOMDocument();
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

        $xml = new \DOMDocument('1.0','utf-8');
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