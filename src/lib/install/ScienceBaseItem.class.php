<?php


class ScienceBaseItem {

    public $baseUrl = 'https://www.sciencebase.gov/catalog/item/';
    public $files = null;
    public $id;

    public function __construct ($id) {
        $this->id = $id;
    }

    public function getFiles () {
        if ($this->files != null) {
            return $this->files;
        }

        $data = json_decode(
                file_get_contents($this->baseUrl . $this->id . '?format=json'),
                true);
        $this->files = array();
        foreach ($data['files'] as $file) {
            $this->files[$file['name']] = $file;
        }
        return $this->files;
    }

    public function getUrl ($path) {
        $files = $this->getFiles();

        if (!isset($files[$path])) {
            return null;
        }
        return $files[$path]['url'];
    }

}

