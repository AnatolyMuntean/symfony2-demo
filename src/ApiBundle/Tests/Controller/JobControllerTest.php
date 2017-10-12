<?php
/**
 * @file
 */

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testGetJobs()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/jobs.json');
        $this->assertTrue(404 == $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/api/jobs/symfony.json');
        $this->assertTrue(404 == $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/api/jobs/sensio_labs.json');
        $this->assertTrue(200 == $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $jobs = json_decode($client->getResponse()->getContent());
        $this->assertTrue(null !== $jobs);

        foreach ($jobs as $job) {
            $this->assertTrue('Programming' == $job->category_name);
        }
    }
}
