<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Model;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Model\Domain;

class DomainTest extends TestCase
{
    public function testDomainConstructor()
    {
        $data = [
            'id' => 1,
            'domain' => 'example.com',
            'tracklink_domain' => 'track.example.com',
            'tracklink_domain_is_verified' => true,
            'auth_domain_is_verified' => false,
            'dns_selector' => 'selector1',
            'dns_record' => 'v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDF...',
            'click_track' => true,
            'open_track' => true,
            'unsub_track' => false,
            'created_at' => 1640995200,
            'modified_at' => 1640995300,
            'is_verified' => true,
            'status' => true,
            'auth_domain' => 'auth.example.com',
            'auth_domain_dkim_record' => 'dkim-record',
            'auth_domain_dkim_selector' => 'dkim-selector'
        ];

        $domain = new Domain($data);

        $this->assertEquals(1, $domain->getId());
        $this->assertEquals('example.com', $domain->getDomain());
        $this->assertEquals('track.example.com', $domain->getTracklinkDomain());
        $this->assertTrue($domain->isTracklinkDomainVerified());
        $this->assertFalse($domain->isAuthDomainVerified());
        $this->assertEquals('selector1', $domain->getDnsSelector());
        $this->assertEquals('v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDF...', $domain->getDnsRecord());
        $this->assertTrue($domain->hasClickTrack());
        $this->assertTrue($domain->hasOpenTrack());
        $this->assertFalse($domain->hasUnsubTrack());
        $this->assertEquals(1640995200, $domain->getCreatedAt());
        $this->assertEquals(1640995300, $domain->getModifiedAt());
    }

    public function testDomainWithNullValues()
    {
        $domain = new Domain([
            'id' => null,
            'domain' => null,
            'tracklink_domain' => null,
            'tracklink_domain_is_verified' => null,
            'auth_domain_is_verified' => null,
            'dns_selector' => null,
            'dns_record' => null,
            'click_track' => null,
            'open_track' => null,
            'unsub_track' => null,
            'created_at' => null,
            'modified_at' => null,
            'is_verified' => null,
            'status' => null,
            'auth_domain' => null,
            'auth_domain_dkim_record' => null,
            'auth_domain_dkim_selector' => null
        ]);

        $this->assertNull($domain->getId());
        $this->assertNull($domain->getDomain());
        $this->assertNull($domain->getTracklinkDomain());
        $this->assertNull($domain->isTracklinkDomainVerified());
        $this->assertNull($domain->isAuthDomainVerified());
        $this->assertNull($domain->getDnsSelector());
        $this->assertNull($domain->getDnsRecord());
        $this->assertNull($domain->hasClickTrack());
        $this->assertNull($domain->hasOpenTrack());
        $this->assertNull($domain->hasUnsubTrack());
        $this->assertNull($domain->getCreatedAt());
        $this->assertNull($domain->getModifiedAt());
    }

    public function testDomainSetters()
    {
        $domain = new Domain();

        $domain->setId(5)
               ->setDomain('test.com')
               ->setTracklinkDomain('track.test.com')
               ->setTracklinkDomainVerified(true)
               ->setAuthDomainVerified(true)
               ->setDnsSelector('selector2')
               ->setDnsRecord('new-dns-record')
               ->setClickTrack(false)
               ->setOpenTrack(false)
               ->setUnsubTrack(true)
               ->setCreatedAt(1640995400)
               ->setModifiedAt(1640995500);

        $this->assertEquals(5, $domain->getId());
        $this->assertEquals('test.com', $domain->getDomain());
        $this->assertEquals('track.test.com', $domain->getTracklinkDomain());
        $this->assertTrue($domain->isTracklinkDomainVerified());
        $this->assertTrue($domain->isAuthDomainVerified());
        $this->assertEquals('selector2', $domain->getDnsSelector());
        $this->assertEquals('new-dns-record', $domain->getDnsRecord());
        $this->assertFalse($domain->hasClickTrack());
        $this->assertFalse($domain->hasOpenTrack());
        $this->assertTrue($domain->hasUnsubTrack());
        $this->assertEquals(1640995400, $domain->getCreatedAt());
        $this->assertEquals(1640995500, $domain->getModifiedAt());
    }

    public function testDomainToArray()
    {
        $data = [
            'id' => 3,
            'domain' => 'arraytest.com',
            'tracklink_domain' => 'track.arraytest.com',
            'tracklink_domain_is_verified' => true,
            'auth_domain_is_verified' => false,
            'dns_selector' => 'array-selector',
            'dns_record' => 'array-dns-record',
            'click_track' => true,
            'open_track' => false,
            'unsub_track' => true,
            'created_at' => 1640995600,
            'modified_at' => 1640995700,
            'is_verified' => null,
            'status' => null,
            'auth_domain' => null,
            'auth_domain_dkim_record' => null,
            'auth_domain_dkim_selector' => null
        ];

        $domain = new Domain($data);
        $array = $domain->toArray();

        $this->assertEquals($data, $array);
    }

    public function testDomainJsonSerialization()
    {
        $data = [
            'id' => 4,
            'domain' => 'json.com',
            'tracklink_domain' => 'track.json.com',
            'tracklink_domain_is_verified' => true,
            'auth_domain_is_verified' => true,
            'dns_selector' => 'json-selector',
            'dns_record' => 'json-dns-record',
            'click_track' => false,
            'open_track' => false,
            'unsub_track' => false,
            'created_at' => 1640995800,
            'modified_at' => 1640995900,
            'is_verified' => null,
            'status' => null,
            'auth_domain' => null,
            'auth_domain_dkim_record' => null,
            'auth_domain_dkim_selector' => null
        ];

        $domain = new Domain($data);
        $json = json_encode($domain);
        $decoded = json_decode($json, true);

        $this->assertEquals($data, $decoded);
    }

    public function testDomainGetCreatedAtDateTime()
    {
        $domain = new Domain(['created_at' => 1640995200]);
        $dateTime = $domain->getCreatedAtDateTime();

        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertEquals('2022-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testDomainGetCreatedAtDateTimeWithNullValue()
    {
        $domain = new Domain(['created_at' => null]);
        $dateTime = $domain->getCreatedAtDateTime();

        $this->assertNull($dateTime);
    }

    public function testDomainGetModifiedAtDateTime()
    {
        $domain = new Domain(['modified_at' => 1640995300]);
        $dateTime = $domain->getModifiedAtDateTime();

        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertEquals('2022-01-01 00:01:40', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testDomainGetModifiedAtDateTimeWithNullValue()
    {
        $domain = new Domain(['modified_at' => null]);
        $dateTime = $domain->getModifiedAtDateTime();

        $this->assertNull($dateTime);
    }

    public function testDomainWithEmptyData()
    {
        $domain = new Domain();

        $this->assertNull($domain->getId());
        $this->assertNull($domain->getDomain());
        $this->assertNull($domain->getTracklinkDomain());
        $this->assertNull($domain->isTracklinkDomainVerified());
        $this->assertNull($domain->isAuthDomainVerified());
        $this->assertNull($domain->getDnsSelector());
        $this->assertNull($domain->getDnsRecord());
        $this->assertNull($domain->hasClickTrack());
        $this->assertNull($domain->hasOpenTrack());
        $this->assertNull($domain->hasUnsubTrack());
        $this->assertNull($domain->getCreatedAt());
        $this->assertNull($domain->getModifiedAt());
    }
}