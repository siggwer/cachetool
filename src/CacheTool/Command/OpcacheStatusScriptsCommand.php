<?php

namespace CacheTool\Command;

use CacheTool\Util\Formatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OpcacheStatusScriptsCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('opcache:status:scripts')
            ->setDescription('Show scripts in the opcode cache')
            ->setHelp('');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureExtensionLoaded('Zend OPcache');

        $info = $this->getCacheTool()->opcache_get_status(true);
        $scripts = $info['scripts'];


        $table = $this->getHelper('table');
        $table
            ->setHeaders(array(
                'Hits',
                'Memory',
                'Filename'
            ))
            ->setRows($this->processFilelist($info['scripts']))
        ;

        $table->render($output);
    }

    protected function processFileList(array $cacheList)
    {
        $list = array();

        foreach ($cacheList as $item) {
            $list[] = array(
                number_format($item['hits']),
                Formatter::bytes($item['memory_consumption']),
                $this->processFilename($item['full_path']),
            );
        }

        return $list;
    }

    protected function processFilename($filename)
    {
        $dir = getcwd();

        if (0 === strpos($filename, $dir)) {
            return "." . substr($filename, strlen($dir));
        }

        return $filename;
    }
}
