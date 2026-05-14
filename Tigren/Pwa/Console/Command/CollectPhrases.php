<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Console\Command;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\DriverPool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class CollectPhrases
 * @package Tigren\Pwa\Console\Command
 */
class CollectPhrases extends Command
{
    /**
     * @var DriverPool|null
     */
    protected $driverPool;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * CollectPhrases constructor.
     * @param ResourceConnection $resourceConnection
     * @param Filesystem $filesystem
     * @param DriverPool|null $driverPool
     * @param string|null $name
     * @throws FileSystemException
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Filesystem $filesystem,
        DriverPool $driverPool = null,
        string $name = null
    ) {
        parent::__construct($name);
        $this->resourceConnection = $resourceConnection;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->driverPool = $this->driverPool ?: ObjectManager::getInstance()->get(DriverPool::class);
        $this->driver = $this->driverPool->getDriver(DriverPool::FILE);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('pwa:translate:collect-phrases');
        $this->addOption(
            'sourceType',
            null,
            InputOption::VALUE_OPTIONAL,
            'Source file type',
            'json'
        );
        $this->addOption(
            'i18nPath',
            null,
            InputOption::VALUE_OPTIONAL,
            'Folder path that contains translation files'
        );
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $locales = [
                'en_US',
                'el_GR',
                'es_ES',
                'fr_FR',
                'it_IT',
                'nl_NL',
                'pt_PT',
                'th_TH',
                'tr_TR'
            ];

            $sourceType = $input->getOption('sourceType');
            $i18nPath = $input->getOption('i18nPath');
            if (!$i18nPath) {
                $i18nPath = BP . '/pub/media/i18n';
            }

            if ($sourceType === 'csv') {
                $pwaPhrases = $this->getCsvContent($output, "$i18nPath/pwa/default.csv");
                $pwaReversedPhrases = $this->getCsvContent($output, "$i18nPath/pwa/default.csv", true);
            } else {
                $pwaPhrases = $this->getJsonContent($output, "$i18nPath/pwa/default.json");
                $pwaReversedPhrases = $this->getJsonContent($output, "$i18nPath/pwa/default.json", true);
            }

            $connection = $this->resourceConnection->getConnection();
            $inlineTranslations = [];
            $inlineTranslationFetchedData = $connection->fetchAll(
                $connection->select()
                    ->from($this->resourceConnection->getTableName('translation'))
            );
            foreach ($inlineTranslationFetchedData as $inlineTranslationFetchedRow) {
                $inlineTranslations[$inlineTranslationFetchedRow['locale']][$inlineTranslationFetchedRow['string']] = $inlineTranslationFetchedRow['translate'];
            }

            foreach ($locales as $locale) {
                $packagePhrases = $this->getCsvContent($output,
                    "$i18nPath/package/$locale.csv");
                $themePhrases = $this->getCsvContent($output,
                    "$i18nPath/theme/$locale.csv");
                $magentoPhrases = array_merge($packagePhrases, $themePhrases);

                if (isset($inlineTranslations[$locale])) {
                    $magentoPhrases = array_merge($magentoPhrases, $inlineTranslations[$locale]);
                }

                $firstPwaCollectedPhrases = [];
                foreach ($magentoPhrases as $magentoPhraseKey => $magentoPhraseTranslation) {
                    if (isset($pwaReversedPhrases[$magentoPhraseKey])) {
                        if (!is_array($pwaReversedPhrases[$magentoPhraseKey])) {
                            $firstPwaCollectedPhrases[$pwaReversedPhrases[$magentoPhraseKey]] = $magentoPhraseTranslation;
                        } else {
                            foreach ($pwaReversedPhrases[$magentoPhraseKey] as $pwaReversedPhraseKey) {
                                $firstPwaCollectedPhrases[$pwaReversedPhraseKey] = $magentoPhraseTranslation;
                            }
                        }
                    }
                }

                $pwaCollectedPhrases = [];
                foreach ($pwaPhrases as $pwaPhraseKey => $pwaPhraseTranslation) {
                    if (isset($firstPwaCollectedPhrases[$pwaPhraseKey])) {
                        $pwaCollectedPhrases[$pwaPhraseKey] = $firstPwaCollectedPhrases[$pwaPhraseKey];
                    } else {
                        $pwaCollectedPhrases[$pwaPhraseKey] = $pwaPhraseTranslation;
                    }
                }

                $this->putJsonContent("$i18nPath/pwa/$locale.json", $pwaCollectedPhrases);
            }
        } catch (Throwable $e) {
            $output->writeln('<error>Critical issue: ' . $e->getMessage() . '</error>');
            while ($e = $e->getPrevious()) {
                $output->writeln('<error>previous: ' . $e->getMessage() . '</error>');
            }
        }
    }

    /**
     * Load the generated CSV
     *
     * @param OutputInterface $output
     * @param string $filePath
     * @param bool $isReversed
     * @return array
     * @throws FileSystemException
     */
    private function getJsonContent(OutputInterface $output, string $filePath, $isReversed = false)
    {
        $results = $this->driver->fileGetContents($filePath);
        $results = json_decode($results);

        foreach ($results as $key => $value) {
            if (!$isReversed) {
                $csv[$key] = $value;
            } else {
                if (isset($csv[$value]) && !is_array($csv[$value])) {
                    $existingValue = $csv[$value];
                    unset($csv[$value]);
                    $csv[$value] = [];
                    $csv[$value][] = $existingValue;
                    $csv[$value][] = $key;
                } elseif (isset($csv[$value])) {
                    $csv[$value][] = $key;
                } else {
                    $csv[$value] = $key;
                }
            }
        }

        return $csv;
    }

    /**
     * Load the generated CSV
     *
     * @param OutputInterface $output
     * @param string $filePath
     * @param bool $isReversed
     * @return array
     * @throws FileSystemException
     */
    private function getCsvContent(OutputInterface $output, string $filePath, $isReversed = false)
    {
        $csv = [];

        try {
            $resource = $this->driver->fileOpen($filePath, 'r');
        } catch (Exception $e) {
            return $csv;
        }

        while ($result = $this->driver->fileGetCsv($resource)) {
            if (!isset($result[0]) || !isset($result[1])) {
                continue;
            }

            if (!$isReversed) {
                $csv[$result[0]] = $result[1];
            } else {
                if (isset($csv[$result[1]]) && !is_array($csv[$result[1]])) {
                    $existingValue = $csv[$result[1]];
                    unset($csv[$result[1]]);
                    $csv[$result[1]] = [];
                    $csv[$result[1]][] = $existingValue;
                    $csv[$result[1]][] = $result[0];
                } elseif (isset($csv[$result[1]])) {
                    $csv[$result[1]][] = $result[0];
                } else {
                    $csv[$result[1]] = $result[0];
                }
            }
        }
        $this->driver->fileClose($resource);

        return $csv;
    }

    /**
     * Replace the saveData method by allowing to select the input mode
     *
     * @param string $file
     * @param array $data
     * @param string $mode
     *
     * @return $this
     *
     * @throws FileSystemException
     */
    public function putJsonContent($file, $data, $mode = 'w')
    {
        $fileHandler = fopen($file, $mode);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->driver->fileWrite($fileHandler, $json);
        fclose($fileHandler);
        return $this;
    }

    /**
     * @param $file
     * @param $data
     * @return $this
     * @throws FileSystemException
     */
    public function putCsvContent($file, $data)
    {
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        foreach ($data as $key => $value) {
            $data = [];
            $data[] = $key;
            $data[] = $value;
            $stream->writeCsv($data);
        }

        return $this;
    }
}
