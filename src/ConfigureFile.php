<?php

namespace AstroQuiz;
use Brus\File;

class ConfigureFile extends File
{
    private $parameters = array(
        'password' => null,
        'question-file' => null,
        'width-image' => null
    );

    public function getPassword()
    {
        if (!isset($this->parameters['password'])) {
            $this->loadConfigureParameters();
        }

        return $this->parameters['password'];
    }

    public function getFilenameWithQuestions()
    {
        if (!isset($this->parameters['question-file'])) {
            $this->loadConfigureParameters();
        }

        return $this->parameters['question-file'];
    }

    public function getImagesWidth()
    {
        if (!isset($this->parameters['width-image'])) {
            $this->loadConfigureParameters();
        }

        return $this->parameters['width-image'];
    }

    private function loadConfigureParameters()
    {
        $fd = $this->descriptor();
        $size = $this->size();
        flock($fd, LOCK_SH);

        while ($size--) {
            $line = fgets($fd);
            foreach ($this->parameters as $key => $value) {
                $modkey = $key . ":";
                $regex = "/^" . $modkey . "/";
                if (preg_match($regex, $line)) {
                    $this->parameters[$key] = trim(str_replace($modkey, "", $line));
                }
            }
        }

        rewind($fd);
        flock($fd, LOCK_UN);

        foreach ($this->parameters as $key => $value) {
            if (!isset($value) || $value == "") {
                throw new WrongConfiguration("Incorrect structure in " . $this->name() . " file");
            }
        }
    }
}

class WrongConfiguration extends \Exception
{
}
