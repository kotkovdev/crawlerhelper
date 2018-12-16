<?php
namespace App\Classes;

class WGET {
    private $command;
    private $settings;
    private $urls;
    private $path;
    private $ignoreRobotsTxt = false;
    private $spanHosts = false;
    private $convertLinks = false;
    private $function;

    public function __construct($settings = array())
    {
        $this->settings = $settings;
    }

    private function setFunction($function)
    {
        $this->function = $function;
    }

    private function setIgnoreRobotsTxt($value)
    {
        $this->ignoreRobotsTxt = $value;
    }

    private function setSpanHosts($value)
    {
        $this->spanHosts = $value;
    }

    private function setConvertLinks($value)
    {
        $this->convertLinks = $value;
    }

    public function setUrls($urls)
    {
        if (strlen($urls) <= 0) {
            throw new Exception ("Empty urls list");
        }
        $this->urls = explode(PHP_EOL, $urls);
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function process($settings = array())
    {
        $this->parseConfig();
        foreach ($this->urls as $url) {
            $this->execute($url);
        }

    }

    private function parseUrl($url)
    {
        $url = trim($url, " \t\n\r");
        $url = parse_url($url);
        return $url['host'] . $url['path'];
    }

    private function parseConfig()
    {
        foreach ($this->settings as $key => $item) {
            switch ($key) {
                case 'urls' : $this->setUrls($item); break;
                case 'ignore_robots_txt': if ($item == 'on') { $this->setIgnoreRobotsTxt(true); } break;
                case 'span_hosts': if ($item == 'on') { $this->setSpanHosts(true); } break;
                case 'convert_links': if ($item == 'on') { $this->setConvertLinks(true); } break;
                case 'function' : $this->setFunction($item); break;
            }
        }
    }

    private function buildCommand($url)
    {
        /**
         * Set url
         */
        $command = 'wget ' . $url . ' -E - np --default-page=index.php';
        /**
         * Generate path, make dir and add params
         */
        $downloadPath = $this->path . time() . '_' . $url;
        $command .= ' -P ' . $downloadPath;
        /**
         * Set ignoring robots.txt
         */
        if ($this->ignoreRobotsTxt) {
            $command .= ' -e robots=off';
        }

        if ($this->spanHost) {
            $command .= ' --span-hosts';
        }

        if ($this->convertLinks) {
            $command .= ' --convert-links';
        }

        if ($this->function == 'download') {
            $command .= ' -p -k';
        }

        $this->command = $command;
    }

    private function execute($url) {
        $url = $this->parseUrl($url);
        $this->buildCommand($url);
        $output = array();
        exec($this->command, $output, $status);
    }
}