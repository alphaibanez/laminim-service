<?php

namespace Lkt\FileReader;

use Lkt\FileReader\Drivers\FSDriverInterface;

abstract class FSAbstract implements FSInterface
{

    /**
     * @var \Lkt\FileReader\Drivers\FSDriverInterface
     */
    protected $filereader;

    /**
     * @var string
     */
    protected $path;

    /**
     * FSAbstract constructor.
     *
     * @param \Lkt\FileReader\Drivers\FSDriverInterface $driver
     */
    public function __construct(FSDriverInterface $driver)
    {
        $this->filereader = $driver;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {

        if (property_exists($this, $name) && $name !== 'filereader') {
            return $this->{$name};
        }

        return null;
    }

    /**
     * @return array
     */
    public function info(): array
    {
        return pathinfo($this->path);
    }

}
