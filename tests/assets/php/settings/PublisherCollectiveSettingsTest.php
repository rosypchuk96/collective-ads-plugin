<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include __DIR__.'/../../../publisher-collective_mock_functions.php';
include __DIR__.'/../../../../assets/php/settings/PublisherCollectiveSettings.php';

/**
 * Class PublisherCollectiveSettingsTest.
 * @runTestsInSeparateProcesses
 */
final class PublisherCollectiveSettingsTest extends TestCase
{
    public static function getProperty($object, $property)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function testSuccessReceivedIfExtraParamsSent(): void
    {
        $_POST['pc-ads-txt-extra-params'] = 'some-value';
        $publisherCollectiveSettings = new PublisherCollectiveSettings();
        $this->getProperty($publisherCollectiveSettings, 'resultMessage');
        $publisherCollectiveSettings->handleSubmission();
        $resultMessage = $this->getProperty($publisherCollectiveSettings, 'resultMessage');
        $this->assertEquals($resultMessage['status'], 'success');
        $this->assertEquals($resultMessage['message'], 'Successfully undated');
    }

    public function testSuccessReceivedIfExtraParamsEmpty(): void
    {
        $_POST['pc-ads-txt-extra-params'] = '';
        $publisherCollectiveSettings = new PublisherCollectiveSettings();
        $this->getProperty($publisherCollectiveSettings, 'resultMessage');
        $publisherCollectiveSettings->handleSubmission();
        $resultMessage = $this->getProperty($publisherCollectiveSettings, 'resultMessage');
        $this->assertEquals($resultMessage['status'], $publisherCollectiveSettings::RESULT_STATUS['SUCCESS']);
        $this->assertEquals($resultMessage['message'], $publisherCollectiveSettings::RESULT_MESSAGES['SUCCESS']);
    }

    public function testNULLReceivedIfExtraParamsNOTSent(): void
    {
        $publisherCollectiveSettings = new PublisherCollectiveSettings();
        $this->getProperty($publisherCollectiveSettings, 'resultMessage');
        $publisherCollectiveSettings->handleSubmission();
        $resultMessage = $this->getProperty($publisherCollectiveSettings, 'resultMessage');
        $this->assertEquals($resultMessage['status'], null);
        $this->assertEquals($resultMessage['message'], '');
    }
}
