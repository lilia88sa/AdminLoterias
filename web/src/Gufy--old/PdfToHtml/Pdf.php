<?php

namespace App\Gufy\PdfToHtml;

class Pdf
{
    protected $file;
    protected $info;

    public function __construct($file, $options = [])
    {
        $this->file = $file;
        $class = $this;
        array_walk($options, function ($item, $key) use ($class) {
            $class->$key = $item;
        });

        return $this;
    }

    public function getInfo()
    {
        $this->checkInfo();

        return $this->info;
    }

    protected function info()
    {

        if (PHP_OS === 'WINNT') {
            $content = shell_exec('"'.$this->bin().'" "'.$this->file.'"');
        }
        else {
            $content = shell_exec($this->bin()." '".$this->file."'");
        }

        // print_r($info);
        $options = explode("\n", $content);
        $info = [];
        foreach ($options as &$item) {
            if (!empty($item)) {
                list($key, $value) = explode(":", $item);
                $info[ str_replace([" "], ["_"], strtolower($key)) ] = trim($value);
            }
        }
        $this->info = $info;

        return $this;
    }

    public function getDom($options = [])
    {
        $this->checkInfo();

        return new Html($this->file, $options);
    }

    public function html($page = 1, $options = [])
    {
        $dom = $this->getDom($options);

        return $dom->raw($page);
    }

    public function getPages()
    {
        $this->checkInfo();

        return $this->info['pages'];
    }

    private function checkInfo()
    {
        if ($this->info == null)
            $this->info();
    }

    public function bin()
    {
        return Config::get('pdfinfo.bin', '/usr/bin/pdfinfo');
    }
}
