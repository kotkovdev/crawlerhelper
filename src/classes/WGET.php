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
    private $recursive = false;

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

    public function setRecursive($recursive) {
        $this->recursive = $recursive;
    }

    public function process()
    {
        $this->parseConfig();
        $output = [];
        foreach ($this->urls as $url) {
            $output[] = $this->execute($url);
        }
        return $output;
    }

    private function parseUrl($url)
    {
        $url = trim($url, " \t\n\r");
        $url = parse_url($url);
        if (isset($url['path'])) {
            return $url['host'] . $url['path'];
        } else {
            return $url['host'];
        }

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
                default: break;
            }
        }
    }

    private function buildCommand($url)
    {
        /**
         * Set url
         */
        $command = 'wget ' . $url . ' -E -np --default-page=index.php';
        /**
         * Generate path, make dir and add params
         */
        $instanceName = time() . '_' . $url;
        $downloadPath = $this->path . $instanceName;
        $command .= ' -P ' . $downloadPath;
        /**
         * Set ignoring robots.txt
         */
        if ($this->ignoreRobotsTxt) {
            $command .= ' -e robots=off';
        }

        if ($this->spanHosts) {
            $command .= ' --span-hosts';
        }

        if ($this->convertLinks) {
            $command .= ' --convert-links';
        }

        if ($this->function == 'download') {
            $command .= ' -p -k';
        }

        if ($this->recursive) {
            $command .= ' -r';
        }

        $this->command = $command;
        return $instanceName;
    }

    private function execute($url) {
        $url = $this->parseUrl($url);
        $instanceName = $this->buildCommand($url);
        $output = array();
        exec($this->command, $output, $status);
        return ['instance' => $instanceName, 'output' => $output, 'status' => $status, 'url' => $url];
    }
}