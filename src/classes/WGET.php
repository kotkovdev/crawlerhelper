<?php
namespace App\Classes;

class WGET {
    /**
     * @var builded command for WGET
     */
    private $command;

    /**
     * @var array settings for parsing and execute
     */
    private $settings;

    /**
     * @var URLs list
     */
    private $urls;

    /**
     * @var Saving instances path
     */
    private $path;

    /**
     * @var int Depth
     */
    private $depth = 10;

    /**
     * @var Function type
     */
    private $type;

    /**
     * @var bool Ignpring robots txt
     */
    private $ignoreRobotsTxt = false;

    /**
     * @var bool Span hosts
     */
    private $spanHosts = false;

    /**
     * @var bool Convert parsed links
     */
    private $convertLinks = false;

    /**
     * @var Function value for first function
     */
    private $function;

    /**
     * @var bool Recursive parsing
     */
    private $recursive = false;

    /**
     * @var int Timestamp for log and instances
     */
    private $timestamp;

    /**
     *
     * Initialize WGET helper and set settings
     *
     * WGET constructor.
     * @param array $settings
     */
    public function __construct($settings = array())
    {
        $this->settings = $settings;
        $this->timestamp = time();
    }

    /**
     *
     * Set function for first function (download|list) resources
     *
     * @param $function
     */
    private function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     *
     * Set ignore txt
     *
     * @param $value
     */
    private function setIgnoreRobotsTxt($value)
    {
        $this->ignoreRobotsTxt = $value;
    }

    /**
     *
     * Set span hosts
     *
     * @param $value
     */
    private function setSpanHosts($value)
    {
        $this->spanHosts = $value;
    }

    /**
     *
     * Set convert links
     *
     * @param $value
     */
    private function setConvertLinks($value)
    {
        $this->convertLinks = $value;
    }

    /**
     *
     * Set URLs list
     *
     * @param $urls
     */
    public function setUrls($urls)
    {
        if (strlen($urls) <= 0) {
            throw new Exception ("Empty urls list");
        }
        $this->urls = explode(PHP_EOL, $urls);
    }

    /**
     *
     * Set path for instances
     *
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     *
     * Set recursive
     *
     * @param $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     *
     * Set parsing depth
     *
     * @param $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     *
     * Set function type
     *
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * Run process
     *
     * @return array output
     */
    public function process()
    {
        $this->parseConfig();
        $output = [];
        foreach ($this->urls as $url) {
            $output[] = $this->execute($url);
        }
        return $output;
    }

    /**
     *
     * Parse urls list
     *
     * @param $url
     * @return string
     */
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

    /**
     * Parse config from array
     */
    private function parseConfig()
    {
        foreach ($this->settings as $key => $item) {
            switch ($key) {
                case 'type' : $this->setType($item); break;
                case 'urls' : $this->setUrls($item); break;
                case 'ignore_robots_txt': if ($item == 'on') { $this->setIgnoreRobotsTxt(true); } break;
                case 'span_hosts': if ($item == 'on') { $this->setSpanHosts(true); } break;
                case 'convert_links': if ($item == 'on') { $this->setConvertLinks(true); } break;
                case 'function' : $this->setFunction($item); break;
                case 'depth' : $this->setDepth($item); break;
                default: break;
            }
        }
    }

    /**
     *
     * Build command for WGET
     *
     * @param $url
     * @return string
     */
    private function buildCommand($url)
    {
        $instanceName = $this->timestamp . '_' . $url;
        $downloadPath = $this->path . $instanceName;

        switch ($this->type) {
            case 1:
                switch ($this->function) {
                    case 'download' : $command = 'wget ' . $url . ' -E -np -p'; break;
                    case 'list' : $command = 'wget --spider ' . $url . ' -r -nd -nv'; break;
                    default: throw new Exception('Crawler initializing error'); break;
                }
                break;
            case 2:
                $command = 'wget ' . $url . ' -E -p';
                break;
            case 3:
                $command = 'wget ' . $url . ' --spider -r -nd -nv';
                break;
            default: throw new Exception('Crawler initializing error'); break;
        }

        /**
         * Set url
         */
        //$command = 'wget ' . $url . ' -E -np --default-page=index.php';
        /**
         * Generate path, make dir and add params
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

        if ($this->type == 1 || $this->type == 2) {
            $command .= ' -P ' . $downloadPath;
        }

        $command .= ' -o ' . LOG_PATH . '/' . $instanceName . '.log';

        if ($this->recursive) {
            $command .= ' -r';
        }

        if ($this->depth) {
            $command .= ' -l ' . $this->depth;
        }

        /**
         * Set ignoring robots.txt
         */

        $this->command = $command;
        return $instanceName;
    }

    /**
     *
     * Execute builded command
     *
     * @param $url
     * @return array
     */
    private function execute($url) {
        $url = $this->parseUrl($url);
        $instancePath = $this->buildCommand($url);
        $instanceName = $this->timestamp . '_' . $url;
        $output = array();
        exec($this->command, $output, $status);
        return ['path' => $instancePath, 'name' => $instanceName, 'output' => $output, 'status' => $status, 'url' => $url, 'command' => $this->command];
    }
}